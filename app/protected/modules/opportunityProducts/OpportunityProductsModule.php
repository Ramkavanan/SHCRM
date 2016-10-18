<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class OpportunityProductsModule extends SecurableModule
    {
        const RIGHT_CREATE_OpportunityProducts = 'Create OpportunityProducts';
        const RIGHT_DELETE_OpportunityProducts = 'Delete OpportunityProducts';
        const RIGHT_ACCESS_OpportunityProducts = 'Access OpportunityProducts Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('OpportunityProduct');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                              = LabelUtil::getTranslationParamsForAllModules();
            $labels                              = array();
            $labels[self::RIGHT_CREATE_OpportunityProducts] = Zurmo::t('OpportunityProductsModule', 'Create OpportunityProductsModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_OpportunityProducts] = Zurmo::t('OpportunityProductsModule', 'Delete OpportunityProductsModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_OpportunityProducts] = Zurmo::t('OpportunityProductsModule', 'Access OpportunityProductsModulePluralLabel Tab', $params);
            return $labels;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array();
            $metadata['global'] = array(
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
            return 'OpportunityProduct';
        }

        public static function getSingularCamelCasedName()
        {
            return 'OpportunityProduct';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_OpportunityProducts;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_OpportunityProducts;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_OpportunityProducts;
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'OpportunityProductsSearchForm';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('OpportunityProductsModule', 'Opportunity Product', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('OpportunityProductsModule', 'Opportunity Products', array(), null, $language);
        }
        
        public static function modelsAreNeverGloballySearched()
        {
            return true;
        }
    }
?>