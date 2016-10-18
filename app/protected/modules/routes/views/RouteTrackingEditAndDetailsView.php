<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class RouteTrackingEditAndDetailsView extends SecuredEditAndDetailsView {

    public static function getDefaultMetadata() {
        $metadata = array(
            'global' => array(
                'toolbar' => array(
                    'elements' => array(
                        array('type' => 'CancelLink', 'renderType' => 'Edit'),
                        array('type' => 'SaveButton', 'renderType' => 'Edit'),
//                        array('type' => 'ListLink',
//                            'renderType' => 'Details',
//                            'label' => "eval:Yii::t('Default', 'Return to List')"
//                        ),
//                        array('type' => 'EditLink', 'renderType' => 'Details'),
//                        array('type' => 'AuditEventsModalListLink', 'renderType' => 'Details'),
                    ),
                ),
                'derivedAttributeTypes' => array(
                    'DateTimeCreatedUser',
                    'DateTimeModifiedUser',
                ),
                'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                'panels' => array(
                    array(
                        'title' => 'Route Tracking Detail',
                        'rows' => array(
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
//                                            array('attributeName' => 'name', 'type' => 'Text'),
                                        ),
                                    ),
                                )
                            ),
                             array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'date_of_service', 'type' => 'Text'),
                                        ),
                                    ),
                                 ),
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'service_start_time', 'type' => 'Text'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'service_end_time', 'type' => 'Text'),
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
        return Yii::t('Default', 'Create RouteTrackingModuleSingularLabel', LabelUtil::getTranslationParamsForAllModules());
    }
    

}

?>
