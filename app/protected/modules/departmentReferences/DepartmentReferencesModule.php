<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class DepartmentReferencesModule extends SecurableModule
    {
        const RIGHT_CREATE_DEPARTMENTREFERENCES = 'Create DepartmentReferences';
        const RIGHT_DELETE_DEPARTMENTREFERENCES = 'Delete DepartmentReferences';
        const RIGHT_ACCESS_DEPARTMENTREFERENCES = 'Access DepartmentReferences Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('DepartmentReference');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                              = LabelUtil::getTranslationParamsForAllModules();
            $labels                              = array();
            $labels[self::RIGHT_CREATE_DEPARTMENTREFERENCES] = Zurmo::t('DepartmentReferencesModule', 'Create DepartmentReferencesModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_DEPARTMENTREFERENCES] = Zurmo::t('DepartmentReferencesModule', 'Delete DepartmentReferencesModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_DEPARTMENTREFERENCES] = Zurmo::t('DepartmentReferencesModule', 'Access DepartmentReferencesModulePluralLabel Tab', $params);
            return $labels;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array();
            $userIdArr = array();
            $isUsersInAcountMangerGroup = FALSE;
            $AccountManagerGroup = Group::getByName(User::ACCOUNTMANAGER);  //Access Account manager group
            if(!empty($AccountManagerGroup)){
                foreach($AccountManagerGroup->users as $AccountManagerUserId)
                {
                    $userIdArr[] = $AccountManagerUserId->id;
                }
            }
            if(in_array(Yii::app()->user->userModel->id, $userIdArr)){
               $isUsersInAcountMangerGroup = TRUE;
            }
            if(empty(Yii::app()->user->userModel->isRootUser) && $isUsersInAcountMangerGroup == FALSE){
                $metadata['global'] = array(    //Meta data for normal user
                    'globalSearchAttributeNames' => array()
                );
            }else{
                $metadata['global'] = array(    //Meta data for super user And Account Manager(Group).
                    'tabMenuItems' => array(
                        array(
                            'label'  => "Department Reference",
                            'url'    => array('/departmentReferences/default'),
                            'right'  => self::RIGHT_ACCESS_DEPARTMENTREFERENCES,
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
            }
            return $metadata;
        }

        public static function getPrimaryModelName()
        {
            return 'DepartmentReference';
        }

        public static function getSingularCamelCasedName()
        {
            return 'DepartmentReference';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_DEPARTMENTREFERENCES;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_DEPARTMENTREFERENCES;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_DEPARTMENTREFERENCES;
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'DepartmentReferencesSearchForm';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('DepartmentReferencesModule', 'DepartmentReference', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('DepartmentReferencesModule', 'DepartmentReference', array(), null, $language);
        }
    }
?>