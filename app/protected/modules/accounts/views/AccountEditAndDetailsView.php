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

    class AccountEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type'  => 'SaveButton',    'renderType' => 'Edit'),
                            array('type'  => 'CancelLink',    'renderType' => 'Edit'),
                            array('type' => 'EditLink',       'renderType' => 'Details'),
                            array('type' => 'AuditEventsModalListLink',  'renderType' => 'Details'),
                            array('type' => 'CopyLink',       'renderType' => 'Details'),
                            array('type' => 'AccountDeleteLink', 'renderType' => 'Details'),
                            array(
                                    'type' => 'PrintViewLink',
                                    'renderType' => 'Details',
                                    'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=850,height=600,scrollbars=yes"); return false;')
                                ),
                        ),
                    ),
                    'nonPlaceableAttributeNames' => array(
                        'account',
                        'owner',
                        'latestActivityDateTime'
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'title'=> 'Account Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'officeFax', 'type' => 'Phone'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'fullName', 'type' => 'Text'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'primaryEmail', 'type' => 'EmailAddressInformation'),      
                                            ),
                                        ),
                                    )
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
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'industry', 'type' => 'DropDown', 'addBlank' => true),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'avghouseIncoCstm', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'propValueCstm', 'type' => 'CurrencyValue'),  
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                //array('attributeName' => 'employees', 'type' => 'Integer'),
                                                array('attributeName' => 'siteDetailCstm', 'type' => 'DropDown', 'addBlank' => true),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'website', 'type' => 'Url'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(

                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'incumbProvidCstm', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'billingAddress', 'type' => 'Address'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'shippingAddress', 'type' => 'Address'),
                                            ),
                                        ),
                                    )
                                ),
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
                                    )
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
            return Zurmo::t('AccountsModule', 'Create AccountsModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }

        public static function getModuleClassName()
        {
            return 'AccountsModule';
        }

        protected function renderAfterFormLayoutForDetailsContent()
        {
            return AccountDetailsViewUtil::renderAfterFormLayoutForDetailsContent($this->getModel());
        }
    }
    
    Yii::app()->clientScript->registerScript('numericValidation',
    '$("#Account_officePhone").attr("placeholder", "(___) ___-____");
        $("#Account_officePhone").attr("maxlength","10");
        $("#Account_officePhone").keypress(function(e){
            this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,"($1) $2-$3");
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }            
        });
        $("#Account_mobilePhone").attr("placeholder", "(___) ___-____");
        $("#Account_mobilePhone").attr("maxlength","10");
        $("#Account_mobilePhone").keypress(function(e){
            this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,"($1) $2-$3");
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("#Account_officeFax").attr("placeholder", "(___) ___-____");
        $("#Account_officeFax").attr("maxlength","10");
        $("#Account_officeFax").keypress(function(e){
            this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,"($1) $2-$3");
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }            
        });
    ');
?>
