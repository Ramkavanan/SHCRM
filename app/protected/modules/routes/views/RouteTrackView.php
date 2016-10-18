<?php
  
    /**
     * View for showing the print view.
     */
    class RouteTrackView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {                      
            $content    = '';            
            $agreement  = Route::getById($this->Id);
            $agPdctMap;            
            $agmt_id_arr = '';
            $countOfSelectedRow = 0;
            $CategoryKeyCount = 0;
            $themeName  = Yii::app()->theme->name;
            $logoUrl    = Yii::app()->themeManager->baseUrl . '/' . $themeName . '/images/login_page.png';
            $content .= '
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
                        <div class="clsSmallFont" style="font-size:10px;">
                            <div class="panel">
                                <table border="0" width="100%" class="" cellpadding: 2px; cellspacing: 2px;>
                                    <tr>
                                        <td style="padding:10px; font-size:16px;"><b>Route Name :</b> '.$agreement->name.'</td>                            
                                    </tr>
                                    <tr style="">
                                        <td style="padding:10px; font-size:14px;"><b>Date Of Service :</b> _________________</td>
                                    </tr>
                                    <tr style="">
                                        <td style="padding:10px; font-size:14px;"><b>Route :</b> Service Start Time  __________  Service End Time  _________</td>
                                    </tr>
                                </table><br><br>';
            
                                $agmtProds   = RouteProducts::getRouteProdByAgmtIdRouteId($this->Id);
                                $agreementPdctMap = array();
                                $agreementPdctIds = array();
                                $agmt = array();
                                foreach ($agmtProds as $agmtProd) {                                    
                                    $agreementPdctIds[$agmtProd->agreementproduct->Category][$agmtProd->agreementproduct->costbook->productcode.'-'.$agmtProd->agreementproduct->Assembly_Product_Code]['present_agmt_ids'][] = $agmtProd->agreement->id;
                                    $agreementPdctMap[$agmtProd->agreementproduct->Category][$agmtProd->agreementproduct->costbook->productcode.'-'.$agmtProd->agreementproduct->Assembly_Product_Code] = $agmtProd;
                                    $agmt[$agmtProd->agreement->id] = $agmtProd->agreement->name;
                                }
                                
                               // $agmt   = RouteAgreement::getAgmtByRouteId($this->Id);         
                                    $i =1;
                                    $content .= '<div class="attributesContainer">
                                                    <div>
                                                        <div class="panel">
                                                            <table class="form-fields">
                                                                <tbody>';

                                                        foreach ($agmt as $agreementKey => $agreementName) {
                                                            $content .='<tr>
                                                                            <td style="width:25px; padding-left:10px;"><b>' .$i.'.</b></td>
                                                                            <td style="align:left; padding-left:5px;">'.$agreementName.'</td>
                                                                            
                                                                        </tr>';
                                                            $i++;   
                                                         }                                         
                                                         $content .='</tbody>
                                                             </table>                                 
                                                        </div>
                                                    </div>
                                               </div>';

                   $content .='<div class="panel details-table">
                                    <table class="form-fields">                                
                                        <tbody>
                                            <tr>
                                                <td>Product Code</td>
                                                <td>Assembly</td>
                                                <td>Product</td>
                                                <td>UOM</td>';
                                                $j =1;
                                                foreach ($agmt as $agreementKey => $agreement) {
                                                    $agmt_id_arr[] = $agreementKey;
                                                    $content .='<td>'.$j.'</td>';
                                                    $j++;
                                                }
                        $content .='</tr>';
                                                            
                        $column_count = 4+count($agmt_id_arr);
                        foreach ($agreementPdctMap as $CategoryKey => $agreementArray) {
                            $content .='<tr>
                                        <th colspan="'.$column_count.'" class="align_left" style="background-color:gray; color:white; padding-left: 5px; text-align: left;font-size:14px;">' . $CategoryKey . ' </th>
                                    </tr>';
                            foreach ($agreementArray as $agreementKey => $agreementpdt) {
                                $agreementProduct = 1;                        
                                $content .='<tr>
                                                <td style="font-size:12px">' . $agreementpdt->agreementproduct->costbook->productcode . '
                                                    <input value=' . $agreementpdt->agreementproduct->costbook->id . ' name="productId" id="productId_' . $countOfSelectedRow . '" type="hidden">
                                                </td>
                                                <td style="font-size:12px">' . $agreementpdt->agreementproduct->Assembly_Product_Code . '</td>
                                                <td style="font-size:12px">' . $agreementpdt->agreementproduct->name . '</td>
                                                <td style="font-size:12px">' . $agreementpdt->agreementproduct->costbook->unitofmeasure . '</td>';
                                                foreach ($agmt_id_arr as $agmt_id) {
                                                    if(in_array($agmt_id, $agreementPdctIds[$CategoryKey][$agreementpdt->agreementproduct->costbook->productcode.'-'.$agreementpdt->agreementproduct->Assembly_Product_Code]['present_agmt_ids']))
                                                    {
                                                        //To get the Agmtprod Id based on the agmt & costbook Id
                                                        $getAgmtProdId   = AgreementProduct::getAgmtProdIdByAgmtIdCostBookId($agmt_id, $agreementpdt->agreementproduct->costbook->id, $agreementpdt->agreementproduct->costbook->productcode, $agreementpdt->agreementproduct->Assembly_Product_Code, $agreementpdt->agreementproduct->Category);
//                                                        $totalavailableUnits = '';
//                                                        $remainingUnits = '';
//                                                        if(!empty($getAgmtProdId))
//                                                        {
//                                                            $consumedUnits   = round($getAgmtProdId->consumed_units, 4);
//                                                            if(isset($getAgmtProdId->frequency)){
//                                                                $totalavailableUnits = round($getAgmtProdId->quantity,4) * round($getAgmtProdId->frequency,4);
//                                                                $remainingUnits = $totalavailableUnits-$consumedUnits;
//                                                            }else{
//                                                                $totalavailableUnits = round($getAgmtProdId->quantity,4);
//                                                                $remainingUnits = $totalavailableUnits-$consumedUnits;
//                                                            }
//                                                        }
                                                        $content .='<td style="text-align:left; font-weight:normal; font-size:13px;"><input type="text" name="route_prod_consumed" class="routeProdConsumed" style="width:60%;"></td>';
                                                    }
                                                    else
                                                    {
                                                        $content .='<td></td>';
                                                    }
                                                }                                  
                                            $content .=' </tr>';
                                $countOfSelectedRow++;
                            }
                            $CategoryKeyCount++;
                        }          
                                    
            $content .= '<tr><td colspan="'.$column_count.'"><p>&nbsp;</p></td></tr></tbody></table></div>';                    
            return $content;
        }
    }
?>
