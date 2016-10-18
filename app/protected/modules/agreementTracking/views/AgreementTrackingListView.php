<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingListView extends SecuredListView {

    public static function getDefaultMetadata() {
        $metadata = array(
            'global' => array(
                'panels' => array(
                    array(
                        'title' => 'Agreement Information',
                        'rows' => array(
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'name', 'type' => 'text', 'isLink' => true),
                                        ),
                                    ),
                                )
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'total_selected_products', 'type' => 'Text'),
                                        ),
                                    ),
                                   
                                ),
                            ),
                             array('cells' =>
                                array(
                                    array(
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
                        ),
                    ),
                ),
            ),
        );
        return $metadata;
    }

}

?>
