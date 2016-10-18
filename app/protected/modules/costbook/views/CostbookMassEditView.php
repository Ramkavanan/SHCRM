<?php

    class CostbookMassEditView extends MassEditView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'SaveButton'),
                            array('type' => 'CancelLink'),
                        ),
                    ),
                    'nonPlaceableAttributeNames' => array(
                        'productcode',
                        'productname',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
		                array(
		                    'title'=> 'Product Detail',
		                    'rows' => array(
		                        array('cells' =>
		                            array(
		                                array(
		                                    'elements' => array(
		                                        array('attributeName' => 'productcode', 'type' => 'Text'),
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
		                                        array('attributeName' => 'productname', 'type' => 'Text'),
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
		                                        array('attributeName' => 'category', 'type' => 'CheckBox'),
		                                    ),
		                                ),
		                             /*  array(
		                                    'elements' => array(
		                                        
		                                    ),
		                                ),  */
		                            ),

		                        ),
		                    array('cells' =>
		                            array(
		                                array(
		                                    'elements' => array(
		                                   
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
		                    'title'=> 'Product Information',
		                    'rows' => array(
		                        array('cells' =>
		                            array(
		                                array(
		                                    'elements' => array(
		                                        array('attributeName' => 'description', 'type' => 'TextArea'),
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
		                            ),
		                        ),
		                        array('cells' =>
		                            array(
		                                array(
		                                    'elements' => array(
		                                        array('attributeName' => 'proposaltext', 'type' => 'TextArea'),
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
    }
?>
