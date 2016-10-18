<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class AgreementProductEditAndDetailsView extends SecuredEditAndDetailsView
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
                                                array('attributeName' => 'agreement', 'type' => 'Agreement'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Product', 'type' => 'Costbook'),
                                            ),
                                        ),
                                    ),
                                ),
                                //Row 2
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                //array('attributeName' => 'Product', 'type' => 'Costbook'),
						  array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Assembly_Product_Code', 'type' => 'Text'),
                                            ),
                                        ),
                                    ),
                                ),
                                // Row 3
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Total_MHR', 'type' => 'Decimal'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                //array('attributeName' => 'Final_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                
                                //Row 4
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Frequency', 'type' => 'Decimal'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                               // array('attributeName' => 'Total_MHR', 'type' => 'Decimal'),
                                            ),
                                        ),
                                    )
                                ),
                                //Row 5
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Cloned_Product', 'type' => 'CheckBox'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                               // array('attributeName' => 'Total_Direct_Cost', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                             ),
                        ),
			
			// Panel Second start
			array(
                            'title'=>'System Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Old_Id', 'type' => 'Text'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Assembly_Frequency', 'type' => 'Decimal'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Old_Id_name', 'type' => 'Text'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Assembly_Product', 'type' => 'Costbook'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Category', 'type' => 'Text'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'Assembly_Quantity', 'type' => 'Decimal'),
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
                                                array('attributeName' => 'Quantity', 'type' => 'Decimal'),
                                            ),
                                        ),
                                    )
                                ),
				 array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'minFrequency', 'type' => 'Decimal'),
                                            ),
                                        ),
                                         array(
                                            'elements' => array(
                                                array('attributeName' => 'minQuantity', 'type' => 'Decimal'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                        ),
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
