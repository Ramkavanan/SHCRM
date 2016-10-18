<?php

/**
 * Render the create or edit agreeement for recurring type view.
 * 
 * @author Ramachandran.K (ramakavanan@gmail.com)
 */
 class AgreementRecurringEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $isUsersInAccountManageGroup = CostbookUtils::GetIsAccountManagerGroup();
            if(!empty(Yii::app()->user->userModel->isRootUser) || $isUsersInAccountManageGroup == TRUE || Yii::app()->user->userModel->role->name == Constant::GM){
                $elements['elements'] = array(    // Elements for super user, Account manager group users and GM role.
                    array('type' => 'SaveButton', 'renderType' => 'Edit'),
                    array('type' => 'CancelLink', 'renderType' => 'Edit'),
                    array('type' => 'ListLink',  'renderType' => 'Details', 'label' => "eval:Yii::t('Default', 'Return to List')"),
                    array('type' => 'CopyLink',       'renderType' => 'Details'),
                    array('type' => 'AgreementDeleteLink', 'renderType' => 'Details'),
                    array('type' => 'EditLink', 'renderType' => 'Details'),
                    array(
                            'type' => 'PrintViewLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=850,height=600,scrollbars=yes"); return false;')
                        ),
                    array(
                            'type' => 'TrackResetLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'return confirm("Are you sure to reset the tracking details?")')
                        ),
                    array(
                            'type' => 'TrackViewLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=850,height=600,scrollbars=yes"); return false;')
                        ),
                    array(
                            'type' => 'ActivateLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'return confirm("Are you sure to Activate the Agreement?")')
                        ),
                    array(
                            'type' => 'DeactivateLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'return confirm("Are you sure to Deactivate the Agreement?")')
                        ),
                    array(
                            'type' => 'AgreementCloseLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'return confirm("Are you sure to Close the Agreement?")')
                        ),
                );
            }else{
                $elements['elements'] = array(    // Elements for normal user.
                    array('type' => 'SaveButton', 'renderType' => 'Edit'),
                    array('type' => 'CancelLink', 'renderType' => 'Edit'),
                    array('type' => 'ListLink',  'renderType' => 'Details', 'label' => "eval:Yii::t('Default', 'Return to List')"),
                    array('type' => 'CopyLink',       'renderType' => 'Details'),
                    array('type' => 'AgreementDeleteLink', 'renderType' => 'Details'),
                    array('type' => 'EditLink', 'renderType' => 'Details'),
                    array(
                            'type' => 'PrintViewLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=850,height=600,scrollbars=yes"); return false;')
                        ),
                    array(
                            'type' => 'TrackViewLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=850,height=600,scrollbars=yes"); return false;')
                        ),
                    array(
                            'type' => 'ActivateLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'return confirm("Are you sure to Activate the Agreement?")')
                        ),
                    array(
                            'type' => 'DeactivateLink',
                            'renderType' => 'Details',
                            'htmlOptions'    => array('onClick' => 'return confirm("Are you sure to Deactivate the Agreement?")')
                        ),
                );
            }    
            $metadata = array(
                'global' => array(
                    'toolbar' => $elements,
                    'derivedAttributeTypes' => array(
                        'DateTimeCreatedUser',
                        'DateTimeModifiedUser',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
			    array(
				'title'=> 'Agreement Detail',
				'rows' => array(
				    array('cells' =>
					array(
					    array(
						'detailViewOnly' => false,
						'elements' => array(
						    array('attributeName' => 'RecordType', 'type' => 'Text'),
						),
					    ),
					   array(
                                               'detailViewOnly' => true,
						'elements' => array(
                                                    array('attributeName' => 'opportunity', 'type' => 'Opportunity'),
						),
					    ),
					)
					
				    ),
                                    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Agreement', 'type' => 'Agreement'),
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
						    array('attributeName' => 'account', 'type' => 'Account'),
						),
					    ),
					   array(
						'elements' => array(
						    array('attributeName' => 'Initial_Sales_Rep', 'type' => 'User'),
						),
					    ),
					),
					
				    ),
				array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'name', 'type' => 'Text'),
						),
					    ),
					   array(
						'elements' => array(
						    array('attributeName' => 'Contract_Number', 'type' => 'Text'),
						),
					    ),
					)
					
				    ),
				array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Status', 'type' => 'DropDown', 'addBlank' => true),
						),
					    ),
					   array(
                                                'detailViewOnly' => true,
                                                'elements' => array(
                                                    array('attributeName' => 'owner', 'type' => 'User'),
                                                 ),
					    ),
					)
					
				    ),
				array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Deactivation_Date', 'type' => 'Date'),
						),
					    ),
					   array(
						'elements' => array(
                                                    array('attributeName' => 'Estimator', 'type' => 'User'),
						   // array('attributeName' => 'OwnerExpirationNotice', 'type' => 'DropDown', 'addBlank' => true),
						),
					    ),
					)
					
				    ),

				array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Agreement_Type', 'type' => 'DropDown', 'addBlank' => true),
						),
					    ),
					   array(
						'elements' => array(
                                                    array('attributeName' => 'Estimator_Approval', 'type' => 'CheckBox'),

						),
					    ),
					)
				    ),
				array('cells' =>
					array(
					    array(
						'elements' => array(
                                                    array('attributeName' => 'Estimator_Approval_Date', 'type' => 'DateTime'),
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
			//Next Section with same panel
			array(
				'title'=> 'Description Information',
				'rows' => array(
				    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Description', 'type' => 'TextArea'),
						),
					    ),
                                            array(
                                                'elements' => array(
                                                ),
                                            ),
					),
					
				    ),

				),
			    ),
			// Next Section
			array(
				'title'=> 'Agreement Information',
				'rows' => array(
				    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Current_GPM', 'type' => 'Decimal'),
						),
					    ),
					   array(
						'elements' => array(
						   array('attributeName' => 'Total_Direct_Costs', 'type' => 'CurrencyValue'),
						),
					    ),
					),
					
				    ),
				    array('cells' =>
					array(
					   array(
                                                'detailViewOnly' => true,
						'elements' => array(
						   array('attributeName' => 'suggestedPrice', 'type' => 'CurrencyValue'),
						),
					    ),
                                            array(
						'elements' => array(
						    array('attributeName' => 'newCurrent_GPM', 'type' => 'Decimal'),
						),
					    ),
					),
					
				    ),
				array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Current_Annual_Amount', 'type' => 'CurrencyValue'),
						),
					    ),
					   array(
						'elements' => array(
						   array('attributeName' => 'ContractTerm', 'type' => 'Integer'),
						),
					    ),
					),
					
				    ),
				array('cells' =>
					array(
					    array(
						'elements' => array(
//						    array('attributeName' => 'First_Year_Amount', 'type' => 'CurrencyValue'),
						),
					    ),
					   array(
						'elements' => array(
						    array('attributeName' => 'StartDate', 'type' => 'Date'),
						),
					    ),
					)
					
				    ),
				array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'SpecialTerms', 'type' => 'TextArea'),
						),
					    ),
					)
				    ),
				array('cells' =>
					array(
					   array(
						'elements' => array(
						    array('attributeName' => 'Date_of_First_Service', 'type' => 'Date'),
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
                                                    array('attributeName' => 'budget', 'type' => 'CurrencyValue'),
						),
					    ),
					   array(
						'elements' => array(
						    array('attributeName' => 'Agreement_Expiration', 'type' => 'Date'),
						),
					    ),
					)
					
				    ),
				),
			    ),
                        
                        // Man Hour Information
			array(
                                'detailViewOnly' => true,
				'title'=> 'Man Hour Information',
				'rows' => array(
				    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Total_MHR', 'type' => 'Text'),
						),
					    ),
					    array(
						'elements' => array(
						    array('attributeName' => 'Rev_MH', 'type' => 'CurrencyValue'),
						),
					    ),
					)
					
				    ), 
                                    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Year_to_Date_MHR', 'type' => 'Text'),
						),
					    )
					)
				    ),
                                     array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'MHR_Used_Percentage', 'type' => 'Text'),
						),
					    ),
					    array(
						'elements' => array(
						    array('attributeName' => 'Total_Available_MHR', 'type' => 'Text'),
						),
					    ),
					)
				    ),
				
				),
			    ),
                        
                        // Material Information
			array(
                                'detailViewOnly' => true,
				'title'=> 'Material Information',
				'rows' => array(
				    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Total_Material', 'type' => 'Text'),
						),
					    ),
					    array(
						'elements' => array(
						    array('attributeName' => 'Material_Year_To_Date', 'type' => 'Text'),
						),
					    ),
					)
				    ),
                                    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Material_Used_Percentage', 'type' => 'Text'),
						),
					    ),
					    array(
						'elements' => array(
						    array('attributeName' => 'Available_Material', 'type' => 'Text'),
						),
					    ),
					)
				    ),
                                ),
			    ),
                        
                        // Equipment Information
			array(
                                'detailViewOnly' => true,
				'title'=> 'Equipment Information',
				'rows' => array(
				    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Total_Equipment', 'type' => 'Text'),
						),
					    ),
					    array(
						'elements' => array(
						    array('attributeName' => 'Equipment_Year_To_Date', 'type' => 'Text'),
						),
					    ),
					)
				    ),
                                    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Equipment_Used_Percentage', 'type' => 'Text'),
						),
					    ),
					    array(
						'elements' => array(
						    array('attributeName' => 'Available_Equipment', 'type' => 'Text'),
						),
					    ),
					)
				    ),
                                ),
			    ),
                        //Cumulative Information
                        array(
                                'detailViewOnly' => true,
				'title'=> 'Cumulative Information',
				'rows' => array(
				    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Cumulative_Year_to_Date_MHR', 'type' => 'Text'),
						),
					    ),
					)
				    ),
                                    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Cumulative_Year_to_Date_Material', 'type' => 'Text'),
						),
					    ),
					)
				    ),
                                    array('cells' =>
					array(
					    array(
						'elements' => array(
						    array('attributeName' => 'Cumulative_Year_to_Date_Equipment', 'type' => 'Text'),
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

        protected function getNewModelTitleLabel()
        {
            return Yii::t('Default', 'Create AgreementsModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }
        
        protected function resolveElementInformationDuringFormLayoutRender(& $elementInformation)
        {
            parent::resolveElementInformationDuringFormLayoutRender($elementInformation);
            $automaticMappingDisabled = AgreementsModule::isAutomaticProbabilityMappingDisabled();
            if ($automaticMappingDisabled === false)
            {
                if ($elementInformation['attributeName'] == 'Agreement' || $elementInformation['attributeName'] == 'RecordType')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'Estimator_Approval_Date' || $elementInformation['attributeName'] == 'Estimator_Approval')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'Contract_Number')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'Status' || $elementInformation['attributeName'] == 'Agreement_Type' )
                {
                    $elementInformation['disabled'] = true;
                }
            }
        }
    }    
    
    Yii::app()->clientScript->registerScriptFile(
        Yii::app()->getAssetManager()->publish(
            Yii::getPathOfAlias('application.modules.agreements.elements.assets')) . '/AgreementUtils.js'
    );
?>
