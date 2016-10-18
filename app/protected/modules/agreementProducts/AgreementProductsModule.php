<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AgreementProductsModule extends SecurableModule
    {
        const RIGHT_CREATE_AgreementProducts = 'Create AgreementProducts';
        const RIGHT_DELETE_AgreementProducts = 'Delete AgreementProducts';
        const RIGHT_ACCESS_AgreementProducts = 'Access AgreementProducts Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('AgreementProduct');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                              = LabelUtil::getTranslationParamsForAllModules();
            $labels                              = array();
            $labels[self::RIGHT_CREATE_AgreementProducts] = Zurmo::t('AgreementProductsModule', 'Create AgreementProductsModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_AgreementProducts] = Zurmo::t('AgreementProductsModule', 'Delete AgreementProductsModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_AgreementProducts] = Zurmo::t('AgreementProductsModule', 'Access AgreementProductsModulePluralLabel Tab', $params);
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
            return 'AgreementProduct';
        }

        public static function getSingularCamelCasedName()
        {
            return 'AgreementProduct';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_AgreementProducts;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_AgreementProducts;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_AgreementProducts;
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'AgreementProductsSearchForm';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('AgreementProductsModule', 'Agreement Product', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('AgreementProductsModule', 'Agreement Products', array(), null, $language);
        }
        
        public static function modelsAreNeverGloballySearched()
        {
            return true;
        }
    }
?>
