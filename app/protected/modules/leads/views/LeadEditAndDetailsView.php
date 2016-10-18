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

    class LeadEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'SaveButton', 'renderType' => 'Edit'),
                            array('type' => 'CancelLink', 'renderType' => 'Edit'),
                            array('type' => 'EditLink',      'renderType' => 'Details'),
                            array('type' => 'AuditEventsModalListLink', 'renderType' => 'Details'),
                            array('type' => 'ConvertLink',   'renderType' => 'Details'),
                            array('type' => 'CopyLink',       'renderType' => 'Details'),
                            array('type' => 'LeadDeleteLink', 'renderType' => 'Details'),
                            array(
                                    'type' => 'PrintViewLink', 
                                    'renderType' => 'Details',
                                    'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow","width=850,height=600,scrollbars=yes"); return false;'
                                 )
                                ),

                        ),
                    ),
                    'derivedAttributeTypes' => array(
                        'TitleFullName',
                        'LeadStateDropDown',
                    ),
                    'nonPlaceableAttributeNames' => array(
                        'title',
                        'firstName',
                        'lastName',
                        'state',
                        'account',
                        'owner',
                        'latestActivityDateTime'
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'title'=> 'Lead Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'companyName', 'type' => 'Text'),
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'null', 'type' => 'LeadStateDropDown'),
                                            ),
                                        ),
                                    ),
                                ),
                        array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'null', 'type' => 'TitleFullName'),
                                        ),
                                    ),
                                ),
                           ),
                         array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'officePhone', 'type' => 'Phone'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'mobilePhone', 'type' => 'Phone'),
                                            ),
                                        ),
                                    ),
                                ),       
                         array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'primaryEmail', 'type' => 'EmailAddressInformation'), 
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'website', 'type' => 'Url'),
                                            ),
                                        ),
                                    ),
                                ),       
                                
                         array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'statusCstm', 'type' => 'DropDown', 'addBlank' => true),
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            array('attributeName' => 'industry', 'type' => 'DropDown', 'addBlank' => true),
                                         ),
                                    ),
                                ),
                           ),
                                
                          array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'nxtStepDateCstm', 'type' => 'Date'),
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            array('attributeName' => 'leadSiteclasCstm', 'type' => 'DropDown', 'addBlank' => true),
                                        ),
                                    ),
                                ),
                           ),
                           array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'leadIncumproCstm', 'type' => 'Text'), 
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            array('attributeName' => 'leadSiteDetaCstm', 'type' => 'DropDown', 'addBlank' => true),
                                        ),
                                    ),
                                ),
                           ),
                           array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'referalTypeCstm', 'type' => 'DropDown', 'addBlank' => true), 
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            array('attributeName' => 'source', 'type' => 'DropDown', 'addBlank' => true), 
                                        ),
                                    ),
                                ),
                           ),
                           array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'leadCommentCstm', 'type' => 'Text'),
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            array('attributeName' => 'officeFax', 'type' => 'Phone'), 
                                        ),
                                    ),
                                ),
                           ),
                           array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'areaOfIntereCstm', 'type' => 'MultiSelectDropDown', 'addBlank' => true),
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            array('attributeName' => 'preContactCstm', 'type' => 'DropDown', 'addBlank' => true),
                                        ),
                                    ),
                                ),
                           ),
                        ),
                     ),
                     array(
                            'title'=> 'Description Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'description', 'type' => 'TextArea'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        array(
                            'title'=> 'Address Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'primaryAddress', 'type' => 'Address'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'donotMailCstm', 'type' => 'CheckBox'),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

        protected function getNewModelTitleLabel()
        {
            return Zurmo::t('LeadsModule', 'Create LeadsModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }

        protected function renderAfterFormLayoutForDetailsContent()
        {
            return ContactDetailsViewUtil::renderAfterFormLayoutForDetailsContent($this->getModel(), null);
        }
    }
    
    Yii::app()->clientScript->registerScript('LastNameDisabled',
       '$("#Contact_lastName").attr("id","ContactLastName");
        $("#Contact_mobilePhone").attr("id","ContactMobilePhone");
        $("#Contact_officePhone").attr("id","ContactOfficePhone");
        $("#Contact_primaryEmail_emailAddress").attr("id","ContactPrimaryEmailEmailAddress");
        $("#ContactOfficePhone").attr("placeholder", "(___) ___-____");
        $("#ContactOfficePhone").attr("maxlength","10");
        $("#ContactOfficePhone").keypress(function(e){
            this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,"($1) $2-$3");
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }            
        });
        $("#ContactMobilePhone").attr("placeholder", "(___) ___-____");
        $("#ContactMobilePhone").attr("maxlength","10");
        $("#ContactMobilePhone").keypress(function(e){
            this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,"($1) $2-$3");
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("#Contact_officeFax").attr("placeholder", "(___) ___-____");
        $("#Contact_officeFax").attr("maxlength","10");
        $("#Contact_officeFax").keypress(function(e){
            this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,"($1) $2-$3");
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }            
        });
    ');
?>
