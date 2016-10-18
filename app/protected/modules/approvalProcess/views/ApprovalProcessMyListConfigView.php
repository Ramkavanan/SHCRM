<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApprovalProcessMyListConfigView
 *
 * @author ramachandran
 */
class ApprovalProcessMyListConfigView extends MyListConfigView{
    public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'SaveButton'),
                        ),
                    ),
                    'nonPlaceableAttributeNames' => array(
                        'anyMixedAttributes',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'title' => 'List Filters',
                            'rows' => array(

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

        public static function getDisplayDescription()
        {
            return Zurmo::t('ApprovalProcessModule', 'My ApprovalProcessModulePluralLabel', LabelUtil::getTranslationParamsForAllModules());
        }

        public static function getModelForMetadataClassName()
        {
            return 'ApprovalProcessSearchForm';
        }
}

?>
