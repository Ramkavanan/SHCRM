<?php
    class AgreementProductsModalSearchView extends SearchView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'panels' => array(
                        array(
                            'locked' => true,
                            'title'  => 'Basic Search',
                            'rows' => array(
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
                        array(
                            'title' => 'Advanced Search',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                )
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
            return 'AgreementProductsSearchForm';
        }
    }
?>
