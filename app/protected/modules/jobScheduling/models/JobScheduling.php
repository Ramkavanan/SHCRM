<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JobScheduling extends Item {
    public function __toString()
        {
            if (trim($this->name) == '')
            {
                return Yii::t('Default', '(Unnamed)');
            }
            return $this->name;
        }

        public static function getModuleClassName()
        {
            return 'JobSchedulingModule';
        }

		public static function getDeptRefById($id) {
			return self::makeModels(ZurmoRedBean::find('jobscheduling', "id =:id", array(':id' => $id)));
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
                    'name',
                    'crewName',
                    'archive',
                    'status',
                    'scheduleDate',
                    'user_id',
                ),
               
                'rules' => array(
                    array('agreement',         'required'),
                    array('name',           'required'),
                    array('name',           'type',           'type'  => 'string'),
                    array('name',           'length',         'max'   => 100),
                    array('crewName',           'required'),
                    array('crewName',           'type',           'type'  => 'string'),
                    array('crewName',           'length',         'max'   => 100),
                    array('archive',       'type',         'type'   => 'string'),
                    array('status',       'type',         'type'   => 'string'),
                    array('scheduleDate', 'type', 'type' => 'date'),
                    array('user_id',       'type',         'type'   => 'integer'),
                ),
                 'relations' => array(
                    'agreement'     => array(static::HAS_ONE,   'Agreement'),
                    'agreementjobs'     => array(static::HAS_MANY,   'AgreementJobs'),
                    
                ),
                'elements' => array(
                    'name'   => 'Text',
                    'crewName'    => 'Text',
                    'status'      => 'Text',
                    'agreement'   => 'Agreement',
                    'scheduleDate' => 'Date',
                    'user_id' => 'Text',
                ),
                'customFields' => array(
//                    'status'		      => 'JobStatusValue',
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
                    'name'              => Zurmo::t('JobSchedulingModule', 'Job Name',  $params, null, $language),
                    'week_no'       => Zurmo::t('JobSchedulingModule', 'Week Number',  $params, null, $language),
                    'sch_id'           => Zurmo::t('JobSchedulingModule', 'Schedule Id',  $params, null, $language),

                    'status'           => Zurmo::t('JobSchedulingModule', 'Job Status',  $params, null, $language),

                    'agreement'   => Zurmo::t('JobSchedulingModule',  'AgreementsModuleSingularLabel', $params, null, $language),
                    'scheduleDate' => Zurmo::t('JobSchedulingModule', 'Schedule Date', $params, null, $language),
                    'user_id' => Zurmo::t('JobSchedulingModule', 'Created By User', $params, null, $language),
                )
            );
        }
        
        public static function getJobsByAgmntId($agmnt_id) {
            return self::makeModels(ZurmoRedBean::find('jobscheduling', "agreement_id =:id", array(':id' => $agmnt_id)));
        }
        
    public static function getAllAgmtJobs($reportYear) {
        $active_ids_arr = Agreement::getActiveDraftCustomId();
        if($active_ids_arr){
            $query = "SELECT js.id, a.name, js.agreement_id, week_day, value FROM jobscheduling AS js 
                    LEFT JOIN agreementjobs AS aj ON aj.jobscheduling_id = js.id AND YEAR(week_day) = YEAR('".$reportYear."')
                    LEFT JOIN agreement AS a ON js.agreement_id = a.id AND a.archive IS NULL
                    WHERE a.status_customfield_id IN (".$active_ids_arr.") AND js.status='Active' AND js.archive IS NULL AND week_day IS NOT NULL";
            
            $jobScheduleData = ZurmoRedBean::getAll($query);
            return $jobScheduleData;
        }
        else {
            return;
        }        
    }
    
    public static function getAllCategoryJobs($reportYear) {
        $active_ids_arr = Agreement::getActiveDraftCustomId();
        if($active_ids_arr){
           $query = "SELECT js.id, a.name as agmtName, js.agreement_id, c.name, aj.category_id, week_day, value FROM jobscheduling AS js 
                    LEFT JOIN agreementjobs AS aj ON aj.jobscheduling_id = js.id AND YEAR(week_day) = YEAR('".$reportYear."')
                    LEFT JOIN category AS c ON aj.category_id = c.id
                    LEFT JOIN agreement AS a ON js.agreement_id = a.id AND a.archive IS NULL
                    WHERE a.status_customfield_id IN (".$active_ids_arr.") AND js.archive IS NULL AND week_day IS NOT NULL";
           
            $jobScheduleData = ZurmoRedBean::getAll($query);
            return $jobScheduleData;
        }
        else {
            return;
        }        
    }
    
    public static function getAllAgmtOwnerJobs($reportYear) {
        $active_ids_arr = Agreement::getActiveDraftCustomId();
        if($active_ids_arr){
           $query = "SELECT js.id, a.name as agmtName, js.agreement_id, week_day, value, os.owner__user_id, p.firstname, p.lastname  FROM jobscheduling AS js 
                    LEFT JOIN agreementjobs AS aj ON aj.jobscheduling_id = js.id AND YEAR(week_day) = YEAR('".$reportYear."')
                    LEFT JOIN agreement AS a ON js.agreement_id = a.id AND a.archive IS NULL
                    LEFT JOIN ownedsecurableitem AS os ON a.ownedsecurableitem_id = os.id 
                    LEFT JOIN _user AS u ON os.owner__user_id = u.id
                    LEFT JOIN person AS p ON u.person_id = p.id
                    WHERE a.status_customfield_id IN (".$active_ids_arr.") AND js.archive IS NULL AND week_day IS NOT NULL
                    ";
           
            $jobScheduleData = ZurmoRedBean::getAll($query);
            return $jobScheduleData;
        }
        else {
            return;
        }        
    }

    public function getWeeklyScheduledHours(){
        $active_ids_arr = Agreement::getActiveDraftCustomId();
        $query = "SELECT js.id,scheduledate,ROUND(SUM(aj.value),2) hours,DATE(week_day) week_start_date,
                    CONCAT(DATE_FORMAT(week_day,'%m/%d'),' W ',WEEK(week_day)) week_start,WEEK(week_day) week_no 
                    FROM jobscheduling js
                    INNER JOIN agreementjobs aj ON aj.jobscheduling_id = js.id 
                    LEFT JOIN agreement AS a ON js.agreement_id = a.id AND a.archive IS NULL
                    WHERE a.status_customfield_id IN (".$active_ids_arr.") AND js.status='Active' AND aj.week_day IS NOT NULL AND js.archive IS NULL
                    AND YEAR(aj.week_day) = YEAR(CURDATE()) 
                    GROUP BY aj.week_day;";
        return ZurmoRedBean::getAll($query);
    }
}
?>
