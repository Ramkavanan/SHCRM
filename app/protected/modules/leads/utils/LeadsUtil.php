<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2015 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2015. All rights reserved".
     ********************************************************************************/

    /**
     * Helper class with functions
     * to assist in working with Leads module
     * information
     */
    class LeadsUtil
    {
        const LEAD_CONVERSION_ACCOUNT_DATA_SESSION_KEY = 'leadConversionAccountPostData';

        /**
         * Given a contact and an account, use the mapping in the
         * Leads Module to copy attributes from contact to Account
         * order number is.
         * @param $contact Contact model
         * @param $account Account model
         * @return Account, with mapped attributes from Contact
         */
        public static function attributesToAccount(Contact $contact, Account $account)
        {
            assert('!empty($contact->id)');
            $metadata = LeadsModule::getMetadata();
            $map = $metadata['global']['convertToAccountAttributesMapping'];
            foreach ($map as $contactAttributeName => $accountAttributeName)
            {
                $account->$accountAttributeName = $contact->$contactAttributeName;
            }
            return $account;
        }

        /**
         * Given a post data array, map the lead to account attributes
         * but only if the post data does not contain a set attribute.
         * This method is used when a posted form has an empty value on
         * an input field.  We do not want to set the mapped field since
         * the use of setAttributes will pick up the correct information
         * from the posted data.  This will allow form validation to work
         * properly in the case where a mapped field is cleared to blank
         * in the input field and submitted. Such an event should trigger
         * a form validation error.
         * @see LeadsUtil::attributesToAccount
         * @param $contact Contact model
         * @param $account Account model
         * @param $postData array of posted form data
         * @return Account, with mapped attributes from Contact
         */
        public static function attributesToAccountWithNoPostData(Contact $contact, Account $account, array $postData)
        {
            assert('is_array($postData)');
            assert('!empty($contact->id)');
            $metadata = LeadsModule::getMetadata();
            $map = $metadata['global']['convertToAccountAttributesMapping'];
            foreach ($map as $contactAttributeName => $accountAttributeName)
            {
                if (!isset($postData[$accountAttributeName]))
                {
                    $account->$accountAttributeName = $contact->$contactAttributeName;
                }
            }
            return $account;
        }

        public static function storeIntoSession($key, $value)
        {
            Yii::app()->session->add($key, $value);
        }

        public static function getFromSession($key)
        {
            return Yii::app()->session->get($key);
        }

        public static function removeFromSession($key)
        {
            Yii::app()->session->remove($key);
        }

        public static function createAccountForLeadConversionFromAccountPostData($accountPostData, $contact, $controllerUtil)
        {
            if (isset($accountPostData['AccountSkip']) && $accountPostData['AccountSkip'] == true)
            {
                return null;
            }
            elseif (isset($accountPostData['SelectAccount']) && $accountPostData['SelectAccount'] == true)
            {
                $account = Account::getById(intval($accountPostData['accountId']));
                return $account;
            }
            elseif (isset($accountPostData['CreateAccount']) && $accountPostData['CreateAccount'] == true)
            {
                unset($accountPostData['CreateAccount']);
                $account = new Account();
                $account = LeadsUtil::attributesToAccountWithNoPostData($contact, $account, $accountPostData);
                $savedSuccessfully = false;
                $modelToStringValue = null;
                $account            = $controllerUtil->saveModelFromPost($accountPostData, $account, $savedSuccessfully,
                                                                            $modelToStringValue, false);
                if (!$account->getErrors())
                {
                    $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::makeBySecurableItem($contact);
                    ExplicitReadWriteModelPermissionsUtil::resolveExplicitReadWriteModelPermissions($account, $explicitReadWriteModelPermissions);
                    if (!$account->save())
                    {
                        throw new NotSupportedException();
                    }else{
                        $fullName = $account->firstName .' '.$account->lastName;    //For save fullname for acount
                        $account->fullName = trim($fullName);
                        $account->save();
                    }
                    return $account;
                }
            }
        }

        /**
         * If no states exist, throws MissingContactsStartingStateException
         * @return ContactState object
         */
        public static function getStartingState()
        {
            $states = ContactState::getAll('order');
            if (count($states) == 0)
            {
                throw new MissingContactsStartingStateException();
            }
            return $states[0];
        }

        /**
         * Get an array of only the states from the starting state onwards, order/name pairings of the
         * existing lead states ordered by order.
         * @return array
         */
        public static function getLeadStateDataFromStartingStateOnAndKeyedById()
        {
            $leadStatesData = array();
            $states            = ContactState::getAll('order');
            $startingState     = ContactsUtil::getStartingStateId();
            foreach ($states as $state)
            {
                if ($startingState == $state->id)
                {
                    break;
                }
                $leadStatesData[$state->id] = $state->name;
            }
            return $leadStatesData;
        }

        /**
         * Get an array of only the states from the starting state onwards, order/translated label pairings of the
         * existing lead states ordered by order.
         * @param string $language
         * @return array
         */
        public static function getLeadStateDataFromStartingStateKeyedByIdAndLabelByLanguage($language)
        {
            assert('is_string($language)');
            $leadStatesData = array();
            $states            = ContactState::getAll('order');
            $startingState     = ContactsUtil::getStartingStateId();
            foreach ($states as $state)
            {
                if ($startingState == $state->id)
                {
                    break;
                }
                $leadStatesData[$state->id] = ContactsUtil::resolveStateLabelByLanguage($state, $language);
            }
            return $leadStatesData;
        }

        /**
         * Get an array of states from the starting state onwards, id/translated label pairings of the
         * existing contact states ordered by order.
         * @param string $language
         * @return array
         */
        public static function getLeadStateDataFromStartingStateLabelByLanguage($language)
        {
            assert('is_string($language)');
            $leadStatesData = array();
            $states            = ContactState::getAll('order');
            $startingState     = ContactsUtil::getStartingStateId();
            foreach ($states as $state)
            {
                if ($startingState == $state->id)
                {
                    break;
                }
                $state->name = ContactsUtil::resolveStateLabelByLanguage($state, $language);
                $leadStatesData[] = $state;
            }
            return $leadStatesData;
        }

        /**
         * @param ContactState $state
         * @return bool
         */
        public static function isStateALead(ContactState $state)
        {
            $leadStatesData = self::getLeadStateDataFromStartingStateOnAndKeyedById();
            if ($state->id > 0 && isset($leadStatesData[$state->id]))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @param string $stateName
         * @return bool
         */
        public static function isStateALeadByStateName($stateName)
        {
            assert('is_string($stateName)');
            $leadStatesData = self::getLeadStateDataFromStartingStateOnAndKeyedById();
            foreach ($leadStatesData as $leadStateName)
            {
                if ($stateName == $leadStateName)
                {
                    return true;
                }
            }
            return false;
        }
public static function makePrintView($data, $leadId) {
            $content    = '';          
            $lead       = Contact::getById($leadId);
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
                            <td colspan="4" style="padding:20px;">'.$lead->firstName.'<hr></td>                            
                        </tr>                        
                        <tr style="font-size:12px;">
                            <th>Company Name</th><td colspan="1">'.$lead->companyName.'</td>
                            <th>Status</th><td colspan="1">'.($lead->statusCstm != '(None)' ? $lead->statusCstm : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Name</th><td colspan="3">'.$lead->firstName.' '. $lead->lastName .'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Lead Status</th><td colspan="1">'.($lead->statusCstm != '(None)' ? $lead->statusCstm : '').'</td>
                            <th>Industry</th><td colspan="1">'.($lead->industry != '(None)' ? $lead->industry : '').'</td>    
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Next Step Date</th><td colspan="1">'.($lead->nxtStepDateCstm != '(None)' ? $lead->nxtStepDateCstm : '').'</td>
                            <th>Site Classification</th><td colspan="1">'.($lead->leadSiteDetaCstm != '(None)' ? $lead->leadSiteDetaCstm : '').'</td>                            
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Site Detail</th><td colspan="1">'.($lead->website != '(None)' ? $lead->website : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">

                        </tr>
                        <tr style="font-size:12px;">
                            <th>Source</th><td colspan="1">'.($lead->source != '(None)' ? $lead->source : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Office Phone</th><td colspan="1">'.($lead->officePhone != '(None)' ? $lead->officePhone : '').'</td>
                            <th>Referral Type</th><td colspan="1">'.($lead->referalTypeCstm != '(None)' ? $lead->referalTypeCstm : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Mobile Phone</th><td colspan="1">'.($lead->mobilePhone != '(None)' ? $lead->mobilePhone : '').'</td>    
                            <th>Office Fax</th><td colspan="1">'.($lead->officeFax != '(None)' ? $lead->officeFax : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Lead Comment</th><td colspan="1">'.($lead->leadCommentCstm != '(None)' ? $lead->leadCommentCstm : '').'</td>
                            <th>Email</th><td colspan="1">'.($lead->primaryEmail != '(None)' ? $lead->primaryEmail : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Area of Interest</th><td colspan="1">'.($lead->areaOfIntereCstm != '(None)' ? $lead->areaOfIntereCstm : '').'</td>
                            <th>Website</th><td colspan="1">'.($lead->website != '(None)' ? $lead->website : '').'</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <th>Incumbent Provider</th><td colspan="1">'.($lead->leadIncumproCstm != '(None)' ? $lead->leadIncumproCstm : '').'</td>
                        </tr>
                    </tbody>
                  </table>                   
                <div id="show_expand">
                    <div class="panel">
                        <div class="panelTitle">Description Information</div>
                        <div id="descDiv" class="DivToHide" style="diplay:block;">
                            <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                                <tbody>
                                    <tr style="font-size:12px; padding:20px;"> 
                                        <th>Description</th>
                                        <td colspan="3" style="font-size:12px; padding:1%;">'.($lead->description != '(None)' ? $lead->description : '').'</td>                                
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panelTitle">Address Information</div>
                        <div id="addressInfoDiv" class="DivToHide" style="diplay:block;">
                            <table class="form-fields double-column"><colgroup><col class="col-0"><col class="col-1"><col class="col-2"><col class="col-3"></colgroup>
                                <tbody>
                                    <tr style="font-size:12px; padding:20px;"> 
                                        <th>Primary Address</th>
                                            <td colspan="1" style="font-size:12px; padding:1%;">'.($lead->primaryAddress != '(None)' ? $lead->primaryAddress : '').'
                                            </td>                                
                                        <th>Do Not Mail</th>
                                            <td colspan="1" style="font-size:12px;">'.($lead->donotMailCstm > 0 ? '<input id="" type="checkbox" checked="1" disabled="1" style= "color:green">' : '<input id="" type="checkbox" disabled="1">').'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <div style="padding:20px;">
                            <hr>
                            <div>
                                </br>Copyright Vertware, Inc. All rights reserved.
                            </div>
                        </div>        
                    </div>
                </div>
                </body>
                </html>
';
            return $content;
	}
    }
?>
