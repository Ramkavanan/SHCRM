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

    class Opportunity extends OwnedSecurableItem implements StarredInterface
    {
        public static function getByName($name)
        {
            return self::getByNameOrEquivalent('name', $name);
        }

        /**
         * @return value of what is considered the 'closed won' stage. It could be in the future named something else
         * or changed by the user.  This api will be expanded to handle that.  By default it will return 'Closed Won'
         */
        public static function getStageClosedWonValue()
        {
            return 'Closed Won';
        }

        protected function beforeSave()
        {
            if (parent::beforeSave())
            {
                // To skip the validation for the archive
                if(Yii::app()->controller->action->id == 'archive')
                    return true;
                // Ends here
                
                $automaticMappingDisabled = OpportunitiesModule::isAutomaticProbabilityMappingDisabled();
                if (!isset($this->originalAttributeValues['probability']) && $automaticMappingDisabled === false)
                {
                    $this->resolveStageToProbability();
                    $currencyHelper = Yii::app()->currencyHelper;
                     if (Yii::app()->controller->action->id == 'projectType' || Yii::app()->controller->action->id == 'recurringType' || Yii::app()->controller->action->id == 'copy' || Yii::app()->controller->action->id == 'edit') {
                           if ($this->account->id < 0 ) { 
                                $this->addError('account', Zurmo::t('Core', 'Account Name cannot be blank.'));
                                $currencyHelper->resetErrors();
                                return false;
                            } 
                       }
                    
                    if($this->stage->value == Constant::WON && $this->recordType->value == Constant::RECURRING) {
                        if ($this->expectedStartDate == '' && $this->aggregateGPM == '') {
                            $this->addError('expectedStartDate', Zurmo::t('Core', 'Expected Start Date cannot be blank.'));
                            $this->addError('aggregateGPM', Zurmo::t('Core', 'Gross Profit Margin cannot be blank.'));
                            $currencyHelper->resetErrors();
                            return false;
                        }
                        if ($this->expectedStartDate == '' ) {
                            $this->addError('expectedStartDate', Zurmo::t('Core', 'Expected Start Date cannot be blank.'));
                            $currencyHelper->resetErrors();
                            return false;
                        }
                        if ($this->aggregateGPM == '') {
                            $this->addError('aggregateGPM', Zurmo::t('Core', 'Gross Profit Margin cannot be blank.'));
                            $currencyHelper->resetErrors();
                            return false;
                        }
                        return true;		     
                    }  else if($this->stage->value == Constant::WON && $this->recordType->value == Constant::PROJECT) {
                        if ($this->expectedStartDate == '') {
                            $this->addError('expectedStartDate', Zurmo::t('Core', 'Expected Start Date cannot be blank.'));
                            $this->addError('aggregateGPM', Zurmo::t('Core', 'Gross Profit Margin cannot be blank.'));
                            $currencyHelper->resetErrors();
                            return false;
                        }
                        return true;
                    }
                    if ($this->stage->value == Constant::KICKOFF && $this->recordType->value == Constant::RECURRING){
                        if ($this->aggregateGPM == '') {
                            $this->addError('aggregateGPM', Zurmo::t('Core', 'Gross Profit Margin cannot be blank.'));
                            $currencyHelper->resetErrors();
                            return false;
                        }
                        return true;
                    }
                    if ($this->stage->value == Constant::SOLUTIONPHASE || $this->stage->value == Constant::ESTIMATE )  {
                        if ($this->estimatorApproval == 0 && ($this->estimator->id <= 0)) {
                            $this->addError('estimatorApproval', Zurmo::t('Core', 'Please Check Estimator Approval'));
                            $this->addError('estimator', Zurmo::t('Core', 'Please select Estimator'));
                            $currencyHelper->resetErrors();
                            return false;
                        } else if ($this->estimatorApproval == 0){
                            $this->addError('estimatorApproval', Zurmo::t('Core', 'Please Check Estimator Approval'));
                            $currencyHelper->resetErrors();
                            return false;
                        } else if ($this->estimator->id <= 0) {
                            $this->addError('estimator', Zurmo::t('Core', 'Please select Estimator'));
                            $currencyHelper->resetErrors();
                            return false;
                        } else if ($this->gm->id <= 0) {
                            $this->addError('gm', Zurmo::t('Core', 'Please select GM'));
                            $currencyHelper->resetErrors();
                            return false;
                        }
                        return true;
                    }
                   
                    if(Yii::app()->controller->action->id == 'projectType' || Yii::app()->controller->action->id == 'recurringType' || Yii::app()->controller->action->id == 'create' || Yii::app()->controller->action->id == 'edit'){
                        if ($this->stage->value == Constant::FINALPRICING){ 
                                $this->addError('stage', Zurmo::t('Core', 'Cannot be changed to Final Pricing stage manually'));
                                $currencyHelper->resetErrors();
                                return false;
                        }else if ($this->stage->value == Constant::FINALPROPOSAL){
                                $this->addError('stage', Zurmo::t('Core', 'Cannot be changed to Final Proposal stage manually'));
                                $currencyHelper->resetErrors();
                                return false;
                        }else if ($this->stage->value == Constant::AGREEMENT){
                                $this->addError('stage', Zurmo::t('Core', 'Cannot be changed to Agreement stage manually'));
                                $currencyHelper->resetErrors();
                                return false;
                        }
                    }
                    return true;
           	}
            }
            else
            {
                return false;
            }
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
            return 'OpportunitiesModule';
        }

        public static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language), array(
                'account'     => Zurmo::t('AccountsModule',    'AccountsModuleSingularLabel', $params, null, $language),
		'amount'      => Zurmo::t('OpportunitiesModule', 'Amount',  array(), null, $language),
                'closeDate'   => Zurmo::t('OpportunitiesModule', 'Close Date',  array(), null, $language),
		'agreement'   => Zurmo::t('AgreementsModule',  'AgreementsModuleSingularLabel', $params, null, $language),
		'Opportunity'   => Zurmo::t('OpportunitiesModule',  'Cloned From Opportunity', $params, null, $language),
		'description' => Zurmo::t('ZurmoModule',         'Description',  array(), null, $language),
                'contacts'    => Zurmo::t('ContactsModule',      'ContactsModulePluralLabel',   $params, null, $language),
                'probability' => Zurmo::t('OpportunitiesModule', 'Probability (%)',  array(), null, $language),
                'stage'       => Zurmo::t('ZurmoModule',         'Stage',  array(), null, $language),
                'source'      => Zurmo::t('ContactsModule',      'Source',   array(), null, $language),
                'nextStep'      => Zurmo::t('OpportunitiesModule', 'Next Step',  array(), null, $language),
                'name'        => Zurmo::t('Core',                'Opportunity',  array(), null, $language),
                'recordType'    => Zurmo::t('OpportunitiesModule', 'Record Type',  array(), null, $language),
                'expectedRevenue' => Zurmo::t('OpportunitiesModule', 'Expected Revenue',  array(), null, $language),
                'forecastCategory' => Zurmo::t('OpportunitiesModule', 'Forecast Category Name',  array(), null, $language),				
                'pricebook'    => Zurmo::t('OpportunitiesModule', 'Price Book',  array(), null, $language),
                'campaign'    => Zurmo::t('OpportunitiesModule', 'Primary Campaign Source',  array(), null, $language),
                'owner'     => Zurmo::t('OpportunitiesModule',      'Opportunity owner', $params, null, $language),
                'isPrivate'    => Zurmo::t('OpportunitiesModule', 'Private',  array(), null, $language),
                'totalOpportunityQuantity'    => Zurmo::t('OpportunitiesModule', 'Quantity',  array(), null, $language),
                'types'    => Zurmo::t('OpportunitiesModule', 'Type',  array(), null, $language),
                'forInternalUseOnly'    => Zurmo::t('OpportunitiesModule', 'Internal Use Notes',  array(), null, $language),
                'aggregateGPM'    => Zurmo::t('OpportunitiesModule', 'Aggregate GPM',  array(), null, $language),
                'agreementExecuted'    => Zurmo::t('OpportunitiesModule', 'Agreement Executed',  array(), null, $language),
                'amount4Import'    => Zurmo::t('OpportunitiesModule', 'Amount 4 Import',  array(), null, $language),
                'budget'    => Zurmo::t('OpportunitiesModule', 'Budget',  array(), null, $language),
                'changeOrder'    => Zurmo::t('OpportunitiesModule', 'Change Order',  array(), null, $language),
                'createAgreement'    => Zurmo::t('OpportunitiesModule', 'Create Agreement',  array(), null, $language),
                'daysSinceLastUpdateOrActivity'    => Zurmo::t('OpportunitiesModule', 'Days since last update or activity',  array(), null, $language),
                'estimator'      => Zurmo::t('OpportunitiesModule', 'Estimator',  array(), null, $language),
                'estimatorApproval' => Zurmo::t('OpportunitiesModule', 'Estimator Approval',  array(), null, $language),
                'estimatorApprovalDate'      => Zurmo::t('OpportunitiesModule', 'Approval Date',  array(), null, $language),
                'estimatorUser' => Zurmo::t('OpportunitiesModule', 'Estimator User',  array(), null, $language),
                'expectedStartDate'      => Zurmo::t('OpportunitiesModule', 'Expected Start Date',  array(), null, $language),
                'goals'      => Zurmo::t('OpportunitiesModule', 'Goals',  array(), null, $language),
                'fiscalPeriod'      => Zurmo::t('OpportunitiesModule', 'Fiscal Period',  array(), null, $language),
                'age'      => Zurmo::t('OpportunitiesModule', 'Age',  array(), null, $language),
                'grossProfitMargin'      => Zurmo::t('OpportunitiesModule', 'Gross Profit Margin',  array(), null, $language),
                'heritageProject'      => Zurmo::t('OpportunitiesModule', 'Heritage Project',  array(), null, $language),
                'intialTermLengths'      => Zurmo::t('OpportunitiesModule', 'Initial Term Length',  array(), null, $language),
                'loOpOldID4Import'      => Zurmo::t('OpportunitiesModule', 'LO OP Old ID 4 Import',  array(), null, $language),
                'managementPricingApproval'      => Zurmo::t('OpportunitiesModule', 'Pricing Approval',  array(), null, $language),
                'managementPricingApprovalDate'      => Zurmo::t('OpportunitiesModule', 'Approval Date',  array(), null, $language),
                'oldAccountID'      => Zurmo::t('OpportunitiesModule', 'Old Account ID',  array(), null, $language),
                'oldAgreementID'      => Zurmo::t('OpportunitiesModule', 'Old Agreement ID',  array(), null, $language),
                'oldOpportunityID'      => Zurmo::t('OpportunitiesModule', 'Old Opportunity ID',  array(), null, $language),
                'oldOwnerChangeback'      => Zurmo::t('OpportunitiesModule', 'Old Owner - Change back',  array(), null, $language),
                'oldPriceBook2Id'      => Zurmo::t('OpportunitiesModule', 'Old Price Book2 Id',  array(), null, $language),
                'oldRecordTypeID'      => Zurmo::t('OpportunitiesModule', 'Old Record Type ID',  array(), null, $language),
                'opportunityTypes'      => Zurmo::t('OpportunitiesModule', 'Opportunity Type',  array(), null, $language),
                'proposalTenderedDate'      => Zurmo::t('OpportunitiesModule', 'Tendered Date',  array(), null, $language),
                'reasonLost'      => Zurmo::t('OpportunitiesModule', 'Reason Lost',  array(), null, $language),
                'redFlag'      => Zurmo::t('OpportunitiesModule', 'RED FLAG',  array(), null, $language),
                'reportGPM'      => Zurmo::t('OpportunitiesModule', 'Report GPM',  array(), null, $language),
                'revenueMHR'      => Zurmo::t('OpportunitiesModule', 'Revenue / MHR',  array(), null, $language),
                'suggestedPrice'      => Zurmo::t('OpportunitiesModule', 'Suggested Price',  array(), null, $language),
                'totalDirectCosts'      => Zurmo::t('OpportunitiesModule', 'Total Direct Costs',  array(), null, $language),
                'totalMHR'      => Zurmo::t('OpportunitiesModule', 'Total MHR',  array(), null, $language),
				'archive'      => Zurmo::t('OpportunitiesModule', 'Archive',  array(), null, $language),                
				'finalAmount'      => Zurmo::t('OpportunitiesModule', 'Final Amount',  array(), null, $language),
                'meetings'    => Zurmo::t('MeetingsModule',      'MeetingsModulePluralLabel', $params, null, $language),
                'notes'       => Zurmo::t('NotesModule',         'NotesModulePluralLabel', $params, null, $language),
				'gm'      => Zurmo::t('OpportunitiesModule', 'GM',  array(), null, $language),
                'tasks'       => Zurmo::t('TasksModule',         'TasksModulePluralLabel', $params, null, $language),
                'status_changed_date'      => Zurmo::t('OpportunitiesModule', 'Won Date',  array(), null, $language),
                'oldFinalAmount'      => Zurmo::t('OpportunitiesModule', 'Old Final Amount',  array(), null, $language),
                'oldaggregateGPM'      => Zurmo::t('OpportunitiesModule', 'Old Aggregate GPM',  array(), null, $language),));            
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
                    'closeDate',
                    'description',
                    'name',
                    'probability',
                    'nextStep',
                    'recordType',
                    'pricebook',
                    'campaign',
                    'isPrivate',
                    'totalOpportunityQuantity',
                    'forInternalUseOnly',
                    'aggregateGPM',
                    'agreementExecuted',
                    'amount4Import',
                    'changeOrder',
                    'createAgreement',
                    'daysSinceLastUpdateOrActivity',
                    'estimatorApproval',
                    'estimatorApprovalDate',
                    'estimatorUser',
                    'expectedStartDate',
                    'goals',
                    'grossProfitMargin',
                    'heritageProject',
                    'loOpOldID4Import',
                    'managementPricingApproval',
                    'managementPricingApprovalDate',
                    'oldAccountID',
                    'oldAgreementID',
                    'oldOpportunityID',
                    'oldOwnerChangeback',
                    'oldPriceBook2Id',
                    'oldRecordTypeID',
                    'proposalTenderedDate',
                    'redFlag',
                    'reportGPM',
                    'revenueMHR',
                    'suggestedPrice',
                    'totalDirectCosts',
                    'totalMHR',
                    'fiscalPeriod',
                    'age',
                    'archive',
                    'status_changed_date',
                    'oldFinalAmount',
                    'oldaggregateGPM',
                    ),
                'relations' => array(
                    'recordType'    => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,RedBeanModel::LINK_TYPE_SPECIFIC, 'recordType'),
                    'account'       => array(static::HAS_ONE,   'Account'),
                    'amount'        => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'amount'),
                    'agreement'     => array(static::HAS_ONE,   'Agreement'),
                    'estimator'     => array(static::HAS_ONE,  'User', static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'Estimator'),
                    'gm'     => array(static::HAS_ONE,  'User', static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'GM'),
                    'expectedRevenue'=> array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'expectedRevenue'),
                    'forecastCategory' => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED, static::LINK_TYPE_SPECIFIC, 'forecastCategory'),
                    'source'        => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED, static::LINK_TYPE_SPECIFIC, 'source'),	
                    'stage'         => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED, static::LINK_TYPE_SPECIFIC, 'stage'),
                    'types'         => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED, static::LINK_TYPE_SPECIFIC, 'types'),
                    'intialTermLengths'  => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED, static::LINK_TYPE_SPECIFIC, 'intialTermLengths'),
                    'opportunityTypes'   => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED, static::LINK_TYPE_SPECIFIC, 'opportunityTypes'),
                    'reasonLost'         => array(static::HAS_ONE,   'OwnedCustomField', static::OWNED, static::LINK_TYPE_SPECIFIC, 'reasonLost'),
                    'products'      => array(static::HAS_MANY, 'Product'),
                    'contacts'      => array(static::MANY_MANY, 'Contact'),
                    'projects'      => array(static::MANY_MANY, 'Project'),
                    'budget'        => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'budget'),
                    'approvalProcess' => array(static::HAS_ONE, 'ApprovalProcess'),
                    'finalAmount'   => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'finalAmount'),
                    'revenueMHR' => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'revenueMHR'),
                    'suggestedPrice' => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'suggestedPrice'),
                    'totalDirectCosts' => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'totalDirectCosts'),
                    'opportunityProduct' => array(static::HAS_MANY, 'OpportunityProduct'),
                    'Opportunity'     => array(static::HAS_ONE,   'Opportunity'),
                ),
                'derivedRelationsViaCastedUpModel' => array(
                    'meetings' => array(static::MANY_MANY, 'Meeting', 'activityItems'),
                    'notes'    => array(static::MANY_MANY, 'Note',    'activityItems'),
                    'tasks'    => array(static::MANY_MANY, 'Task',    'activityItems'),
                ),
                'rules' => array(
                    array('closeDate',     'required'),
                    array('closeDate',     'type',      'type' => 'date'),
                    array('description',   'type',      'type' => 'string'),
                    array('archive',   'type',      'type' => 'string'),
                    array('name',          'required'),
                    array('name',          'type',      'type' => 'string'),
                    array('name',          'length',    'min'  => 1, 'max' => 64),
                    array('nextStep',      'type',      'type' => 'string'),
                    array('recordType',    'type',      'type' => 'string'),
                    array('opportunityTypes',   'type',      'type' => 'string'),
                    array('pricebook',    'type',      'type' => 'string'),
                    array('campaign',       'type',      'type' => 'string'),
                    array('probability',   'type',      'type' => 'integer'),
                    array('probability',   'numerical', 'min' => 0, 'max' => 100),
                    array('probability',   'required'),
                    array('probability',   'default',   'value' => 0),
                    array('probability',   'probability'),
                    array('stage',         'required'),
                    array('isPrivate',       'type',      'type' => 'boolean'),
                    array('totalOpportunityQuantity',   'type',      'type' => 'integer'),
                    array('forInternalUseOnly',       'type',      'type' => 'string'),
                    array('aggregateGPM',   'type',      'type' => 'float'),
                    array('aggregateGPM',   'numerical', 'min' => 0, 'max' => 100),
                    array('aggregateGPM',   'default',   'value' => 0),
                    array('agreementExecuted',       'type',      'type' => 'boolean'),
                    array('amount4Import',       'type',      'type' => 'string'),
                    array('budget',       'type',      'type' => 'float'),
                    array('budget',        'required'),
                    array('changeOrder',       'type',      'type' => 'boolean'),
                    array('createAgreement',       'type',      'type' => 'boolean'),
                    array('daysSinceLastUpdateOrActivity',       'type',      'type' => 'integer'),
                    array('estimatorApproval',       'type',      'type' => 'boolean'),
                    array('estimatorApprovalDate',       'type',      'type' => 'date'),
                    array('estimatorUser',       'type',      'type' => 'boolean'),
                    array('expectedStartDate',       'type',      'type' => 'date'),
                    array('goals',       'type',      'type' => 'string'),
                    array('grossProfitMargin',       'type',      'type' => 'string'),
                    array('heritageProject',       'type',      'type' => 'boolean'),
                    array('loOpOldID4Import',       'type',      'type' => 'boolean'),
                    array('managementPricingApproval',       'type',      'type' => 'boolean'),
                    array('managementPricingApprovalDate',       'type',      'type' => 'date'),
                    array('oldAccountID',       'type',      'type' => 'string'),
                    array('oldAccountID',          'length',     'max' => 100),
                    array('oldAgreementID',       'type',      'type' => 'string'),
                    array('oldAgreementID',          'length',     'max' => 100),
                    array('oldOpportunityID',       'type',      'type' => 'string'),
                    array('oldOpportunityID',          'length',     'max' => 100),
                    array('oldOwnerChangeback',       'type',      'type' => 'string'),
                    array('oldPriceBook2Id',       'type',      'type' => 'string'),
                    array('oldRecordTypeID',       'type',      'type' => 'string'),
                    array('proposalTenderedDate',        'type',      'type' => 'date'),
                    array('redFlag',         'type',     'type' => 'string'),
                    array('reportGPM',       'type',     'type' => 'string'),
                    array('totalMHR',        'type',     'type' => 'float'),
                    array('fiscalPeriod',          'type',      'type' => 'string'),
                    array('fiscalPeriod',          'length',     'max' => 100),
                    array('age',          'type',      'type' => 'integer'),
                    array('finalAmount',   'type',           'type'   => 'float'),      
                    array('revenueMHR',   'type',           'type'   => 'float'), 
                    array('suggestedPrice',   'type',           'type'   => 'float'), 
                    array('totalDirectCosts',   'type',           'type'   => 'float'),
                    array('status_changed_date',    'type',           'type'  => 'date'),
                    array('oldFinalAmount',   'type', 'type'   => 'float'),
                    array('oldaggregateGPM',   'type', 'type'   => 'float'),
                ),
                'elements' => array(
                    'account'     => 'Account',
                    'amount'      => 'CurrencyValue',
                    'closeDate'   => 'Date',
                    'agreement'   => 'Agreement',
                    'opportunity' => 'Opportunity',
                    'estimator'	=> 'User',
                    'gm'	=> 'User',
                    'description' => 'TextArea',
                    'oldAccountID' => 'Text',
                    'oldAgreementID' => 'Text',
                    'oldOpportunityID' => 'Text',
                    'probability' => 'Text', 
                    'expectedRevenue'  => 'CurrencyValue',
                    'nextStep'    => 'Text',
                    'pricebook'   => 'Text',
                    'campaign'    => 'Text',
                    'isPrivate'   => 'CheckBox',
                    'totalOpportunityQuantity'  => 'Integer',
                    'forInternalUseOnly'  => 'Text',
                    'aggregateGPM'  => 'Decimal',
                    'agreementExecuted'  => 'CheckBox',
                    'amount4Import'  => 'Text',
                    'budget'  => 'CurrencyValue',
                    'finalAmount'  => 'CurrencyValue',
                    'changeOrder'  => 'CheckBox',
                    'createAgreement'  => 'CheckBox',
                    'daysSinceLastUpdateOrActivity'  => 'Integer',
                    'estimatorApproval'  => 'CheckBox',
                    'estimatorApprovalDate'  => 'DateTime',
                    'estimatorUser'  => 'CheckBox',
                    'expectedStartDate'  => 'Date',
                    'goals'  => 'TextArea',
                    'grossProfitMargin'  => 'Text',
                    'heritageProject'  => 'CheckBox',
                    'loOpOldID4Import' => 'Text',
                    'managementPricingApproval'  => 'CheckBox',
                    'managementPricingApprovalDate'  => 'DateTime',
                    'proposalTenderedDate'  => 'Date',
                    'redFlag'  => 'Text',
                    'reportGPM'  => 'Text',
                    'revenueMHR'  => 'CurrencyValue',
                    'suggestedPrice'  => 'CurrencyValue',
                    'totalDirectCosts'  => 'CurrencyValue',
                    'totalMHR'  => 'Decimal',
                    'status_changed_date'  => 'Date',
                    'finalAmount'  => 'Decimal',
                    'oldaggregateGPM'  => 'Decimal',
                ),
                'customFields' => array(
                    'recordType'  => 'RecordTypes',
                    'stage'  => 'SalesStages',
                    'source' => 'LeadSources',
                    'forecastCategory' => 'ForecastCategoryNames',
                    'types' => 'Types',
                    'intialTermLengths' => 'IntialTermLengths',
                    'opportunityTypes' => 'OpportunityTypes',
                    'reasonLost' => 'ReasonLost',
                ),
                'defaultSortAttribute' => 'createdDateTime',
                'rollupRelations' => array(
                    'contacts',
                ),
                'noAudit' => array(
                    'description'
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
            return 'Opportunity';
        }

        public static function hasReadPermissionsOptimization()
        {
            return true;
        }

        public static function getGamificationRulesType()
        {
            return 'OpportunityGamification';
        }
        
        //For getting the oppertunity details
        public static function getOptById($id) {
			return self::makeModels(ZurmoRedBean::find('opportunity', "id =:id", array(':id' => $id)));
		}

        private function resolveStageToProbability()
        {
            if ($this->stage === null)
            {
                throw new NotSupportedException();
            }
            else
            {
                $this->probability = OpportunitiesModule::getProbabilityByStageValue($this->stage->value);
            }
        }
        
        public static function getTotalFinalAmt($stages, $whereCon='1=1')
        {
            
            if(!empty($stages)){
                $stages = implode("','",$stages);
                $query = "SELECT ROUND(SUM(IFNULL(t3.value,0))) finalamt FROM opportunity t1
                    INNER JOIN customfield t2 ON t1.stage_customfield_id = t2.id AND t2.value IN ('$stages')
                    INNER JOIN currencyvalue t3 ON t1.finalamount_currencyvalue_id =t3.id
                    WHERE $whereCon AND t1.archive IS NULL;";
                $totalFinalAmt = ZurmoRedBean::getAll($query);
                return $totalFinalAmt;
            }    
                
        }
        //For Updating the status changed date
        public static function updateOwnDate(){
            $query = "UPDATE opportunity op,(SELECT DATE(i.modifieddatetime) modified_date,op.id,op.status_changed_date         FROM opportunity op
                        INNER JOIN customfield cfs ON cfs.id=op.stage_customfield_id AND cfs.value='Won'
                        INNER JOIN ownedsecurableitem o ON o.id = op.ownedsecurableitem_id
                        INNER JOIN securableitem s ON s.id = o.securableitem_id
                        INNER JOIN item i ON i.id = s.item_id
                        WHERE status_changed_date IS NULL AND 
                        YEAR(i.modifieddatetime) = YEAR(CURDATE())) tmp
                        SET op.status_changed_date = tmp.modified_date 
                        WHERE op.id= tmp.id;";            
            return ZurmoRedBean::exec($query);
        }
                
        public static function getPipeLineReport()
        {
            $query = "SELECT sales_person,ROUND(SUM(project_amt),2) project_amt,ROUND(SUM(recurring_amt),2) recurring_amt FROM(SELECT 
                        u.id user_id,
                        CONCAT(p.firstname,' ',p.lastname) sales_person, 
                        IF(cfr.value='Project Final',cur.value,0) project_amt,
                        IF(cfr.value='Recurring Final',cur.value,0) recurring_amt
                     FROM opportunity op
                    INNER JOIN customfield cfs ON cfs.id=op.stage_customfield_id AND cfs.value='Final Proposal'
                    INNER JOIN currencyvalue cur ON cur.id=op.finalamount_currencyvalue_id
                    INNER JOIN customfield cfr ON cfr.id=op.recordtype_customfield_id
                    INNER JOIN ownedsecurableitem o ON o.id = op.ownedsecurableitem_id
                    INNER JOIN securableitem s ON s.id = o.securableitem_id
                    INNER JOIN item i ON i.id = s.item_id
                    INNER JOIN _user u ON u.id = i.createdbyuser__user_id
                    INNER JOIN person p ON p.id = u.person_id
                    WHERE archive IS NULL) T
                    GROUP BY user_id;";
            $pipeline = ZurmoRedBean::getAll($query);
            return $pipeline;
        }
        
        public static function getClosedSaleReport()
        {
            $query = "SELECT sales_person,ROUND(SUM(project_amt),2) project_amt,ROUND(SUM(recurring_amt),2) recurring_amt FROM(
                        SELECT 
                            u.id user_id,
                            CONCAT(p.firstname,' ',p.lastname) sales_person, 
                            IF(cfr.value='Project Final',cur.value,0) project_amt,
                            IF(cfr.value='Recurring Final',cur.value,0) recurring_amt
                         FROM opportunity op
                        INNER JOIN customfield cfs ON cfs.id=op.stage_customfield_id AND cfs.value='Won'
                        INNER JOIN currencyvalue cur ON cur.id=op.finalamount_currencyvalue_id
                        INNER JOIN customfield cfr ON cfr.id=op.recordtype_customfield_id
                        INNER JOIN ownedsecurableitem o ON o.id = op.ownedsecurableitem_id
                        INNER JOIN securableitem s ON s.id = o.securableitem_id
                        INNER JOIN item i ON i.id = s.item_id
                        INNER JOIN _user u ON u.id = i.createdbyuser__user_id
                        INNER JOIN person p ON p.id = u.person_id
                        WHERE archive IS NULL AND YEAR(status_changed_date) = YEAR(CURDATE())) T
                        GROUP BY user_id;";
            $closedSale = ZurmoRedBean::getAll($query);
            return $closedSale;
        }
        
        public static function getOppourtunityPipeLineReport($user_id='')
        {
            $cond = '';
            if($user_id)
                $cond = ' AND u.id = '.$user_id;
            
            $query = "SELECT op.name as oppName, op.id AS opp_id,u.id AS user_id, cfs.value AS stage, cfr.value AS Recordtype, CONCAT(p.firstname,' ',p.lastname) sales_person, cur.value AS price 
                    FROM opportunity op
                    INNER JOIN customfield cfs ON cfs.id=op.stage_customfield_id 
                    AND (cfs.value='Final Proposal' OR cfs.value='Consulting' OR cfs.value='Estimate')
                    INNER JOIN currencyvalue cur ON cur.id=op.finalamount_currencyvalue_id AND cur.value > 0
                    INNER JOIN customfield cfr ON cfr.id=op.recordtype_customfield_id    
                    INNER JOIN ownedsecurableitem o ON o.id = op.ownedsecurableitem_id
                    INNER JOIN securableitem s ON s.id = o.securableitem_id   
                    INNER JOIN item i ON i.id = s.item_id
                    INNER JOIN _user u ON u.id = i.createdbyuser__user_id             
                    INNER JOIN person p ON p.id = u.person_id
                    WHERE ARCHIVE IS NULL AND cfs.value IS NOT NULL". $cond;
            $returnArr = ZurmoRedBean::getAll($query);
            return $returnArr;
        }
    }
?>
