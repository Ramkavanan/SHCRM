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
     * The base View for a dashboard view
     */
    abstract class DashboardView extends PortletFrameView
    {
        protected $model;
        protected $isDefaultDashboard;

        public function __construct($controllerId, $moduleId, $uniqueLayoutId, $model, $params)
        {
            assert('$model instanceof Dashboard');
            $this->controllerId        = $controllerId;
            $this->moduleId            = $moduleId;
            $this->uniqueLayoutId      = $uniqueLayoutId;
            $this->model               = $model;
            $this->modelId             = $model->id;
            $this->layoutType          = $model->layoutType;
            $this->isDefaultDashboard  = $model->isDefault;
            $this->params              = $params;
        }

        /**
         * Override to allow for making a default set of portlets
         * via metadata optional.
         * @param string $uniqueLayoutId
         * @param array $metadata
         * @return array
         */
        protected function getPortlets($uniqueLayoutId, $metadata)
        {
            assert('is_string($uniqueLayoutId)');
            assert('is_array($metadata)');
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition($uniqueLayoutId, Yii::app()->user->userModel->id, $this->params);
            if (empty($portlets) && $this->isDefaultDashboard)
            {
                $portlets = Portlet::makePortletsUsingMetadataSortedByColumnIdAndPosition($uniqueLayoutId, $metadata, Yii::app()->user->userModel, $this->params);
                Portlet::savePortlets($portlets);
            }
            return PortletsSecurityUtil::resolvePortletsForCurrentUser($portlets);
        }

        protected function renderContent()
        {
            $actionElementContent = $this->renderActionElementBar(false);
            if ($actionElementContent != null)
            {
                $content  = '<div class="view-toolbar-container clearfix"><nav class="pillbox clearfix">';
                $content .= $actionElementContent;
                $content .= '</nav></div>';
            }
            $this->portlets = $this->getPortlets($this->uniqueLayoutId, self::getMetadata());
            $content .= $this->renderPortlets($this->uniqueLayoutId);
            $content    .= $this->renderReportView(); 
            return $content;
        }

        /**
         * Render a toolbar above the form layout. This includes
         * a link to edit the dashboard as well as a link to add
         * portlets to the dashboard
         * @return A string containing the element's content.
          */
        protected function renderActionElementBar($renderedInForm)
        {
            $content = parent::renderActionElementBar($renderedInForm);

            $deleteDashboardLinkActionElement  = new DeleteDashboardMenuActionElement(
                $this->controllerId,
                $this->moduleId,
                $this->modelId,
                array('htmlOptions' => array('confirm' => Zurmo::t('HomeModule', 'Are you sure want to delete this dashboard?')),
                      'iconClass'   => 'icon-delete')
            );
            $pushDashboardLinkActionElement  = new PushDashboardLinkActionElement(
                $this->controllerId,
                $this->moduleId,
                $this->modelId,
                array('htmlOptions' => array('id' => 'PushDashboardLink'),
                      'iconClass'   => 'icon-push-dashboard')
            );
            if (ActionSecurityUtil::canCurrentUserPerformAction($deleteDashboardLinkActionElement->getActionType(),
                $this->model) && !$this->isDefaultDashboard)
            {
                $content .= $deleteDashboardLinkActionElement->render();
            }
            if (PushDashboardUtil::canCurrentUserPushDashboardOrLayout())
            {
                $content .= $pushDashboardLinkActionElement->render();
            }
            $content .= $this->renderChangeDashboardMenuActionContent();
            return $content;
        }

        protected function renderChangeDashboardMenuActionContent()
        {
            $dashboardsData = Dashboard::getRowsByUserId(Yii::app()->user->userModel->id);
            if (count($dashboardsData) > 1)
            {
                foreach ($dashboardsData as $key =>  $dashboardData)
                {
                    if ($dashboardData['id'] == $this->model->id)
                    {
                        unset($dashboardsData[$key]);
                    }
                }
                $changeDashboardMenuActionElement  = new ChangeDashboardMenuActionElement(
                    $this->controllerId,
                    $this->moduleId,
                    $this->modelId,
                    array('htmlOptions' => array('id' => 'ChangeDashboardsMenu'),
                          'iconClass'   => 'icon-change-dashboard',
                          'dashboardsData' => $dashboardsData));
                return $changeDashboardMenuActionElement->render();
            }
        }
        protected function renderReportView(){
            $content = '';
            $title = 'REPORTS';
            $portletId = 'REPORTS_IN_YEAR_BASED_REC';
            $outerPortletId = 'REPORTS_IN_YEAR_BASED_REC_ID';            
            $content .= $this->renderKPIPortlets($outerPortletId, $portletId, $title);
            return $content;
        }
        protected function renderKPIPortlets($outerPortletId, $porletId, $title){
            $content ='';
            $content .= '<div class="juiportlet-widget" id='.$outerPortletId.'>
                            <div class="juiportlet-widget-head">
                                <h3>'.$title.'</h3>
                            </div>
                            <div class="juiportlet-widget-content">
                                <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                    <div class="ApprovalProcessMyListView SecuredMyListView MyListView ListView ModelView ConfigurableMetadataView MetadataView">
                                        <div id='.$porletId.' class="cgrid-view type-approvalProcess">
                                            ';
            $content .='<div class="table-wrapper">
                                                <table id="report_table_home" class="items">
                                                    <thead class="header">
                                                        <tr class="first_child">
                                                            <th>Names </th>';
                                                        
            for($i = 0;$i < 53; $i++){
                $content .= '<th> W'.$i.'</th>';
            }                
            $content .= '</tr>
                                       </thead>
                                       <tbody class="results">
                                           <tr class="first_child">
                                           <th>Agreement 1 </th>';
            for($i = 1;$i < 53; $i++){
                $content .= '<td> '.$i.$i.$i.$i.'</td>';
            }
            $content .= '</tr><tr><th class="first_child">Agreement 2 </th>';
            for($i = 1;$i < 53; $i++){
                $content .= '<td> '.$i.$i.$i.$i.$i.'</td>';
            }

            $content .= '</tr>
                                           </tbody>
                                       </table>
                                   </div>';

            $content .= '<div class="pager horizontal"><ul class="yiiPager" id="yw4"><li class="refresh hidden"><a href="/app/index.php/home/defaultPortlet/myListDetails?portletId=155">refresh</a></li></ul></div>
                                            <div class="list-preloader"><span class="z-spinner"></span></div>
                                            <div title="/app/index.php/home/default" style="display:none" class="keys"><span>240</span><span>232</span><span>180</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
            return $content;
        }
    }
?>
