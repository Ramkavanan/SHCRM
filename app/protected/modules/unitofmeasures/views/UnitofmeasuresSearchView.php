<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class UnitofmeasuresSearchView extends SavedDynamicSearchView
    {
    public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
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
            return 'UnitofmeasuresSearchForm';
        }
    }
?>
