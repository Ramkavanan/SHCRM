<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookSearchView
 *
 * @author ideas2it
 */
class CostbookSearchView extends SavedDynamicSearchView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
					'definedNestedAttributes' => array(
                        array('departmentreference' => array(
                            'name',
                        )),
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
            return 'CostbookSearchForm';
        }
}
