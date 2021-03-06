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
class CostbookAssemblyView extends SecuredEditAndDetailsView{
    public static function getDefaultMetadata() {
        $metadata = array('global'=>array(
                'toolbar' => array(
                        'elements' => array(
                            array('type' => 'SaveButton',    'renderType' => 'Edit'),
                            array('type' => 'CancelLink',    'renderType' => 'Edit'),
                            array('type' => 'ListLink', 'renderType' => 'Details', 'label' => "eval:Yii::t('Default', 'Return to List')" ),
                            array('type' => 'EditLink',       'renderType' => 'Details'),
                            array('type' => 'AccountDeleteLink', 'renderType' => 'Details'),
                        ),
                    ),
                'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                'panels' => array(
                    array(
                        'title'=> 'Product Detail',
                        'rows' => array(
                            array('cells' =>
                                array(
                                    array(
                                        'detailViewOnly' => true,
                                        'elements' => array(
                                            array('attributeName' => 'costofgoodssold', 'type' => 'DropDown'),
                                        ),
                                    ),
                                    array(
                                        'elements' => array(
                                        ),
                                    ),
                                )
                            ),
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'productname', 'type' => 'Text'),
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
                                            array('attributeName' => 'unitofmeasure', 'type' => 'DropDown', 'addBlank' => true),
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
                    array(
                        'title'=> 'Categories',
                        'rows' => array(
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'category', 'type' => 'MultiSelectDropDown'),
                                        ),
                                    ),
                                   array(
                                        'elements' => array(
                                            
                                        ),
                                    ),
                                ),

                            ),
                       ),
                    ),
                    array(
                        'title'=> 'Product Information',
                        'rows' => array(
                            array('cells' =>
                                array(
                                    array(
                                        'elements' => array(
                                            array('attributeName' => 'description', 'type' => 'TextArea'),
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
                                            array('attributeName' => 'scopeofwork', 'type' => 'TextArea'),
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
                                            array('attributeName' => 'proposaltext', 'type' => 'TextArea'),
                                        ),
                                    ),                                    
                                    array(
                                        'elements' => array(

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

    protected function resolveElementInformationDuringFormLayoutRender(& $elementInformation)
    {
        parent::resolveElementInformationDuringFormLayoutRender($elementInformation);
        if(preg_match('/edit/', $_SERVER['REQUEST_URI'])){
            $edit_mode = 'true';
        } else {
            $edit_mode = 'false';
        }
        if ( ($elementInformation['attributeName'] == 'productcode' ) && $edit_mode == 'true' )
        {
            $elementInformation['disabled'] = true;
        }
    }

    protected function getNewModelTitleLabel()
    {
        return 'Costbook - Assembly Master Level';
    }
}

// To replace 'Save' button name to 'Next'
Yii::app()->clientScript->registerScript('Save',
    '$("#saveyt2 .z-label").html("Select Existing Assemblies");
     $("#Costbook_category_values").after("<span class=panelTitle>You can select / deselect multiple categories</span>");
     $("#Costbook_assemblydetail").attr("disabled", "disabled");
     $("#Costbook_assemblydetail").after("<span class=panelTitle>You can select / deselect the products by clicking Select Existing Assemblies</span>");
');
