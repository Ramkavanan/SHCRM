<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApprovalProcessMyListView
 *
 * @author ramachandran
 */
class ApprovalProcessMyListView extends SecuredMyListView{
    public static function getDefaultMetadata()
        {
            $metadata = array(
                'perUser' => array(
                    'title' => "eval:Zurmo::t('ApprovalProcessModule', 'My ApprovalProcessModulePluralLabel', LabelUtil::getTranslationParamsForAllModules())",
                    'searchAttributes' => array(),
                ),
                'global' => array(
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => '', 'type' => 'AcceptLink', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'comments', 'type' => 'TextArea'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'opportunity', 'type' => 'Opportunity', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'agreement', 'type' => 'Agreement', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'assignedto', 'type' => 'User', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'createdDateTime', 'type' => 'dateTime'),
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

        public static function getModuleClassName()
        {
            return 'ApprovalProcessModule';
        }

        public static function getDisplayDescription()
        {
            return Zurmo::t('ApprovalProcessModule', 'My ApprovalProcessModulePluralLabel', LabelUtil::getTranslationParamsForAllModules());
        }

        protected function getSearchModel()
        {
            $modelClassName = $this->modelClassName;
            return new ApprovalProcessSearchForm(new ApprovalProcess(false));
        }

        protected static function getConfigViewClassName()
        {
            return 'ApprovalProcessMyListConfigView';
        }
        
        /**
         * @return array
         */
        protected function makeSearchAttributeData()
        {
            $searchAttributeData = array();
            $searchAttributeData['clauses'] = array(
                1 => array(
                    'attributeName'        => 'actualapprover',
                    'relatedAttributeName' => 'id',
                    'operatorType'         => 'equals',
                    'value'                => Yii::app()->user->userModel->id,
                ),
                2 => array(
                    'attributeName'        => 'Status',
                    'relatedAttributeName' => 'value',
                    'operatorType'         => 'equals',
                    'value'                => ApprovalProcess::PENDING,
                )
            );
            $searchAttributeData['structure'] = '(1 AND 2)';
            return $searchAttributeData;
        }
        
        protected function resolveSortDescendingForDataProvider()
        {
            return true;
        }
        
 }

?>
