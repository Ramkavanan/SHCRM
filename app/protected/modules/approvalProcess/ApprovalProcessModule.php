<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApprovalProcessModule extends SecurableModule
    {
        const RIGHT_CREATE_APPROVALPROCESS = 'Create ApprovalProcess';
        const RIGHT_DELETE_APPROVALPROCESS = 'Delete ApprovalProcess';
        const RIGHT_ACCESS_APPROVALPROCESS = 'Access ApprovalProcess Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('ApprovalProcess');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                              = LabelUtil::getTranslationParamsForAllModules();
            $labels                              = array();
            $labels[self::RIGHT_CREATE_APPROVALPROCESS] = Zurmo::t('ApprovalProcessModule', 'Create ApprovalProcessModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_APPROVALPROCESS] = Zurmo::t('ApprovalProcessModule', 'Delete ApprovalProcessModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_APPROVALPROCESS] = Zurmo::t('ApprovalProcessModule', 'Access ApprovalProcessModulePluralLabel Tab', $params);
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
            return 'ApprovalProcess';
        }

        public static function getSingularCamelCasedName()
        {
            return 'ApprovalProcess';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_APPROVALPROCESS;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_APPROVALPROCESS;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_APPROVALPROCESS;
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'ApprovalProcessSearchForm';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('ApprovalProcessModule', 'ApprovalProcess', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('ApprovalProcessModule', 'ApprovalProcess', array(), null, $language);
        }
        
        public static function canHaveWorkflow()
        {
            return true;
        }
        
        public static function canHaveContentTemplates()
        {
            return true;
        }
    }
?>
