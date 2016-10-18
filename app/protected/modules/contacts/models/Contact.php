<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2015 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2015. All rights reserved".
     ********************************************************************************/

    class Contact extends Person implements StarredInterface
    {
        public static function getByName($name)
        {
            return ZurmoModelSearch::getModelsByFullName('Contact', $name);
        }

        protected static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language),
                array(
                    'account'          => Zurmo::t('AccountsModule', 'AccountsModuleSingularLabel',    $params, null, $language),
                    'companyName'      => Zurmo::t('ContactsModule', 'Company Name',  array(), null, $language),
                    'description'      => Zurmo::t('ZurmoModule', 'Description',  array(), null, $language),
                    'industry'         => Zurmo::t('ZurmoModule', 'Industry',  array(), null, $language),
                    'latestActivityDateTime' => Zurmo::t('ZurmoModule', 'Latest Activity Date Time', array(), null, $language),
                    'meetings'         => Zurmo::t('MeetingsModule', 'MeetingsModulePluralLabel', $params, null, $language),
                    'notes'            => Zurmo::t('NotesModule', 'NotesModulePluralLabel', $params, null, $language),
                    'opportunities'    => Zurmo::t('OpportunitiesModule', 'OpportunitiesModulePluralLabel', $params, null, $language),
                    'secondaryAddress' => Zurmo::t('ContactsModule', 'Secondary Address',  array(), null, $language),
                    'secondaryEmail'   => Zurmo::t('ZurmoModule', 'Secondary Email',  array(), null, $language),
                    'source'           => Zurmo::t('ContactsModule', 'Source', $params, null, $language),
                    'state'            => Zurmo::t('ZurmoModule', 'Status', $params, null, $language),
                    'tasks'            => Zurmo::t('TasksModule', 'TasksModulePluralLabel', $params, null, $language),
                    'website'          => Zurmo::t('ZurmoModule', 'Website',  array(), null, $language),
                    'assistPhoneCstm'      => Zurmo::t('ContactsModule', 'Assistant Phone',  array(), null, $language),
                    'birthDateCstm'      => Zurmo::t('ContactsModule', 'Birth date',  array(), null, $language),
                    'jisawCstm'      => Zurmo::t('ContactsModule', 'Jigsaw',  array(), null, $language),
                    'dontCallCstm'      => Zurmo::t('ContactsModule', 'Do Not Call',  array(), null, $language),
                    'hasOptFaxCstm'      => Zurmo::t('ContactsModule', 'Fax Opt Out',  array(), null, $language),
                    'homePhoneCstm'      => Zurmo::t('ContactsModule', 'Home Phone',  array(), null, $language),
                    'lastTouReqdaCstm'      => Zurmo::t('ContactsModule', 'Last Stay-in-Touch Request Date',  array(), null, $language),
                    'lastTouSavdaCstm'      => Zurmo::t('ContactsModule', 'Last Stay-in-Touch Save Date',  array(), null, $language),
                    'reportToCstm'      => Zurmo::t('ContactsModule', 'Reports To',  array(), null, $language),
                    'inActiveCstm'      => Zurmo::t('ContactsModule', 'In Active',  array(), null, $language),
                    'leadCommentCstm'      => Zurmo::t('ContactsModule', 'Lead Comment',  array(), null, $language),
                    'oldAccountIDCstm'      => Zurmo::t('ContactsModule', 'Old Account ID',  array(), null, $language),
                    'oldContactIDCstm'      => Zurmo::t('ContactsModule', 'Old Contact Id',  array(), null, $language),
                    'oldReprtIdCstm'      => Zurmo::t('ContactsModule', 'Old Reports to ID',  array(), null, $language),
                    'preContactCstm'      => Zurmo::t('ContactsModule', 'Mode of Contact',  array(), null, $language),
                    'relshipAcctCstm'      => Zurmo::t('ContactsModule', 'Relationship to Account',  array(), null, $language),
                    'workPhoneCstm'      => Zurmo::t('ContactsModule', 'Work Phone',  array(), null, $language),
                    'annualRevenuCstm'      => Zurmo::t('ContactsModule', 'Annual Revenue',  array(), null, $language),
                    'emailOptOutCstm'      => Zurmo::t('ContactsModule', 'Email Opt Out',  array(), null, $language),
                    'statusCstm'      => Zurmo::t('ContactsModule', 'Lead Status',  array(), null, $language),
                    'nooEmpCstm'      => Zurmo::t('ContactsModule', 'No. of Employees',  array(), null, $language),
                    'leadRatingCstm'      => Zurmo::t('ContactsModule', 'Rating',  array(), null, $language),
                    'leadAgeCstm'      => Zurmo::t('ContactsModule', 'Age',  array(), null, $language),
                    'areaofIntstCstm'      => Zurmo::t('ContactsModule', 'Lead - Area of Interest',  array(), null, $language),
                    'dateScrumbbeCstm'      => Zurmo::t('ContactsModule', 'Date Scrubbed',  array(), null, $language),
                    'donotMailCstm'      => Zurmo::t('ContactsModule', 'Do Not Mail',  array(), null, $language),
                    'leadIncumproCstm'      => Zurmo::t('ContactsModule', 'Incumbent Provider',  array(), null, $language),
                    'leadEntryByCstm'      => Zurmo::t('ContactsModule', 'Lead Entry By',  array(), null, $language),
                    'nxtStepDateCstm'      => Zurmo::t('ContactsModule', 'Next Step Date',  array(), null, $language),
                    'oldLeadIdCstm'      => Zurmo::t('ContactsModule', 'Old Lead ID',  array(), null, $language),
                    'referalTypeCstm'      => Zurmo::t('ContactsModule', 'Referral Type',  array(), null, $language),
                    'leadSiteclasCstm'      => Zurmo::t('ContactsModule', 'Site Classification',  array(), null, $language),
                    'leadSiteDetaCstm'      => Zurmo::t('ContactsModule', 'Site Detail',  array(), null, $language),
                    'leadAssignCstm'      => Zurmo::t('ContactsModule', 'Assign using active assignment rule',  array(), null, $language),
                    'areaOfIntereCstm'      => Zurmo::t('ContactsModule', 'Area of Interest',  array(), null, $language),
                    'assignedBy'      => Zurmo::t('ContactsModule', 'Assigned By',  array(), null, $language),
                    'assignedDateCstm'      => Zurmo::t('ContactsModule', 'Assigned Date',  array(), null, $language),
                )
            );
        }

        public static function getModuleClassName()
        {
            return 'ContactsModule';
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
                    'companyName',
                    'description',
                    'latestActivityDateTime',
                    'website',
                    'googleWebTrackingId',
                    'assistPhoneCstm',
                    'birthDateCstm',
                    'jisawCstm',
                    'dontCallCstm',
                    'hasOptFaxCstm',
                    'homePhoneCstm',
                    'lastTouReqdaCstm',
                    'lastTouSavdaCstm',
                    'otherPhoneCstm',
                    'reportToCstm',
                    'inActiveCstm',
                    'leadCommentCstm',
                    'oldAccountIDCstm',
                    'oldContactIDCstm',
                    'oldReprtIdCstm',
                    'workPhoneCstm',
                    'emailOptOutCstm',
                    'nooEmpCstm',
                    'leadAgeCstm',
                    'dateScrumbbeCstm',
                    'donotMailCstm',
                    'leadIncumproCstm',
                    'leadEntryByCstm',
                    'nxtStepDateCstm',
                    'oldLeadIdCstm',
                    'leadAssignCstm',
                    'assignedDateCstm',
                    'isPrimaryContact'
                ),
                'relations' => array(
                    'account'             => array(static::HAS_ONE,   'Account'),
                    'accountAffiliations' => array(static::HAS_MANY, 'AccountContactAffiliation', static::OWNED, 
                                                static::LINK_TYPE_SPECIFIC, 'contactAffiliation'),
                    'industry'            => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'industry'),
                    'products'            => array(static::HAS_MANY, 'Product'),
                    'opportunities'       => array(static::MANY_MANY, 'Opportunity'),
                    'secondaryAddress'    => array(static::HAS_ONE,   'Address',          static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'secondaryAddress'),
                    'secondaryEmail'      => array(static::HAS_ONE,   'Email',            static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'secondaryEmail'),
                    'source'              => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'source'),
                    'state'               => array(static::HAS_ONE,   'ContactState', static::NOT_OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'state'),
                    'projects'            => array(static::MANY_MANY, 'Project'),
                    'preContactCstm'      => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'preContactCstm'),
                    'relshipAcctCstm'     => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'relshipAcctCstm'),
                    'annualRevenuCstm'    => array(static::HAS_ONE,   'CurrencyValue', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'annualRevenuCstm'),
                    'statusCstm'          => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'statusCstm'),
                    'leadRatingCstm'      => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'leadRatingCstm'),
                    'areaofIntstCstm'     => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'areaofIntstCstm'),
                    'referalTypeCstm'     => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'referalTypeCstm'),
                    'leadSiteclasCstm'    => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'leadSiteclasCstm'),
                    'leadSiteDetaCstm'    => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'leadSiteDetaCstm'),
                    'areaOfIntereCstm'    => array(static::HAS_ONE,   'OwnedMultipleValuesCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'areaOfIntereCstm'),
                    
                    'assignedBy'     => array(static::HAS_ONE,  'User', static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'assignedBy'),
                ),
                'derivedRelationsViaCastedUpModel' => array(
                    'meetings' => array(static::MANY_MANY, 'Meeting', 'activityItems'),
                    'notes'    => array(static::MANY_MANY, 'Note',    'activityItems'),
                    'tasks'    => array(static::MANY_MANY, 'Task',    'activityItems'),
                ),
                'rules' => array(
                    array('companyName',            'type',    'type' => 'string'),
                    array('companyName',            'length',  'min'  => 1, 'max' => 64),
                    array('description',            'type',    'type' => 'string'),
                    array('latestActivityDateTime', 'readOnly'),
                    array('latestActivityDateTime', 'type', 'type' => 'datetime'),
                    array('state',                  'required'),
                    array('website',                'url',     'defaultScheme' => 'http'),
                    array('googleWebTrackingId',    'type',    'type' => 'string'),
                    array('assistPhoneCstm',  'type', 'type' => 'string'),
                    array('assistPhoneCstm',  'length', 'max' => 24), 
                    array('birthDateCstm', 'type', 'type' => 'date'),
                    array('birthDateCstm', 'dateTimeDefault', 'value' => ''),
                    array('jisawCstm',  'type', 'type' => 'string'),
                    array('jisawCstm',  'length', 'max' => 255),
                    array('dontCallCstm',    'type',           'type'  => 'boolean'),
                    array('dontCallCstm',  'default', 'value' => 0),
                    array('hasOptFaxCstm',    'type',           'type'  => 'boolean'),
                    array('hasOptFaxCstm',  'default', 'value' => 0),
                    array('homePhoneCstm',  'type', 'type' => 'string'),
                    array('homePhoneCstm',  'length', 'max' => 24),
                    array('lastTouReqdaCstm', 'type', 'type' => 'datetime'),
                    array('lastTouReqdaCstm', 'dateTimeDefault', 'value' => ''),
                    array('lastTouSavdaCstm', 'type', 'type' => 'datetime'),
                    array('lastTouSavdaCstm', 'dateTimeDefault', 'value' => ''),
                    array('otherPhoneCstm',  'type', 'type' => 'string'),
                    array('otherPhoneCstm',  'length', 'max' => 24),
                    array('reportToCstm',  'type', 'type' => 'string'),
                    array('reportToCstm',  'length', 'max' => 255),
                    array('inActiveCstm',    'type',           'type'  => 'boolean'),
                    array('inActiveCstm',  'default', 'value' => 0),
                    array('leadCommentCstm',  'type', 'type' => 'string'),
                    array('leadCommentCstm',  'length', 'max' => 255),
                    array('oldAccountIDCstm',  'type', 'type' => 'string'),
                    array('oldAccountIDCstm',  'length', 'max' => 255),
                    array('oldContactIDCstm',  'type', 'type' => 'string'),
                    array('oldContactIDCstm',  'length', 'max' => 255),
                    array('oldReprtIdCstm',  'type', 'type' => 'string'),
                    array('oldReprtIdCstm',  'length', 'max' => 255),
                    array('workPhoneCstm',  'type', 'type' => 'string'),
                    array('workPhoneCstm',  'length', 'max' => 24),
                    array('emailOptOutCstm',    'type',           'type'  => 'boolean'),
                    array('emailOptOutCstm',  'default', 'value' => 0),
                    array('nooEmpCstm',   'type',      'type' => 'integer'),
                    array('leadAgeCstm',   'type',      'type' => 'integer'),
                    array('dateScrumbbeCstm', 'type', 'type' => 'date'),
                    array('dateScrumbbeCstm', 'dateTimeDefault', 'value' => ''),
                    array('assignedDateCstm', 'type', 'type' => 'date'),
                    array('assignedDateCstm', 'dateTimeDefault', 'value' => ''),
                    array('donotMailCstm',    'type',           'type'  => 'boolean'),
                    array('donotMailCstm',  'default', 'value' => 0),
                    array('leadIncumproCstm',  'type', 'type' => 'string'),
                    array('leadIncumproCstm',  'length', 'max' => 255),
                    array('leadEntryByCstm',  'type', 'type' => 'string'),
                    array('leadEntryByCstm',  'length', 'max' => 255),
                    array('nxtStepDateCstm', 'type', 'type' => 'date'),
                    array('nxtStepDateCstm', 'dateTimeDefault', 'value' => ''),
                    array('oldLeadIdCstm',  'type', 'type' => 'string'),
                    array('oldLeadIdCstm',  'length', 'max' => 255),
                    array('leadSiteclasCstm',  'default', 'value' => 'Property Owner'),
                    array('leadAssignCstm',    'type',           'type'  => 'boolean'),
                    array('leadAssignCstm',  'default', 'value' => 0),
                    array('isPrimaryContact',    'type',           'type'  => 'boolean'),
                    array('isPrimaryContact',  'default', 'value' => 1),

                ),
                'elements' => array(
                    'account'                 => 'Account',
                    'description'             => 'TextArea',
                    'latestActivityDateTime'  => 'DateTime',
                    'secondaryEmail'          => 'EmailAddressInformation',
                    'secondaryAddress'        => 'Address',
                    'state'                   => 'ContactState',
                    'assistPhoneCstm'         => 'Phone',
                    'birthDateCstm'           => 'Date',
                    'jisawCstm'               => 'Text',
                    'dontCallCstm'            => 'CheckBox',
                    'hasOptFaxCstm'           => 'CheckBox',
                    'homePhoneCstm'           => 'Phone',
                    'lastTouReqdaCstm'        => 'DateTime',
                    'lastTouSavdaCstm'        => 'DateTime',
                    'source'                  => 'DropDown',
                    'otherPhoneCstm'          => 'Phone',
                    'reportToCstm'            => 'Text',
                    'inActiveCstm'            => 'CheckBox',
                    'leadCommentCstm'         => 'Text',
                    'oldAccountIDCstm'        => 'Text',
                    'oldContactIDCstm'        => 'Text',
                    'oldReprtIdCstm'          => 'Text',
                    'preContactCstm'          => 'DropDown',
                    'relshipAcctCstm'         => 'DropDown',
                    'workPhoneCstm'           => 'Phone',
                    'annualRevenuCstm'        => 'CurrencyValue',
                    'emailOptOutCstm'         => 'CheckBox',
                    'statusCstm'              => 'DropDown',
                    'nooEmpCstm'              => 'Integer',
                    'leadRatingCstm'          => 'DropDown',
                    'leadAgeCstm'             => 'Integer',
                    'areaofIntstCstm'         => 'DropDown',
                    'dateScrumbbeCstm'        => 'Date',
                    'donotMailCstm'           => 'CheckBox',
                    'leadIncumproCstm'        => 'Text',
                    'leadEntryByCstm'         => 'Text',
                    'nxtStepDateCstm'         => 'Date',
                    'oldLeadIdCstm'           => 'Text',
                    'referalTypeCstm'         => 'DropDown',
                    'leadSiteclasCstm'        => 'DropDown',
                    'leadSiteDetaCstm'        => 'DropDown',
                    'leadAssignCstm'          => 'CheckBox',
                    'areaOfIntereCstm'        => 'MultiSelectDropDown',
                    'companyName'             => 'Text',
                    'assignedBy'	=> 'User',
                    'assignedDateassignedDateCstm'	=> 'Date',
                ),
                'customFields' => array(
                    'industry' => 'Industries',
                    'source'   => 'LeadSources',
                    'preContactCstm' => 'Precontact',
                    'relshipAcctCstm' => 'Relshipacct',
                    'statusCstm' => 'Status',
                    'state' => 'State',
                    'leadRatingCstm' => 'Leadrating',
                    'areaofIntstCstm' => 'Areaofintst',
                    'referalTypeCstm' => 'Referaltype',
                    'leadSiteclasCstm' => 'Leadsiteclas',
                    'leadSiteDetaCstm' => 'Leadsitedeta',
                    'areaOfIntereCstm' => 'Areaofinstes'
                ),
                'defaultSortAttribute' => 'createdDateTime',
                'rollupRelations' => array(
                    'opportunities',
                ),
                'noAudit' => array(
                    'description',
                    'latestActivityDateTime',
                    'website',
                    'assistPhoneCstm',
                    'birthDateCstm',
                    'jisawCstm',
                    'dontCallCstm',
                    'hasOptFaxCstm',
                    'homePhoneCstm',
                    'lastTouReqdaCstm',
                    'lastTouSavdaCstm',
                    'otherPhoneCstm',
                    'reportToCstm',
                    'inActiveCstm',
                    'leadCommentCstm',
                    'oldAccountIDCstm',
                    'oldContactIDCstm',
                    'oldReprtIdCstm',
                    'preContactCstm',
                    'relshipAcctCstm',
                    'workPhoneCstm',
                    'annualRevenuCstm',
                    'emailOptOutCstm',
                    'statusCstm',
                    'nooEmpCstm',
                    'leadRatingCstm',
                    'leadAgeCstm',
                    'areaofIntstCstm',
                    'dateScrumbbeCstm',
                    'donotMailCstm',
                    'leadIncumproCstm',
                    'leadEntryByCstm',
                    'nxtStepDateCstm',
                    'oldLeadIdCstm',
                    'referalTypeCstm',
                    'leadSiteclasCstm',
                    'leadSiteDetaCstm',
                    'leadAssignCstm',
                    'areaOfIntereCstm',
                    'state',
                    'assignedDateCstm',
                    'assignedBy'
                ),
                'indexes' => array(
                    'person_id' => array(
                        'members' => array('person_id'),
                        'unique' => false),
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        public static function getRollUpRulesType()
        {
            return 'Contact';
        }

        public static function hasReadPermissionsOptimization()
        {
            return true;
        }

        public static function getGamificationRulesType()
        {
            return 'ContactGamification';
        }

        /**
         * Override since Person has its own override.
         * @see RedBeanModel::getLabel
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getLabel($language = null)
        {
            if (null != $moduleClassName = static::getModuleClassName())
            {
                return $moduleClassName::getModuleLabelByTypeAndLanguage('Singular', $language);
            }
            return get_called_class();
        }

        /**
         * Override since Person has its own override.
         * @see RedBeanModel::getPluralLabel
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getPluralLabel($language = null)
        {
            if (null != $moduleClassName = static::getModuleClassName())
            {
                return $moduleClassName::getModuleLabelByTypeAndLanguage('Plural', $language);
            }
            return static::getLabel($language) . 's';
        }

        public static function hasReadPermissionsSubscriptionOptimization()
        {
            return true;
        }

        public static function supportsQueueing()
        {
            return true;
        }

        public function setLatestActivityDateTime($dateTime)
        {
            assert('is_string($dateTime)');
            AuditUtil::saveOriginalAttributeValue($this, 'latestActivityDateTime', $dateTime);
            $this->unrestrictedSet('latestActivityDateTime', $dateTime);
        }

        protected function afterDelete()
        {
            parent::afterDelete();
            ContactsUtil::resolveMarketingListMembersByContact($this);
        }

        /**
         * Override to handle the set read-only latestActivityDateTime attribute on the import scenario.
         * (non-PHPdoc)
         * @see RedBeanModel::isAllowedToSetReadOnlyAttribute()
         */
        public function isAllowedToSetReadOnlyAttribute($attributeName)
        {
            if ($this->getScenario() == 'importModel' || $this->getScenario() == 'searchModel')
            {
                if ( $attributeName == 'latestActivityDateTime')
                {
                    return true;
                }
                else
                {
                    return parent::isAllowedToSetReadOnlyAttribute($attributeName);
                }
            }
        }
        
        public static function getByOldContactId($oldId) {
            return self::makeModels(ZurmoRedBean::find('contact', "oldcontactidcstm =:oldcontactidcstm", array(':oldcontactidcstm' => $oldId)));
        }
        
        public static function getByOldLeadId($oldId) {
            return self::makeModels(ZurmoRedBean::find('contact', "oldleadidcstm =:oldleadidcstm", array(':oldleadidcstm' => $oldId)));
        }
        
        public function saveModelFromAPIData($data, $model, & $savedSuccessfully){
            $model->companyName = $data['companyName'];
            $model->lastName = $data['lastName'];
            $model->website = $data['website'];
            $model->description = $data['description'];
            $model->state = ContactState::getById($data['state']['id']);
            //$model->account = Account::getById($data['account']['id']);
            //$person = new Person();
            $model->firstName = $data['firstName'];
            $model->lastName = $data['lastName'];
            $model->jobTitle = $data['jobTitle'];
            $model->department = $data['department'];
            $model->mobilePhone = $data['mobilePhone'];
            $model->officePhone = $data['officePhone'];
            $model->officeFax = $data['officeFax'];
            $model->save();
            $savedSuccessfully = true;
            return $model;
        }
    }
?>
