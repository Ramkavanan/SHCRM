<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class JobSchedulingEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'CancelLink', 'renderType' => 'Edit'),
                            array('type' => 'JobSaveButton', 'renderType' => 'Edit'),
                            array('type' => 'ListLink',
                                'renderType' => 'Details',
                                'label' => "eval:Yii::t('Default', 'Return to List')"
                            ),
                            array('type' => 'EditLink', 'renderType' => 'Details'),
                            array(
                                'type' => 'PrintViewLink',
                                'renderType' => 'Details',
                                'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=850,height=600,scrollbars=yes"); return false;')
                            ),
                            array('type' => 'JobSchedulingDeleteLink', 'renderType' => 'Details'),                            
                        ),
                    ),
                    'derivedAttributeTypes' => array(
                        'DateTimeCreatedUser',
                        'DateTimeModifiedUser',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'agreement', 'type' => 'Agreement'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'crewName', 'type' => 'Text'),
                                            ),
                                        ),
                                        array(
                                            'detailViewOnly' => true,
                                            'elements' => array(
                                                array('attributeName' => 'status', 'type' => 'Text'),
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
            return Yii::t('Default', 'Create Job Scheduling',
                                     LabelUtil::getTranslationParamsForAllModules());
        }
    }
    
    if (Yii::app()->controller->action->id == 'edit') {
        Yii::app()->clientScript->registerScript('AgmtDisabled',
            '$("#JobScheduling_agreement_name").prop("readonly", true);
             $("#JobScheduling_agreement_name").css({"background-color":"#efefef"}); 
             $("#JobScheduling_agreement_SelectLink").hide();
        ');
    }
?>
