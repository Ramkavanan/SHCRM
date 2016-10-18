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
class CostbookEditAndDetailsView extends SecuredEditAndDetailsView{
    public static function getDefaultMetadata()
    {
        $metadata = array('global'=>array(
            'toolbar' => array(
                'elements' => array(
                    array('type' => 'EditLink',       'renderType' => 'Details'),
                    array('type' => 'AuditEventsModalListLink',  'renderType' => 'Details'),
                    array('type' => 'CopyLink',       'renderType' => 'Details'),
                    array('type' => 'AccountDeleteLink', 'renderType' => 'Details'),
                ),
            ),
            'derivedAttributeTypes' => array(
                'DateTimeCreatedUser',
                'DateTimeModifiedUser',
            ),
            'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
            'panels' => array(
                array(
                    'title'=> 'Cost of Goods Selection',
                    'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'costofgoodssold', 'type' => 'DropDown', 'addBlank' => true),
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
        return Zurmo::t('costbook', 'Create CostbookModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
    }

    public static function getModuleClassName()
    {
        return 'CostbookModule';
    }
    protected function renderAfterFormLayoutForDetailsContent()
    {
    //   return CostbookDetailsViewUtil::renderAfterFormLayoutForDetailsContent($this->getModel());
    }

}
