<?php
    /**
     * View for showing the configuration parameters for the @see agreementsMyListView.
     */
    class AgreementsMyListConfigView extends MyListConfigView
    {
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
                                                array('attributeName' => 'Status', 'type' => 'DropDown', 'addBlank' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'ownedItemsOnly', 'type' => 'CheckBox'),
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
            return Zurmo::t('AgreementsModule', 'My AgreementsModulePluralLabel', LabelUtil::getTranslationParamsForAllModules());
        }

        public static function getModelForMetadataClassName()
        {
            return 'AgreementsSearchForm';
        }
    }
?>
