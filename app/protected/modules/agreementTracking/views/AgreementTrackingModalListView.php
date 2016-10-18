<?php

class AgreementTrackingModalListView extends ModalListView {

    public static function getDefaultMetadata() {
        $metadata = array(
            'global' => array(
                'nonPlaceableAttributeNames' => array(
                    'owner',
                ),
                'panels' => array(
                    array(
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
                        ),
                    ),
                ),
            ),
        );
        return $metadata;
    }

}

?>
