<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AgreementJobs extends Item {

        public static function getModuleClassName()
        {
            return 'JobSchedulingAgmtModule';
        }

        public static function canSaveMetadata()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'week_no',                    
                    'value',
                    'week_day'
                ),
               
                'rules' => array(
                    array('week_no',           'required'),
                    array('week_no',           'type',           'type'  => 'string'),
                    array('week_no',           'length',         'max'   => 100),
                    array('sch_id',           'required'),
                    array('sch_id',           'type',           'type'  => 'string'),
                    array('sch_id',           'length',         'max'   => 100),
                    array('value',           'required'),
                    array('value',           'type',           'type'  => 'string'),
                    array('value',           'length',         'max'   => 100),
                    array('week_day', 'type', 'type' => 'date'),
                ),
                 'relations' => array(
                    'jobscheduling'      => array(static::HAS_ONE, 'JobScheduling'),
                    'category'      => array(static::HAS_ONE, 'Category'),
                ),
                'elements' => array(
                    'week_no'   => 'Text',
                    'value'     => 'Text',
                    'week_day' => 'Date',
                ),
                'defaultSortAttribute' => 'createdDateTime',
                'noAudit' => array(
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        protected static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language),
                array(
                    'week_no'       => Zurmo::t('JobSchedulingModule', 'Week Number',  $params, null, $language),
                    'value'           => Zurmo::t('JobSchedulingModule', 'Value',  $params, null, $language),
                )
            );
        }
        
        public static function getAgmtJobsByJobId($job_id) {
            return self::makeModels(ZurmoRedBean::find('agreementjobs', "jobscheduling_id =:jobscheduling_id", array(':jobscheduling_id' => $job_id)));
        }
}
?>
