<?php

    /**
     * Agreement model have the elements and its relation as well as rules.
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class Agreement extends OwnedSecurableItem implements StarredInterface
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
            return 'AgreementsModule';
        }

        public static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language), array(
                'opportunity'   => Zurmo::t('OpportunitiesModule',  'OpportunitiesModuleSingularLabel', $params, null, $language),
                'account'     => Zurmo::t('AccountsModule',      'AccountsModuleSingularLabel', $params, null, $language),
                'owner'     => Zurmo::t('AgreementsModule',      'Agreement owner', $params, null, $language),
                'meetings'    => Zurmo::t('MeetingsModule',      'MeetingsModulePluralLabel', $params, null, $language),
                'notes'       => Zurmo::t('NotesModule',         'NotesModulePluralLabel', $params, null, $language),
                'tasks'       => Zurmo::t('TasksModule',         'TasksModulePluralLabel', $params, null, $language),
		'ActivatedBy'		=> Zurmo::t('AgreementsModule', 'Activated By',  $params, null, $language),
                'CompanySigned'		=> Zurmo::t('AgreementsModule', 'Company Signed By',  $params, null, $language),
                'archive'		=> Zurmo::t('AgreementsModule', 'Archive',  $params, null, $language),
                'CustomerSigned'	=> Zurmo::t('AgreementsModule', 'Customer Signed By',  $params, null, $language),
                'Account_Manager'	=> Zurmo::t('AgreementsModule', 'Account Manager',  $params, null, $language),
                'Estimator'		=> Zurmo::t('AgreementsModule', 'Estimator',  $params, null, $language),
                'Initial_Sales_Rep'	=> Zurmo::t('AgreementsModule', 'Initial Sales Representive',  $params, null, $language),
                'BillingAddress'	=> Zurmo::t('AgreementsModule', 'Billing Address',  $params, null, $language),
                'ShippingAddress'	=> Zurmo::t('AgreementsModule', 'Shipping Address',  $params, null, $language),
                'ActivatedDate'		=> Zurmo::t('AgreementsModule', 'Activated Date',  $params, null, $language),
                'CompanySignedDate'	=> Zurmo::t('AgreementsModule', 'Company Signed Date',  $params, null, $language),
                'EndDate'		=> Zurmo::t('AgreementsModule', 'Agreement End Date',  $params, null, $language),
                'name'			=> Zurmo::t('AgreementsModule', 'Agreement Name',  $params, null, $language),
                'RecordType'		=> Zurmo::t('AgreementsModule', 'Agreement Record Type',  $params, null, $language),
                'StartDate'		=> Zurmo::t('AgreementsModule', 'Agreement Start Date',  $params, null, $language),
                'ContractTerm'		=> Zurmo::t('AgreementsModule', 'Agreement Term (Months)',  $params, null, $language),
                'CustomerSignedDate'	=> Zurmo::t('AgreementsModule', 'Customer Signed Date',  $params, null, $language),
                'CustomerSignedTitle'	=> Zurmo::t('AgreementsModule', 'Customer Signed Title',  $params, null, $language),
                'Description'		=> Zurmo::t('AgreementsModule', 'Description',  $params, null, $language),
                'OwnerExpirationNotice'	=> Zurmo::t('AgreementsModule', 'Owner Expiration Notice',  $params, null, $language),
                'Pricebook2'		=> Zurmo::t('AgreementsModule', 'Price Book',  $params, null, $language),
                'SpecialTerms'		=> Zurmo::t('AgreementsModule', 'Special Terms',  $params, null, $language),
                'Status'		=> Zurmo::t('AgreementsModule', 'Status',  $params, null, $language),
                'Proposal_OID'		=> Zurmo::t('AgreementsModule', '(Do Not Edit This) ',  $params, null, $language),
                'Agreement_Temp_ID'	=> Zurmo::t('AgreementsModule', '(For Internal Use Only)',  $params, null, $language),
                'Agreement_Type'	=> Zurmo::t('AgreementsModule', 'Agreement Type',  $params, null, $language),
                'Anticipated_Start_Date'	=> Zurmo::t('AgreementsModule', 'Anticipated Start Date',  $params, null, $language),
                'Clone_Approval'	=> Zurmo::t('AgreementsModule', 'Clone Approval',  $params, null, $language),
                'Agreement'		=> Zurmo::t('AgreementsModule', 'Cloned From',  $params, null, $language),
                'Clone_Process'		=> Zurmo::t('AgreementsModule', 'Clone Process',  $params, null, $language),
                'Clone_Process_Email'	=> Zurmo::t('AgreementsModule', 'Clone Process Email',  $params, null, $language),
                'Contract_Number'	=> Zurmo::t('AgreementsModule', 'Agreement Number',  $params, null, $language),
                'Current_Annual_Amount'	=> Zurmo::t('AgreementsModule', 'Current Annual Amount',  $params, null, $language),
                'Current_GPM'		=> Zurmo::t('AgreementsModule', 'Agreement GPM',  $params, null, $language),
                'newCurrent_GPM'		=> Zurmo::t('AgreementsModule', 'Current GPM',  $params, null, $language),
                'Customer_Signed_Value'	=> Zurmo::t('AgreementsModule', 'Customer Signed Value',  $params, null, $language),
                'Date_of_First_Service'	=> Zurmo::t('AgreementsModule', 'Date of First Service',  $params, null, $language),
                'Deactivate'		=> Zurmo::t('AgreementsModule', 'Deactivate',  $params, null, $language),
                'Deactivation_Date'	=> Zurmo::t('AgreementsModule', 'Deactivation Date',  $params, null, $language),
                'Estimator_Approval'	=> Zurmo::t('AgreementsModule', 'Estimator Approval',  $params, null, $language),
                'Estimator_Approval_Date'	=> Zurmo::t('AgreementsModule', 'Estimator Approval Date',  $params, null, $language),
                'Estimator_Auto_Approval'	=> Zurmo::t('AgreementsModule', 'Estimator Auto Approval ',  $params, null, $language),
                'Evergreen'		=> Zurmo::t('AgreementsModule', 'Evergreen',  $params, null, $language),
                'First_Year_Amount'	=> Zurmo::t('AgreementsModule', 'First Year Amount',  $params, null, $language),
                'GPM_Change'		=> Zurmo::t('AgreementsModule', 'GPM Change',  $params, null, $language),
                'Hours_Remaining_MHR'	=> Zurmo::t('AgreementsModule', 'Hours Remaining MHR',  $params, null, $language),
                'LO_AG_ID_Old'		=> Zurmo::t('AgreementsModule', 'LO AG ID Old',  $params, null, $language),
                'Management_Approval'	=> Zurmo::t('AgreementsModule', 'Management Approval',  $params, null, $language),
                'Management_Approval_Date'	=> Zurmo::t('AgreementsModule', 'Management Approval Date',  $params, null, $language),
                'Old_Agreement_ID'	=> Zurmo::t('AgreementsModule', 'Old Agreement ID',  $params, null, $language),
                'Old_Agreement_Number'	=> Zurmo::t('AgreementsModule', 'Old Agreement Number',  $params, null, $language),
                'Previous_Amount'	=> Zurmo::t('AgreementsModule', 'Previous Amount',  $params, null, $language),
                'Previous_GPM'		=> Zurmo::t('AgreementsModule', 'Previous GPM',  $params, null, $language),
                'Price_Change'		=> Zurmo::t('AgreementsModule', 'Price Change',  $params, null, $language),
                'Project_Agreement_Amount'	=> Zurmo::t('AgreementsModule', 'Project Agreement Amount',  $params, null, $language),
                'Proposed_Gross_Profit_Margin'	=> Zurmo::t('AgreementsModule', 'Proposed Gross Profit Margin',  $params, null, $language),
                'Agreement_Expiration'	=> Zurmo::t('AgreementsModule', 'Renewal Date',  $params, null, $language),
                'Role_Match'		=> Zurmo::t('AgreementsModule', 'Role Match',  $params, null, $language),
                'Sales_Rep'		=> Zurmo::t('AgreementsModule', 'Sales Rep',  $params, null, $language),
                'Set_Owner_to_Creator'	=> Zurmo::t('AgreementsModule', 'Set Owner to Creator',  $params, null, $language),
                'Total_Agreement_Products'	=> Zurmo::t('AgreementsModule', 'Total Agreement Products',  $params, null, $language),
                'Total_Available_MHR'	=> Zurmo::t('AgreementsModule', 'Total Remaining MHR',  $params, null, $language),
                'Total_Direct_Costs'	=> Zurmo::t('AgreementsModule', 'Total Direct Costs',  $params, null, $language),
                'Total_Non_Agreement_Products'	=> Zurmo::t('AgreementsModule', 'Total Non-Agreement Products',  $params, null, $language),
                'Total_Products_Tracked'	=> Zurmo::t('AgreementsModule', 'Total Products Tracked',  $params, null, $language),
                'URL_Host'		=> Zurmo::t('AgreementsModule', 'URL Host',  $params, null, $language),
                'URL_Name'		=> Zurmo::t('AgreementsModule', 'URL Name',  $params, null, $language),
                'Website'		=> Zurmo::t('AgreementsModule', 'Website',  $params, null, $language),
                'XREF'			=> Zurmo::t('AgreementsModule', 'XREF',  $params, null, $language),
                'Year_to_Date_MHR'	=> Zurmo::t('AgreementsModule', 'Year to Date MHR',  $params, null, $language),
                'Total_MHR'	=> Zurmo::t('AgreementsModule', 'Total MHR',  $params, null, $language),
                'Rev_MH'	=> Zurmo::t('AgreementsModule', 'Rev/MH',  $params, null, $language),
                'Total_Equipment'        => Zurmo::t('AgreementsModule', 'Total Equipment',  $params, null, $language),
                'Available_Equipment'    => Zurmo::t('AgreementsModule', 'Total Remaining Equipment',  $params, null, $language),
                'Total_Material'         => Zurmo::t('AgreementsModule', 'Total Material',  $params, null, $language),
                'Available_Material'     => Zurmo::t('AgreementsModule', 'Total Remaining Material',  $params, null, $language),
                'MHR_Used_Percentage'       => Zurmo::t('AgreementsModule', '% Used MHR',  $params, null, $language),      
                'Material_Year_To_Date'     => Zurmo::t('AgreementsModule', 'Year to Date Units(M)',  $params, null, $language),
                'Material_Used_Percentage'  => Zurmo::t('AgreementsModule', '% Used Units (M)',  $params, null, $language),
                'Equipment_Year_To_Date'    => Zurmo::t('AgreementsModule', 'Year to Date Units(E)',  $params, null, $language),
                'Equipment_Used_Percentage' => Zurmo::t('AgreementsModule', '% Used Units (E)',  $params, null, $language),
                'Cumulative_Year_to_Date_MHR'    => Zurmo::t('AgreementsModule', 'Cumulative Year to Date MHR',  $params, null, $language),
                'Cumulative_Year_to_Date_Material' => Zurmo::t('AgreementsModule', 'Cumulative Year to Date Units(M)',  $params, null, $language),
                'Cumulative_Year_to_Date_Equipment'=> Zurmo::t('AgreementsModule', 'Cumulative Year to Date Units(E)',  $params, null, $language),
                'Non_Agmt_Prod_Direct_Cost'         => Zurmo::t('AgreementsModule', 'Non Agreement Products Direct Cost',  $params, null, $language),
                'budget'    => Zurmo::t('AgreementsModule', 'Budget',  array(), null, $language),
                'suggestedPrice'      => Zurmo::t('AgreementsModule', 'Suggested Price',  array(), null, $language),
                'status_changed_date'	=> Zurmo::t('AgreementsModule', 'Completion Date',  $params, null, $language),
            ));
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
			'ActivatedDate',
			'CompanySignedDate',
			'EndDate',
			'RecordType',
			'StartDate',
			'ContractTerm',
			'CustomerSignedDate',
			'CustomerSignedTitle',
			'Description',
			'Pricebook2',
			'SpecialTerms',
			'Proposal_OID',
			'Agreement_Temp_ID',
			'Anticipated_Start_Date',
			'Clone_Approval',
			'Clone_Process',
			'Contract_Number',
			'Current_GPM',
                        'newCurrent_GPM',
			'Date_of_First_Service',
			'Deactivate',
			'Deactivation_Date',
			'Estimator_Approval',
			'Estimator_Approval_Date',
			'Estimator_Auto_Approval',
			'Evergreen',
			'GPM_Change',
			'Hours_Remaining_MHR',
			'LO_AG_ID_Old',
			'Management_Approval',
			'Management_Approval_Date',
			'Old_Agreement_ID',
			'Old_Agreement_Number',
			'Previous_GPM',
			'Proposed_Gross_Profit_Margin',
			'Agreement_Expiration',
			'Role_Match',
			'Set_Owner_to_Creator',
			'Total_Agreement_Products',
			'Total_Available_MHR',
			'Total_Direct_Costs',
			'Total_Non_Agreement_Products',
			'Total_Products_Tracked',
			'URL_Host',
			'URL_Name',
			'Website',
			'XREF',
			'Year_to_Date_MHR',
			'Rev_MH',
			'Total_MHR',
                        'Used_MHR',
                        'Total_Equipment',
                        'Used_Equipment',
                        'Available_Equipment',
                        'Total_Material',
                        'Used_Material',
                        'Available_Material',
                        'MHR_Used_Percentage',
                        'Material_Year_To_Date',
                        'Material_Used_Percentage',
                        'Equipment_Year_To_Date',
                        'Equipment_Used_Percentage',
                        'Cumulative_Year_to_Date_MHR',
                        'Cumulative_Year_to_Date_Material',
                        'Cumulative_Year_to_Date_Equipment',
                        'Non_Agmt_Prod_Direct_Cost',
                        'suggestedPrice',
                        'archive',
                        'jobScheduled',
                        'status_changed_date',
                        'reset_count',
                        'avg_gpm',
                ),
                'relations' => array(
                    'opportunity'     => array(static::HAS_ONE,   'Opportunity'),
                    'jobscheduling'      => array(static::HAS_MANY, 'JobScheduling'),
                    'account'             => array(RedBeanModel::HAS_ONE,   'Account'),
                    'ActivatedBy'  => array(static::HAS_ONE,  'User', static::NOT_OWNED,static::LINK_TYPE_SPECIFIC, 'ActivatedBy'),
                    'agreementProduct'             => array(RedBeanModel::HAS_MANY,   'AgreementProduct'),
                    'agreementtracking'             => array(RedBeanModel::HAS_MANY,   'AgreementTracking'),
                    'BillingAddress' => array(RedBeanModel::HAS_ONE,   'Address',          RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'BillingAddress'),
                    //'CompanySigned'  => array(static::HAS_ONE,  'User', static::NOT_OWNED,
                      //           static::LINK_TYPE_SPECIFIC, 'CompanySigned'),
                    'CompanySigned'  => array(static::HAS_ONE,  'User', static::NOT_OWNED,
                                 static::LINK_TYPE_SPECIFIC, 'CompanySigned'),
                    'CustomerSigned'  => array(static::HAS_ONE,  'Contact', static::NOT_OWNED,
                                 static::LINK_TYPE_SPECIFIC, 'CustomerSigned'),
                    'ShippingAddress' => array(RedBeanModel::HAS_ONE,   'Address',          RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'ShippingAddress'),
                    'Account_Manager'  => array(static::HAS_ONE,  'User', static::NOT_OWNED,
                                 static::LINK_TYPE_SPECIFIC, 'Account_Manager'),
                    'Estimator'  => array(static::HAS_ONE,  'User', static::NOT_OWNED,
                                 static::LINK_TYPE_SPECIFIC, 'Estimator'),
                    'Initial_Sales_Rep' => array(static::HAS_ONE,  'User', static::NOT_OWNED,
                                 static::LINK_TYPE_SPECIFIC, 'Initial_Sales_Rep'),
                    'Sales_Rep' => array(static::HAS_ONE,  'User', static::NOT_OWNED,
                                 static::LINK_TYPE_SPECIFIC, 'Sales_Rep'),
                    'OwnerExpirationNotice' => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'OwnerExpirationNotice'),
                    'Status' => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Status'),
                    'Agreement_Type' => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Agreement_Type'),
                    'Agreement'             => array(RedBeanModel::HAS_ONE,   'Agreement'),
                    'Clone_Process_Email'   => array(RedBeanModel::HAS_ONE,   'Email',            RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Clone_Process_Email'),
                    'Current_Annual_Amount'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Current_Annual_Amount'),
                    'Customer_Signed_Value'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Customer_Signed_Value'),
                    'First_Year_Amount'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'First_Year_Amount'),
                    'Previous_Amount'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Previous_Amount'),
                    'Price_Change'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Price_Change'),
                    'Project_Agreement_Amount'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Project_Agreement_Amount'),
                    'Total_Direct_Costs'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Total_Direct_Costs'),
                    'Rev_MH'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Rev_MH'),
                    'Non_Agmt_Prod_Direct_Cost'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED,
                                            RedBeanModel::LINK_TYPE_SPECIFIC, 'Non_Agmt_Prod_Direct_Cost'),
                    'budget'        => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'budget'),
                    'suggestedPrice' => array(static::HAS_ONE,   'CurrencyValue',    static::OWNED, static::LINK_TYPE_SPECIFIC, 'suggestedPrice'),
                    'route'         => array(static::MANY_MANY,  'Route'),
                ),
                'derivedRelationsViaCastedUpModel' => array(
                    'meetings' => array(static::MANY_MANY, 'Meeting', 'activityItems'),
                    'notes'    => array(static::MANY_MANY, 'Note',    'activityItems'),
                    'tasks'    => array(static::MANY_MANY, 'Task',    'activityItems'),
                ),
                'rules' => array(
                   	array('account',        'required'),
                        array('archive',          'type',      'type' => 'string'),
                        array('name',          'required'),
                        array('name',          'type',      'type' => 'string'),
                        array('name',          'length',    'min'  => 1, 'max' => 64),
			array('ActivatedDate', 'type', 'type' => 'datetime'),
			array('CompanySignedDate', 'type', 'type' => 'datetime'),
			array('EndDate', 'type', 'type' => 'datetime'),
			array('RecordType',           'type',           'type'  => 'string'),
                    	array('RecordType',           'length',         'max'   => 100),
			array('StartDate', 'type', 'type' => 'date'),
			//array('StartDate',        'required'),
			array('Status',        'required'),
			array('ContractTerm', 'type', 'type' => 'integer'),
			array('ContractTerm',        'required'),
			array('CustomerSignedDate', 'type', 'type' => 'datetime'),
			array('CustomerSignedTitle', 'type', 'type' => 'string'),
			array('Description',    'type',           'type'  => 'string'),
			array('Pricebook2',    'type',           'type'  => 'string'),
			array('SpecialTerms',    'type',           'type'  => 'string'),
			array('Proposal_OID',    'type',           'type'  => 'string'),
			array('Agreement_Temp_ID',    'type',           'type'  => 'string'),
			array('Anticipated_Start_Date',    'type',           'type'  => 'datetime'),
			array('Clone_Approval',    'type',           'type'  => 'boolean'),
			array('Clone_Process',    'type',           'type'  => 'string'),
			array('Contract_Number',    'type',           'type'  => 'string'),
			array('Contract_Number',    'length',         'max'   => 100),
                        array('Current_GPM',        'required'),
                        array('Current_GPM',   'type',      'type' => 'float'),
                        array('Current_GPM',   'numerical', 'min' => 0, 'max' => 100),
                        array('Current_GPM',   'default',   'value' => 0),
                        array('Current_GPM',   'numerical',      'precision' => 2),
                        array('newCurrent_GPM',   'type',      'type' => 'float'),
                       // array('newCurrent_GPM',   'numerical', 'min' => 0),
                       // array('newCurrent_GPM',   'default',   'value' => 100),
                       // array('newCurrent_GPM',   'numerical',      'precision' => 2),
			array('Project_Agreement_Amount',        'required'),
			array('Date_of_First_Service',    'type',           'type'  => 'date'),
			array('Deactivate',    'type',           'type'  => 'boolean'),
			array('Deactivation_Date',    'type',           'type'  => 'date'),
			array('Estimator_Approval',    'type',           'type'  => 'boolean'),
			array('Estimator_Approval_Date',    'type',           'type'  => 'datetime'),
			array('Estimator_Auto_Approval',    'type',           'type'  => 'boolean'),
			array('Evergreen',    'type',           'type'  => 'boolean'),
			array('GPM_Change',    'type',           'type'  => 'float'),
			array('GPM_Change',   'length',         'max'   => 5),
			array('GPM_Change',   'numerical',      'precision' => 2),
			array('Hours_Remaining_MHR',    'type',           'type'  => 'float'),
			array('Hours_Remaining_MHR',   'length',         'max'   => 18),
			array('Hours_Remaining_MHR',   'numerical',      'precision' => 4),
			array('LO_AG_ID_Old',    'type',           'type'  => 'string'),
			array('Management_Approval',    'type',           'type'  => 'boolean'),
			array('Management_Approval_Date',    'type',           'type'  => 'datetime'),
			array('Old_Agreement_ID',    'type',           'type'  => 'string'),
			array('Old_Agreement_Number',    'type',           'type'  => 'string'),
                        array('Old_Agreement_Number',   'length',         'max'   => 10),
			array('Previous_GPM',    'type',           'type'  => 'float'),
			array('Previous_GPM',   'length',         'max'   => 5),
			array('Previous_GPM',   'numerical',      'precision' => 2),
			array('Proposed_Gross_Profit_Margin',    'type',           'type'  => 'float'),
			array('Proposed_Gross_Profit_Margin',   'length',         'max'   => 18),
			array('Proposed_Gross_Profit_Margin',   'numerical',      'precision' => 2),
            //array('Agreement_Expiration',    'required'),
			array('Agreement_Expiration',    'type',           'type'  => 'date'),
			array('Role_Match',    'type',           'type'  => 'float'),
			array('Set_Owner_to_Creator',    'type',           'type'  => 'boolean'),
			array('Total_Agreement_Products',    'type',           'type'  => 'float'),
			array('Total_Available_MHR',    'type',           'type'  => 'float'),
			array('Total_Direct_Costs',    'type',           'type'  => 'float'),
			array('Total_MHR',    'type',           'type'  => 'float'),
                        array('Used_MHR',    'type',           'type'  => 'float'),
			array('Rev_MH',    'type',           'type'  => 'float'),
			array('Total_Non_Agreement_Products',    'type',           'type'  => 'float'),
			array('Total_Products_Tracked',    'type',           'type'  => 'float'),
			array('URL_Host',    'type',           'type'  => 'string'),
			array('URL_Name',    'type',           'type'  => 'string'),
			array('Website',     'url',     'defaultScheme' => 'http'),
			array('XREF',    'type',           'type'  => 'integer'),
			array('Year_to_Date_MHR',    'type', 'type'  => 'float'),
                        array('Total_Equipment',    'type', 'type'  => 'float'),
                        array('Used_Equipment',    'type', 'type'  => 'float'),
                        array('Available_Equipment',    'type', 'type'  => 'float'),
                        array('Total_Material',    'type', 'type'  => 'float'),
                        array('Used_Material',    'type', 'type'  => 'float'),
                        array('Available_Material',    'type', 'type'  => 'float'),   
                        array('MHR_Used_Percentage',    'type', 'type'  => 'float'),
                        array('Material_Year_To_Date',    'type', 'type'  => 'float'),
                        array('Material_Used_Percentage',    'type', 'type'  => 'float'),
                        array('Equipment_Year_To_Date',    'type', 'type'  => 'float'),
                        array('Equipment_Used_Percentage',    'type', 'type'  => 'float'),
                        array('Cumulative_Year_to_Date_MHR',    'type', 'type'  => 'float'),
                        array('Cumulative_Year_to_Date_Material',    'type', 'type'  => 'float'),
                        array('Cumulative_Year_to_Date_Equipment',    'type', 'type'  => 'float'),
                        array('Non_Agmt_Prod_Direct_Cost',    'type',           'type'  => 'float'),
                        array('Available_Material',    'type', 'type'  => 'float'),
                        array('suggestedPrice',   'type',           'type'   => 'float'),
                        array('budget',       'type',      'type' => 'float'),
                        array('budget',        'required'),
                        array('jobScheduled',          'type',      'type' => 'string'),
                        array('status_changed_date',    'type',           'type'  => 'date'),
                        array('avg_gpm',       'type',      'type' => 'float'),
                        array('reset_count',       'type',      'type' => 'integer'),
                ),
                'elements' => array(
                    'opportunity'   => 'Opportunity',
                    'account'		=> 'Account',
                    'ActivatedBy'		=> 'User',
                    'CompanySigned'		=> 'User',
                    'CustomerSigned'	=> 'Contact',
                    'Account_Manager'	=> 'User',
                    'Estimator'		=> 'User',
                    'Initial_Sales_Rep'	=> 'User',
                    'BillingAddress'	=> 'Address',
                    'ShippingAddress'	=> 'Address',
                    'name'			=> 'Text',
                    'ActivatedDate'		=> 'DateTime',
                    'CompanySignedDate'	=> 'DateTime',
                    'EndDate'		=> 'DateTime',
                    'RecordType'		=> 'Text',
                    'StartDate'		=> 'Date',
                    'ContractTerm'		=> 'Integer',
                    'CustomerSignedDate'	=> 'DateTime',
                    'CustomerSignedTitle'	=> 'Text',
                    'Description'		=> 'TextArea',
                    'Pricebook2'		=> 'Text',
                    'SpecialTerms'		=> 'TextArea',
                    'Proposal_OID'		=> 'Text',
                    'Agreement_Temp_ID'	=> 'Text',
                    'Anticipated_Start_Date'	=> 'DateTime',
                    'Clone_Approval'	=> 'CheckBox',
                    'Agreement'		=> 'Agreement',
                    'Clone_Process'		=> 'Text',
                    'Clone_Process_Email'	=> 'EmailAddressInformation',
                    'Contract_Number'	=> 'Text',
                    'Current_Annual_Amount'	=> 'CurrencyValue',
                    'Current_GPM'		=> 'Decimal',
                    'newCurrent_GPM'		=> 'Decimal',
                    'Customer_Signed_Value'	=> 'CurrencyValue',
                    'Date_of_First_Service'	=> 'Date',
                    'Deactivate'		=> 'CheckBox',
                    'Deactivation_Date'	=> 'Date',
                    'Estimator_Approval'	=> 'CheckBox',
                    'Estimator_Approval_Date'	=> 'DateTime',
                    'Estimator_Auto_Approval'	=> 'CheckBox',
                    'Evergreen'		=> 'CheckBox',
                    'First_Year_Amount'	=> 'CurrencyValue',
                    'GPM_Change'		=> 'Decimal',
                    'Hours_Remaining_MHR'	=> 'Decimal',
                    'LO_AG_ID_Old'		=> 'Text',
                    'Management_Approval'	=> 'CheckBox',
                    'Management_Approval_Date'	=> 'DateTime',
                    'Old_Agreement_ID'	=> 'Text',
                    'Old_Agreement_Number'	=> 'Text',
                    'Previous_Amount'	=> 'CurrencyValue',
                    'Previous_GPM'		=> 'Decimal',
                    'Price_Change'		=> 'CurrencyValue',
                    'Project_Agreement_Amount'	=> 'CurrencyValue',
                    'Proposed_Gross_Profit_Margin'	=> 'Decimal',
                    'Agreement_Expiration'	=> 'Date',
                    'Role_Match'		=> 'Decimal',
                    'Sales_Rep'		=> 'User',
                    'Set_Owner_to_Creator'	=> 'CheckBox',
                    'Total_Agreement_Products'	=> 'Decimal',
                    'Total_Available_MHR'	=> 'Decimal',
                    'Total_Direct_Costs'	=> 'CurrencyValue',
                    'Total_Non_Agreement_Products'	=> 'Decimal',
                    'Total_Products_Tracked'	=> 'Decimal',
                    'Total_MHR'	=> 'Decimal',
                    'Used_MHR'	=> 'Decimal',
                    'Rev_MH'	=> 'CurrencyValue',
                    'URL_Host'		=> 'Text',
                    'URL_Name'		=> 'Text',
                    'XREF'			=> 'Integer',
                    'Year_to_Date_MHR'	=> 'Decimal',
                    'Total_Equipment' => 'Decimal',
                    'Used_Equipment' => 'Decimal',
                    'Available_Equipment' => 'Decimal',
                    'Total_Material' => 'Decimal',
                    'Used_Material' => 'Decimal',
                    'Available_Material' => 'Decimal',
                    'MHR_Used_Percentage' => 'Decimal',
                    'Material_Year_To_Date' => 'Decimal',
                    'Material_Used_Percentage' => 'Decimal',
                    'Equipment_Year_To_Date' => 'Decimal',
                    'Equipment_Used_Percentage' => 'Decimal',
                    'Cumulative_Year_to_Date_MHR' => 'Decimal',
                    'Cumulative_Year_to_Date_Material' => 'Decimal',
                    'Cumulative_Year_to_Date_Equipment' => 'Decimal',
                    'Non_Agmt_Prod_Direct_Cost'	=> 'CurrencyValue',
                    'budget'  => 'CurrencyValue',
                    'suggestedPrice'  => 'CurrencyValue',
                    'status_changed_date'	=> 'Date',
                ),
                'customFields' => array(
                    'OwnerExpirationNotice'   => 'OwnerExpirationNotices',
		    'Status'		      => 'AgreementStatus',
		    'Agreement_Type' 	      => 'AgreementTypes',
                ),
                'defaultSortAttribute' => 'createdDateTime',
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
            return 'Agreement';
        }

        public static function hasReadPermissionsOptimization()
        {
            return true;
        }

        public function save($runValidation = true, array $attributeNames = null) { 
            if($this->Contract_Number == null) {
                /**
                * Comment by Sundar P - 10-Sep-2016
                * We can update the contract_number in agreements default controller after successfull save.  
                parent::save(false, $attributeNames);
                $this->setTreatCurrentUserAsOwnerForPermissions(true);
                $this->Contract_Number = 'Agmnt-'.$this->id;
                **/
                return parent::save(false, $attributeNames);
            } else {
                return parent::save(false, $attributeNames);
            }            
        }
        
        public static function getAllRecurringActiveAgmt($page, $limit=2, $name='',$agmt_ids='')
        {
            if($name != '')
            {
                $name = ' AND name LIKE \'%'.$name.'%\'';
            }
            else
            {
                $name = '';
            }
            
            $active_ids_arr = self::getActiveCustomId();
            if($active_ids_arr)            
                return ZurmoRedBean::find('agreement', "recordtype =:recordtype AND status_customfield_id IN (".$active_ids_arr.") AND id IN (".implode(',', $agmt_ids).")".$name." ORDER BY id LIMIT ".(($page-1)*$limit).", ".$limit, array(":recordtype" => "Recurring Agreement"));
            else
                return;
        }
        
        public static function getAllRecurringActiveAgmtCount($agmt_ids=''){
            $active_ids_arr = self::getActiveCustomId();
            if($active_ids_arr)
                return ZurmoRedBean::find('agreement', "recordtype =:recordtype AND status_customfield_id IN (".$active_ids_arr.") AND id IN (".implode(',', $agmt_ids).") ORDER BY id", array(":recordtype" => "Recurring Agreement"));
            else
                return;
        }
        
        public static function getRecurringActiveAgrementsById($agreementIds){
            
            $data = Yii::app()->db->createCommand()->select('*')->from('agreement')->where(array('in', 'id', array(3036,3034)))->queryAll();
            return  ZurmoRedBean::query($data);
        }
                
        public static function getByInId($ids)
        {
           $query = "SELECT id, name FROM agreement where id IN (".implode(',',$ids).") ";
           return  ZurmoRedBean::getAll($query);
        }
        
        public static function getActiveCustomId($type='')
        {
            $query = "SELECT id FROM customfield where value='Active'";
            $active_id_arr = ZurmoRedBean::getAll($query);           
            if(count($active_id_arr))
            {
                $active_id_str = '';
                foreach($active_id_arr as $active_id)
                {
                    $active_id_str[] = $active_id['id'];
                }
                
                if($type == 'array')
                    return $active_id_str;
                else
                    return implode(',', $active_id_str);
            }
            else
                return;
        }
        
        public static function getActiveDraftCustomId($type='')
        {
            $query = "SELECT id FROM customfield where value='Active' OR value='Draft'";
            $active_id_arr = ZurmoRedBean::getAll($query);
            if(count($active_id_arr))
            {
                $active_id_str = '';
                foreach($active_id_arr as $active_id)
                {
                    $active_id_str[] = $active_id['id'];
                }
                
                if($type == 'array')
                    return $active_id_str;
                else
                    return implode(',', $active_id_str);
            }
            else
                return;
        }
        
        public static function getAllActiveAgmtForJob($name='')
        {
            if($name != '')
            {
                $name = ' AND name LIKE \'%'.$name.'%\'';
            }
            else
            {
                $name = '';
            }
            
            $active_ids_arr = self::getActiveDraftCustomId();            
            if($active_ids_arr)
                return ZurmoRedBean::find('agreement', "status_customfield_id IN (".$active_ids_arr.") AND total_mhr > 0 AND archive IS NULL ".$name);
            else
                return;
        }
        protected function beforeSave()
        {
            if(Yii::app()->controller->action->id == 'create' || Yii::app()->controller->action->id == 'edit'){
                $currencyHelper = Yii::app()->currencyHelper;
                if ($this->Agreement_Expiration == '' ) {
                    $this->addError('Agreement_Expiration', Zurmo::t('Core', 'Renewal cannot be blank.'));
                    $currencyHelper->resetErrors();
                    return false;
                }
            }
            return true;
        }
        
        public function getTotalAmtForRecAgreements()
        {
            $query = "SELECT IFNULL(ROUND(SUM(t3.value)),'0') AS RecurringAgmnt FROM agreement t1 
                    INNER JOIN customfield t2 ON t1.status_customfield_id = t2.id AND t2.value = '".Constant::ACTIVEAGREEMENT."'
                    INNER JOIN currencyvalue t3 ON t1.Current_Annual_Amount_currencyvalue_id = t3.id 
                    WHERE recordtype ='".Constant::RECURRINGAGREEMENT."' AND t1.archive IS NULL AND t1.contract_number IS NOT NULL AND t1.name != ''";

            $agreement = ZurmoRedBean::getAll($query);
            return $agreement;
        }
        
