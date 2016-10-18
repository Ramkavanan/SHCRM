<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JobSchedulingListView extends SecuredListView
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
                                                array('attributeName' => 'crewName', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'status', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'agreement', 'type' => 'Agreement', 'isLink' => true),
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
    Yii::app()->clientScript->registerScript('Disabled_mass_delete_SelectAll ',
       '$("#list-view-deleteMassActionAll").css("display","none");
        $("#MassDeleteMenuActionElement--yt4 span").text("Archive");
//        $( "#list-view-deleteMassActionSelected" ).click(function() {
//            var res = confirm( "Are you sure you want to archive this opportunity and releated agreement?" );
////            alert(res);return false;
//          });        
    ');
?>
