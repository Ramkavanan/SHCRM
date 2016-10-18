<?php
  
    /**
     * View for showing the print view.
     */
    class AgreementTrackView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {                      
            $content    = '';            
            $agreement  = Agreement::getById($this->Id);
            $agProducts = AgreementProduct::getAllAgmntProducts($this->Id);             
            $count      = count($agProducts);            
            $agPdctMap;            
            
            $themeName  = Yii::app()->theme->name;
            $logoUrl    = Yii::app()->themeManager->baseUrl . '/' . $themeName . '/images/login_page.png';
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
                                        <td style="padding:10px; font-size:14px;"><b>Project Name :</b> '.$agreement->name.'</td>                            
                                    </tr>
                                    <tr style="">
                                        <td style="padding:10px; font-size:14px;"><b>Account :</b> '.$agreement->account.'</td>
                                    </tr>
                                    <tr style="">
                                        <td style="padding:10px; font-size:14px;"><b>Project Manager :</b> '.$agreement->owner.'</td>
                                    </tr>
                                </table>
                               <div id="agProducts" style="diplay:block; padding: 10px;">';
                                  if(count($agProducts) > 0) {
                                        foreach($agProducts as $row) {
                                            $agPdctMap[$row->Category][] = $row;
                                        }
                                        
                                        $content .= '
                                            <table border="0" width="100%" class="">
                                                         <colgroup span="5"></colgroup>';

                                        $content .= '<thead style="color: black;padding: 10px;">
                                                                <th style="font-weight: bold;padding: 5px;text-align:left; font-size:12px;">Product Code</th>
                                                                <th style="font-weight: bold;padding: 5px;text-align:left; font-size:12px;">Product Name</th>                                        
                                                                <th style="font-weight: bold;padding: 5px;text-align:left; font-size:12px;">QTY</th>
                                                                <th style="font-weight: bold;padding: 5px;text-align:left; font-size:12px;">UOM</th>
                                                                <th style="font-weight: bold;padding: 5px;text-align:left; font-size:12px;">Phase 1 <br>___/___/___ </th>
                                                           </thead>
                                                           <tbody>';
                                         foreach ($agPdctMap as $key => $optpdctArray)  {
                                            $content .= '<th  style="padding: 3px; text-align: left; background-color:gray;color:white; font-size:13px;" colspan="5">'.$key.'</th>';
                                             foreach ($optpdctArray as $optKey => $optpdt){
                                                $content .= '<tr>
                                                    <td style="width: 8%; text-align: left; padding: 10px; font-size:14px;">'.$optpdt->costbook->productcode.'</td>
                                                    <td style="width: 15%; text-align: left; padding: 10px; font-size:14px;">'.$optpdt->name.'</td>
                                                    <td style="width: 5%; text-align: left; padding: 10px; font-size:14px;">'.$optpdt->Quantity.'</td>
                                                    <td style="width: 4%; text-align: left; padding: 10px; font-size:14px;">Budget: <br>'.$optpdt->costbook->unitofmeasure.'</td>
                                                    <td style="width: 10%; text-align: left; padding: 10px; font-size:14px;">'.$optpdt->Quantity.' <br><input type="text" style="width:80px;" /></td>
                                                 </tr>';
                                              }
                                         }
                                            $content .= '</tbody></table>';
                                     }
                               $content .= '</div>
                            </div>
                        </div>
                    </body>
                </html>';
            
            return $content;
        }
    }
?>
