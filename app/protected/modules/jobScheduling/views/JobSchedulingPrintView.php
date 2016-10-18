<?php
  
    /**
     * View for showing the print view.
     */
    class JobSchedulingPrintView extends View    {
       protected $data;
	   protected $Id;
        
	public function __construct($data, $id) {
        $this->data    = $data;
	    $this->Id      = $id;
	}
    
    public function renderContent(){
        $content = "";
        $id =  intval($this->Id);
        $jobSchedule = JobScheduling::getById($id);
        $agmtId = $jobSchedule->agreement->id;
        $themeName  = Yii::app()->theme->name;
        $logoUrl    = Yii::app()->themeManager->baseUrl . '/' . $themeName . '/images/login_page.png';
        
                
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
                <html class="zurmo" lang="en">
                <head>
                <style>
                    @font-face{font-family: "zurmo_gamification_symbly_rRg";font-weight: normal;font-style: normal;unicode-range: U+00-FFFF;}
                    .clsSmallFont{
                        color: #545454;
                        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                        font-size: 12px;
                    }
                    .details-table td, .details-table th{
                            font-weight: normal;
                    }
                    .zurmo{
                         font-size: 12px;
                    }                    
                    @media all{
                        #page-one, .footer, .page-break { display:none; }
                        .button-list{
                            display: block;
                        }.page_count_header{
                            display: block;
                            font-size: 14px;
                            font-weight: bold;
                            margin: 10px 30px;
                        }
                    }
                    @media print{
                        #page-one, .footer, .page-break{ 
                            display: block;
                            color:red; 
                            font-family:Arial; 
                            font-size: 16px; 
                            text-transform: uppercase; 
                        }
                        .page-break{
                            page-break-before:always;
                        } 
                        .button-list{
                            display:none;       
                        }.display-count{
                            display:none;       
                        }.page_count_header{
                            display:none;                             
                        }.numbers_size{
                            font-size:10px;
                        } 
                        @page { size: landscape; }                       
                    }
                </style>
                
                <link rel="stylesheet" type="text/css" href="/app/themes/default/css/zurmo-blue.css" />
                <link rel="stylesheet" type="text/css" href="/app/themes/default/css/imports-blue.css" />
                <link rel="stylesheet" type="text/css" href="/app/themes/default/custom-css/I2it-Custom.css" />
                
                </head>
                   <body class="blue">
                        <div style="padding:20px;">
                        <img src='.$logoUrl.'>                    
                        <ul style="padding:0px; margin-left:650px;" class="button-list">
                            <li><a href="javascript:window.close();">Close Window</a></li>
                            <li><a href="javascript:window.print();">Print This Page</a></li>
                        </ul>
                    </div>
                ';  
            $tableCreation .= '<div class="details-table clsSmallFont" style="font-size:10px;">
                        <table class="form-fields double-column">
                            <colgroup>
                                <col class="col-0"><col class="col-0"><col class="col-0"><col class="col-0">
                                <col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td colspan="8" style="padding:0px 20px;">'.$jobSchedule->name.'<hr></td>                            
                                </tr>                        
                                <tr>
                                    <th colspan="2" style="text-align:right;">Agreement: </th><td colspan="6">'.$jobSchedule->agreement->name.'</td>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align:right;">Job Name: </th><td colspan="6">'.$jobSchedule->name.'</td>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align:right;">Crew Name: </th><td colspan="6">'.$jobSchedule->crewName.'</td>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align:right;">Job Status: </th><td colspan="6">'.$jobSchedule->status.'</td>
                                </tr>
                                </tbody></table></div> ';

                $tableCreation .= '<div class="panel details-table"><div class="panelTitle"><h3>Job Scheduling Information</h3></div>';
               
                $total_week_numbers = 1;
                $previous_week_count = 1;
                $print_line_count = 13;
                $page_count = 1;
                foreach($season_arr as $season=>$month_arr)
                {
                    //$tableCreation .='<span class="display-count">=>'.$print_line_count.'</span>'; 
                    if(($page_count == 1 && $print_line_count > 22) || ($page_count > 1 && $print_line_count > 19)){
                        $tableCreation .='<span class="page-break"></span>';
                        $print_line_count = 0;
                        $page_count++;
                        $tableCreation .='<span  class="page_count_header">Page: '.$page_count.'</span>';
                        $print_line_count = $print_line_count+3;
                    }else{
                        $print_line_count = $print_line_count+3;
                        $tmp_line_count = $print_line_count+1;
                        //$tableCreation .='<span class="display-count">else=>'.$tmp_line_count.'</span>'; 
                        if(($page_count == 1 && $tmp_line_count > 22) || ($page_count > 1 && $tmp_line_count > 16  && $tmp_line_count < 22)){
                            $tableCreation .='<span class="page-break"></span>';
                            $print_line_count = 0;
                            $page_count++;
                            $tableCreation .='<span  class="page_count_header">Page: '.$page_count.'</span>';
                        }
                    }
                    //$tableCreation .='<span class="display-count">=>'.$print_line_count.'</span>'; 
                    $tableCreation .='<div style="margin: 0 2% 0 2%; font-weight: bold; border-color: black; border-style: solid; border-width: 1px 1px 0px 1px; padding: 0.5%; background-color : #3C8DBC; color: #FFFFFF;">'.$season.'</div>
                                    <table id="jobDetailsTable" class="form-fields" border = "1">
                                    <colgroup>
                                        <col class="col-1">
                                    </colgroup>
                                    <tbody><tr><td colspan="2" width="250"></td>';
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
                $categoryColumn ='<table id="jobDetailsTable" class="form-fields" border = "1">
                                    <colgroup>
                                        <col class="col-1">
                                    </colgroup>
                                    <tbody><tr><td colspan="2" width="250">Category</td>';

                $num_of_weeks = array_sum($week);
                $previous_week_count += $num_of_weeks;
                $processed_previous_week_count = $previous_week_count - $num_of_weeks;                            
                $total_week_numbers = $processed_previous_week_count;                            

                for($i=1; $i<=$num_of_weeks; $i++)
                {                                    
                    $tableCreation .='<td style = "text-align: center; font-size: 14px;">W '.$total_week_numbers.'</td>';
                    $categoryColumn .='<td style = "text-align: center; font-size: 14px;">W '.$total_week_numbers.'</td>';
                    $total_week_numbers++;
                }
                
                $tableCreation .=' </tr>';
                $categoryColumn .=' </tr>';
                $temp_line_count = $print_line_count - count($arr_cat);
                foreach ($arr_cat as $catKey => $catName) {
                    $print_line_count++;
                    //$tableCreation .='<span class="display-count">cat=>'.$print_line_count.'</span>'; 
                    if(($print_line_count > 22 && $print_line_count < 24 && $page_count ==1) || ($print_line_count > 16 && $print_line_count < 19 && $page_count > 1)){
                        $tableCreation .=' </tbody></table>';
                        $tableCreation .='<span class="page-break"></span>'; 
                        $page_count++;
                        $tableCreation .='<span  class="page_count_header">Page: '.$page_count.'</span>';
                        $tableCreation .=$categoryColumn;
                        $print_line_count = 1; 
                    }                    
                    $tableCreation .='<tr>
                                <td colspan="2" style = "font-weight: normal;">' . $catName['name'] . '</td>';
                                    $total_week_numbers_box = $processed_previous_week_count;
                                    
                                    for($i=1; $i<=$num_of_weeks; $i++)
                                    {                                    
                                        if(isset($jobArr[$catKey.'_week_'.$total_week_numbers_box]))
                                        {
                                           $tableCreation .='<td class="numbers_size" style = "font-weight: normal;">'.$jobArr[$catKey.'_week_'.$total_week_numbers_box].'</td>';
                                        }
                                        else
                                           $tableCreation .='<td>&nbsp</td>';
                                        $total_week_numbers_box++;
                                    }                                                        
                    $tableCreation .=' </tr>';
                    
                }              
                $print_line_count++;
                $tableCreation .=' </tbody></table>';                
            } 
            
            $tableCreation .=' </div></body></html>';
                                        
            $content .= $tableCreation;
            return $content;          
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
