<?php
  
    /**
     * Render html to create OpportunityReportView
     */
    class OpportunityReportView extends View {        
        private $reportFor;
        private $userId;
        /**
         * @param type $reportFor value assigend based on the reports
         */
	public function __construct($reportFor='Pipeline', $userId='1') {           
            $this->reportFor = $reportFor;
            $this->userId = $userId;
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
        
        public function generateHTMLStartView($report_for='Pipeline', $count) {      
            return $content =   '<p>&nbsp;</p>
                        <div class="BreadCrumbView" id="ReportBreadCrumbView">
                            <div class="breadcrumbs">
                                <a href='.Yii::app()->getBaseUrl(true).'/index.php/reports/default/index>Reports</a> / <span>'.$report_for.' Report</span>
                            </div>
                        </div>
                            <div id="ReportDetailsView" class="DetailsView ModelView ConfigurableMetadataView MetadataView">
                        <h1>
                        <span class="truncated-title" threedots="Opportunity Report">
                        <span class="ellipsis-content">Opportunity Report</span>
                        </span>
                        </h1>
                        </div>
                        <p>&nbsp;</p>
                            <div class="juiportlet-widget">
                                <div class="juiportlet-widget-head">
                                    <h3>Pipeline Report</h3>
                                    <div class="portlet-actions-container">
                                    <div id="report-results-grid-view-summary-clone" class="list-view-items-summary-clone">'.$count.' result(s)</div>
                                    </div>    
                                </div>
                                <div class="juiportlet-widget-content">
                                    <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                        <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                            <div id="MatrixReportResultsGridView" class="cgrid-view type-approvalProcess ReportResultsGridView">';
        }
        
        public function generatePanelWrapperHTML() {
            if($this->reportFor == 'Pipeline')
                return $this->oppPipeLineReports();
            else if($this->reportFor == 'user')
                return $this->oppPipeLineReportsByUser();
            else if($this->reportFor == 'Account')
                return $this->agmtOwnerReports();
        }
        
        public function generateNoRecordsContentHTML($report_for='Pipeline') {
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
            Yii::app()->clientScript->registerScript('toggleProcess',
                "$('.toggleDiv').click(function(e){
                    e.preventDefault();       
                    
                    if ($('#toggleDiv_'+this.id).is(':hidden'))
                    {                                                
                        $('#'+this.id).text('-');
                        $('#'+this.id).parent().parent().addClass('expanded-row');
                    }
                    else
                    {
                        $('#'+this.id).text('+');
                        $('#'+this.id).parent().parent().removeClass('expanded-row');
                    }

                    $('#toggleDiv_'+this.id).toggle('slow');
                });
            ");
        }
        
        public function renderBodyContent($oppArr,$forName='userName', $randNumber)
        {
            $content = '<div id="report-results-grid-view">
                            <table id="" class="items">
                                <thead class="header">
                                    <tr class="first_child">
                                        <th>&nbsp; </th>
                                        <th colspan="3" style="text-align:center">Project Final</th>
                                        <th colspan="3" style="text-align:center">Recurring Final</th>
                                        <th>&nbsp; </th>';                        
            $content .=' </tr>
                        <tr class="first_child" style="color:#363671">
                        <th>Salesperson </th>
                                    <th>Consulting</th>
                                    <th>Estimate</th>
                                    <th>Final Proposal</th>
                                    <th>Consulting</th>
                                    <th>Estimate</th>
                                    <th>Final Proposal</th>
                                    <th class="report_row_total"> Total </th></tr>
                ';
                
                foreach ($oppArr as $oppKey => $oppData) {
                    $oppRowTotal = '';
                    if($oppKey == $randNumber)
                    {
                        $tblrowClass       = "report_rows";
                        $tblheaderClass    = "report_col_total_label";
                        $tbldataClass      = "report_col_total";    
                        $tbldataTotClass   = "report_row_total";
                    }
                    else
                    {
                        $tblrowClass       = "first_child";
                        $tblheaderClass    = "";
                        $tbldataClass      = "";
                        $tbldataTotClass   = "";
                    }
                    
                    $content .='<tr class='.$tblrowClass.'>';
                    if(strlen($oppData[$forName]) > 5){                        
                        $content .="<th class='$tblheaderClass' title ='$oppData[$forName]'><a href=".Yii::app()->getBaseUrl(true)."/index.php/opportunities/default/OppPipeLineReportsByUser?id=".$oppKey." style='text-decoration:underline'>".$oppData[$forName]."</a></th>";
                    }
                    else {
                        $content .="<th class='$tblheaderClass' title ='$oppData[$forName]'>".$oppData[$forName]."</th>";
                    }
                    
                    if(isset($oppData['Consulting_Project Final']))
                    {
                        $content .=' <td class='.$tbldataTotClass.'>'.$this->formatNumber($oppData['Consulting_Project Final']).'</td>';
                        $oppRowTotal += $oppData['Consulting_Project Final'];
                    }
                    else
                        $content .='<td class='.$tbldataTotClass.'>0</td>';
                    
                    if(isset($oppData['Estimate_Project Final']))
                    {
                        $content .=' <td class='.$tbldataTotClass.'>'.$this->formatNumber($oppData['Estimate_Project Final']).'</td>';
                        $oppRowTotal += $oppData['Estimate_Project Final'];
                    }
                    else
                        $content .='<td class='.$tbldataTotClass.'>0</td>';
                    
                    if(isset($oppData['Final Proposal_Project Final']))
                    {
                        $content .=' <td class='.$tbldataTotClass.'>'.$this->formatNumber($oppData['Final Proposal_Project Final']).'</td>';
                        $oppRowTotal += $oppData['Final Proposal_Project Final'];
                    }
                    else
                        $content .='<td class='.$tbldataTotClass.'>0</td>';
                    
                    // For recurring
                    if(isset($oppData['Consulting_Recurring Final'])) {
                        $content .=' <td class='.$tbldataTotClass.'>'.$this->formatNumber($oppData['Consulting_Recurring Final']).'</td>';
                        $oppRowTotal += $oppData['Consulting_Recurring Final'];
                    }
                    else
                        $content .='<td class='.$tbldataTotClass.'>0</td>';
                    
                    if(isset($oppData['Estimate_Recurring Final'])) {         
                        $content .=' <td class='.$tbldataTotClass.'>'.$this->formatNumber($oppData['Estimate_Recurring Final']).'</td>';
                        $oppRowTotal += $oppData['Estimate_Recurring Final'];
                    }
                    else
                        $content .='<td class='.$tbldataTotClass.'>0</td>';
                    
                    if(isset($oppData['Final Proposal_Recurring Final'])) {      
                        $content .=' <td class='.$tbldataTotClass.'>'.$this->formatNumber($oppData['Final Proposal_Recurring Final']).'</td>';
                        $oppRowTotal += $oppData['Final Proposal_Recurring Final'];
                    }
                    else
                        $content .='<td class='.$tbldataTotClass.'>0</td>';
                    
                    $content .=' <td class="report_row_total">'.$this->formatNumber($oppRowTotal).'</td></tr>';
                }
                
                $content .=' </thead><tbody class="results">';
                
            $content .='    </tr></tbody>
                        </table>
                    </div>';
            
            return $content;
        }

        public function oppPipeLineReports() {
            $oppData = Opportunity::getOppourtunityPipeLineReport();
            
            if(empty($oppData))
                return $this->generateNoRecordsContentHTML('Pipeline');
            
            $lastArr = count($oppData)-1;
            $oppArr = array();
            $randNumber = time();
            foreach($oppData as $oppDataKey=>$oppDataArr)
            {
                if($oppDataKey == $lastArr)
                {
                    $oppArr[$randNumber]['userName'] = 'Total';
                }
                
                $oppArr[$oppDataArr['user_id']]['userName'] = $oppDataArr['sales_person'];
                if(isset($oppArr[$oppDataArr['user_id']][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']]))
                    $oppArr[$oppDataArr['user_id']][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] += $oppDataArr['price'];
                else
                   $oppArr[$oppDataArr['user_id']][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] = $oppDataArr['price'];
                
                // For the Colom wise calculations
                if(isset($oppArr[$randNumber][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']]))
                    $oppArr[$randNumber][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] += $oppDataArr['price'];
                else
                   $oppArr[$randNumber][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] = $oppDataArr['price'];
            }            
            
            // To move that total arr element to the last of the array
            $totalArr = $oppArr[$randNumber];
            unset($oppArr[$randNumber]);
            $totalResult = count($oppArr);
            $oppArr[$randNumber] = $totalArr;
            
            $content = $this->generateHTMLStartView('Pipeline', $totalResult);            
            $content .= $this->renderBodyContent($oppArr, 'userName', $randNumber);
            $content .= $this->generateHTMLEndView();
            return $content;
        }
        
        public function oppPipeLineReportsByUser() {
            $oppData = Opportunity::getOppourtunityPipeLineReport($this->userId);
            
            if(empty($oppData))
                return $this->generateNoRecordsContentHTML('Pipeline');
            
            $lastArr = count($oppData)-1;
            $oppArr = array();
            $randNumber = time();   
            $salesPerson = '';
            foreach($oppData as $oppDataKey=>$oppDataArr)
            {
                $salesPerson = $oppDataArr['sales_person'];
                 
                $oppArr[$oppDataArr['stage']][$oppDataArr['Recordtype']][$oppDataArr['opp_id']]['oppName'] = $oppDataArr['oppName'];
                $oppArr[$oppDataArr['stage']][$oppDataArr['Recordtype']][$oppDataArr['opp_id']]['oppprice'] = $oppDataArr['price'];
                
                if(isset($oppArr[$oppDataArr['stage']][$oppDataArr['Recordtype']]['price']))
                    $oppArr[$oppDataArr['stage']][$oppDataArr['Recordtype']]['price'] += $oppDataArr['price'];
                else
                  $oppArr[$oppDataArr['stage']][$oppDataArr['Recordtype']]['price'] = $oppDataArr['price'];
            }
            
            $content = '';
            $i = 1;
            foreach($oppArr as $stageKey=>$stageVal)
            {
                $content .= $this->generateHTMLStartViewUser($stageKey, $i, $salesPerson);
                $content .= $this->renderBodyContentUser($stageVal, $i);
                $content .= $this->generateHTMLEndView();
                $i++;
            }
            return $content;
        }
        
        public function renderBodyContentUser($stageVal, $count)
        {
            $content ='';
            $totalPrice =0;           
            $content .= '<div id="report-results-grid-view">
                            <table id="" class="items">
                                <thead class="header">
                                    <tr class="first_child">
                                        <th>Sl.No</th>
                                        <th>Opportunity</th>
                                        <th>Amount ($)</th>
                                        </tr></thead><tbody class="results">';
                    
                    foreach ($stageVal as $typeKey => $oppVal) {    
                        $i=1;  
                        $totalPrice += $oppVal['price'];
                        $content .=' <tr class="expanded-row selected"><th><a href="#" class="toggleDiv" id="'.str_replace(" ", "_", $typeKey).'_'.$count.'">-</a></th><th colspan=1 style="text-align:left">'.$typeKey.'</th><th>'.$this->formatNumber($oppVal['price']).'</th></tr>';
                        $content .=' <tr><td class="hasDrillDownContent" colspan=3><div id="toggleDiv_'.str_replace(" ", "_", $typeKey).'_'.$count.'" style="display:block;" class="drillDownContent">
                            <table class="items"><tbody class="results">';
                        foreach ($oppVal as $oppKey => $oppDataArr) {
                            if(isset($oppDataArr['oppName'])){
                                $content .=' <tr><td>'.$i.'.</td><td>'.$oppDataArr['oppName'].'</td>
                                    <td colspan=2>'.$this->formatNumber($oppDataArr['oppprice']).'</td>
                                    </tr>';
                                $i++;
                            }
                        }
                        $content .='</tbody> </table></div></td></tr>';
                    }
                   $content .=' <tr class="report_rows"><th colspan="2" class="report_col_total_label" title="Total">Total</th>
                                <th class="report_row_total">'.$this->formatNumber($totalPrice).'</th>
                                </tr>'; 
                   
                $content .='    </tbody>
                            </table>
                        </div>';
            return $content;
        }
        
        public function generateHTMLStartViewUser($report_for='Pipeline', $count, $username='') {      
            $headContent = '';
            if($count == 1)
            {
                $headContent = '<div class="BreadCrumbView" id="ReportBreadCrumbView">
                                    <div class="breadcrumbs">
                                        <a href='.Yii::app()->getBaseUrl(true).'/index.php/reports/default/index>Reports</a> / <span>Pipeline Report</span>
                                    </div>
                                </div>
                                <div id="ReportDetailsView" class="DetailsView ModelView ConfigurableMetadataView MetadataView">
                                <h1>
                                <span class="truncated-title" threedots="Opportunity Report">
                                <span class="ellipsis-content">Opportunity details for '.$username.'</span>
                                </span>
                                </h1>
                                </div>
                                <p>&nbsp;</p>';
            }
             $content =   '<p>&nbsp;</p>
                        '.$headContent.'
                            
                            <div class="juiportlet-widget">
                                <div class="juiportlet-widget-head">
                                    <h3>'.$report_for.'</h3>
                                    <div class="portlet-actions-container">
                                    <div id="report-results-grid-view-summary-clone" class="list-view-items-summary-clone"></div>
                                    </div>    
                                </div>
                                <div class="juiportlet-widget-content">
                                    <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                        <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                            <div id="MatrixReportResultsGridView" class="cgrid-view type-approvalProcess ReportResultsGridView">';
             
             return $content;
        }
        
        public static function formatNumber($number)
        {
            return number_format($number, 2);
        }
    }
?>