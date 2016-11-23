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
     * View to display to users upon login.  Shows information such as tips, helpful links and ideas of what to do.
     */
    class WelcomeView extends View
    {
        protected $tipContent;

        protected $splashImageName;

        protected $hasDashboardAccess;

        protected static function renderHelpfulLinksContent()
        { return;
            $content  = '<div class="help-section">';
            $content .= '<h3>' . Zurmo::t('HomeModule', 'Helpful Links') . '</h3>';
            $content .= '<ul>';
            $content .= '<li>' . ZurmoHtml::link(Zurmo::t('HomeModule', 'Join the forum'), 'http://www.zurmo.org/forums') . '</li>';
            $content .= '<li>' . ZurmoHtml::link(Zurmo::t('HomeModule', 'Read the wiki'),  'http://zurmo.org/wiki') . '</li>';
            $content .= '<li>' . ZurmoHtml::link(Zurmo::t('HomeModule', 'View a tutorial'), 'http://www.zurmo.org/tutorials') . '</li>';
            $content .= '<li>' . ZurmoHtml::link(Zurmo::t('HomeModule', 'Watch a video'), 'http://zurmo.org/screencasts') . '</li>';
            $content .= '</ul>';
            $content .= '</div>';
            return $content;
        }

        protected static function resolveAndRenderSubscriptionContent()
        { return;
            if (Yii::app()->edition != 'Community')
            {
                return;
            }
            $content  = '<div class="help-section get-pro-message-small">';
            $content .= '<h3>' . Zurmo::t('ZurmoModule', 'Achieve more with a Zurmo subscription') . '</h3>';
            $content .= '<p>' .Zurmo::t('HomeModule', 'Get more features, proactive support, access ' .
                                               'to training and consulting, blazing fast hosting, ' .
                                               'and in-depth documentation with a Zurmo subscription.');
            $content .= '<br/><a href="http://www.zurmo.com/needSupport.php?source=welcome">' .
                        Zurmo::t('ZurmoModule', 'Learn More' . '</a>');
            $content .= '</p></div>';
            return $content;
        }

        protected static function renderSocialLinksContent()
        {
            return AboutView::renderSocialLinksContent();
        }

        /**
         * @param string $tipContent
         * @param bool $hasDashboardAccess
         */
        public function __construct($tipContent, $hasDashboardAccess)
        {
            assert('is_string($tipContent)');
            assert('is_bool($hasDashboardAccess)');
            $this->tipContent                = $tipContent;
            $this->hasDashboardAccess        = $hasDashboardAccess;
        }

        protected function renderContent()
        {
            $cssClass = 'class="has-message"';
            $loggedIn = false;
            $hasOutlook = Yii::app()->user->userModel->isOutlookSynced; 
            if(isset($hasOutlook)){
                $loggedIn = true;
            }
           // $cssClass = '';
            if (Yii::app()->edition != 'Community')
            {
                    $cssClass = '';
            }
            $params     = LabelUtil::getTranslationParamsForAllModules();
            $rand       = mt_rand(1, 11);
            $themeName  = Yii::app()->theme->name;
            $imgUrl     = Yii::app()->themeManager->baseUrl . '/' . $themeName . '/images/welcome-gallery-' . $rand . '.png';
            $content    = '<div>';
            $content    .= '<div class="outlook_link_div">';
            $content    .= $this->renderDashboardLinkContent();            
            if (!$loggedIn) {
                $content    .= $this->renderOutlookLinkContent();                
            }
            else {
                //$content    .= '<p>Hello, <br> Your Outlook Account is '.Yii::app()->user->userModel->outlookEmail.' is Synced with ShinnedHawks!</p>';
            }
            $content    .= '</div>';
            $content    .= '<div class="kpi_div" >';
            $content    .= $this->renderKPIs();    //Render KPIs.
            $content    .= '</div></div>';
            
            /**
             * Chart render using zurmo chart useing amcharts.
             */
            $content    .= '<div class="chart_outer">';
            $content    .= $this->renderCharts();
            $content    .= '</div>';
            
            /**
             * Chart render by ajax call in file DashBoardUtils.js (Use to create amcharts).
             */
            $content    .= '<div style="width:100%;height: 470px;">';
            $content    .= '<div class="dashBoard_JobWeeklySchChart dashBoard_step_two_chart" id="pipeline">';
            $content    .= '<div class="chart_header">Pipeline</div>';
            $content    .= '<div id="pipeline_chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>';
            $content    .= '</div>';
            
            /**
             * Chart render by ajax call in file DashBoardUtils.js (Use to create amcharts).
             */
            $content    .= '<div class="dashBoard_JobWeeklySchChart dashBoard_step_two_chart" id="closed_sales">';
            $content    .= '<div class="chart_header">Closed Sales</div>';
            $content    .= '<div id="closed_sales_chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>';
            $content    .= '</div>';
            $content    .= '</div>';
            
            /**
             * Chart render using zurmo chart useing amcharts.
             */
            $content    .= '<div class="dashBoard_JobWeeklySchChart" id="Job_Weekly_Sch_Chart">';
            $content    .= '<div class="chart_header">Weekly Scheduled Hours</div>';
            $content    .= $this->renderJobWeeklySchChart();    //Render KPI Tables.
            $content    .= '</div>';
            $content    .= '</div>';
            return $content;
        }

        protected function renderCharts(){
            $content    ='';
            $content    .= '<div class="dashBoard_Chart" id="pro_dashBoard_Chart">';
            $content    .= '<div class="chart_header">Project Agreements - GPM</div>';
            $content    .= $this->renderProCurrentGPMVsAgmntGPMChart();
            $content    .= '</div>';
            $content    .= '<div class="dashBoard_Chart" id="rec_dashBoard_Chart">';
            $content    .= '<div class="chart_header">Recurring Agreements - GPM</div>';
            $content    .= $this->renderRecCurrentGPMVsAgmntGPMChart();            
            $content    .= '</div>';
            $content    .= '<div class="dashBoard_Chart" id="track_dashBoard_Chart">';
            $content    .= '<div class="chart_header">Agreements - Rev/Mhr</div>';
            $content    .= $this->renderAgmntRevMhrVsTrackingRevMhrChart();
            $content    .= '</div>';
            return $content;
        }

        /**
         * Get data base on status (Active)
         */
        protected function renderProCurrentGPMVsAgmntGPMChart(){
            $status = array(Constant::ACTIVEAGREEMENT,  Constant::COMPLETEDAGREEMENT);
            $recAgreementGPMs = Agreement::getAvgGPMByRecordType(Constant::PROJECTAGREEMENT, implode("','", $status));
            $dataArray = Array
                            (
                                Array
                                    (
                                        'value' =>$recAgreementGPMs[0]['Agreement_GPM'],
                                        'displayLabel' => 'Agreement GPM %',
                                        'color' => '#0EADD4',
                                    ),
                                Array(
                                        'value' => $recAgreementGPMs[0]['Current_GPM'],
                                        'displayLabel' => 'Current GPM %',
                                        'color' => '#63C5DA',
                                    )
                            );
            $chart_dataPro = new ProGPMChartView('ProGPM', null, 'Project');
            return $chart_dataPro->setAgId($dataArray);
        }     
        
        /**
         * Get data base on status (Active$status, Completed)
         */
        protected function renderRecCurrentGPMVsAgmntGPMChart(){
            $status = array(Constant::ACTIVEAGREEMENT);
            $recAgreementGPMs = Agreement::getAvgGPMByRecordType(Constant::RECURRINGAGREEMENT, implode("','", $status));
            $dataArray = Array
                            (
                                Array
                                    (
                                        'value' =>$recAgreementGPMs[0]['Agreement_GPM'],
                                        'displayLabel' => 'Agreement GPM %',
                                        'color' => '#0EADD4',
                                    ),
                                Array(
                                        'value' => $recAgreementGPMs[0]['Current_GPM'],
                                        'displayLabel' => 'Current GPM %',
                                        'color' => '#63C5DA',
                                    )
                            );
            $chart_data = new GPMChartView('GPM', null, 'Recurring');
            return $chart_data->setAgIdMhr($dataArray);
        }     
        
        /**
         * Sum of current annual amount for Recurring Agreement.
         * Sum of project agreement amount for Recurring Agreement.
         */
        protected function renderAgmntRevMhrVsTrackingRevMhrChart(){
            $agreements = Agreement::getAgmntVsTrackingDatas();
            $chartDatas = ChartDashboardUtil::getAgreementRevMhrAndTrackingRevMhr($agreements);
            $dataArray = Array
                            (
                                Array
                                    (
                                        'value' =>$chartDatas[0]['agmnt_rev_mhr_final'],
                                        'displayLabel' => 'Agreement Rev/Mhr',
                                        'color' => '#0EADD4',
                                    ),
                                Array(
                                        'value' => $chartDatas[0]['tracking_rev_mhr_final'],
                                        'displayLabel' => 'Tracking Rev/Mhr',
                                        'color' => '#63C5DA',
                                    )
                            );
            $chart_dataPro = new AgmntMhrVsTrackMhrChartView('MHRs', null, 'agmnt_Vs_tracking');
            return $chart_dataPro->setAgId($dataArray);
        }
        
        /**
         * Get job based on Active state and not in archive.
         */
        protected function renderJobWeeklySchChart(){
            $JobSchedule = JobScheduling::getWeeklyScheduledHours();
            $agmntDatas = $this->getWeeklyTableContent($JobSchedule);
            foreach ($agmntDatas as $date => $agmntData) {
                $jobSchData[]= Array
                    (
                        'value' =>$agmntData,
                        'displayLabel' => $date,
                        'color' => '#0EADD4',
                    );
            }
            $dataArray = $jobSchData;
            $chart_dataPro = new JobWeeklySchChartView('MHRs', null, 'Job_weekly_sch');
            return $chart_dataPro->setAgId($dataArray);
        }
        
        protected function renderKPIs(){
            $tables = '';
            $tables .= '<div class="kpi_div">
<!--                            <div class="kpi_inner_div">
                                <div style="background-color: #FF0000;" class="kpi_topic_div">
                                    <span class="KPI_Topics">Activity</span>
                                </div>
                                <div class="kpi_sub_topic">
                                    <span class="KPI_Inner_Span" id="count_of_meetings">0</span>
                                </div>
                            </div> -->
                            <div class="kpi_inner_div">
                                <div style="background-color: #75B749;" class="kpi_topic_div">
                                    <span class="KPI_Topics">Sales Pipeline</span>
                                </div>
                                <div class="kpi_sub_topic">
                                    <span class="KPI_Inner_Span" id="totalFinalAmt">$0</span>
                                </div>
                            </div>
                            <div style="margin-left: 0.4%;" class="kpi_inner_div">
                                <div style="background-color: #0EADD4;" class="kpi_topic_div">
                                    <span class="KPI_Topics">Book of business</span>
                                </div>
                                <div class="kpi_sub_topic">
                                    <span class="KPI_Inner_Span" id="Total_Rec_Amt">$0</span>
                                </div>
                            </div>
                            <div style="margin-left: 0.4%;" class="kpi_inner_div">
                                <div style="background-color: #E77E23;" class="kpi_topic_div">
                                    <span class="KPI_Topics">New sales</span>
                                </div>
                                <div class="kpi_sub_topic">
                                    <span class="KPI_Inner_Span" id="Total_Project_Amt">$0</span>
                                </div>
                            </div>
                        </div>
                        ';
            return $tables;
        }
        
        protected function renderWeekly_Scheduled_Hours(){
            $content = '';
            $title = 'WEEKLY SCHEDULED HOURS';
            $portletId = 'WEEKLY_SCHEDULED_HOURS';
            $outerPortletId = 'OUTER_WEEKLY_SCHEDULED_HOURS';
            $content .= $this->renderKPIPortlets($outerPortletId, $portletId, $title);
            return $content;
        }
        
        protected function renderClosed_Sales_Report(){
            $headerParams = array('Agreement Name', 'Agreement Rev/Mhr', 'Tracking Rev/Mhr');
            $content = '';
            $title = 'CLOSED SALES REPORT';
            $portletId = 'Closed_Sales_Report';
            $outerPortletId = 'REVENUE_BY_MAN_HOUR_TRACKING_VS_AGREEMENT_ID';
            $content .= $this->renderKPIPortlets($outerPortletId, $portletId, $title, $headerParams);
            return $content;
        }
        
        protected function renderPipeline_Report(){
            $headerParams = array('Agreement Name', 'Agreement Rev/Mhr', 'Tracking Rev/Mhr');
            $content = '';
            $title = 'PIPELINE REPORT';
            $portletId = 'Pipeline_Report';
            $outerPortletId = 'OUTER_PIPELINE_REPORT';
            $content .= $this->renderKPIPortlets($outerPortletId, $portletId, $title, $headerParams, $portletId);
            return $content;
        }
        
        protected function renderKPIPortlets($outerPortletId, $porletId, $title, $headerParams = null, $portletId = null){
            $content ='';
            $content .= '<div class="juiportlet-widget" id='.$outerPortletId.'>
                            <div class="juiportlet-widget-head">
                                <h3>'.$title.'</h3>
                            </div>
                            <div class="juiportlet-widget-content">
                                <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                    <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                        <div id='.$porletId.' class="cgrid-view type-approvalProcess">';
                                        if(empty($headerParams)){
                                            $content .= $this->renderScheduledHoursTableContent();
                                        }else{
                                            $content .= $this->renderClosedSaleTableContent($portletId);
                                        }
                                            
                            $content .='</div>
                                    </div>
                                </div>
                            </div>
                        </div>';
            return $content;
        }
        
        protected function renderClosedSaleTableContent($portletId){
            $tab_content = '';
            $tab_content .= '<table class="items report_contents" style="border-color: #DDDDDD;">';
            if($portletId == 'Pipeline_Report'){
                $opportunitys = Opportunity::getPipeLineReport();
            }else{
                $opportunitys = Opportunity::getClosedSaleReport();
            }    
            $agmntDatas = $this->getClosedSaleHeader($opportunitys);
            $tab_content .= $agmntDatas;
            $tab_content .= '</table>';
            return $tab_content;
            
        }
        
        protected function getClosedSaleHeader($opportunitys){
            $tab_content = '';
            $totalProAndRec = 0;
            $totalPro = 0;
            $totalRec = 0;
            $finalTotal = 0;
            $tab_content .= '<thead><tr>
                                <th>Sales Person</th>
                                <th>Project Amount</th>
                                <th>Recurring Amount</th>
                                <th>Total Amount</th>
                            </tr></thead>';
            $tab_content .= '<tbody><tr>
                                <td colspan="4">
                                <div class="table_content_scroll">
                                <table class="items">
                                <tbody>';
            foreach ($opportunitys as $opportunity) {
            $totalProAndRec = $opportunity['project_amt'] + $opportunity['recurring_amt'];
            $finalTotal += $totalProAndRec;
            $totalPro += $opportunity['project_amt'];
            $totalRec += $opportunity['recurring_amt'];
            $tab_content .= '<tr>
                                <td width="20%" align="center">'.$opportunity['sales_person'].'</td>
                                <td width="20%" align="center">'.$opportunity['project_amt'].'</td>
                                <td width="20%" align="center">'.$opportunity['recurring_amt'].'</td>
                                <td width="20%" align="center">'.$totalProAndRec.'</td>
                            </tr>';                
            }
            $tab_content .= '<tr>
                                <th>Total</th>
                                <td>'.$totalPro.'</td>
                                <td>'.$totalRec.'</td>
                                <td>'.$finalTotal.'</td>
                            </tr>';
            $tab_content .= '</tbody></table></div></td></tr></tbody>';
            return $tab_content;

        }

        protected function renderScheduledHoursTableContent(){
            $tab_content = '';
            $tab_content .= '<table class="items" border=1; style="border-color: #DDDDDD;">';
            $agreements = JobScheduling::getWeeklyScheduledHours();
            $agmntDatas = $this->getWeeklyTableContent($agreements);
            $tab_content .= $agmntDatas;
            $tab_content .= '</table>';
            return $tab_content;
        }
        
        protected function renderScheduledHoursTableHeader($headerParams, $weeklySchDatas, $agreements) {
            $chartDatas = array();
            $weekData = array();
            foreach ($headerParams as $key => $headerParam) {
                $valueKeys = array_search($headerParam, $weeklySchDatas);
                if($valueKeys !== false){
                    $chartDatas[$headerParam] = $agreements[$valueKeys]['hours'];
                }else{
                    $chartDatas[$headerParam] = 0;
                }
            }
            return $chartDatas;
        }

        protected function getWeeklyTableContent($agreements){
            $table = '';
            $weekArray = array();
            $weeklySchDatas = array();
            $year = date('Y'); // Get current year
            $startDate = "01-01-{$year}";
            $endDate = "31-12-{$year}";
            $weekCount = 1;
            $weekData = array();
            for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime('+1 day', $i)) {
              if (date('N', $i) == 1){ //Monday == 1
                $weekArray[] = date("m/d", $i).' '."W $weekCount"; 
                $weekCount ++;
              }  
            }
            foreach ($agreements as $agreement) {
                $weeklySchDatas[] = $agreement['week_start'];
            }
            $chartContent = $this->renderScheduledHoursTableHeader($weekArray, $weeklySchDatas, $agreements);
            return $chartContent;
        }
        
        protected function renderTipsContent()
        {
            if ($this->tipContent != null)
            {
                $content  = '<div class="help-section daily-tip">';
                $content .= '<h3>' . Zurmo::t('HomeModule', 'Tip of the Day') . '</h3>';
                $content .= '<ul>';
                $content .= '<li>' . $this->tipContent . '</li>';
                $content .= '</ul>';
                $content .= self::renderNextTipAjaxLink('tip-of-day-next-page-link', Zurmo::t('HomeModule', 'Next Tip'));
                $content .= '</div>';
                return $content;
            }
        }

        protected static function renderNextTipAjaxLink($id, $label)
        {
            assert('is_string($id)');
            assert('is_string($label)');
            $url       = Yii::app()->createUrl('home/default/getTip');
            // Begin Not Coding Standard
            return       ZurmoHtml::ajaxLink($label, $url,
                         array('type' => 'GET',
                               'dataType' => 'json',
                               'success' => "js:function(data){
                                    $('.daily-tip').find('li').html(data);
                              }"),
                         array('id' => $id, 'href' => '#'));
            // End Not Coding Standard
        }

        protected function renderDashboardLinkContent()
        {
            if ($this->hasDashboardAccess)
            {
                $label    = ZurmoHtml::wrapLabel(Zurmo::t('HomeModule', 'Go to the dashboard'));
                $content  = ZurmoHtml::link($label, Yii::app()->createUrl('home/default'), array('class' => 'dashboard-link z-button green-button','style'=>'margin:0% 0% 0% 1%;float:right;'));
                return $content;
            }
        }
        
        protected function renderOutlookLinkContent()
        {
           $redirectUri = outlookCalendar::getRedirectUrl();
            $outlookUrl = outlookCalendar::getLoginUrl($redirectUri);
            if ($this->hasDashboardAccess)
            {
                $label    = ZurmoHtml::wrapLabel(Zurmo::t('HomeModule', 'Sync Your Outlook Calendar'));
                $content  = ZurmoHtml::link($label, $outlookUrl, array('class' => 'dashboard-link z-button green-button outlook_sync','style'=>'margin:0% 0% 0% 0%;'));
                return $content;
            }
        }

        protected function renderHideLinkContent()
        { 
            if ($this->hasDashboardAccess)
            {
                $label    = '<span></span>' . Zurmo::t('HomeModule', 'Don\'t show me this screen again');
                $content  = '<div class="hide-welcome">'.ZurmoHtml::link($label, Yii::app()->createUrl('home/default/hideWelcome'));
                $content .= ' <i>(' . Zurmo::t('HomeModule', 'Don\'t worry you can turn it on again') . ')</i></div>';
                return $content;
            }
        }
    }
    
    Yii::app()->clientScript->registerScriptFile(
        Yii::app()->getAssetManager()->publish(
            Yii::getPathOfAlias('application.modules.home.elements.assets')) . '/DashBoardUtils.js'
    );
?>
