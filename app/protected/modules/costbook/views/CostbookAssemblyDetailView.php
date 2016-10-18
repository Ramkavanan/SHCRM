<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookEditAndDetailsView
 *
 * @author ideas2it
 */
class CostbookAssemblyDetailView extends SecuredEditAndDetailsView {
    public static function getDefaultMetadata(){
        $metadata = array('global'=>array(
            'toolbar' => array(
                        'elements' => array(
                            array('type'  => 'SaveButton',    'renderType' => 'Edit'),
                            array('type'  => 'CancelLink',    'renderType' => 'Edit'),
                            array('type' => 'EditLink',       'renderType' => 'Details'),
                            array('type' => 'AuditEventsModalListLink',  'renderType' => 'Details'),
                            array('type' => 'CopyLink',       'renderType' => 'Details'),
                            array('type' => 'AccountDeleteLink', 'renderType' => 'Details'),
                        ),
                ),
                'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                'panels' => array(
                    array(
                        'title'=> 'Search',
                        'rows' => array(
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'assemblycategory', 'type' => 'DropDown'),
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            
                                        ),
                                    ),
                                ),
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'costofgoodssoldassembly', 'type' => 'DropDown'),
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                           
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

    protected function getNewModelTitleLabel()
    {
        return 'Costbook - Assembly Detail Level';
    }

    protected function renderAfterFormLayout($form) {
        $content  = "<div>";
        $content  = "<div><input type='button' value='button' name='' /></div>";
        $content  = "</div>";
        return $content;
    }

    
}
