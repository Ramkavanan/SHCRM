<?php
  
    /**
     * View for showing the print view.
     */
    class AccountPrintView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {                      
            $content   = '';
            $account = Account::getById($this->Id);
            
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
<script>
    function divShow()
    {      
        var elems = document.getElementsByClassName("DivToHide");
        for(var i = 0; i < elems.length; i++) {
            elems[i].style.display = "block";
        }
    }
    function divHide()
    {        
        var elems = document.getElementsByClassName("DivToHide");
        for(var i = 0; i < elems.length; i++) {
            elems[i].style.display = "none";
        }
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
                <table class="form-fields double-column">
                    <colgroup>
                        <col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td colspan="4" style="padding:20px;">'.$account->name.'<hr></td>                            
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Name</th><td colspan="1">'.$account->name.'</td>
                            <th>Office Phone</th><td colspan="1">'.$account->officePhone.'</td> 
                        </tr>
                        <tr style="font-size:12px;">
                              <th>Office Fax</th><td colspan="1">'.$account->officeFax.'</td>                           
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Industry</th><td colspan="1">'.($account->industry != '(None)' ? $account->industry : '').'</td>
                            <th>Average Household Income</th><td colspan="1"> $ '.($account->avghouseIncoCstm != '(None)' ? $account->avghouseIncoCstm : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Property Value</th><td colspan="1"> $ '.($account->propValueCstm != '(None)' ? $account->propValueCstm : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Site Detail</th><td colspan="1">'.($account->siteDetailCstm != '(None)' ? $account->siteDetailCstm : '').'</td>
                            <th>Website</th><td colspan="1">'.($account->website != '(None)' ? $account->website : '').'</td>                            
                        </tr>
                        <tr style="font-size:12px;">
                             <th>Incumbent Provider</th><td colspan="1">'.($account->incumbProvidCstm != '(None)' ? $account->incumbProvidCstm : '').'</td>
                        </tr>  
                     </tbody>
                  </table>
                  
                  <div class="panel">
                    <div class="panelTitle">Address Information</div>
                    <div id="addressDiv" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px; padding:20px;"> 
                                    <th>Billing Address</th>
                                    <td colspan="2" style="font-size:12px;">
                                        '.($account->billingAddress != '(None)' ? $account->billingAddress : '').'
                                   </td>                                
                                </tr>
                                <tr style="font-size:12px; padding:20px;"> 
                                    <th>Shipping Address</th>
                                    <td colspan="2" style="font-size:12px;">
                                        '.($account->shippingAddress != '(None)' ? $account->shippingAddress : '').'
                                   </td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>                  
                </div>
                
                <div class="panel">
                    <div class="panelTitle">Description Information</div>
                    <div id="description" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px; padding:20px;"> 
                                    <th>Description</th>
                                    <td colspan="3" style="font-size:12px; padding:20px;">
                                        '.($account->description != '(None)' ? $account->description : '').'
                                   </td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>                  
                </div>
                </div>
                </body>
                </html>';
            
            return $content;
        }
    }
?>
