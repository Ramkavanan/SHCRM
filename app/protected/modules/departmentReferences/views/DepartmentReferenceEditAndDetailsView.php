<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class DepartmentReferenceEditAndDetailsView extends SecuredEditAndDetailsView
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
                            array('attributeName' => 'name', 'type' => 'Text'),
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
                )
            ),
           
           
         
            array('cells' =>
                array(
                    array(
                        'detailViewOnly' => false,
                        'elements' => array(
                            array('attributeName' => 'burdonCost', 'type' => 'Decimal'),
                        ),
                    ),
                    array(
                        'detailViewOnly' => false,
                        'elements' => array(
                            array('attributeName' => 'laborCost', 'type' => 'Decimal'),
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
    
	Yii::app()->clientScript->registerScript('append$Symbol',
        '$("table tbody tr th").filter(function() { 
                return $.text([this]) == "Department Overhead";}).next().prepend("<span>$</span>");
           $("table tbody tr th").filter(function() { 
                return $.text([this]) == "Labor Cost";}).next().prepend("<span>$</span>");
            ');
?>
