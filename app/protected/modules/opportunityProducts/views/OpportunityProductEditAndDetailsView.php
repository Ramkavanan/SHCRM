<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class OpportunityProductEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'CancelLink', 'renderType' => 'Edit'),
                            array('type' => 'SaveButton', 'renderType' => 'Edit'),
                            array('type' => 'ListLink',
                                'renderType' => 'Details',
                                'label' => "eval:Yii::t('Default', 'Return to List')"
                            ),
                            array('type' => 'EditLink', 'renderType' => 'Details'),
                            array('type' => 'AuditEventsModalListLink', 'renderType' => 'Details'),
				array('type' => 'CopyLink',       'renderType' => 'Details'),
                        ),
                    ),
                    'derivedAttributeTypes' => array(
                        'DateTimeCreatedUser',
                        'DateTimeModifiedUser',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
			array(
			    'title' => 'Information',
                            'rows' => array(
                               // Row 1
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'opportunity', 'type' => 'Opportunity'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Category', 'type' => 'Text'),
                                            ),
                                        ),
                                    ),
                                ),
                                //Row 2
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'costbook', 'type' => 'Costbook'),
						 // array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Product_Cost_of_Goods_Sold', 'type' => 'Text'),
                                            ),
                                        ),
                                    ),
                                ),
                                // Row 3
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Frequency', 'type' => 'Decimal'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Final_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                
                                //Row 4
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Quantity', 'type' => 'Decimal'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Total_MHR', 'type' => 'Decimal'),
                                            ),
                                        ),
                                    )
                                ),
                                //Row 5
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                              //  array('attributeName' => '', 'type' => ''),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Total_Direct_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                             ),
                        ),
			// Panel Second
			array(
                            'title'=>'Additional Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Burden_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Materials_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),

                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Category_GPM', 'type' => 'Decimal'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Other_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                
                                 array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Equipment_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Sub_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                
                                 array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                //array('attributeName' => 'Equipment_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Labor_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                        ),// Panel Second end
			// Panel Third start
			/**array(
                            'title'=>'System Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Opportunity_Key', 'type' => 'Text'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Old_Auto_Number_Name', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Old_Opportunity_Products_ID', 'type' => 'Text'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Old_product_Id', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                //array('attributeName' => 'Equipment_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Old_Opportunity_ID', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                        ),*/
			//Panel Third End
		    ),
                ),
            );
            return $metadata;
        }

        protected function getNewModelTitleLabel()
        {
            return Yii::t('Default', 'Create OpportunityProductsModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }
    }
?>
