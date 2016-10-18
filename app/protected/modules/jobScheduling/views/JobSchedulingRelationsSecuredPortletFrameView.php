<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AgreementRelationsSecuredPortletFrameView
 *
 * @author ideas2it
 */
class JobSchedulingRelationsSecuredPortletFrameView extends ModelRelationsSecuredPortletFrameView{
    
    private $showAsTabs;
    
    public function __construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible = true, $portletsAreMovable = true, $showAsTabs = false, $layoutType = '100', $portletsAreRemovable = true) {        
        parent::__construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible, $portletsAreMovable, $showAsTabs, $layoutType, $portletsAreRemovable);
    }

    //put your code here
    protected function renderPortlets($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true) {
        if (!$this->showAsTabs)
            {
                return $this->renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible, $portletsAreMovable, $portletsAreRemovable);
            }
            assert('is_bool($portletsAreCollapsible) && $portletsAreCollapsible == false');
            assert('is_bool($portletsAreMovable) && $portletsAreMovable == false');
            return $this->renderPortletsTabbed();
    }
    protected function renderPortletsTabbed()
        {
            assert('count($this->portlets) == 1 || count($this->portlets) == 0');
            if (count($this->portlets) == 1)
            {
                $tabItems = array();
                foreach ($this->portlets[1] as $noteUsed => $portlet)
                {
                    $tabItems[$portlet->getTitle()] = array(
                        'id'      => $portlet->getUniquePortletPageId(),
                        'content' => $portlet->renderContent()
                    );
                }
                $cClipWidget = new CClipWidget();
                $cClipWidget->beginClip("JuiTabs");
                $cClipWidget->widget('zii.widgets.jui.CJuiTabs', array(
                    'id' => $this->uniqueLayoutId . '-portlet-tabs',
                    'tabs' => $tabItems
                ));
                $cClipWidget->endClip();
                return $cClipWidget->getController()->clips['JuiTabs'];
            }
        }
        protected function renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true){
            assert('is_string($uniqueLayoutId)');
            assert('is_bool($portletsAreCollapsible)');
            assert('is_bool($portletsAreMovable)');
            assert('is_bool($portletsAreRemovable)');
            $juiPortletsWidgetItems = array();
            foreach ($this->portlets as $column => $columnPortlets)
            {
                foreach ($columnPortlets as $position => $portlet)
                {
                    $className = get_class($portlet->getView());
                    if (method_exists($className, 'canUserRemove'))
                    {
                        $removable      = $className::canUserRemove();
                    }
                    else
                    {
                        $removable      = $portletsAreRemovable;
                    }
                    $additionalOptionMenuItems = array();
                    if (method_exists($className, 'getAdditionalOptionMenuItems'))
                    {
                        $additionalOptionMenuItems = $className::getAdditionalOptionMenuItems();
                    }
                    if($className == 'JobSchedulingDetailsPortletView') {
						
                        $juiPortletsWidgetItems[$column][$position] = array(
                          'id'                        => $portlet->id,
                          'uniqueId'                  => $portlet->getUniquePortletPageId(),
                          'title'                     => $portlet->getTitle(),
                          'content'                   => $portlet->renderContent().$this::renderViewForMhrList($portlet),
                          'headContent'               => $portlet->renderHeadContent(),
                          'editable'                  => $portlet->isEditable(),                          
                          'collapsed'                 => $portlet->collapsed,
                          'removable'                 => $removable,
                          'uniqueClass'               => $this->resolveUniqueClass($portlet),
                          'portletParams'             => $portlet->getPortletParams(),
                          'additionalOptionMenuItems' => $additionalOptionMenuItems,
                      );  
                    }
                    else{
                        $juiPortletsWidgetItems[$column][$position] = array(
                            'id'                        => $portlet->id,
                            'uniqueId'                  => $portlet->getUniquePortletPageId(),
                            'title'                     => $portlet->getTitle(),
                            'content'                   => $portlet->renderContent(),
                            'headContent'               => $portlet->renderHeadContent(),
                            'editable'                  => $portlet->isEditable(),							
                            'collapsed'                 => $portlet->collapsed,
                            'removable'                 => $removable,
                            'uniqueClass'               => $this->resolveUniqueClass($portlet),
                            'portletParams'             => $portlet->getPortletParams(),
                            'additionalOptionMenuItems' => $additionalOptionMenuItems,
                        );
                    }                                        
                }
            }   
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("JuiPortlets");
            $cClipWidget->widget('application.core.widgets.JuiPortlets', array(
                'uniqueLayoutId' => $uniqueLayoutId,
                'moduleId'       => $this->moduleId,
                'saveUrl'        => Yii::app()->createUrl($this->moduleId . '/defaultPortlet/SaveLayout'),
                'layoutType'     => $this->getLayoutType(),
                'items'          => $juiPortletsWidgetItems,
                'collapsible'    => $portletsAreCollapsible,
                'movable'        => $portletsAreMovable,
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips['JuiPortlets'];
        }  
        
        protected function renderViewForMhrList(Portlet $portlet) {
            if ($this->params['relationModel']->modelClassNameToBean['JobScheduling']->id != null) {
                $id = $this->params['relationModel']->modelClassNameToBean['JobScheduling']->id;
                
                $jobSchedule = JobScheduling::getById($id);
                $agmtId = $jobSchedule->agreement->id;
                
                $content = '';
                $tableCreation = '';
                $cat_arr = array();
                $arr_cat = array();
                $jobArr  = array();
                
                $jobData = AgreementJobs::getAgmtJobsByJobId($id);
                
                foreach($jobData as $jobDataArr)
                {     
                    $jobArr[$jobDataArr->category->id.'_week_'.$jobDataArr->week_no] = $jobDataArr->value;
                }
                
                $createdDateTime = $jobSchedule->scheduleDate;
                
                $time=strtotime($createdDateTime);
                $monthCond=date("m",$time);
                $yearCond=date("Y",$time);
                
                $agmt_prod_arr = AgreementProduct::getAgmtProdCategoryByAgmtId($agmtId);
              
                foreach($agmt_prod_arr as $cat_arr)
                {
                    $category_arr = Category::getCategoryByName($cat_arr->Category);
                    if(!empty($category_arr)){    
                        $arr_cat[$category_arr[0]->id]['name']    = $cat_arr->Category;
                        $arr_cat[$category_arr[0]->id]['mhr']     = $cat_arr->Total_MHR;
                    }    
                }                
                                
                switch ($monthCond) {
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
                
               $tableCreation .= '
                            <div class="panel details-table">
                                <div class="panelTitle"><h3>Job Scheduling Information</h3></div>';
               
                $total_week_numbers = 1;
                $previous_week_count = 1;
                foreach($season_arr as $season=>$month_arr)
                {
                    $tableCreation .='<div style="margin: 0 2% 0 2%; font-weight: bold; border-color: black; border-style: solid; border-width: 1px 1px 0px 1px; padding: 0.5%; background-color : #3C8DBC; color: #FFFFFF;">'.$season.'</div>
                                    <table id="jobDetailsTable" class="form-fields" border = "1" style="">
                                    <colgroup>
                                        <col class="col-1">
                                    </colgroup>
                                    <tbody><tr><td colspan="2"></td>';
                    $week = array();
                    
                    // To get the year of the season for the week issues
                    if(isset($seasonDateArr[1]))
                        $yearCond = $seasonDateArr[1];
                    // Ends Here

                    foreach ($month_arr as $MonthNumber) {
                        $week[$MonthNumber] = $this->getWeeksCount($MonthNumber, $yearCond);
                        $tableCreation .='<td colspan="'.$week[$MonthNumber].'" style = "text-align: center;">'. date("F", mktime(0, 0, 0, $MonthNumber)).'</td>';
                    }  
                 $tableCreation .='</td></tr>';                             
                 $tableCreation .='<tr><td colspan="2">Category</td>';

                $num_of_weeks = array_sum($week);
                $previous_week_count += $num_of_weeks;
                $processed_previous_week_count = $previous_week_count - $num_of_weeks;                            
                $total_week_numbers = $processed_previous_week_count;                            

                for($i=1; $i<=$num_of_weeks; $i++)
                {                                    
                    $tableCreation .='<td style = "text-align: center;">W '.$total_week_numbers.'</td>';
                    $total_week_numbers++;
                }
                
                $tableCreation .=' </tr>';
                            
                foreach ($arr_cat as $catKey => $catName) {
                $tableCreation .='<tr>
                                <td colspan="2" style = "font-weight: normal;">' . $catName['name'] . '</td>';
                                    $total_week_numbers_box = $processed_previous_week_count;
                                    
                                    for($i=1; $i<=$num_of_weeks; $i++)
                                    {                                    
                                        if(isset($jobArr[$catKey.'_week_'.$total_week_numbers_box]))
                                        {
                                           $tableCreation .='<td style = "font-weight: normal;">'.$jobArr[$catKey.'_week_'.$total_week_numbers_box].'</td>';
                                        }
                                        else
                                           $tableCreation .='<td>&nbsp</td>';
                                        $total_week_numbers_box++;
                                    }                                                        
                $tableCreation .=' </tr>';
            }              
            
            $tableCreation .=' </tbody>
                                </table>';
                            } 
                                
                                
                                
                                    
                                        
                                       
                $content .= $tableCreation;
                return $content;            
            } 
            else 
            {
                return $portlet->renderContent();
            }
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
