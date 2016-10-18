<?php

    /**
     * Agreement Searchview to display the dynamic search options
     * to agreement listview page
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsSearchView extends SavedDynamicSearchView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'definedNestedAttributes' => array(
                        array('account' => array(
                            'name',
                        )),
                    ),
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
                            'advancedSearchType' => static::ADVANCED_SEARCH_TYPE_DYNAMIC,
                            'rows'   => array(),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

        public static function getModelForMetadataClassName()
        {
            return 'AgreementsSearchForm';
        }
    }
?>
