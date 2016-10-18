<?php
  
    /**
     * Render html to create JobReportView
     */
    class JobReportView extends View {
        private $weekArr;
        private $reportFor;
        private $reportYear;
        private $yearPart;
        /**
         * @param type $weekArr value assigend based on the current year's week date array
         */
	public function __construct($weekArr, $reportFor='Agreement', $reportYear='') {
            $this->weekArr = $weekArr;
            $this->reportFor = $reportFor;
            $this->reportYear = $reportYear;
            $yearPart = explode('-', $reportYear);
            $this->yearPart = $yearPart[0];
	}
  
        public function renderContent()
        {   
            $content = $this->generateHTMLView();
            $content .= $this->renderScripts();
            return $content;
        }
        
        public function generateHTMLView(){
            return  $this->generatePanelWrapperHTML();            
        }        
        
        public function generateHTMLStartView($report_for='Agreement',$count=0, $year='') {
            if($year == '')
                $year = date('Y');
            
            $previousYear = date("Y",strtotime("-1 year"));
            $currentYear = date("Y");
            $nextYear = date("Y",strtotime("+1 year"));
            $previousYearSelected = '';
            $currentYearSelected = '';
            $nextYearSelected = '';
            
            if($previousYear == $year)
                $previousYearSelected = 'selected="selected"';
            else if($currentYear == $year)
                $currentYearSelected = 'selected="selected"';
            else if($nextYear == $year)
                $nextYearSelected = 'selected="selected"';
            
            
            return $content =   '<p>&nbsp;</p>
                        <div class="BreadCrumbView" id="ReportBreadCrumbView">
                            <div class="breadcrumbs">
                                <a href='.Yii::app()->getBaseUrl(true).'/index.php/reports/default/index>Reports</a> / <span>'.$report_for.' Report</span>
                            </div>
                        </div>
                            <div id="ReportDetailsView" class="DetailsView ModelView ConfigurableMetadataView MetadataView">
                        <h1>
                        <span class="truncated-title" threedots="Job Schedule Report - '.$report_for.'">
                        <span class="ellipsis-content">Job Schedule Report - '.$report_for.'</span>
                        </span>
                        </h1>
                        </div>
                        <p>&nbsp;</p>
                            <div class="juiportlet-widget">
                                <div class="juiportlet-widget-head">
                                    <h3>Reports - For the year</h3>
                                        &nbsp;<span>
                                            <select id="reportByYear">
                                                <option '.$previousYearSelected.' value="'.$previousYear.'">'.$previousYear.'</option>
                                                <option '.$currentYearSelected.' value="'.$currentYear.'">'.$currentYear.'</option>
                                                <option '.$nextYearSelected.' value="'.$nextYear.'">'.$nextYear.'</option>
                                            </select>
                                        </span>
                                    <div class="portlet-actions-container">
                                    <div id="report-results-grid-view-summary-clone" class="list-view-items-summary-clone">'.$count.' result(s)</div>
                                    </div>    
                                </div>
                                <div class="juiportlet-widget-content">
                                    <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                        <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                            <div id="reports_agreements" class="cgrid-view type-approvalProcess">';
        }
        
        public function generatePanelWrapperHTML() {
            if($this->reportFor == 'Agreement')
                return $this->agreementReports();
            else if($this->reportFor == 'Category')
                return $this->categoryReports();
            else if($this->reportFor == 'Account')
                return $this->agmtOwnerReports();
        }
        
        public function generateNoRecordsContentHTML($report_for='Agreement', $count, $year) {
            $content = $this->generateHTMLStartView($report_for, $count, $year);
            $content .= '<div class=""><table id="" class="items">
                            <tr><td> No records found</td></tr>
                        </table></div>';
            $content .= $this->generateHTMLEndView();
            return $content;
        }
        
        public function generateHTMLEndView() {
            return $content ='     </div>
                                </div>
                            </div>
                        </div>
                    </div>';
        }
        
        public function renderScripts() {
            $redirectUrl =  Yii::app()->request->getBaseUrl(true) . '/index.php/reports/default/details?id='.Yii::app()->request->getParam('id').'&reportYear=';
            Yii::app()->clientScript->registerScript('FormSubmit',
                "$('#reportByYear').change(function(){
                     hostInfo = '" . $redirectUrl . "';
                     url = hostInfo + this.value;
                     window.location.href=url
                });
            ");
        }
        
        public function renderBodyContent($jobArr,$forName='agmtName', $randNumber)
        {
            $content = '<div class="table-wrapper">
                            <table id="report_table_home" class="items">
                                <thead class="header">
                                    <tr class="first_child">
                                        <th>Name</th>';
                        foreach ($this->weekArr as $weekNumber) {
                            $content .='<th>W'.$weekNumber.'</th>';
                        }
                $content .='<th class="report_row_total"> Total </th>';   
                $content .=' </tr></thead><tbody class="results">';
                foreach ($jobArr as $AgmtKey => $jobData) {
                    $agmtRowTotal = '';
                    if($AgmtKey == $randNumber)
                    {
                        $tblrowClass       = "report_rows";
                        $tblheaderClass    = "report_col_total_label";
                        $tbldataClass      = "report_col_total";                        
                    }
                    else
                    {
                        $tblrowClass       = "first_child";
                        $tblheaderClass    = "";
                        $tbldataClass      = "";
                    }
                    
                    $content .='<tr class='.$tblrowClass.'>';
                    if(strlen($jobData[$forName]) > 18){
                        $content .="<th class='$tblheaderClass' title ='$jobData[$forName]'>".substr($jobData[$forName],0, 18)."...</th>";
                    }else{
                        $content .="<th class='$tblheaderClass' title ='$jobData[$forName]'>".$jobData[$forName]."</th>";
                    }
                                foreach ($this->weekArr as $dayKey => $weekNumber) {
                                    $scheduledValue='-';
                                    if(isset($jobData[$dayKey]))
                                    {
                                       $scheduledValue=$jobData[$dayKey];
                                       $agmtRowTotal += $scheduledValue;
                                    }
                                                                            
                                    $content .='<td class='.$tbldataClass.'>'.$scheduledValue.'</td>';
                                }
                    $content .=' <td class="report_row_total">'.$agmtRowTotal.'</td></tr>';
                }
            $content .='    </tr></tbody>
                        </table>
                    </div>';
            
            return $content;
        }

        public function agreementReports() {
            $jobData = JobScheduling::getAllAgmtJobs($this->reportYear);
            
            if(empty($jobData))
                return $this->generateNoRecordsContentHTML('Agreement', 0, $this->yearPart);
            
            $lastArr = count($jobData)-1;
            //$lastArr = array_pop(array_keys($jobData));
            $jobArr = array();
            $randNumber = time();
            foreach($jobData as $jobDataKey=>$jobDataArr)
            {
                if($jobDataKey == $lastArr)
                {
                    $jobArr[$randNumber]['agmtName'] = 'Total';
                }
                
                $jobArr[$jobDataArr['agreement_id']]['agmtName'] = $jobDataArr['name'];
                if(isset($jobArr[$jobDataArr['agreement_id']][$jobDataArr['week_day']]))
                    $jobArr[$jobDataArr['agreement_id']][$jobDataArr['week_day']] += $jobDataArr['value'];
                else
                   $jobArr[$jobDataArr['agreement_id']][$jobDataArr['week_day']] = $jobDataArr['value'];
                
                // For the Colom wise calculations
                if(isset($jobArr[$randNumber][$jobDataArr['week_day']]))
                    $jobArr[$randNumber][$jobDataArr['week_day']] += $jobDataArr['value'];
                else
                   $jobArr[$randNumber][$jobDataArr['week_day']] = $jobDataArr['value'];
            } 
            
            // To move that total arr element to the last of the array
            $totalArr = $jobArr[$randNumber];
            unset($jobArr[$randNumber]);
            $totalResult = count($jobArr);
            $jobArr[$randNumber] = $totalArr;
            
            $content = $this->generateHTMLStartView('Agreement', $totalResult, $this->yearPart);            
            $content .= $this->renderBodyContent($jobArr, 'agmtName', $randNumber);            
            $content .= $this->generateHTMLEndView();
            return $content;
        }
        
        public function categoryReports() {
            $jobData = JobScheduling::getAllCategoryJobs($this->reportYear);
            
            if(empty($jobData))
                return $this->generateNoRecordsContentHTML('Category', 0, $this->yearPart);
            
            $lastArr = count($jobData)-1;
            $jobArr = array();
            $randNumber = time();
            
            foreach($jobData as $jobDataKey=>$jobDataArr)
            {     
                if($jobDataArr['name'])
                {
                    if($jobDataKey == $lastArr)
                        $jobArr[$randNumber]['catName'] = 'Total';
                    
                    $jobArr[$jobDataArr['category_id']]['catName'] = $jobDataArr['name'];
                    if(isset($jobArr[$jobDataArr['category_id']][$jobDataArr['week_day']]))
                        $jobArr[$jobDataArr['category_id']][$jobDataArr['week_day']] += $jobDataArr['value'];
                    else
                       $jobArr[$jobDataArr['category_id']][$jobDataArr['week_day']] = $jobDataArr['value'];
                
                    // For the Colom wise calculations
                    if(isset($jobArr[$randNumber][$jobDataArr['week_day']]))
                        $jobArr[$randNumber][$jobDataArr['week_day']] += $jobDataArr['value'];
                    else
                       $jobArr[$randNumber][$jobDataArr['week_day']] = $jobDataArr['value'];
                }
            } 
            
            // To move that total arr element to the last of the array
            $totalArr = $jobArr[$randNumber];
            unset($jobArr[$randNumber]);
            $totalResult = count($jobArr);
            $jobArr[$randNumber] = $totalArr;
            
            $content = $this->generateHTMLStartView('Category', $totalResult, $this->yearPart);            
            $content .= $this->renderBodyContent($jobArr, 'catName', $randNumber);            
            $content .= $this->generateHTMLEndView();
            return $content;
        }
        
        public function agmtOwnerReports() {
            $jobData = JobScheduling::getAllAgmtOwnerJobs($this->reportYear);            
            
            if(empty($jobData))
                return $this->generateNoRecordsContentHTML('Account', 0, $this->yearPart);
            
            $lastArr = count($jobData)-1;
            $jobArr = array();
            $randNumber = time();
            
            foreach($jobData as $jobDataKey=>$jobDataArr)
            {     
                if($jobDataKey == $lastArr)
                    $jobArr[$randNumber]['usrName'] = 'Total';
                
                $jobArr[$jobDataArr['owner__user_id']]['usrName'] = $jobDataArr['firstname'].' '.$jobDataArr['lastname'];
                if(isset($jobArr[$jobDataArr['owner__user_id']][$jobDataArr['week_day']]))
                    $jobArr[$jobDataArr['owner__user_id']][$jobDataArr['week_day']] += $jobDataArr['value'];
                else
                   $jobArr[$jobDataArr['owner__user_id']][$jobDataArr['week_day']] = $jobDataArr['value'];

                // For the Colom wise calculations
                if(isset($jobArr[$randNumber][$jobDataArr['week_day']]))
                    $jobArr[$randNumber][$jobDataArr['week_day']] += $jobDataArr['value'];
                else
                   $jobArr[$randNumber][$jobDataArr['week_day']] = $jobDataArr['value'];
            }
            
            // To move that total arr element to the last of the array
             $totalArr = $jobArr[$randNumber];
             unset($jobArr[$randNumber]);
             $totalResult = count($jobArr);
             $jobArr[$randNumber] = $totalArr;
            
            $content = $this->generateHTMLStartView('Account', $totalResult, $this->yearPart);
            $content .= $this->renderBodyContent($jobArr, 'usrName', $randNumber);
            $content .= $this->generateHTMLEndView();
            return $content;
        }
    }
?>