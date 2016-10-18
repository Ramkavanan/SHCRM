<?php
  
    /**
     * View for showing the print view.
     */
    class OppurtunityEstimationPrintView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {
            $content    = '';   
            $oppProduct = '';
            $opportunity = Opportunity::getById($this->Id);
            
            $opptProducts = OpportunityProduct::getAllByOpptId(intval($this->Id));
            $count = count($opptProducts);
            $totalDirectCost = 0;
            $totalMH = 0;
            $suggestedPrice = 0;
            $opptPdctMap;
            if(count($opptProducts) > 0) {
                foreach($opptProducts as $row) {
                    $opptPdctMap[$row->Category][] = $row;
                }
                $tableCreation = '';
                
                $tableCreation .= '
                    <table border="1" width="100%" class="">
                                 <colgroup span="5"></colgroup>';

                $tableCreation .= '<thead style="background-color:#E6E6E6; color: black;padding: 2px;">
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Product Code</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Product Name</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Unit of Measure</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Quantity</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">Frequency</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">MH</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">L</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">OH</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">M</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">E</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">S</th>
                                            <th style="font-weight: bold;padding: 2px;text-align:center; font-size:10px;">O</th>
                                            <th style="font-weight: bold;padding-top: 2px;text-align:center; font-size:10px;">Total Direct Cost</th>
                                            <th style="font-weight: bold;padding-top: 2px;text-align:center; font-size:10px;">Total Final Price</th>
                                       </thead><tbody>';
                            $totalDirectCost1=0;
                            foreach ($opptPdctMap as $categoryKey1 => $optpdctArray1)  {
                                foreach ($optpdctArray1 as $optKey1 => $optpdt1){
                                     $totalDirectCost1 += $optpdt1->Total_Direct_Cost->value;	
                                }
                            }
                            
                 foreach ($opptPdctMap as $key => $optpdctArray)  {
                    $tableCreation .= '<th  style="padding: 3px; text-align: left; font-weight: bold;background-color:gray;color:white; font-size:13px;" colspan="14">'.$key.'</th>';
                     foreach ($optpdctArray as $optKey => $optpdt){
                        $totalDirectCost += $optpdt->Total_Direct_Cost->value;
                        $suggestedPrice += $optpdt->Final_Cost->value;
                        $totalMH += $optpdt->Total_MHR;
                        $tableCreation .= '<tr>
                            <td style="width: 8%; text-align: left; padding: 3px; font-size:11px;">'.$optpdt->costbook->productcode.'</td>
                            <td style="width: 15%; text-align: left; padding: 3px; font-size:11px;">'.$optpdt->name.'</td>
                            <td style="width: 4%; text-align: center; padding: 3px; font-size:11px;">'.$optpdt->costbook->unitofmeasure.'</td>
                            <td style="width: 5%; text-align: center; padding: 3px; font-size:11px;">'.$optpdt->Quantity.'</td>
                            <td style="width: 5%; text-align: center; padding: 3px; font-size:11px;">'.$optpdt->Frequency.'</td>
                            <td style="width: 6%; text-align: center; padding: 3px; font-size:11px;">'.$optpdt->Total_MHR.'</td>
                            <td style="width: 6%;padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Labor_Cost->value, 2).'</td>
                            <td style="width: 6%;  padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Burden_Cost->value, 2).'</td>
                            <td style="width: 6%;padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Materials_Cost->value, 2).'</td>
                            <td style="width: 6%;padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Equipment_Cost->value, 2).'</td>
                            <td style="width: 6%;  padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Sub_Cost->value, 2).'</td>
                            <td style="width: 6%;  padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Other_Cost->value, 2).'</td>
                            <td style="width: 16%;  padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Total_Direct_Cost->value, 2).'</td>
                            <td style="width: 16%;  padding-top: 2px; text-align: right; padding: 3px; font-size:11px;">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)),2) .'</td>
                        </tr>';
                      }

                 }
                    $tableCreation .= '</tbody></table>';
                    
                    if($totalMH > 0){
                         $revMHR = round(($suggestedPrice/$totalMH),2);
                         $finalAmount = round(($opportunity->finalAmount->value/$totalMH),2);
                     }else{
                         $revMHR = 0.0;
                         $finalAmount = 0.0;
                     }
                    
                    if($suggestedPrice > 0 && $opportunity->finalAmount->value > 0){
                           $tableCreation .= '<table style="margin-left: 20%; margin-top:2%;" border="0"
                                cellpadding="2" width="60%" text-align="right">
                            <tr>
                                <td rowspan="2" style="text-align:center; font-weight: bold;color:black; font-size:12px;">Direct Cost</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:12px;">Total</td>
                                <td style="text-align:right;"></td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:12px;">Suggested</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:12px;">Final</td>
                            </tr>
                            <tr>
                                <td style="text-align:right; color:black; font-size:12px;">'.OpportunityProductUtils::getCurrencyType() .round($totalDirectCost,2).'</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:12px;">Price</td>
                                <td style="text-align:right; color:green; font-size:12px;">'.OpportunityProductUtils::getCurrencyType() .round($suggestedPrice,2).'</td>
                                <td style="text-align:right; color:green; font-size:12px;">'.OpportunityProductUtils::getCurrencyType() .round($opportunity->finalAmount->value,2).'</td>
                            </tr>
                            <tr>
                                <td style="text-align:center; font-weight: bold;color:black; font-size:12px;">MH</td>
                                <td style="text-align:right; color:black; font-size:12px;">'.round($totalMH, 2).'</td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:12px;">Rev/MH</td>
                                <td style="text-align:right; color:black; font-size:12px;">'.OpportunityProductUtils::getCurrencyType() . round($revMHR, 2).'</td>
                                <td style="text-align:right; font-size:12px;">'.OpportunityProductUtils::getCurrencyType() .round($finalAmount, 2).'</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:right;"></td>
                                <td style="text-align:right; font-weight: bold;color:black; font-size:12px;">Aggregate GPM%</td>
                                <td style="text-align:right; color:black; font-size:12px;">'.round(((($suggestedPrice - $totalDirectCost)/$suggestedPrice)*100),2).' </td>
                                <td style="text-align:right; color:black; font-size:12px;">'.round(((($opportunity->finalAmount->value -$totalDirectCost )/$opportunity->finalAmount->value)*100),2).'</td>
                            </tr>
                        </table>';
                           $oppProduct .= $tableCreation;
                    }
                }
            
            $themeName  = Yii::app()->theme->name;
            $logoUrl    = Yii::app()->themeManager->baseUrl . '/' . $themeName . '/images/login_page.png';
            $content = '
                <html class="zurmo" lang="en">
                <head>
                <style>
                    @font-face{format("svg");font-weight: normal;font-style: normal;unicode-range: U+00-FFFF;}
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

                </head>
                   <body class="blue">

                <div>
                    <div style="padding:20px;">                    
                        <img src='.$logoUrl.'>
                    
                        <ul style="padding:2px; margin-left:650px;">
                            <li><a href="javascript:window.close();">Close Window</a></li>
                            <li><a href="javascript:window.print();">Print This Page</a></li> 
                        </ul>
                    </div>
                </div>
                <div class="details-table clsSmallFont" style="font-size:10px;">
                                              
                <div class="panel"> 
                    <table border="0" width="100%" class="">                                 
                       <tr style="">
                           <td style="text-align:left; font-weight: bold;color:black; font-size:15px;">'.$opportunity->name.'</td>
                       </tr>
                       <tr style="">
                           <td style="text-align:left; font-weight: bold;color:black; font-size:13px;">Number Of Products : '.$count.'</td>
                       </tr>
                    </table>
                   <div id="oppProducts" style="diplay:block; padding: 10px;">'.$oppProduct.'</div>
                </div>
                </body>
                </html>';
            return $content;
        }
    }
?>
