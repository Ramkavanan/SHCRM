<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookListView
 *
 * @author ideas2it
 */
class CostbookListView extends SecuredListView
    {
        public static function getDefaultMetadata()
        {
            $CostCatalogUserIdArr = array();
            $CostCatalogGroup = Group::getByName(Constant::CATALOGMANAGER);
            foreach($CostCatalogGroup->users as $CostCatalogGroupUserId)
            {
                $CostCatalogUserIdArr[] = $CostCatalogGroupUserId->id;
            }
            if(in_array(Yii::app()->user->userModel->id, $CostCatalogUserIdArr)){
                Yii::app()->clientScript->registerScript('HideToolbar',
                    '$("#MassDeleteMenuActionElement--yt4").hide();
                     $("#MassEditMenuActionElement--yt2").hide();
                ');
            }
            
            
            $metadata = array(
                'global' => array(
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'productcode', 'type' => 'Text', 'isLink' => true),
                                            ),
                                        ),
					array(
                                            'elements' => array(
                                                array('attributeName' => 'productname', 'type' => 'Text'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'costofgoodssold', 'type' => 'Text'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'unitofmeasure', 'type' => 'Text'),
                                            ),
                                        ),
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'category', 'type' => 'Text'),
//                                                array('attributeName' => 'productkey', 'type' => 'Text'),
                                            ),
                                        ),
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
    ');
?>