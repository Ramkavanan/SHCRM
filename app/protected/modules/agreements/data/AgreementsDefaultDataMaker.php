<?php
    /**
     * Class to make default data that needs to be created upon an installation.
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsDefaultDataMaker extends DefaultDataMaker
    {
  	public function make() {
		$values = array(
		        Zurmo::t('CustomField', '15 Days'),
		        Zurmo::t('CustomField', '30 Days'),
		        Zurmo::t('CustomField', '45 Days'),
		        Zurmo::t('CustomField', '60 Days'),
			Zurmo::t('CustomField', '90 Days'),
			Zurmo::t('CustomField', '120 Days'),
		    );
		static::makeCustomFieldDataByValuesAndDefault('OwnerExpirationNotices', $values);

		$values = array(
                        Zurmo::t('CustomField', 'Draft'),
            		Zurmo::t('CustomField', 'Active'),
            		Zurmo::t('CustomField', 'Pending'),
			Zurmo::t('CustomField', 'Completed'),
			Zurmo::t('CustomField', 'Deactivated'),
			Zurmo::t('CustomField', 'Closed'),
		    );
		static::makeCustomFieldDataByValuesAndDefault('AgreementStatus', $values);

		$values = array(
		        Zurmo::t('CustomField', 'Opportunity'),
		        Zurmo::t('CustomField', 'Clone'),
		    );
		static::makeCustomFieldDataByValuesAndDefault('AgreementTypes', $values);

		$values = array(
		        Zurmo::t('CustomField', 'Project Agreement'),
		        Zurmo::t('CustomField', 'Recurring Agreement'),
		);
		static::makeCustomFieldDataByValuesAndDefault('AgreementRecordTypes', $values);
	}
    }
?>
