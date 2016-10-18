<?php

    class RoutesModalSearchView extends SearchView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'nonPlaceableAttributeNames' => array(
                        'productcode',
                    ),
                    'panels' => array(
                        array(
                            'locked' => true,
                            'title'  => 'Basic Search',
                            'rows'   => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'anyMixedAttributes',
                                                      'type' => 'AnyMixedAttributesSearch', 'wide' => true),
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

        public static function getDesignerRulesType()
        {
            return 'ModalSearchView';
        }

        public static function getModelForMetadataClassName()
        {
            return 'RoutesSearchForm';
        }
    }
?>