//        public function getActiveAgmntsByType($recordType = null, $startDate = null, $endDate = null){
//            $active_ids_arr = self::getActiveCustomId();
//            if(empty($startDate)){
//                $month_ini = new DateTime("first day of last month"); //First date of prev month.
//                $startDate = $month_ini->format('Y-m-d'); // 2016-08-01
//            }
//            if(empty($endDate)){
//                $month_end = new DateTime("last day of last month"); //Last date of prev month.
//                $endDate = $month_end->format('Y-m-d'); // 2016-08-31
//            }
//            
//            if($active_ids_arr){
//                if(empty($recordType)){
//                  
//                    $query = "SELECT *,
//                                ((SUM(unit_consumed_direct_cost) / (1 - final_agmnt_gpm)) / SUM(labour_consumed)) tracking_rev_mhr
//                                FROM (
//                                SELECT t1.*,t2.product_code,
//                                    (if(recordtype = 'Recurring Agreement',
//                                    (select value from currencyvalue c WHERE c.id = current_annual_amount_currencyvalue_id),
//                                    (select value from currencyvalue c WHERE c.id = project_agreement_amount_currencyvalue_id)
//                                    )) agmnt_rev_mhr,
//                                    CASE substr(t2.product_code,1,1) 
//                                    WHEN 'E' THEN (t4.costperunit * t3.consumed_unit)
//                                    WHEN 'L' THEN (t4.costperunit * t3.consumed_unit)
//                                    WHEN 'M' THEN (t4.costperunit * t3.consumed_unit)
//                                    ELSE '0'
//                                    END unit_consumed_direct_cost
//                                    ,(current_gpm/100) final_agmnt_gpm
//                                    ,CASE SUBSTR(t2.product_code, 1, 1)
//                                    WHEN 'L' THEN t3.consumed_unit
//                                    ELSE '0'
//                                END labour_consumed
//                                FROM agreement t1 
//                                INNER JOIN agreementproduct t2 ON t2.agreement_id = t1.id AND substr(t2.product_code,1,1) != 'A'
//                                INNER JOIN agreementtrackingproducts t3 ON t3.agreementproduct_id = t2.id
//                                INNER JOIN costbook t4 ON t2.costbook_id= t4.id            
//                                    WHERE t1.status_customfield_id IN (".$active_ids_arr.") AND DATE(startdate) BETWEEN '".$startDate."' AND '".$endDate."' AND t1.archive IS NULL)T  GROUP BY T.id";
//
//                    /*
//                    $query = "SELECT t1.*,t2.value current_annual_amount,t3.value project_agreement_amount FROM agreement t1 
//                        INNER JOIN currencyvalue t2 ON t2.id = t1.current_annual_amount_currencyvalue_id
//                        INNER JOIN currencyvalue t3 ON t3.id = t1.project_agreement_amount_currencyvalue_id
//                        WHERE t1.status_customfield_id IN (".$active_ids_arr.") AND DATE(startdate) BETWEEN '".$startDate."' AND '".$endDate."' AND t1.archive IS NULL  ORDER BY t1.id DESC";
//                    */
//                    $agreement = ZurmoRedBean::getAll($query);
//                    return $agreement;
//
//                    //return ZurmoRedBean::find('agreement', "status_customfield_id IN (".$active_ids_arr.") AND DATE(startdate) BETWEEN '".$startDate."' AND '".$endDate."' AND archive IS NULL  ORDER BY id DESC");
//                }else{
//                    return ZurmoRedBean::find('agreement', "recordtype =:recordtype AND status_customfield_id IN (".$active_ids_arr.") AND DATE(startdate) BETWEEN '".$startDate."' AND '".$endDate."' AND archive IS NULL  ORDER BY id DESC", array(":recordtype" => $recordType));
//                }
//            }
//        }
        
        public function getAgmntVsTrackingDatas(){
            $query = "SELECT *,((SUM(unit_consumed_direct_cost) / (1 - final_agmnt_gpm)) / SUM(labour_consumed)) tracking_rev_mhr
                                FROM (
                                SELECT t1.*,t2.product_code,
                                    (if(recordtype = 'Recurring Agreement',
                                    (select value from currencyvalue c WHERE c.id = current_annual_amount_currencyvalue_id),
                                    (select value from currencyvalue c WHERE c.id = project_agreement_amount_currencyvalue_id)
                                    )) agmnt_rev_mhr,
                                    CASE substr(t2.product_code,1,1) 
                                    WHEN 'E' THEN (t4.costperunit * t3.consumed_unit)
                                    WHEN 'L' THEN (t4.costperunit * t3.consumed_unit)
                                    WHEN 'M' THEN (t4.costperunit * t3.consumed_unit)
                                    ELSE '0'
                                    END unit_consumed_direct_cost
                                    ,(current_gpm/100) final_agmnt_gpm
                                    ,CASE SUBSTR(t2.product_code, 1, 1)
                                    WHEN 'L' THEN t3.consumed_unit
                                    ELSE '0'
                                END labour_consumed
                                FROM agreement t1 
                                INNER JOIN agreementproduct t2 ON t2.agreement_id = t1.id AND substr(t2.product_code,1,1) != 'A'
                                INNER JOIN agreementtracking t5 ON t5.agreement_id =  t1.id
                                INNER JOIN customfield t6 ON t6.id =  t1.status_customfield_id
                                INNER JOIN agreementtrackingproducts t3 ON t3.agreementproduct_id = t2.id
                                INNER JOIN costbook t4 ON t2.costbook_id= t4.id            
                                    WHERE t6.value IN (\"Active\",\"Completed\") 
                                    AND YEAR(t5.tracking_date) = YEAR(CURDATE()) 
                                    AND t1.archive IS NULL)T  
                                    GROUP BY T.id;";
            return ZurmoRedBean::getAll($query);
        }

        

        public function getAvgGPMByRecordType($recType,$status){
            $query = "SELECT ROUND(SUM(agmnt_res.current_gpm)/COUNT(1),2) Agreement_GPM,ROUND(SUM(agmnt_res.newcurrent_gpm)/COUNT(1),2) Current_GPM FROM (
                    SELECT agmnt.id,agmnt.name, agmnt.current_gpm, agmnt.newcurrent_gpm FROM agreement agmnt
                   INNER JOIN agreementtracking agmnt_t ON agmnt.id = agmnt_t.agreement_id
                   INNER JOIN customfield cf1 ON agmnt.status_customfield_id = cf1.id AND cf1.value IN('".$status."') AND agmnt.recordtype='".$recType."'
                   WHERE YEAR(agmnt_t.tracking_date) = YEAR(CURDATE()) 
                   GROUP BY agmnt_t.agreement_id) agmnt_res";
            return ZurmoRedBean::getAll($query);
            
        }

        public function getActiveAgmnts($recordType){
            $active_ids_arr = self::getActiveCustomId();
            if($active_ids_arr){
                return ZurmoRedBean::find('agreement', "recordtype =:recordtype AND status_customfield_id IN (".$active_ids_arr.") ORDER BY id DESC", array(":recordtype" => $recordType));
            }
        }
    }
?>
