<?php
    /**
     * The portlet view for costbooks detail view
     */
    class CostbookDetailsPortletView extends CostbookEditAndDetailsView implements PortletViewInterface
    {
        protected $params;
        protected $viewData;
        protected $uniqueLayoutId;

        /**
         * @param array $viewData
         * @param array $params
         * @param string $uniqueLayoutId
         */
        public function __construct($viewData, $params, $uniqueLayoutId)
        {
            assert('isset($params["controllerId"])');
            assert('isset($params["relationModuleId"])');
            assert('$params["relationModel"] instanceof RedBeanModel || $params["relationModel"] instanceof ModelForm');
            assert('isset($params["portletId"])');
            assert('isset($params["redirectUrl"])');
            $this->modelClassName    = $this->getModelClassName();
            $this->viewData          = $viewData;
            $this->params            = $params;
            $this->uniqueLayoutId    = $uniqueLayoutId;
            $this->gridIdSuffix      = $uniqueLayoutId;
            $this->rowsAreSelectable = false;
            $this->gridId            = 'list-view';
            $this->controllerId      = $this->resolveControllerId();
            $this->moduleId          = $this->resolveModuleId();
            parent::__construct('Details', $this->controllerId, $this->moduleId, $params["relationModel"]);
        }

        public function getPortletParams()
        {
            return array();
        }

        public static function getPortletRulesType()
        {
            return 'Detail';
        }

        public static function getModuleClassName()
        {
            return 'CostbooksModule';
        }

        protected static function resolveMetadataClassNameToUse()
        {
            return 'CostbookEditAndDetailsView';
        }

        /**
         * Controller Id for the link to models from rows in the grid view.
         */
        private function resolveControllerId()
        {
            return 'default';
        }

        /**
         * Override to add a starring link to the title
         * @return string
         */
        public function getTitle()
        {
            $starLink = StarredUtil::getToggleStarStatusLink($this->model, null);
            return parent::getTitle() . $starLink;
        }

        /**
         * Module Id for the link to models from rows in the grid view.
         */
        private function resolveModuleId()
        {
            return 'costbooks';
        }

        public static function canUserConfigure()
        {
            return false;
        }

        protected function renderTitleContent()
        {
            return null;
        }

        public static function canUserRemove()
        {
            return false;
        }

        public static function getDesignerRulesType()
        {
            return 'DetailsPortletView';
        }

        /**
         * Override to add a description for the view to be shown when adding a portlet
         */
        public static function getPortletDescription()
        {
        }

        /**
         * Override and return null so we can render the actionElementMenu in the portletHeaderContent
         * @return null
         */
        protected function resolveAndRenderActionElementMenu()
        {
            return null;
        }

        /**
         * @return null|string
         */
        public function renderPortletHeadContent()
        {
            return $this->renderWrapperAndActionElementMenu();
        }
    }
?>
