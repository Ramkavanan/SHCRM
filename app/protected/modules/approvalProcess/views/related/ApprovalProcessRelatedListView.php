<?php

    abstract class ApprovalProcessRelatedListView extends SecuredRelatedListView
    {
        /**
         * @return array
         */
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'perUser' => array(
                    'title' => "Approval Process",
                ),
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array(  'type'             => 'CreateFromRelatedModalLink',
                                    'portletId'        => 'eval:$this->params["portletId"]',
                                    'routeModuleId'    => 'eval:$this->moduleId',
                                    'routeParameters'  => 'eval:$this->getCreateLinkRouteParameters()',
                                    'ajaxOptions'      => 'eval:TasksUtil::resolveAjaxOptionsForModalView("CreateTest")',
                                    'uniqueLayoutId'   => 'eval:$this->uniqueLayoutId',
                                    'modalContainerId' => 'eval:TasksUtil::getModalContainerId()'
                                 ),
                        ),
                    ),
                    'rowMenu' => array(
                        'elements' => array(
                            array(  'type'             => 'EditModalLink',
                                    'htmlOptions'      => 'eval:$this->getActionModalLinksHtmlOptions("Edit")'
                                 ),
                            array(  'type'             => 'CopyModalLink',
                                    'htmlOptions'      => 'eval:$this->getActionModalLinksHtmlOptions("Copy")'
                                 ),
                            array('type' => 'TaskRelatedDeleteLink'),
                        ),
                    ),
                    'derivedAttributeTypes' => array(
                        'CloseTaskCheckBox',
                    ),
                    'nonPlaceableAttributeNames' => array(
                        'latestDateTime',
                    ),
                    'gridViewType' => RelatedListView::GRID_VIEW_TYPE_STACKED,
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'opportunity', 'type' => 'Opportunity', 'isLink' => true),
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

        /**
         * @return array
         */
        protected function makeSearchAttributeData()
        {
            $searchAttributeData = array();
            $searchAttributeData['clauses'] = array(
                1 => array(
                    'attributeName'        => 'opportunity',
                    'relatedAttributeName' => 'id',
                    'operatorType'         => 'equals',
                    'value'                => (int)$this->params['relationModel']->getClassId('Opportunity'),
                ),
                2 => array(
                    'attributeName'        => 'Status',
                    'operatorType'         => 'equals',
                    'value'                => ApprovalProcess::PENDING
                )
            );
            $searchAttributeData['structure'] = '(1 or 2)';
            return $searchAttributeData;
        }

        /**
         * @return string
         */
        public static function getModuleClassName()
        {
            return 'ApprovalProcessModule';
        }

        /**
         * Override to handle security/access resolution on links.
         */
        public function getLinkString($attributeString, $attribute)
        {
            return array($this, 'resolveLinkString');
        }

        /**
         * Resolves the link string for task detail modal view
         * @param array $data
         * @param int $row
         * @return string
         */
        public function resolveLinkString($data, $row)
        {
            $content = TasksUtil::getModalDetailsLink($data, $this->controllerId,
                                                      $this->moduleId,
                                                      $this->getActionModuleClassName(), false);
            return $content;
        }

        /**
         * Override to pass the sourceId
         * @return type
         */
        protected function getCreateLinkRouteParameters()
        {
            $routeParams = array_merge( array('sourceId' => $this->getGridViewId()),
                                        parent::getCreateLinkRouteParameters());
            if (($redirectUrl = ArrayUtil::getArrayValue($routeParams, 'redirectUrl')) != null)
            {
                $routeParams['redirectUrl'] = TasksUtil::resolveOpenTasksActionsRedirectUrlForDetailsAndRelationsView($redirectUrl);
            }
            return $routeParams;
        }

        /**
         * Register the additional script for task detail modal
         */
        protected function renderScripts()
        {
            parent::renderScripts();
            Yii::app()->custom->registerTaskModalDetailsScript($this->getGridViewId());
            TasksUtil::registerTaskModalEditScript($this->getGridViewId(), $this->getCreateLinkRouteParameters());
            TasksUtil::registerTaskModalCopyScript($this->getGridViewId(), $this->getCreateLinkRouteParameters());
            TasksUtil::registerTaskModalDeleteScript($this->getGridViewId());
        }

        /**
         * Get action modal link html options based on type
         * @param string $type
         * @return array
         */
        protected function getActionModalLinksHtmlOptions($type)
        {
            if ($type == "Edit")
            {
                return array('class' => 'edit-related-open-task');
            }
            elseif ($type == "Copy")
            {
                return array('class' => 'copy-related-open-task');
            }
        }

        /**
         * Resolve row menu column class.
         * @return string
         */
        protected function resolveRowMenuColumnClass()
        {
            return Yii::app()->custom->resolveRowMenuColumnClassForOpenTaskPortlet($this->getRelationAttributeName());
        }

        /**
         * Gets sort attribute for data provider.
         * @return string
         */
        protected function getSortAttributeForDataProvider()
        {
            return 'id';
        }

        /**
         * @return string
         */
        public function renderPortletHeadContent()
        {
            $parentContent          = parent::renderPortletHeadContent();
            $defaultOptionsContent  = $this->renderWrapperAndActionElementMenu(Zurmo::t('Core', 'Options'));
            $wrappedContent         = Yii::app()->custom->renderHeadContentForPortletOnDetailsAndRelationsView(get_class($this),
                                                                                                                      $this->params,
                                                                                                                      $defaultOptionsContent,
                                                                                                                      $parentContent);
            return $wrappedContent;
        }
    }
?>
