<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class JobSchedulingForMixedModelsSearchListView extends SecuredListView {

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
                                            array('attributeName' => 'crewName', 'type' => 'Text'),
                                        ),
                                    ),
                                )
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
//                                            array('attributeName' => 'burdonCost', 'type' => 'Decimal'),
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
