<?php
  
    /**
     * Render html to create job step2 view
     */
    class JobStep2View extends View    {
        private $jobId;
        private $agmtId;
        private $isEdit;        

        /**
         * @param type $jobId value assigend job ID
         * @param type $edit value assigend based on job Edit mode or job Clone mode. If Edit mode, $edit value assigend as 'edit'. If CLONE, value assigend as 'clone' and If CREATE, value assigend as 'create'.
         * @param type $newClonedJobId value assigend based on job Edit mode or job Clone mode. If Edit mode and Create mode, $edit value assigend as 0. If CLONE mode, value assigend as 'cloned job Id'.
         */
	public function __construct($jobId, $agmtId, $type) {
                $this->jobId = $jobId;
                $this->agmtId = $agmtId;               
                if($type == '')
                    $this->isEdit = 'create';
               else
                   $this->isEdit = $type;
	}
  
        public function renderContent()
        {   
            $content = $this->generateHTMLView();
            $content .= $this->renderScripts();
            return $content;
        }
        
        public function generateHTMLView(){
            return $this->generateHTMLStartView() . $this->generatePanelWrapperHTML() .
                    $this->generateFooterContentHTML() . $this->generateHTMLEndView();
            
        }        
        
        public function generateHTMLStartView() {
            $content = '<div class="SecuredEditAndDetailsView EditAndDetailsView DetailsView ModelView ConfigurableMetadataView MetadataView" id="JobSchedulingStep2EditAnDDetailView"><div class="wrapper"><h1><span class="truncated-title" threedots="Create Agreement"><span class="ellipsis-content">Create Schedule</span></span></h1></div><div class="wide">';
            return $content;
        }
        
        public function generatePanelWrapperHTML() {
            $editvalue = 0;
            $cat_arr = array();
            $arr_cat = array();
            $jobArr  = array();
            if($this->isEdit == 'edit'){
                $editvalue = 1;                
                $jobData = AgreementJobs::getAgmtJobsByJobId($this->jobId);
                
                foreach($jobData as $jobDataArr)
                {     
                    $jobArr[$jobDataArr->category->id.'_week_'.$jobDataArr->week_no] = $jobDataArr->value;                    
                }
                
                $job_data = JobScheduling::getById($this->jobId);
                $createdDateTime = $job_data->scheduleDate;
                
                $time=strtotime($createdDateTime);
                $monthCond=date("m",$time);
                $yearCond=date("Y",$time);
                
                $agmt_prod_arr = AgreementProduct::getAgmtProdCategoryByAgmtId($this->agmtId);
              
                foreach($agmt_prod_arr as $cat_arr)
                {                   
                    $category_arr = Category::getCategoryByName($cat_arr->Category);
                    if(!empty($category_arr)){
                        $arr_cat[$category_arr[0]->id]['name']    = $cat_arr->Category;
                        $arr_cat[$category_arr[0]->id]['mhr']     = (!empty($arr_cat[$category_arr[0]->id]['mhr']) ? $arr_cat[$category_arr[0]->id]['mhr'] + $cat_arr->Total_MHR : $cat_arr->Total_MHR);
                    }    
                }
            }
            else if($this->isEdit == 'create'){
                $agmt_prod_arr = AgreementProduct::getAgmtProdCategoryByAgmtId($this->agmtId);
                foreach($agmt_prod_arr as $cat_arr)
                {
                    $category_arr = Category::getCategoryByName($cat_arr->Category);
                    if(!empty($category_arr)){
                        $arr_cat[$category_arr[0]->id]['name']    = $cat_arr->Category;                        
                        $arr_cat[$category_arr[0]->id]['mhr']     = (!empty($arr_cat[$category_arr[0]->id]['mhr']) ? $arr_cat[$category_arr[0]->id]['mhr'] + $cat_arr->Total_MHR : $cat_arr->Total_MHR);
                    }    
                }
                $monthCond = date('m');
                $yearCond = date("Y");
            }
            
            $csrfTokenName  = Yii::app()->request->csrfTokenName;
            $csrfToken      = Yii::app()->request->csrfToken;
            
            $content = '<div class="attributesContainer">
                            <div class="full-width">
                                <div class="panel">';
            $content .= '<div class="panel details-table">
                        <div class="panelTitle" style="padding-left: 10px;"> Job Scheduling Information</div>';
            if($editvalue == 1){
                $content .= '<input id="jobRenderType" name="jobRenderTypeName" value="edit" type="hidden">';
            }else{
                $content .= '<input id="jobRenderType" name="jobRenderTypeName" value="create" type="hidden">';
            }
            $content .= '       <form id="jobSchedulingForm" action="" method="post">
                                <input type="hidden" value="'.$csrfToken.'" id="YII_CSRF_TOKEN" name="'.$csrfTokenName.'" />';
            
            $content .= '<table id="jobEditDetailsTable" border = "1">
                            <tbody>
                                <tr>
                                    <td style="background-color : #3C8DBC; color: #FFFFFF; width: 15%;">Category</td>';
            foreach ($arr_cat as $catId => $catName)
            {                       
                $content .='<td style="font-weight: normal;">'.$catName['name'].'</td>';
            }     
                        $content .='</tr>
                                <tr>
                                    <td style="background-color : #A3C9DF; color: #FFFFFF; width: 15%;">Total Man Hours</td>';
            foreach ($arr_cat as $catId => $catName)
            {                       
                $content .='<td style="font-weight: normal;">'.$catName['mhr'].'</td>';
            }
                            $content .='</tr>
                                </tbody>
                        </table>';
                            
            foreach ($arr_cat as $catId => $catName)
            {
                $content .= '<input type="hidden" readonly=readonly value="'.$catName['mhr'].'" id="catIds_'.$catId.'" name="'.$catName['name'].'" />';
            }
            
            $content .= '<table class="form-fields" id="weekSchedules" border="0">                                
                                <tbody><tr><td colspan="15">';

            switch (trim($monthCond)) {
                case '01':
                        $season_arr =   array(
                                    'Winter 1 - '.$yearCond => array(1,2),
                                    'Spring - '.$yearCond => array(3,4,5),
                                    'Summer - '.$yearCond => array(6,7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12)
                                );
                    break;
                
                case '02':
                        $season_arr =   array(
                                    'Winter 1 - '.$yearCond => array(2),
                                    'Spring - '.$yearCond => array(3,4,5),
                                    'Summer - '.$yearCond => array(6,7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1),
                                );
                    break;
                
                case '03':
                        $season_arr =   array(
                                    'Spring - '.$yearCond => array(3,4,5),
                                    'Summer - '.$yearCond => array(6,7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                );
                    break;
                
                case '04':
                        $season_arr =   array(
                                    'Spring - '.$yearCond => array(4,5),
                                    'Summer - '.$yearCond => array(6,7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3),
                                );
                    break;
                    
                case '05':
                        $season_arr =   array(
                                    'Spring - '.$yearCond => array(5),
                                    'Summer - '.$yearCond => array(6,7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4),
                                );
                    break;
                
                case '06':
                        $season_arr =   array(
                                    'Summer - '.$yearCond => array(6,7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4,5),
                                );
                    break;
                
                case '07':
                        $season_arr =   array(
                                    'Summer - '.$yearCond => array(7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4,5),
                                    'Summer - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(6),
                                );
                    break;
                
                case '08':
                        $season_arr =   array(
                                    'Summer - '.$yearCond => array(8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4,5),
                                    'Summer - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(6,7),
                                );
                    break;
                
                case '09':
                        $season_arr =   array(
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4,5),
                                    'Summer - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(6,7,8),
                                );
                    break;
                
                case '10':
                        $season_arr =   array(
                                    'Fall - '.$yearCond => array(10),
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4,5),
                                    'Summer - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(6,7,8),
                                    'Fall - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(9),
                                );
                    break;
                
                case '11':
                        $season_arr =   array(
                                    'Winter 2 - '.$yearCond => array(11,12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4,5),
                                    'Summer - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(6,7,8),
                                    'Fall - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(9,10),
                                );
                    break;
                
                case '12':
                        $season_arr =   array(
                                    'Winter 2 - '.$yearCond => array(12),
                                    'Winter 1 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(1,2),
                                    'Spring - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(3,4,5),
                                    'Summer - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(6,7,8),
                                    'Fall - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(9,10),
                                    'Winter 2 - '.date('Y', strtotime('+1 year', strtotime($yearCond))) => array(11),
                                );
                    break;

                default:
                    $season_arr =   array(
                                    'Winter 1 - '.$yearCond => array(1,2),
                                    'Spring - '.$yearCond => array(3,4,5),
                                    'Summer - '.$yearCond => array(6,7,8),
                                    'Fall - '.$yearCond => array(9, 10),
                                    'Winter 2 - '.$yearCond => array(11,12)
                                );
                    break;
            }
            
                            $total_week_numbers = 1;
                            $previous_week_count = 1;
                            foreach($season_arr as $season=>$month_arr)
                            {
                                $content .='<div style="margin: 0 2% 0 2%; font-weight: bold; border-color: black; border-style: solid; border-width: 1px 1px 0px 1px; padding: 0.5%; background-color : #3C8DBC; color: #FFFFFF;">'.$season.'</div>
                                    <table id="jobDetailsTable" class="form-fields" border = "1">
                                    <colgroup>
                                        <col class="col-1">
                                    </colgroup>
                                    <tbody><tr><td colspan="2"></td>';
                                $week = array();
                                $seasonDateArr  = explode('-', $season);
                                
                                // To get the year of the season for the week issues
                                if(isset($seasonDateArr[1]))
                                    $yearCond = $seasonDateArr[1];
                                // Ends Here
                                
                                foreach ($month_arr as $MonthNumber) {
                                    $week[$MonthNumber] = $this->getWeeksCount($MonthNumber, $yearCond);
                                    $content .='<td colspan="'.$week[$MonthNumber].'" style = "text-align: center;">'. date("F", mktime(0, 0, 0, $MonthNumber)).'</td>';
                                }  
                             $content .='</td></tr>';                             
                             $content .='<tr><td colspan="2">Category</td>';
                
                            $num_of_weeks = array_sum($week);
                            $previous_week_count += $num_of_weeks;
                            $processed_previous_week_count = $previous_week_count - $num_of_weeks;                            
                            $total_week_numbers = $processed_previous_week_count;                            
                 
                            for($i=1; $i<=$num_of_weeks; $i++)
                            {                                    
                                $content .='<td style = "text-align: center;">W '.$total_week_numbers.'</td>';
                                $total_week_numbers++;
                            }
                                                        
                $content .=' </tr>';
                            
                            foreach ($arr_cat as $catKey => $catName) {
                $content .='<tr>
                                <td colspan="2"  style="font-weight: normal;">' . $catName['name'] . '</td>';
                                    $total_week_numbers_box = $processed_previous_week_count;
                                    
                                    for($i=1; $i<=$num_of_weeks; $i++)
                                    {                                    
                                        if(isset($jobArr[$catKey.'_week_'.$total_week_numbers_box]))
                                        {
                                           $content .='<td><input class="allow_decimal" id="weekSchedule" type="text" name="'.$catKey.'_week_'.$total_week_numbers_box.'" value="'.$jobArr[$catKey.'_week_'.$total_week_numbers_box].'" ></td>';
                                        }
                                        else
                                           $content .='<td><input class="allow_decimal" id="weekSchedule" type="text" name="'.$catKey.'_week_'.$total_week_numbers_box.'" value=""></td>';
                                        $total_week_numbers_box++;
                                    }                                                        
                $content .=' </tr>';
            }   
            
            $content .=' </tbody>
                                </table>';
                            }
                            $content .= '</td></tr>';
                             $content .='</form></tbody>
                             </table>
                        </div>
                            </div>
                       </div>';
            return $content;
        }
        
        public function generateFooterContentHTML() {
            $content = '<div class="float-bar"><div class="view-toolbar-container clearfix dock"><div class="form-toolbar">';
            $content .='<a href="/app/index.php/jobScheduling/default/edit?id='.$this->jobId.'" class="attachLoading z-button cancel-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Back</span></a>';
            
            $content .='<a href="/app/index.php/jobScheduling/default/details?id='.$this->jobId.'" class="cancel-button" id="CancelLinkActionElement--33-yt3"><span class="z-label">Cancel</span></a>'
                    . '<a href="#" onclick="javascript:createJobScheduleStep2('.$this->jobId.', '.$this->agmtId.');" class="attachLoading z-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Submit</span></a></div></div></div>';
            return $content;
        }
        
        public function generateHTMLEndView() {
            $content = '</div></div>';
            return $content;
        }
        
        public function renderScripts() {
            Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                                Yii::getPathOfAlias('application.modules.jobScheduling.elements.assets')) . '/jobSchedulingTemplateUtils.js', CClientScript::POS_END);
           
            Yii::app()->clientScript->registerScript('HideNotification',
                '$("#FlashMessageView").hide();
            ');
        }
        
        public function getWeeksCount($month, $year)
        {
            /* Commented not to use this, instead to send in the parameter itself
            if($month >= date('m'))
                $year = $year; // To take the current year
            else 
                $year = date('Y', strtotime('+1 year', strtotime($year)));
            */
            $start_day_of_week = 1; // Monday
            // Total number of days in the given month.
            $num_of_days = date("t", mktime(0,0,0,$month,1,$year));
            // Count the number of times it hits $start_day_of_week.
            $num_of_weeks = 0;
            for($i=1; $i<=$num_of_days; $i++)
            {
                $day_of_week = date('w', mktime(0,0,0,$month,$i,$year));
                if($day_of_week==$start_day_of_week)
                    $num_of_weeks++;                
            }
            return $num_of_weeks;
        }
    }
?>

