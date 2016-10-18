<?php
  
    /**
     * View for showing the print view.
     */
    class ContactPrintView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {                      
            $content   = '';
            $contact = Contact::getById($this->Id);
            
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
                            <td colspan="4" style="padding:20px;">'.$contact->firstName.''.$contact->lastName.'<hr></td>                            
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Name</th><td colspan="3">'.$contact->firstName.'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Account</th><td colspan="1">'.$contact->account.'</td>
                            <th>Mode of Contact</th><td colspan="1">'.($contact->preContactCstm != '(None)' ? $contact->preContactCstm : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Department</th><td colspan="1">'.$contact->department.'</td>
                            <th>Office Phone</th><td colspan="1">'.($contact->officePhone != '(None)' ? $contact->officePhone : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Reports To</th><td colspan="1">'.($contact->reportToCstm != '(None)' ? $contact->reportToCstm : '').'</td>
                            <th>Mobile Phone</th><td colspan="1">'.($contact->mobilePhone != '(None)' ? $contact->mobilePhone : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Relationship to Account</th><td colspan="1">'.($contact->relshipAcctCstm != '(None)' ? $contact->relshipAcctCstm : '').'</td>
                            <th>Home Phone</th><td colspan="1">'.($contact->homePhoneCstm != '(None)' ? $contact->homePhoneCstm : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Lead Comment</th><td colspan="1">'.($contact->leadCommentCstm != '(None)' ? $contact->leadCommentCstm : '').'</td>
                            <th>Office Fax</th><td colspan="1">'.($contact->officeFax != '(None)' ? $contact->officeFax : '').'</td>
                        </tr> 
                        <tr style="font-size:12px;">
                            <th>Status</th><td colspan="1">'.($contact->state->name != '(None)' ? $contact->state->name : '').'</td>
                            <th>Email</th><td colspan="1">'.($contact->primaryEmail != '(None)' ? $contact->primaryEmail : '').'</td>
                        </tr>
                     </tbody>
                  </table>
                  
                  <div class="panel">
                    <div class="panelTitle">Address Information</div>
                    <div id="addressDiv" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px; padding:20px;"> 
                                    <th>Primary Address</th>
                                    <td colspan="1" style="font-size:12px;">
                                        '.($contact->primaryAddress != '(None)' ? $contact->primaryAddress : '').'
                                   </td>
                                   <th>Secondary Address</th>
                                    <td colspan="1" style="font-size:12px;">
                                        '.($contact->secondaryAddress != '(None)' ? $contact->secondaryAddress : '').'
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
                                        '.($contact->description != '(None)' ? $contact->description : '').'
                                   </td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>                  
                </div>
                
                <div class="panel">
                    <div class="panelTitle">Additional Information</div>
                    <div id="additionalInformation" class="DivToHide" style="diplay:block;">
                        <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                            <tbody>
                                <tr style="font-size:12px;">
                                    <th>In Active</th><td colspan="1" style="font-size:12px;">'.($contact->inActiveCstm > 0 ? '<input id="" type="checkbox" checked="1" disabled="1">' : '<input id="" type="checkbox" disabled="1">').'</td>
                                    <th>Other Phone Cstm</th><td colspan="1">'.($contact->otherPhoneCstm != '(None)' ? $contact->otherPhoneCstm : '').'</td>
                                </tr>
                                <tr style="font-size:12px;">
                                    <th>Birth date</th><td colspan="1">'.($contact->birthDateCstm != '0000-00-00' ? DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($contact->birthDateCstm) : '').'</td>
                                    <th>Work Phone</th><td colspan="1">'.($contact->workPhoneCstm != '(None)' ? $contact->workPhoneCstm : '').'</td>
                                </tr> 
                                <tr style="font-size:12px;">
                                    <th>Source</th><td colspan="1">'.($contact->source != '(None)' ? $contact->source : '').'</td>
                                    <th>Assistant Phone</th><td colspan="1">'.($contact->assistPhoneCstm != '(None)' ? $contact->assistPhoneCstm : '').'</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                  
                </div>
                
                </body>
                </html>';
            
            return $content;
        }
    }
?>
