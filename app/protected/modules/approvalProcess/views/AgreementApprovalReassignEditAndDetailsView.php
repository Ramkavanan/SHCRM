<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class AgreementApprovalReassignEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'CancelLink', 'renderType' => 'Edit'),
                            array('type' => 'SaveButton', 'renderType' => 'Edit'),
                            array('type' => 'ListLink',
                                'renderType' => 'Details',
                                'label' => "eval:Yii::t('Default', 'Return to List')"
                            ),
                            array('type' => 'EditLink', 'renderType' => 'Details'),
                            array('type' => 'AuditEventsModalListLink', 'renderType' => 'Details'),
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
                                                array('attributeName' => 'agreement', 'type' => 'Agreement', 'isLink' => true),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'actualgmapprover', 'type' => 'User'),
                                            ),
                                        ),
                                    )
                                ),
            array('cells' =>
                array(
                    array(
                        'elements' => array(
                            array('attributeName' => 'comments', 'type' => 'TextArea'),
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
            return Yii::t('Default', 'Create DepartmentReferencesModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }
    }
    
	/*Yii::app()->clientScript->registerScript('append$Symbol',
        '$("table tbody tr th").filter(function() { 
                return $.text([this]) == "Burdon Cost";}).next().prepend("<span>$</span>");
           $("table tbody tr th").filter(function() { 
                return $.text([this]) == "Labor Cost";}).next().prepend("<span>$</span>");
            ');*/
?>
