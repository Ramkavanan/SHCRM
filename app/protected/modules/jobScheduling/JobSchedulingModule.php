<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JobSchedulingModule extends SecurableModule
    {
        const RIGHT_CREATE_JOBSCHEDULING = 'Create JobScheduling';
        const RIGHT_DELETE_JOBSCHEDULING = 'Delete JobScheduling';
        const RIGHT_ACCESS_JOBSCHEDULING = 'Access JobScheduling Tab';

        public function getDependencies()
        {
            return array(
                'configuration',
                'zurmo',
            );
        }

        public function getRootModelNames()
        {
            return array('JobScheduling');
        }

        public static function getTranslatedRightsLabels()
        {
            $params                              = LabelUtil::getTranslationParamsForAllModules();
            $labels                              = array();
            $labels[self::RIGHT_CREATE_JOBSCHEDULING] = Zurmo::t('JobSchedulingModule', 'Create JobSchedulingModulePluralLabel',     $params);
            $labels[self::RIGHT_DELETE_JOBSCHEDULING] = Zurmo::t('JobSchedulingModule', 'Delete JobSchedulingModulePluralLabel',     $params);
            $labels[self::RIGHT_ACCESS_JOBSCHEDULING] = Zurmo::t('JobSchedulingModule', 'Access JobSchedulingModulePluralLabel Tab', $params);
            return $labels;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array();
            $userIdArr = array();
            $AccountManagerGroup = Group::getByName(User::ACCOUNTMANAGER);
            foreach($AccountManagerGroup->users as $AccountManagerUserId)
            {
                $userIdArr[] = $AccountManagerUserId->id;
            }
            $isInAccountManageGroup = FALSE;
            if(in_array(Yii::app()->user->userModel->id, $userIdArr)){
                $isInAccountManageGroup = TRUE;
            }
            if(empty(Yii::app()->user->userModel->isRootUser) && $isInAccountManageGroup == FALSE && Yii::app()->user->userModel->role->name !== Constant::GM){
                $metadata['global'] = array(    //Meta data for normal user
                'globalSearchAttributeNames' => array()
            );  
            }else{
                $metadata['global'] = array(    //Meta data for super user, Account Manager(Group) and General manager(Role)
                'tabMenuItems' => array(
                    array(
                        'label'  => "Job Scheduling",
                        'url'    => array('/jobScheduling/default'),
                        'right'  => self::RIGHT_ACCESS_JOBSCHEDULING,
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
            return 'JobScheduling';
        }

        public static function getSingularCamelCasedName()
        {
            return 'JobScheduling';
        }

        public static function getAccessRight()
        {
            return self::RIGHT_ACCESS_JOBSCHEDULING;
        }

        public static function getCreateRight()
        {
            return self::RIGHT_CREATE_JOBSCHEDULING;
        }

        public static function getDeleteRight()
        {
            return self::RIGHT_DELETE_JOBSCHEDULING;
        }

        public static function getGlobalSearchFormClassName()
        {
            return 'JobSchedulingSearchForm';
        }

        protected static function getSingularModuleLabel($language)
        {
            return Zurmo::t('JobSchedulingModule', 'JobScheduling', array(), null, $language);
        }

        protected static function getPluralModuleLabel($language)
        {
            return Zurmo::t('JobSchedulingModule', 'JobScheduling', array(), null, $language);
        }
        
        public static function isReportable()
        {
            return true;
        }
    }
?>
