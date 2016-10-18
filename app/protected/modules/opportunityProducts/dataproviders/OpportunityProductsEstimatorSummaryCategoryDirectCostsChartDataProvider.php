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
     * Build a data set showing opportunities grouped by the source summed by the total man hour.
     */
    class OpportunityProductsEstimatorSummaryCategoryDirectCostsChartDataProvider extends ChartDataProvider
    {
        public function __construct()
        {
            $this->model = new Opportunity(false);
        }

        public function getXAxisName()
        {
            return $this->model->getAttributeLabel('source');
        }

        public function getYAxisName()
        {
            return $this->model->getAttributeLabel('amount');
        }

	protected $optId;

	public function setOpportunityId($id) {
            $this->optId = $id;
            return $this::getChartData();
	}

        public function getChartData()
        {
            $id =  $_GET['optId'];
            $opptProducts = OpportunityProduct::getAllByOpptId(intval($id));
            $count = count($opptProducts);
            $totalDirectCost = 0;
            $totalMH = 0;
            $suggestedPrice = 0;
            $opptPdctMap;
            $categoryValues = array();
            $totalCostValues = array();

            if(count($opptProducts) > 0) {
                foreach($opptProducts as $row) {
                    $opptPdctMap[$row->Category][] = $row;
                }

                foreach ($opptPdctMap as $key => $optpdctArray)  {
                    foreach ($optpdctArray as $optKey => $optpdt){
                        $categoryValues[] = $key;
                        $totalCostValues[] = $optpdt->Total_Direct_Cost->value;
                        $chartArr = array_combine($categoryValues, $totalCostValues);
                        $totalDirectCost += $optpdt->Total_Direct_Cost->value;
                        $suggestedPrice += $optpdt->Final_Cost->value;
                        $totalMH += $optpdt->Total_MHR;
                    }
                }
                $newChartArr = $this->array_combine_($categoryValues, $totalCostValues);

                foreach($newChartArr as $k => $v) {
                    if(is_array($newChartArr[$k])) {
                        $newChartArr[$k] = array_sum($newChartArr[$k]);
                    } else {
                        $newChartArr[$k] = $newChartArr[$k];
                    }
                }
            }
            $arr_keys = array_keys($newChartArr);
            $arr_values = array_values($newChartArr);

            $chartData                  = array();

            for($i=0; $i< count($newChartArr); $i++) {
                $chartData[$i]['value'] = $arr_values[$i];
                $chartData[$i]['displayLabel'] = $arr_keys[$i];
            }
	    return $chartData;
        }

        public function  array_combine_($keys, $values){
            $result = array();

            foreach ($keys as $i => $k) {
                $result[$k][] = $values[$i];
            }

            array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));

            return $result;
        }
        
        
    }
?>
