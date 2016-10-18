<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class UnitofmeasuresModule extends SecurableModule
    {
        const RIGHT_CREATE_UNITOFMEASURES = 'Create Unitofmeasures';
        const RIGHT_DELETE_UNITOFMEASURES = 'Delete Unitofmeasures';
        const RIGHT_ACCESS_UNITOFMEASURES = 'Access Unitofmeasures Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('Unitofmeasure');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                              = LabelUtil::getTranslationParamsForAllModules();
            $labels                              = array();
            $labels[self::RIGHT_CREATE_UNITOFMEASURES] = Zurmo::t('UnitofmeasuresModule', 'Create UnitofmeasuresModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_UNITOFMEASURES] = Zurmo::t('UnitofmeasuresModule', 'Delete UnitofmeasuresModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_UNITOFMEASURES] = Zurmo::t('UnitofmeasuresModule', 'Access UnitofmeasuresModulePluralLabel Tab', $params);
            return $labels;
        }

        public static function getDefaultMetadata()
        {
            $isUsersInAcountMangerGroup = CostbookUtils::GetIsAccountManagerGroup();
            $isUsersInCtalogManagerGroup = CostbookUtils::GetIsCatalogManagerGroup();
            if(empty(Yii::app()->user->userModel->isRootUser) && $isUsersInAcountMangerGroup == FALSE && $isUsersInCtalogManagerGroup == FALSE){
                $headerMenuItems = array();
            }else{
                $headerMenuItems = array(
                                        array(
                                            'label'  => "eval:Zurmo::t('UnitofmeasuresModule', 'Unit Of Measures')",
                                            'url'    => array('/unitofmeasures/default'),
                                            'right'  => self::RIGHT_ACCESS_UNITOFMEASURES,
                                            'order'  => 7,
                                            'mobile' => false,
                                        ),
                                    );
            }
            $metadata = array();
            $metadata['global'] = array(
                'adminTabMenuItems' => array(
                    array(
                        'label'  => "eval:Zurmo::t('UnitofmeasuresModule', 'UnitofmeasuresModulePluralLabel', \$translationParams)",
                        'url'    => array('/unitofmeasures/default'),
                        'right'  => self::RIGHT_ACCESS_UNITOFMEASURES,
                        'mobile' => true,
                    ),
                ),
                'designerMenuItems' => array(
                    'showFieldsLink' => true,
                    'showGeneralLink' => true,
                    'showLayoutsLink' => true,
                    'showMenusLink' => true,
                ),
                'headerMenuItems' => $headerMenuItems,
                'globalSearchAttributeNames' => array(
                    'name'
                )
            );
            return $metadata;
        }

        public static function getPrimaryModelName()
        {
            return 'Unitofmeasure';
        }

        public static function getSingularCamelCasedName()
        {
            return 'Unitofmeasure';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_UNITOFMEASURES;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_UNITOFMEASURES;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_UNITOFMEASURES;
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'UnitofmeasuresSearchForm';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('UnitofmeasuresModule', 'Unitofmeasure', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('UnitofmeasuresModule', 'Unitofmeasure', array(), null, $language);
        }
        public static function isAutomaticCodeDisabled()
        {
            $metadata = static::getMetadata();
            if (isset($metadata['global']['automaticDisable']))
            {
                return (bool) $metadata['global']['automaticDisable'];
            }
            return false;
        }

    }
?>
