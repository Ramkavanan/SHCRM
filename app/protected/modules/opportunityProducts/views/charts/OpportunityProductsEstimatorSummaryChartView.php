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
     * A chart view for displaying a chart showing all opportunities by total man hour.
     *
     */
    class OpportunityProductsEstimatorSummaryChartView extends ChartView implements PortletViewInterface
    {
	protected $optId;

	public function setOpptId($id) {
            $this->optId = $id;
            return $this::	renderContent();
	}

        public function renderContent()
        {
            $content  = ZurmoHtml::tag('div', array(), $this->renderTotalDirectCostsContent());
            $content .= ZurmoHtml::tag('div', array(), $this->renderCategoryDirectCostsContent());
            return $content;
        }

        protected function renderTotalDirectCostsContent()
        {
            $cssClass = "text-align:center; font-family: 'Helvetica'; font-weight: bold; padding-top:10px; ";
            $cssClassText = "text-align:center; font-family: 'Helvetica'; color:red; ";
            $chartDataProvider  = $this->resolveChartDataProvider('OpportunityProductsEstimatorSummaryTotalDirectCosts');
            $content  = ZurmoHtml::tag('div', array('style' => $cssClass), 'Total Direct Costs (%)');
           // $content .= ZurmoHtml::tag('div', array('style' => $cssClassText), 'Note : Numbers are rounded');
            $content .= $this->renderTotalDirectCostsChartContent($chartDataProvider, $this->uniqueLayoutId . 'TotalDirectCosts');
            $content .= ZurmoHtml::tag('hr', array());
            return $content;
        }

        protected function renderCategoryDirectCostsContent()
        {
            $cssClass = "text-align:center; font-family: 'Helvetica'; font-weight: bold; margin-top:0.8%; padding-top:10px; ";
            $cssClassText = "text-align:center; font-family: 'Helvetica'; color:red; ";
            $chartDataProvider  = $this->resolveChartDataProvider('OpportunityProductsEstimatorSummaryCategoryDirectCosts');
            $content  = ZurmoHtml::tag('div', array('style' => $cssClass), 'Category Direct Costs (%)');
           // $content .= ZurmoHtml::tag('div', array('style' => $cssClassText), 'Note : Numbers are rounded');
            $content .= $this->renderCategoryDirectCostsChartContent($chartDataProvider, $this->uniqueLayoutId . 'CategoryDirectCosts');
            return $content;
        }

        public function renderTotalDirectCostsChartContent(OpportunityProductsEstimatorSummaryTotalDirectCostsChartDataProvider $chartDataProvider, $uniqueId)
        {
            $chartData = $chartDataProvider->getChartData();
            Yii::import('ext.amcharts.AmChartMaker');
            $amChart = new AmChartMaker();
            $amChart->data = $chartData;
            $amChart->id =  $uniqueId;
            $amChart->type = ChartRules::TYPE_GICRM_PIE_2D;
            $amChart->addChartProperties('balloonText',            "'[[title]]: [[percents]]% ($[[value]])'");
            //Legend
            $amChart->addLegendProperties('borderAlpha',           0.2);
            $amChart->addLegendProperties('valueWidth',            35);
            $amChart->addLegendProperties('horizontalGap',         10);
            $amChart->addLegendProperties('markerLabelGap',  20);
            $amChart->addLegendProperties('valueText',  "'$[[value]]'");

            $amChart->addSerialGraph("value", 'column');
            $amChart->xAxisName        = $chartDataProvider->getXAxisName();
            $amChart->yAxisName        = $chartDataProvider->getYAxisName();
            $javascript = $amChart->javascriptChart();
            Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $uniqueId, $javascript);
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("Chart");
            $cClipWidget->widget('application.core.widgets.AmChart', array(
                    'id'        => $uniqueId,
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips['Chart'];

        }

        public function renderCategoryDirectCostsChartContent(OpportunityProductsEstimatorSummaryCategoryDirectCostsChartDataProvider $chartDataProvider, $uniqueId)
        {
            $chartData = $chartDataProvider->getChartData();
            Yii::import('ext.amcharts.AmChartMaker');
            $amChart = new AmChartMaker();
            $amChart->data = $chartData;
            $amChart->id =  $uniqueId;
            $amChart->type = ChartRules::TYPE_GICRM_PIE_2D;
            $amChart->addChartProperties('balloonText',            "'[[title]]: [[percents]]% ($[[value]])'");
            //Legend
            $amChart->addLegendProperties('borderAlpha',           0.2);
            $amChart->addLegendProperties('valueWidth',            35);
            $amChart->addLegendProperties('horizontalGap',         10);
            $amChart->addLegendProperties('markerLabelGap',  20);
            $amChart->addLegendProperties('valueText',  "'$[[value]]'");

            $amChart->addSerialGraph("value", 'column');
            $amChart->xAxisName        = $chartDataProvider->getXAxisName();
            $amChart->yAxisName        = $chartDataProvider->getYAxisName();
            $javascript = $amChart->javascriptChart();
            Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $uniqueId, $javascript);
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("Chart");
            $cClipWidget->widget('application.core.widgets.AmChart', array(
                    'id'        => $uniqueId,
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips['Chart'];
        }

        public function getPortletParams()
        {
            return array();
        }

        public function renderPortletHeadContent()
        {
            return null;
        }

        /**
         * What kind of PortletRules this view follows
         * @return PortletRulesType as string.
         */
        public static function getPortletRulesType()
        {
            return 'Chart';
        }

        /**
         * The view's module class name.
         */
        public static function getModuleClassName()
        {
            return 'OpportunityProductsModule';
        }

        public function getChartDataProviderType()
        {
            return 'OpportunityProductsEstimatorSummaryTotalDirectCosts';
        }
        public function getCategoryChartDataProviderType()
        {
            return 'OpportunityProductsEstimatorSummaryCategoryDirectCosts';
        }

        /**
         * Override to add a description for the view to be shown when adding a portlet
         */
        public static function getPortletDescription()
        {
        }
        
        protected function resolveChartDataProvider($type)
        {
            assert('is_string($type)');
            $chartDataProvider  = ChartDataProviderFactory::createByType($type);
            return $chartDataProvider;
        }
        
    }
?>
