<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingEditAndDetailsView extends SecuredEditAndDetailsView {

    public static function getDefaultMetadata() {
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
                        'title' => 'Agreement Tracking Detail',
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
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'agreement', 'type' => 'Agreement'),

                                        ),
                                    ),
                                 ),
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'total_selected_products', 'type' => 'Text'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                             array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'total_non_agreement_products', 'type' => 'Text'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'tracking_date', 'type' => 'Date'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'total_mhr', 'type' => 'text'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'total_material_units', 'type' => 'text'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                             array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'total_equipment_units', 'type' => 'text'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                             array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'total_quantity_consumed', 'type' => 'text'),
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

    protected function getNewModelTitleLabel() {
        return Yii::t('Default', 'Create AgreementTrackingModuleSingularLabel', LabelUtil::getTranslationParamsForAllModules());
    }
    

}

Yii::app()->clientScript->registerScript('append$Symbol', '$("table tbody tr th").filter(function() { 
    return $.text([this]) == "Burdon Cost";}).next().prepend("<span>$</span>");
    $("table tbody tr th").filter(function() { 
        return $.text([this]) == "Labor Cost";}).next().prepend("<span>$</span>");
    ');
?>
