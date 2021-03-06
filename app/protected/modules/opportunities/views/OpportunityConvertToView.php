<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2015 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2015. All rights reserved".
     ********************************************************************************/

    class OpportunityConvertToView extends EditView
    {
        protected $wrapContentInWrapperDiv = false;

        /**
         * Override to pass in the relation Id as the modelId. In the case of lead conversion, the lead->id is the
         * $modelId. This can then be used for a cancel button to return to the lead detailview.
         * @param string $controllerId
         * @param string $moduleId
         * @param RedsBeanModel $model
         * @param integer $modelId
         */
        public function __construct($controllerId, $moduleId, $model, $modelId)
        {
            assert('is_int($modelId)');
            parent::__construct($controllerId, $moduleId, $model);
            $this->modelId = $modelId;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type'  => 'BackConvertLink'),
                            array('type'  => 'SaveButton', 'label' => "eval:Zurmo::t('ZurmoModule', 'Complete Conversion')"),
                            array('type'  => 'CancelConvertLink'),
                        ),
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'recordType', 'type' => 'DropDown'),
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
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'closeDate', 'type' => 'Date'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'stage', 'type' => 'DropDown', 'addBlank' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'probability', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                                //--------------
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'expectedStartDate', 'type' => 'Date'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
//                                                array('attributeName' => 'aggregateGPM', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'estimatorApproval', 'type' => 'CheckBox'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'estimator', 'type' => 'User'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'gm', 'type' => 'User'),
                                            ),
                                        ),
                                    )
                                ),
                                //--------------
                            ),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

        public static function getDesignerRulesType()
        {
            return 'OpportunityConvertToView';
        }

        /**
         * Override to remove unused float-bar div
         * @param string $content
         * @return string
         */
        protected function resolveAndWrapDockableViewToolbarContent($content)
        {
            assert('is_string($content)');
            if ($this->disableFloatOnToolbar)
            {
                $disableFloatContent = ' disable-float-bar';
            }
            else
            {
                $disableFloatContent = null;
            }
            $content = ZurmoHtml::tag('div', array('class' => 'form-toolbar'), $content);
            $content = ZurmoHtml::tag('div', array('class' => 'view-toolbar-container clearfix dock' . $disableFloatContent), $content);
            return $content;
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

    Yii::app()->clientScript->registerScript('stageValuesToDisabled', "
                    var optiontodisable = ['Final Pricing','Final Proposal','Agreement','Won','Lost'];
                    $('#Opportunity_stage_value option').each(function(){ if($.inArray(this.value,optiontodisable) != -1){ $(this).attr('disabled','disabled')} })
                 ");// Not Coding Standard
?>