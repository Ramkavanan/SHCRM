<?php

/**
 * Render the create or edit agreeement for project type view.
 * 
 * @author Ramachandran.K (ramakavanan@gmail.com)
 */
 class OpportunityProjectEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'SaveButton', 'renderType' => 'Edit'),
                            array('type' => 'Cancel', 'renderType' => 'Edit','isOpt' => TRUE),
                            array('type' => 'ListLink',
                                'renderType' => 'Details',
                                'label' => "eval:Yii::t('Default', 'Return to List')"
                            ),
                            array('type' => 'EditLink', 'renderType' => 'Details'),
                            array('type' => 'OpportunityDeleteLink', 'renderType' => 'Details'),
                            array('type' => 'AuditEventsModalListLink', 'renderType' => 'Details'),
				array('type' => 'CopyLink',       'renderType' => 'Details'),
                            array(
                                    'type' => 'PrintViewLink',
                                    'renderType' => 'Details',
                                    'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=850,height=600,scrollbars=yes"); return false;')
                                ),
                        ),
                    ),
                    'derivedAttributeTypes' => array(
                        'DateTimeCreatedUser',
                        'DateTimeModifiedUser',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'title'=> 'Opportunity Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'detailViewOnly' => false,
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text'),
                                                
                                            ),
                                        ),
                                        array(
                                            'detailViewOnly' => false,
                                            'elements' => array(
                                               array('attributeName' => 'recordType', 'type' => 'DropDown'),
                                            ),
                                        ),
                                     ),
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
                                                 array('attributeName' => 'stage', 'type' => 'DropDown', 'addBlank' => true),                   
                                            ),
                                        ),
                                    ),
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'detailViewOnly' => true,
                                            'elements' => array(
                                                array('attributeName' => 'agreement', 'type' => 'Agreement'),
                                                
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'Opportunity', 'type' => 'Opportunity'),

                                            ),
                                        ),
                                    ),
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'closeDate', 'type' => 'Date'),
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'reasonLost', 'type' => 'DropDown', 'addBlank' => true),
                                            ),
                                        ),
                                    ),
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'opportunityTypes', 'type' => 'DropDown', 'addBlank' => true),
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'probability', 'type' => 'Integer'),
                                            ),
                                        ),
                                    ),
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'goals', 'type' => 'TextArea'),
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'expectedStartDate', 'type' => 'Date'),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        array(
                            'title'=> 'Description Information',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'description', 'type' => 'TextArea'),
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
                        array(
                            'title'=> 'Opportunity Totals and Gross Profit Margins',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'budget', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'finalAmount', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    ),
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'estimator', 'type' => 'User'),
                                            ),
                                        ),
                                        array(
                                            'detailViewOnly' => true,
                                            'elements' => array(
                                                array('attributeName' => 'suggestedPrice', 'type' => 'CurrencyValue'),
                                            ),
                                        ),                                       
                                    ),
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'gm', 'type' => 'User'),
                                            ),
                                        ),                                       
                                         array(
                                            'detailViewOnly' => true,
                                            'elements' => array(
                                                array('attributeName' => 'aggregateGPM', 'type' => 'Decimal'),
                                            ),
                                        ),                                       
                                    ),
                                ),
                               array('cells' =>
                                    array(                                        
                                        array(
                                            'detailViewOnly' => true,
                                            'elements' => array(
                                                array('attributeName' => 'revenueMHR', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                        array(
                                            'detailViewOnly' => true,
                                            'elements' => array(
                                                array('attributeName' => 'totalDirectCosts', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    ),
                                ),
                               array('cells' =>
                                    array(                                        
                                        array(
                                            'detailViewOnly' => true,
                                            'elements' => array(
                                                array('attributeName' => 'totalMHR', 'type' => 'Text'),
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
                                            'detailViewOnly' => true,
                                            'elements' => array(
//                                                array('attributeName' => 'expectedRevenue', 'type' => 'CurrencyValue'),
                                           ),
                                        ),
                                    ),
                                ),
                           ),
                        ),
                        
                        array(
                            'title'=> 'Estimator Summary',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'estimatorApproval', 'type' => 'CheckBox'),
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'estimatorApprovalDate', 'type' => 'Date'),
                                            ),
                                        ),
                                    ),
                                ),
                           ),
                        ),
                        array(
                            'title'=> 'Manager Approval',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'managementPricingApproval', 'type' => 'CheckBox'),
                                            ),
                                        ),
                                       array(
                                            'elements' => array(
                                                array('attributeName' => 'managementPricingApprovalDate', 'type' => 'Date'),
                                            ),
                                        ),
                                    ),
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'createAgreement', 'type' => 'CheckBox'),
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
                        
                        
                    ),
                ),
            );
            return $metadata;
        }

        protected function getNewModelTitleLabel()
        {
            return 'Create Project Opportunity ';
        }
		
		/**
         * Override to disabling probability attribute.
         */
        protected function resolveElementInformationDuringFormLayoutRender(& $elementInformation)
        {
            parent::resolveElementInformationDuringFormLayoutRender($elementInformation);
            $automaticMappingDisabled = OpportunitiesModule::isAutomaticProbabilityMappingDisabled();
            if ($automaticMappingDisabled === false)
            {
                if ($elementInformation['attributeName'] == 'probability')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'managementPricingApproval')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'createAgreement')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'managementPricingApprovalDate')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'estimatorApprovalDate')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'Opportunity')
                {
                    $elementInformation['disabled'] = true;
                }
                if ($elementInformation['attributeName'] == 'recordType' && (Yii::app()->controller->action->id == 'copy' || Yii::app()->session->get('IsRecordTypeEditable') === FALSE))
                {
                    $elementInformation['disabled'] = true;
                }
            }
        }

        protected function renderAfterFormLayout($form)
        {
            parent::renderAfterFormLayout($form);
            $automaticMappingDisabled = OpportunitiesModule::isAutomaticProbabilityMappingDisabled();
            if ($automaticMappingDisabled === false)
            {
                $this->registerStageToProbabilityMappingScript($form);
            }
            if(Yii::app()->controller->action->id == 'copy' || Yii::app()->controller->action->id == 'recurringType' || Yii::app()->controller->action->id == 'projectType'){
                Yii::app()->clientScript->registerScript('stageValuesToDisabled', "
                    var optiontodisable = ['Final Pricing','Final Proposal','Agreement','Won','Lost'];
                    $('#Opportunity_stage_value option').each(function(){ if($.inArray(this.value,optiontodisable) != -1){ $(this).attr('disabled','disabled')} })
                 ");// Not Coding Standard
            }else if(Yii::app()->controller->action->id == 'edit'){
                Yii::app()->clientScript->registerScript('stageValuesToDisabled', "
                    var optiontodisable = ['Final Pricing','Final Proposal','Agreement','Won','Lost'];
                    $('#Opportunity_stage_value option').each(function(){ if($.inArray(this.value,optiontodisable) != -1){ $(this).attr('disabled','disabled')} })
                 ");// Not Coding Standard
            }
        }

        protected function registerStageToProbabilityMappingScript($form)
        {
            $stageInputId       = Element::resolveInputIdPrefixIntoString(array(get_class($this->model), 'stage', 'value'));
            $probabilityInputId = Element::resolveInputIdPrefixIntoString(array(get_class($this->model), 'probability'));
            $mappingData        = OpportunitiesModule::getStageToProbabilityMappingData();
            if (count($mappingData) > 0)
            {
                $jsonEncodedMapping = CJSON::encode($mappingData);
                // In case of edit, we need the exact value from db
                if ($this->model->id > 0)
                {
                    $initialCallToFunction = "";
                }
                else
                {
                    $initialCallToFunction = " stageToProbabilityMapping($('#" . $stageInputId . "'));";
                }
                Yii::app()->clientScript->registerScript('stageToProbabilityMapping', "
                $('#" . $stageInputId . "').unbind('change.probabilityMapping');
                $('#" . $stageInputId . "').bind('change.probabilityMapping', function()
                    {
                        stageToProbabilityMapping($(this));
                    }
                );
                function stageToProbabilityMapping(stageInput)
                {
                    var value  = stageInput.val();
                    var result = $.parseJSON('" . $jsonEncodedMapping . "');
                    $('#" . $probabilityInputId . "').val(0);
                    $.each(result, function(stage, probability)
                    {
                        if (value == stage)
                        {
                            $('#" . $probabilityInputId . "').val(probability);
                            return false;
                        }
                    });
                 }
                 ".$initialCallToFunction // Not Coding Standard
                 );
            }
        }
        
    }
        Yii::app()->clientScript->registerScriptFile(
        Yii::app()->getAssetManager()->publish(
            Yii::getPathOfAlias('application.modules.opportunities.elements.assets')) . '/OpportunityValidation.js'
        );
?>

