<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingForMixedModelsSearchListView extends SecuredListView {

    public static function getDefaultMetadata() {
        $metadata = array(
            'global' => array(
                'panels' => array(
                    array(
                        'rows' => array(
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'name', 'type' => 'Text', 'isLink' => true),
                                        ),
                                    ),
                                )
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'tracking_date', 'type' => 'Date'),
                                        ),
                                    ),
                                )
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'total_mhr', 'type' => 'Decimal'),
                                        ),
                                    ),
                                )
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'total_material_units', 'type' => 'Decimal'),
                                        ),
                                    ),
                                )
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'total_equipment_units', 'type' => 'Decimal'),
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

    public static function getDesignerRulesType() {
        return 'ForMixedModelsSearchListView';
    }

}
