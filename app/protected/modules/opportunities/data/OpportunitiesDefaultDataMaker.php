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

    /**
     * Class to make default data that needs to be created upon an installation.
     */
    class OpportunitiesDefaultDataMaker extends DefaultDataMaker
    {
        /**
         * If you change the stages using the custom management, then make sure to change stageToProbabilityMapping
         * in OpportunitiesModule metadata as well.
         */
        public function make()
        {

            $values = array(
                        Zurmo::t('CustomField', 'Project Final'),
                        Zurmo::t('CustomField', 'Recurring Final'),
            );
            $labels = array();
            static::makeCustomFieldDataByValuesAndDefault('RecordTypes', $values, $values[0]);

            $values = array(
                        //Zurmo::t('CustomField', 'Qualification and Education'),
                        Zurmo::t('CustomField', 'Consulting'),
                        Zurmo::t('CustomField', 'Estimate'),
                        //Zurmo::t('CustomField', 'Final Pricing'),
                        Zurmo::t('CustomField', 'Final Proposal'),
                        Zurmo::t('CustomField', 'Agreement'),
                        Zurmo::t('CustomField', 'Won'),
                        Zurmo::t('CustomField', 'Lost'),
            );
            $labels = array();
            static::makeCustomFieldDataByValuesAndDefault('SalesStages', $values);

	    $values = array(
                        Zurmo::t('CustomField', 'Omitted'),
                        Zurmo::t('CustomField', 'Pipeline'),
                        Zurmo::t('CustomField', 'Best Case'),
                        Zurmo::t('CustomField', 'Commit'),
                        Zurmo::t('CustomField', 'Closed'),
            );
            $labels = array();
            static::makeCustomFieldDataByValuesAndDefault('ForecastCategories', $values);
            
            $values = array(
                        Zurmo::t('CustomField', 'Budget Change'),
                        Zurmo::t('CustomField', 'Communication'),
                        Zurmo::t('CustomField', 'Competitor'),
                        Zurmo::t('CustomField', 'DIY'),
                        Zurmo::t('CustomField', 'Economy'),
                        Zurmo::t('CustomField', 'Other'),
                        Zurmo::t('CustomField', 'Over budget'),
                        Zurmo::t('CustomField', 'Price'),
                        Zurmo::t('CustomField', 'Scheduling'),
                        Zurmo::t('CustomField', 'Stagnant'),
                        Zurmo::t('CustomField', 'Unqualified'), 
            );
            $labels = array();
            static::makeCustomFieldDataByValuesAndDefault('ReasonLost', $values);

            $values = array(
                        Zurmo::t('CustomField', 'Existing Business'),
                        Zurmo::t('CustomField', 'New Business'),
            );
            $labels = array();
            static::makeCustomFieldDataByValuesAndDefault('OpportunityTypes', $values);
            
            $values = array(
                        Zurmo::t('CustomField', 'Less than 36 Months'),
                        Zurmo::t('CustomField', 'Greater than or equal to 36 Months'),
            );
            $labels = array();
            static::makeCustomFieldDataByValuesAndDefault('IntialTermLengths', $values);
            
        }
    }
?>