<?php
    /**
     * The portlet view for agreement detail view
     */
    class RoutesDetailsPortletView extends RouteEditAndDetailsView implements PortletViewInterface
    {
        protected $params;
        protected $viewData;
        protected $uniqueLayoutId;

	private static $recordType;

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
	    self::$recordType        = 'Recurring';

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
            return 'RoutesModule';
        }

        /**
         * Override to add a description for the view to be shown when adding a portlet
         */
        public static function getPortletDescription()
        {
        }
	
        public static function resolveMetadataClassNameToUse()
        {
            return 'RouteEditAndDetailsView';
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
           // $starLink = StarredUtil::getToggleStarStatusLink($this->model, null);
            return parent::getTitle(); //. $starLink;
        }

        /**
         * Module Id for the link to models from rows in the grid view.
         */
        private function resolveModuleId()
        {
            return 'routes';
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
