<?php

    /**
     * Implement the Agreements module in zurmo
     * @author Ramachandran.K(ramakavanan@gamil.com)
     */
    class AgreementsModule extends SecurableModule
    {
        const RIGHT_CREATE_AGREEMENTS = 'Create Agreement';
        const RIGHT_DELETE_AGREEMENTS = 'Delete Agreement';
        const RIGHT_ACCESS_AGREEMENTS = 'Access Agreement Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('Agreement');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                                   = LabelUtil::getTranslationParamsForAllModules();
            $labels                                   = array();
            $labels[self::RIGHT_CREATE_AGREEMENTS] = Zurmo::t('AgreementsModule', 'Create AgreementsModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_AGREEMENTS] = Zurmo::t('AgreementsModule', 'Delete AgreementsModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_AGREEMENTS] = Zurmo::t('AgreementsModule', 'Access AgreementsModulePluralLabel Tab', $params);
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
                ),
       
                'tabMenuItems' => array(
                    array(
                        'label'  => "eval:Zurmo::t('AgreementsModule', 'AgreementsModulePluralLabel', \$translationParams)",
                        'url'    => array('/agreements/default'),
                        'right'  => self::RIGHT_ACCESS_AGREEMENTS,
                        'mobile' => true,
                    ),
                ),
                'shortcutsCreateMenuItems' => array(
                    array(
                        'label'  => "eval:Zurmo::t('AgreementsModule', 'AgreementsModuleSingularLabel', \$translationParams)",
                        'url'    => array('/agreements/default/create'),
                        'right'  => self::RIGHT_CREATE_AGREEMENTS,
                        'mobile' => true,
                    ),
                ),
            );
            return $metadata;
        }

        public static function getPrimaryModelName()
        {
            return 'Agreement';
        }

        public static function getSingularCamelCasedName()
        {
            return 'Agreement';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('AgreementsModule', 'Agreement', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('AgreementsModule', 'Agreements', array(), null, $language);
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_AGREEMENTS;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_AGREEMENTS;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_AGREEMENTS;
        }

        public static function getDefaultDataMakerClassName()
        {
            return 'AgreementsDefaultDataMaker';
        }

        public static function getDemoDataMakerClassNames()
        {
            return array('AgreementsDemoDataMaker');
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'AgreementsSearchForm';
        }

        public static function hasPermissions()
        {
            return true;
        }

        public static function isReportable()
        {
            return true;
        }

        public static function canHaveWorkflow()
        {
            return true;
        }

        public static function canHaveContentTemplates()
        {
            return true;
        }

       /** public static function getStageToProbabilityMappingData()
        {
            $metadata = static::getMetadata();
            if (isset($metadata['global']['stageToProbabilityMapping']))
            {
                return $metadata['global']['stageToProbabilityMapping'];
            }
            return array();
        }

        /**
         * @param string $value
         * @return int
         */
       /** public static function getProbabilityByStageValue($value)
        {
            assert('is_string($value) || $value == null');
            $stageToProbabilityMapping = self::getStageToProbabilityMappingData();
            if (isset($stageToProbabilityMapping[$value]))
            {
                return $stageToProbabilityMapping[$value];
            }
            return 0;
        }
*/
        
        public static function isAutomaticProbabilityMappingDisabled()
        {
            $metadata = static::getMetadata();
            if (isset($metadata['global']['automaticProbabilityMappingDisabled']))
            {
                return (bool) $metadata['global']['automaticProbabilityMappingDisabled'];
            }
            return false;
        }
    }
?>
