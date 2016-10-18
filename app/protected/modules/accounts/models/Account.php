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

    class Account extends OwnedSecurableItem implements StarredInterface
    {
        public static function getByName($name)
        {
            return self::getByNameOrEquivalent('name', $name);
        }

        public function __toString()
        {
            try
            {
                if (trim($this->name) == '')
                {
                    return Zurmo::t('Core', '(Unnamed)');
                }
                return $this->name;
            }
            catch (AccessDeniedSecurityException $e)
            {
                return '';
            }
        }

        public static function getModuleClassName()
        {
            return 'AccountsModule';
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
                    'annualRevenue',
                    'description',
                    'employees',
                    'latestActivityDateTime',
                    'name',
                    'officePhone',
                    'officeFax',
                    'website',
                    'siteCstm',
                    'jigsawCstm',
                    'sicCstm',
                    'sicDescCstm',
                    'tickerSymbolCstm',
                    'extrlDataUrlCstm',
                    'inActiveCstm',
                    'incumbProvidCstm',
                    'oldAcctIdCstm',
                    'oldAcctNoCstm',
                    'oldParentIdCstm',
                    'opprtCountCstm',
                    'teryZipFormCstm',
                    'teryZipCstm',
                    'firstName',
                    'lastName',
                    'fullName',
                    'mobilePhone',
                ),
                'relations' => array(
                    'account'          => array(static::HAS_MANY_BELONGS_TO,  'Account'),
                    'primaryAccountAffiliations'   => array(static::HAS_MANY, 'AccountAccountAffiliation',
                                                            static::OWNED, static::LINK_TYPE_SPECIFIC,
                                                            'primaryAccountAffiliation'),
                    'secondaryAccountAffiliations' => array(static::HAS_MANY, 'AccountAccountAffiliation',
                                                            static::OWNED, static::LINK_TYPE_SPECIFIC,
                                                            'secondaryAccountAffiliation'),
                    'accounts'         => array(static::HAS_MANY,             'Account'),
                    'billingAddress'   => array(static::HAS_ONE,              'Address',          static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'billingAddress'),
                    'products'         => array(static::HAS_MANY,             'Product'),
                    'contactAffiliations' => array(static::HAS_MANY, 'AccountContactAffiliation',
                                                   static::OWNED, static::LINK_TYPE_SPECIFIC,
                                                   'accountAffiliation'),
                    'contacts'         => array(static::HAS_MANY,             'Contact'),
                    'industry'         => array(static::HAS_ONE,              'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'industry'),
                    'opportunities'    => array(static::HAS_MANY,             'Opportunity'),
                    'primaryEmail'     => array(static::HAS_ONE,              'Email',            static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'primaryEmail'),
                    'shippingAddress'  => array(static::HAS_ONE,              'Address',          static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'shippingAddress'),
                    'type'             => array(static::HAS_ONE,              'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'type'),
                    'projects'         => array(static::MANY_MANY,            'Project'),
                    'acctSourceCstm'         => array(static::HAS_ONE,              'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'acctSourceCstm'),
                    'ratingCstm'         => array(static::HAS_ONE,              'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'ratingCstm'),
                    'avghouseIncoCstm'         => array(static::HAS_ONE,              'CurrencyValue', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'avghouseIncoCstm'),
                    'propValueCstm'         => array(static::HAS_ONE,              'CurrencyValue', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'propValueCstm'),
                    'siteClassifyCstm'         => array(static::HAS_ONE,              'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'siteClassifyCstm'),
                    'siteDetailCstm'         => array(static::HAS_ONE,              'OwnedCustomField', static::OWNED,
                                                static::LINK_TYPE_SPECIFIC, 'siteDetailCstm'),
                    'title'          => array(static::HAS_ONE, 'OwnedCustomField', static::OWNED,
                                         static::LINK_TYPE_SPECIFIC, 'title'),
                ),
                'derivedRelationsViaCastedUpModel' => array(
                    'meetings' => array(static::MANY_MANY, 'Meeting', 'activityItems'),
                    'notes'    => array(static::MANY_MANY, 'Note',    'activityItems'),
                    'tasks'    => array(static::MANY_MANY, 'Task',    'activityItems'),
                ),
                'rules' => array(
                    array('annualRevenue', 'type',    'type' => 'float'),
                    array('description',   'type',    'type' => 'string'),
                    array('employees',     'type',    'type' => 'integer'),
                    array('latestActivityDateTime',  'readOnly'),
                    array('latestActivityDateTime',  'type', 'type' => 'datetime'),
                    array('name',          'required'),
                    array('name',          'type',    'type' => 'string'),
                    array('name',          'length',  'min'  => 1, 'max' => 64),
                    array('fullName',          'type',    'type' => 'string'),
                    array('fullName',          'length',  'min'  => 1, 'max' => 64),
                    array('officePhone',   'type',    'type' => 'string'),
                    array('officePhone',   'length',  'min'  => 1, 'max' => 24),
                    array('officeFax',     'type',    'type' => 'string'),
                    array('officeFax',     'length',  'min'  => 1, 'max' => 24),
                    array('website',       'url',     'defaultScheme' => 'http'),
                    array('siteCstm',     'length',  'max' => 255),
                    array('siteCstm',     'type',    'type' => 'string'),
                    array('jigsawCstm',     'length',  'max' => 255),
                    array('jigsawCstm',     'type',    'type' => 'string'),
                    array('sicCstm',     'length',  'max' => 255),
                    array('sicCstm',     'type',    'type' => 'string'),
                    array('sicDescCstm',     'length',  'max' => 255),
                    array('sicDescCstm',     'type',    'type' => 'string'),
                    array('tickerSymbolCstm',     'length',  'max' => 255),
                    array('tickerSymbolCstm',     'type',    'type' => 'string'),
                    array('extrlDataUrlCstm',     'length',  'max' => 255),
                    array('extrlDataUrlCstm',     'url',    'defaultScheme' => 'http'),
                    array('inActiveCstm',     'default',    'value' => '0'),
                    array('inActiveCstm',     'boolean'),
                    array('incumbProvidCstm',     'length',  'max' => 255),
                    array('incumbProvidCstm',     'type',    'type' => 'string'),
                    array('oldAcctIdCstm',     'type',    'type' => 'string'),
                    array('oldAcctIdCstm',     'length',  'max' => 255),
                    array('oldAcctNoCstm',     'type',    'type' => 'string'),
                    array('oldAcctNoCstm',     'length',  'max' => 255),
                    array('oldParentIdCstm',     'type',    'type' => 'string'),
                    array('oldParentIdCstm',     'length',  'max' => 255),
                    array('opprtCountCstm',     'type',    'type' => 'integer'),
                    array('teryZipFormCstm',     'type',    'type' => 'string'),
                    array('teryZipCstm',     'type',    'type' => 'string'),
                    array('lastName',       'type',   'type' => 'string'),
                    array('lastName',       'length', 'min'  => 1, 'max' => 32),
                    array('firstName',       'type',   'type' => 'string'),
                    array('firstName',       'length', 'min'  => 1, 'max' => 32),
                    array('mobilePhone',    'type',   'type' => 'string'),
                    array('officePhone',    'type',   'type' => 'string'),
                ),
                'elements' => array(
                    'mobilePhone'    => 'Phone',
                    'account'                 => 'Account',
                    'billingAddress'          => 'Address',
                    'description'             => 'TextArea',
                    'latestActivityDateTime'  => 'DateTime',
                    'officePhone'             => 'Phone',
                    'officeFax'               => 'Phone',
                    'primaryEmail'            => 'EmailAddressInformation',
                    'shippingAddress'         => 'Address',
                    'siteCstm'         => 'Text',
                    'acctSourceCstm'         => 'DropDown',
                    'jigsawCstm'         => 'Text',
                    'ratingCstm'         => 'DropDown',
                    'sicCstm'         => 'Text',
                    'sicDescCstm'         => 'Text',
                    'tickerSymbolCstm'         => 'Text',
                    'type'         => 'DropDown',
                    'avghouseIncoCstm'         => 'CurrencyValue',
                    'extrlDataUrlCstm'         => 'Url',
                    'inActiveCstm'         => 'CheckBox',
                    'incumbProvidCstm'         => 'Text',
                    'oldAcctIdCstm'         => 'TextArea',
                    'oldAcctNoCstm'         => 'Text',
                    'firstName'         => 'Text',
                    'lastName'         => 'Text',
                    'fullName'         => 'Text',
                    'oldParentIdCstm'         => 'Text',
                    'opprtCountCstm'         => 'Integer',
                    'propValueCstm'         => 'CurrencyValue',
                    'siteClassifyCstm'         => 'DropDown',
                    'siteDetailCstm'         => 'DropDown',
                    'teryZipFormCstm'         => 'TextArea',
                    'teryZipCstm'         => 'TextArea',
                    'industry'         => 'DropDown',
                    
                ),
                'customFields' => array(
                    'industry' => 'Industries',
                    'type'     => 'AccountTypes',
                    'acctSourceCstm'     => 'Acctsource',
                    'ratingCstm'     => 'Rating',
                    'siteClassifyCstm'     => 'Siteclassify',
                    'siteDetailCstm'     => 'Sitedetail',
                    'title' => 'Titles',
                ),
                'defaultSortAttribute' => 'createdDateTime',
                'rollupRelations' => array(
                    'accounts' => array('contacts', 'opportunities'),
                    'contacts',
                    'opportunities',
                ),
                'noAudit' => array(
                    'annualRevenue',
                    'description',
                    'employees',
                    'latestActivityDateTime',
                    'website',
                    'siteCstm',
                    'acctSourceCstm',
                    'jigsawCstm',
                    'ratingCstm',
                    'sicCstm',
                    'sicDescCstm',
                    'tickerSymbolCstm',
                    'avghouseIncoCstm',
                    'extrlDataUrlCstm',
                    'inActiveCstm',
                    'incumbProvidCstm',
                    'oldAcctIdCstm',
                    'oldAcctNoCstm',
                    'oldParentIdCstm',
                    'opprtCountCstm',
                    'propValueCstm',
                    'siteClassifyCstm',
                    'siteDetailCstm',
                    'teryZipFormCstm',
                    'teryZipCstm',
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
            return 'Account';
        }

        public static function hasReadPermissionsOptimization()
        {
            return true;
        }

        public static function getGamificationRulesType()
        {
            return 'AccountGamification';
        }

        protected static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            $paramsForAffiliations = $params;
            $paramsForAffiliations['{primaryAccount}'] = AccountAccountAffiliationsModule::resolveAccountRelationLabel('Singular', 'primary');
            $paramsForAffiliations['{secondaryAccount}'] = AccountAccountAffiliationsModule::resolveAccountRelationLabel('Singular', 'secondary');
            return array_merge(parent::translatedAttributeLabels($language),
                array(
                    'owner'     => Zurmo::t('AccountsModule',      'Account owner', $params, null, $language),
                    'account'                => Zurmo::t('AccountsModule',      'Parent AccountsModuleSingularLabel',  $params, null, $language),
                    'accounts'               => Zurmo::t('AccountsModule',      'AccountsModulePluralLabel',           $params, null, $language),
                    'annualRevenue'          => Zurmo::t('AccountsModule',      'Annual Revenue',                      array(), null, $language),
                    'billingAddress'         => Zurmo::t('AccountsModule',      'Billing Address',                     array(), null, $language),
                    'contacts'               => Zurmo::t('ContactsModule',      'ContactsModulePluralLabel',           $params, null, $language),
                    'description'            => Zurmo::t('ZurmoModule',         'Description',                         array(), null, $language),
                    'employees'              => Zurmo::t('AccountsModule',      'Employees',                           array(), null, $language),
                    'industry'               => Zurmo::t('ZurmoModule',         'Industry',                            array(), null, $language),
                    'latestActivityDateTime' => Zurmo::t('ZurmoModule',         'Latest Activity Date Time',           array(), null, $language),
                    'meetings'               => Zurmo::t('MeetingsModule',      'MeetingsModulePluralLabel',           $params, null, $language),
                    'name'                   => Zurmo::t('Core',                'Name',                                array(), null, $language),
                    'notes'                  => Zurmo::t('NotesModule',         'NotesModulePluralLabel',              $params, null, $language),
                    'officePhone'            => Zurmo::t('ZurmoModule',         'Office Phone',                        array(), null, $language),
                    'officeFax'              => Zurmo::t('ZurmoModule',         'Office Fax',                          array(), null, $language),
                    'opportunities'          => Zurmo::t('OpportunitiesModule', 'OpportunitiesModulePluralLabel',      $params, null, $language),
                    'primaryAccountAffiliations' =>
                        Zurmo::t('AccountAccountAffiliationsModule', '{primaryAccount} Affiliations', $paramsForAffiliations, null, $language),
                    'primaryEmail'           => Zurmo::t('ZurmoModule',         'Email',                       array(), null, $language),
                    'secondaryAccountAffiliations' =>
                        Zurmo::t('AccountAccountAffiliationsModule', '{secondaryAccount} Affiliations', $paramsForAffiliations, null, $language),
                    'shippingAddress'        => Zurmo::t('AccountsModule',      'Shipping Address',                    array(), null, $language),
                    'tasks'                  => Zurmo::t('TasksModule',         'TasksModulePluralLabel',              $params, null, $language),
                    'type'                   => Zurmo::t('Core',                'Type',                                array(), null, $language),
                    'website'                => Zurmo::t('ZurmoModule',         'Website',                             array(), null, $language),
                    'siteCstm'                => Zurmo::t('AccountsModule',         'Site',                             array(), null, $language),
                    'acctSourceCstm'                => Zurmo::t('AccountsModule',         'Account Source',                             array(), null, $language),
                    'jigsawCstm'                => Zurmo::t('AccountsModule',         'Jigsaw',                             array(), null, $language),
                    'ratingCstm'                => Zurmo::t('AccountsModule',         'Rating',                             array(), null, $language),
                    'sicCstm'                => Zurmo::t('AccountsModule',         'Sic',                             array(), null, $language),
                    'sicDescCstm'                => Zurmo::t('AccountsModule',         'SicDesc',                             array(), null, $language),
                    'tickerSymbolCstm'                => Zurmo::t('AccountsModule',         'TickerSymbol',                             array(), null, $language),
                    'avghouseIncoCstm'                => Zurmo::t('AccountsModule',         'Average Household Income',                             array(), null, $language),
                    'extrlDataUrlCstm'                => Zurmo::t('AccountsModule',         'External Data URL',                             array(), null, $language),
                    'inActiveCstm'                => Zurmo::t('AccountsModule',         'In Active',                             array(), null, $language),
                    'incumbProvidCstm'                => Zurmo::t('AccountsModule',         'Incumbent Provider',                             array(), null, $language),
                    'oldAcctIdCstm'                => Zurmo::t('AccountsModule',         'Old Account ID',                             array(), null, $language),
                    'oldAcctNoCstm'                => Zurmo::t('AccountsModule',         'Old Account Number',                             array(), null, $language),
                    'oldParentIdCstm'                => Zurmo::t('AccountsModule',         'Old Parent Id',                             array(), null, $language),
                    'opprtCountCstm'                => Zurmo::t('AccountsModule',         'Opportunity Count',                             array(), null, $language),
                    'propValueCstm'                => Zurmo::t('AccountsModule',         'Property Value',                             array(), null, $language),
                    'siteClassifyCstm'                => Zurmo::t('AccountsModule',         'Site  Classification',                             array(), null, $language),
                    'siteDetailCstm'                => Zurmo::t('AccountsModule',         'Site Detail',                             array(), null, $language),
                    'teryZipFormCstm'                => Zurmo::t('AccountsModule',         'Territory Zips Formatted',                             array(), null, $language),
                    'teryZipCstm'                => Zurmo::t('AccountsModule',         'Territory Zips',                             array(), null, $language),
                    'firstName'      => Zurmo::t('AccountsModule', 'Name', array(), null, $language),
                    'lastName'      => Zurmo::t('AccountsModule', 'Name', array(), null, $language),
                    'fullName'      => Zurmo::t('AccountsModule', 'Primary Contact Name', array(), null, $language),
                    'title'          => Zurmo::t('AccountsModule', 'Salutation', array(), null, $language),
                    'mobilePhone'    => Zurmo::t('AccountsModule', 'Mobile Phone', array(), null, $language),
                )
            );
        }

        public static function hasReadPermissionsSubscriptionOptimization()
        {
            return true;
        }

        public function setLatestActivityDateTime($dateTime)
        {
            assert('is_string($dateTime)');
            AuditUtil::saveOriginalAttributeValue($this, 'latestActivityDateTime', $dateTime);
            $this->unrestrictedSet('latestActivityDateTime', $dateTime);
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
        
        public static function getByOldAccountId($oldId) {
            return self::makeModels(ZurmoRedBean::find('account', "oldacctidcstm =:oldacctidcstm", array(':oldacctidcstm' => $oldId)));
        }
    }
?>