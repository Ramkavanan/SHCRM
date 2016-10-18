<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApprovalProcessListView extends SecuredListView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'comments', 'type' => 'TextArea', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                            array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'opportunity', 'type' => 'Opportunity'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'assignedto', 'type' => 'User'),
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
