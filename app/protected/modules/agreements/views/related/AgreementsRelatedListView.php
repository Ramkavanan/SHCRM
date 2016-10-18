<?php

    abstract class AgreementsRelatedListView extends SecuredRelatedListView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'perUser' => array(
                    'title' => "eval:Zurmo::t('AgreementsModule', 'AgreementsModulePluralLabel', LabelUtil::getTranslationParamsForAllModules())",
                ),
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array(  'type'            => 'CreateFromRelatedListLink',
                                    'routeModuleId'   => 'eval:$this->moduleId',
                                    'routeParameters' => 'eval:$this->getCreateLinkRouteParameters()'),
                        ),
                    ),
                    'rowMenu' => array(
                        'elements' => array(
                            array('type'                      => 'EditLink'),
                            array('type'                      => 'RelatedDeleteLink'),
                            array('type'                      => 'RelatedUnlink',
                                  'relationModelClassName'    => 'eval:get_class($this->params["relationModel"])',
                                  'relationModelId'           => 'eval:$this->params["relationModel"]->id',
                                  'relationModelRelationName' => 'agreements',
                                  'userHasRelatedModelAccess' => 'eval:ActionSecurityUtil::canCurrentUserPerformAction( "Edit", $this->params["relationModel"])'),
                        ),
                    ),
                    'gridViewType' => RelatedListView::GRID_VIEW_TYPE_NORMAL,
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Status', 'type' => 'DropDown'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'RecordType', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Current_Annual_Amount', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'Project_Agreement_Amount', 'type' => 'CurrencyValue'),
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
            return 'AgreementsModule';
        }
    }
?>
