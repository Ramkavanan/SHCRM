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
class CostbookLaborView extends SecuredEditAndDetailsView{
    public static function getDefaultMetadata()
        {
        $metadata = array('global'=>array(
            'toolbar' => array(
            'elements' => array(
                    array('type'  => 'SaveButton',    'renderType' => 'Edit'),
                    array('type'  => 'CancelLink',    'renderType' => 'Edit'),
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
                                            array('attributeName' => 'laborCost', 'type' => 'Text'),
                                        ),
                                    ),
                                ),

                        ),
                        array('cells' =>
                                array(
                                   array(
                                        'elements' => array(
                                           array('attributeName' => 'burdenCost', 'type' => 'Text'),
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
                                            array('attributeName' => 'departmentreference', 'type' => 'DepartmentReference'),
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
                            )
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
	protected function getNewModelTitleLabel()
        {
         //   return Zurmo::t('costbook', 'Create CostbookModuleSingularLabel',
         //                            LabelUtil::getTranslationParamsForAllModules());
            return 'Cost book - Labor';
        }

	/**
     * Override to disabling LaborCost attribute.
     */
    protected function resolveElementInformationDuringFormLayoutRender(& $elementInformation)
    {
        parent::resolveElementInformationDuringFormLayoutRender($elementInformation);
        if ($elementInformation['attributeName'] == 'laborCost' || $elementInformation['attributeName'] == 'burdenCost')
        {
            $elementInformation['disabled'] = true;
        }
        if(preg_match('/edit/', $_SERVER['REQUEST_URI'])){
            $edit_mode = 'true';
        } else {
            $edit_mode = 'false';
        }
        if ( ($elementInformation['attributeName'] == 'productcode' ) && $edit_mode == 'true' )
        {
            $elementInformation['disabled'] = true;
        }
        if ( ($elementInformation['attributeName'] == 'costperunit' ) && $edit_mode == 'true' )
        {
            $elementInformation['disabled'] = true;
        }
    }

    protected function renderContent()
    {
        $this->registerCopyLaborCostAndBurdonCostFromDepartmentReferenceScript();
        return parent::renderContent();
    }

    protected function registerCopyLaborCostAndBurdonCostFromDepartmentReferenceScript()
    {
        $url           = Yii::app()->createUrl('costbook/default/getDepartmentReferenceLaborCostAndBurdonCostToCopy');
        // Begin Not Coding Standard
        Yii::app()->clientScript->registerScript('copyLaborCostAndBurdonCostFromDepartmentReferenceScript', "
            $('#Costbook_departmentreference_id').change(function()
                {
                        $.ajax(
                        {
                            url : '" . $url . "?id=' + $('#Costbook_departmentreference_id').val(),
                            type : 'GET',
                            dataType: 'json',
                            success : function(data)
                            {
                                $('#Costbook_laborCost').val(data.laborCost).trigger('change');
                                $('#Costbook_burdenCost').val(data.burdonCost).trigger('change');
                            },
                            error : function()
                            {
                                //todo: error call
                            }
                        }
                        );
                }
            );
        ");
        // End Not Coding Standard
    }	

}

