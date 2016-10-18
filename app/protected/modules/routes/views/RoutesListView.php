<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RoutesListView extends SecuredListView
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
                                                array('attributeName' => 'name', 'type' => 'Text', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
				array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'crewname', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'createdDateTime', 'type' => 'dateTime'),
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
    
    Yii::app()->clientScript->registerScript('HideToolbar',
        '$("#MassEditMenuActionElement--yt2").hide();
        $("#ExportMenuActionElement--yt3").hide();
        $("#list-view-deleteMassActionAll").hide();
        $("#MassDeleteMenuActionElement--yt4").show();         
    ');
?>
