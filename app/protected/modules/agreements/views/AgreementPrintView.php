<?php
  
    /**
     * View for showing the print view.
     */
    class AgreementPrintView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {                      
            $content   = '';
            $agreement = Agreement::getById($this->Id);           
            
            $themeName  = Yii::app()->theme->name;
            $logoUrl    = Yii::app()->themeManager->baseUrl . '/' . $themeName . '/images/login_page.png';
            /**
             * Modified by Murugan M
             * Date : 10-Sep-2016
             * Agreement Print preview page to fields are added like Agreement view page
             */
            $content = '
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
                </style>
                
                <link rel="stylesheet" type="text/css" href="/app/themes/default/css/zurmo-blue.css" />
                <link rel="stylesheet" type="text/css" href="/app/themes/default/css/imports-blue.css" />
                <script>
                    function divShow()
                    {      
                        var elems = document.getElementsByClassName("DivToHide");
                        for(var i = 0; i < elems.length; i++) {
                            elems[i].style.display = "block";
                        }
                        //document.getElementById("systemInformation").style.display="block";
                        //document.getElementById("estimatorSummary").style.display="block";        
                    }
                    function divHide()
                    {        
                        var elems = document.getElementsByClassName("DivToHide");
                        for(var i = 0; i < elems.length; i++) {
                            elems[i].style.display = "none";
                        }
                       // document.getElementById("systemInformation").style.display="none";
                       // document.getElementById("estimatorSummary").style.display="none";        
                    }
                </script>
                </head>
                   <body class="blue">

                <div>
                    <div style="padding:20px;">                    
                        <img src='.$logoUrl.'>
                    
                        <ul style="padding:2px; margin-left:650px;">
                            <li><a href="javascript:window.close();">Close Window</a></li>
                            <li><a href="javascript:window.print();">Print This Page</a></li>  
                            <li><a href="javascript:divShow();">Expand</a> || <a href="javascript:divHide();">Collapse</a></li>
                        </ul>
                    </div>
                </div>
                <div class="details-table clsSmallFont" style="font-size:10px;">
                    <div class="panel">
                        <table class="form-fields double-column">
                            <colgroup>
                                <col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td colspan="4" style="padding:20px;">'.$agreement->name.'<hr></td>                            
                                </tr>                        
                                <tr style="font-size:12px;">
                                    <th colspan="1">Agreement Record Type</th><td colspan="1">'.$agreement->RecordType.'</td>   
                                    <th colspan="1">Opportunity</th><td colspan="1">'.($agreement->opportunity != '(Unnamed)' ? $agreement->opportunity : '').'</td>
                                </tr>
                                '.($agreement->RecordType == Constant::RECURRINGAGREEMENT ?  
                                '<tr style="font-size:12px;">
                                    <th>Cloned From</th><td colspan="1">'.($agreement->Agreement != "(Unnamed)" ? $agreement->Agreement : '').'</td>
                                </tr>' : '').'
                                <tr style="font-size:12px;">
                                    <th>Account</th><td colspan="1">'.$agreement->account.'</td>
                                 <th>Initial Sales Representive</th><td colspan="1">'.($agreement->Initial_Sales_Rep != '(Unnamed)' ? $agreement->Initial_Sales_Rep : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Agreement Name</th><td colspan="1">'.$agreement->name.'</td>
                                    <th>Agreement Number</th><td colspan="1">'.$agreement->Contract_Number.'</td>
                                </tr>
                                '.($agreement->RecordType == Constant::PROJECTAGREEMENT  ? '
                                <tr style="font-size:12px;">
                                    <th>Cloned From</th><td colspan="1">'.($agreement->Agreement != "(Unnamed)" ? $agreement->Agreement : '').'</td>
                                    <th>Status</th><td colspan="1">'.($agreement->Status != '(None)' ? $agreement->Status : '').'</td>                                    
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Agreement Owner</th><td colspan="1">'.$agreement->owner.'</td>
                                    <th>Account Manager</th><td colspan="1">'.($agreement->Account_Manager != '(Unnamed)' ? $agreement->Account_Manager : '').'</td>                                    
                                </tr>
                                <tr style="font-size:12px;">                                    
                                    <th>Deactivation Date</th><td colspan="1">'.($agreement->Deactivation_Date != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($agreement->Deactivation_Date) : '').'</td>
                                    <th>Owner Expiration Notice	</th><td colspan="1">'.($agreement->OwnerExpirationNotice != '(None)' ? $agreement->OwnerExpirationNotice : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Agreement Type</th><td colspan="1">'.($agreement->Agreement_Type != '(None)' ? $agreement->Agreement_Type : '').'</td>
                                    <th>Estimator</th><td colspan="1">'.($agreement->Estimator != '(Unnamed)' ? $agreement->Estimator : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Estimator Approval</th><td colspan="1" style="font-size:12px;">'.($agreement->Estimator_Approval > 0 ? '<input id="" type="checkbox" checked="1" disabled="1">' : '<input id="" type="checkbox" disabled="1">').'</td>
                                    <th>Estimator Approval Date</th><td colspan="1">'.($agreement->Estimator_Approval_Date != '0000-00-00' ? DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($agreement->Estimator_Approval_Date) : '').'</td>
                                </tr>' 
                                  : 
                                '<tr style="font-size:12px;">
                                    <th>Status</th><td colspan="1">'.($agreement->Status != '(None)' ? $agreement->Status : '').'</td> 
                                    <th>Agreement Owner</th><td colspan="1">'.$agreement->owner.'</td>
                                </tr>
                                <tr style="font-size:12px;">                                    
                                    <th>Deactivation Date</th><td colspan="1">'.($agreement->Deactivation_Date != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($agreement->Deactivation_Date) : '').'</td>
                                    <th>Estimator</th><td colspan="1">'.($agreement->Estimator != '(Unnamed)' ? $agreement->Estimator : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Agreement Type</th><td colspan="1">'.($agreement->Agreement_Type != '(None)' ? $agreement->Agreement_Type : '').'</td>
                                    <th>Estimator Approval</th><td colspan="1" style="font-size:12px;">'.($agreement->Estimator_Approval > 0 ? '<input id="" type="checkbox" checked="1" disabled="1">' : '<input id="" type="checkbox" disabled="1">').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Estimator Approval Date</th><td colspan="1">'.($agreement->Estimator_Approval_Date != '0000-00-00' ? DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($agreement->Estimator_Approval_Date) : '').'</td>
                                </tr>' ).'
                               
                            </tbody>
                      </table>                   
               </div>                            
                <div class="panel">

                    <div class="panelTitle">Description Information</div>
                    <div id="systemInformation" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Description</th><td colspan="3" style="font-size:12px;">'.($agreement->Description != '(None)' ? $agreement->Description : '').'</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="panelTitle">Agreement Information</div>
                    <div id="oppurtunityGPM" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Agreement GPM</th><td colspan="1" style="font-size:12px;">'.($agreement->Current_GPM != '(Unnamed)' ? $agreement->Current_GPM : '').'</td> 
                                    <th>Total Direct Costs</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($agreement->Total_Direct_Costs->value, $agreement->Total_Direct_Costs->currency->code).'</td>    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Suggested Price</th><td colspan="1" style="font-size:12px;">'.Yii::app()->numberFormatter->formatCurrency($agreement->suggestedPrice->value, $agreement->suggestedPrice->currency->code).'</td>    
                                    <th>Current GPM</th><td colspan="1" style="font-size:12px;">'.($agreement->newCurrent_GPM != '' ? $agreement->newCurrent_GPM : '0').'</td> 
                                </tr>
                                '.($agreement->RecordType == Constant::PROJECTAGREEMENT ? '
                                <tr style="font-size:12px;"> 
                                    <th>Current annual amount</th><td colspan="1" style="font-size:12px;"> '.Yii::app()->numberFormatter->formatCurrency($agreement->Project_Agreement_Amount->value, $agreement->Project_Agreement_Amount->currency->code).'</td> 
                                    <th>Start Date</th><td colspan="1" style="font-size:12px;">'.($agreement->StartDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($agreement->StartDate) : '').'</td>    
                                </tr>'
                                 : 
                                 '<tr style="font-size:12px;"> 
                                    <th>Current annual amount</th><td colspan="1" style="font-size:12px;"> '.Yii::app()->numberFormatter->formatCurrency($agreement->Current_Annual_Amount->value, $agreement->Current_Annual_Amount->currency->code).'</td> 
                                    <th>Agreement Term (Months)</th><td colspan="1" style="font-size:12px;">'.($agreement->ContractTerm != '' ? $agreement->ContractTerm : '0').'</td>    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Agreement Start Date</th><td colspan="3" style="font-size:12px;">'.($agreement->StartDate != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($agreement->StartDate) : '').'</td>    
                                </tr>' ).       
                                '<tr style="font-size:12px;"> 
                                    <th>Special Terms</th><td colspan="3" style="font-size:12px;">'.$agreement->SpecialTerms.'</td> 
                                </tr> 
                                '.($agreement->RecordType == Constant::PROJECTAGREEMENT ? '
                                <tr style="font-size:12px;"> 
                                    <th>Anticipated Start Date</th><td colspan="1" style="font-size:12px;">'.($agreement->Anticipated_Start_Date != '0000-00-00 00:00:00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($agreement->Anticipated_Start_Date) : '' ).'</td> 
                                    <th>Budget</th><td colspan="1" style="font-size:12px;" "type" = "CurrencyValue">'.Yii::app()->numberFormatter->formatCurrency($agreement->budget->value, $agreement->budget->currency->code).'</td> 
                                </tr>'
                                :
                                '<tr style="font-size:12px;"> 
                                    <th>Date of First Service	</th><td colspan="3" style="font-size:12px;">'.($agreement->Date_of_First_Service != '0000-00-00 00:00:00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($agreement->Date_of_First_Service) : '' ).'</td> 
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Budget</th><td colspan="1" style="font-size:12px;" "type" = "CurrencyValue">'.Yii::app()->numberFormatter->formatCurrency($agreement->budget->value, $agreement->budget->currency->code).'</td> 
                                    <th>Renewal Date</th><td colspan="1" style="font-size:12px;">'.($agreement->Agreement_Expiration != '0000-00-00 00:00:00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($agreement->Agreement_Expiration) : '' ).'</td> 
                                </tr>' ).    
                            '</tbody>
                        </table>
                    </div>
                    
                                
                
                    
                    <div class="panelTitle">Man Hour Information</div>
                    <div id="systemInformation" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Total MHR</th><td colspan="1" style="font-size:12px;">'.($agreement->Total_MHR > 0 ? $agreement->Total_MHR : '').'</td>                                    
                                    <th>Rev/MH</th><td colspan="1" style="font-size:12px;"> '.Yii::app()->numberFormatter->formatCurrency($agreement->Rev_MH->value, $agreement->Rev_MH->currency->code).'</td> 
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Year to Date MHR</th><td colspan="3" style="font-size:12px;">'.($agreement->Year_to_Date_MHR > 0 ? $agreement->Year_to_Date_MHR : '').'</td>                                    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>% Used MHR</th><td colspan="1" style="font-size:12px;">'.($agreement->MHR_Used_Percentage > 0 ? $agreement->MHR_Used_Percentage : '').'</td>                                    
                                    <th>Total Remaining MHR</th><td colspan="1" style="font-size:12px;">'.($agreement->Total_Available_MHR != '' ? $agreement->Total_Available_MHR : '' ).'</td> 
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="panelTitle">Material Information</div>
                    <div id="systemInformation" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Total Material</th><td colspan="1" style="font-size:12px;">'.($agreement->Total_Material != '' ? $agreement->Total_Material : '0').'</td>                                    
                                    <th>Year to Date Units(M)</th><td colspan="1" style="font-size:12px;"> '.($agreement->Material_Year_To_Date > 0 ? $agreement->Material_Year_To_Date : '').'</td> 
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>% Used Units (M)</th><td colspan="1" style="font-size:12px;">'.($agreement->Material_Used_Percentage > 0 ? $agreement->Material_Used_Percentage : '').'</td>                                    
                                    <th>Total Remaining Material</th><td colspan="1" style="font-size:12px;">'.($agreement->Available_Material != "" ? $agreement->Available_Material : '').'</td> 
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="panelTitle">Equipment Information</div>
                    <div id="systemInformation" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Total Equipment</th><td colspan="1" style="font-size:12px;">'.($agreement->Total_Equipment != '' ? $agreement->Total_Equipment : '0').'</td>                                    
                                    <th>Year to Date Units(E)</th><td colspan="1" style="font-size:12px;"> '.($agreement->Equipment_Year_To_Date > 0 ? $agreement->Equipment_Year_To_Date : '').'</td> 
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>% Used Units (E)</th><td colspan="1" style="font-size:12px;">'.($agreement->Equipment_Used_Percentage > 0 ? $agreement->Equipment_Used_Percentage : '').'</td>                                    
                                    <th>Total Remaining Equipment</th><td colspan="1" style="font-size:12px;">'.($agreement->Available_Equipment != '' ? $agreement->Available_Equipment : '0').'</td> 
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    '.($agreement->RecordType == Constant::RECURRINGAGREEMENT ? '
                    <div class="panelTitle">Cumulative Information</div>
                    <div id="systemInformation" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;"> 
                                    <th>Cumulative Year to Date MHR</th><td colspan="3" style="font-size:12px;">'.($agreement->Cumulative_Year_to_Date_MHR > 0 ? $agreement->Cumulative_Year_to_Date_MHR : '').'</td>                                    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Cumulative Year to Date Units(M)</th><td colspan="3" style="font-size:12px;">'.($agreement->Cumulative_Year_to_Date_Material > 0 ? $agreement->Cumulative_Year_to_Date_Material : '').'</td>                                    
                                </tr>
                                <tr style="font-size:12px;"> 
                                    <th>Cumulative Year to Date Units(E)</th><td colspan="3" style="font-size:12px;">'.($agreement->Cumulative_Year_to_Date_Equipment > 0 ? $agreement->Cumulative_Year_to_Date_Equipment : '').'</td>                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>' : '' ). '

                </div>
                </body>
                </html>';
            
            return $content;
        }
    }
?>
