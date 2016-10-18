<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RoutesModule extends SecurableModule
    {
        const RIGHT_CREATE_ROUTES = 'Create Routes';
        const RIGHT_DELETE_ROUTES = 'Delete Routes';
        const RIGHT_ACCESS_ROUTES = 'Access Routes Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('Route');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                              = LabelUtil::getTranslationParamsForAllModules();
            $labels                              = array();
            $labels[self::RIGHT_CREATE_ROUTES] = Zurmo::t('RoutesModule', 'Create RoutesModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_ROUTES] = Zurmo::t('RoutesModule', 'Delete RoutesModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_ROUTES] = Zurmo::t('RoutesModule', 'Access RoutesModulePluralLabel Tab', $params);
            return $labels;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array();
            $metadata['global'] = array(
                'tabMenuItems' => array(
                    array(
                        'label'  => "Routes",
                        'url'    => array('/routes/default'),
                        'right'  => self::RIGHT_ACCESS_ROUTES,
                    ),
                ),
                'designerMenuItems' => array(
                    'showFieldsLink' => true,
                    'showGeneralLink' => true,
                    'showLayoutsLink' => true,
                    'showMenusLink' => true,
                ),
                'globalSearchAttributeNames' => array(
                    'name'
                )
            );
            return $metadata;
        }

        public static function getPrimaryModelName()
        {
            return 'Route';
        }

        public static function getSingularCamelCasedName()
        {
            return 'Route';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_ROUTES;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_ROUTES;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_ROUTES;
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'RoutesSearchForm';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('RoutesModule', 'Route', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('RoutesModule', 'Routes', array(), null, $language);
        }
    }
?>
