<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AgreementRelationsSecuredPortletFrameView
 *
 * @author ideas2it
 */
class RoutesRelationsSecuredPortletFrameView extends ModelRelationsSecuredPortletFrameView{
    
    private $showAsTabs;
    
    public function __construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible = true, $portletsAreMovable = true, $showAsTabs = false, $layoutType = '100', $portletsAreRemovable = true) {        
        parent::__construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible, $portletsAreMovable, $showAsTabs, $layoutType, $portletsAreRemovable);
    }

    //put your code here
    protected function renderPortlets($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true) {
        if (!$this->showAsTabs)
            {
                return $this->renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible, $portletsAreMovable, $portletsAreRemovable);
            }
            assert('is_bool($portletsAreCollapsible) && $portletsAreCollapsible == false');
            assert('is_bool($portletsAreMovable) && $portletsAreMovable == false');
            return $this->renderPortletsTabbed();
    }
    protected function renderPortletsTabbed()
        {
            assert('count($this->portlets) == 1 || count($this->portlets) == 0');
            if (count($this->portlets) == 1)
            {
                $tabItems = array();
                foreach ($this->portlets[1] as $noteUsed => $portlet)
                {
                    $tabItems[$portlet->getTitle()] = array(
                        'id'      => $portlet->getUniquePortletPageId(),
                        'content' => $portlet->renderContent()
                    );
                }
                $cClipWidget = new CClipWidget();
                $cClipWidget->beginClip("JuiTabs");
                $cClipWidget->widget('zii.widgets.jui.CJuiTabs', array(
                    'id' => $this->uniqueLayoutId . '-portlet-tabs',
                    'tabs' => $tabItems
                ));
                $cClipWidget->endClip();
                return $cClipWidget->getController()->clips['JuiTabs'];
            }
        }
        protected function renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true){
            assert('is_string($uniqueLayoutId)');
            assert('is_bool($portletsAreCollapsible)');
            assert('is_bool($portletsAreMovable)');
            assert('is_bool($portletsAreRemovable)');
            $juiPortletsWidgetItems = array();
            foreach ($this->portlets as $column => $columnPortlets)
            {
                foreach ($columnPortlets as $position => $portlet)
                {
                    $className = get_class($portlet->getView());
                    if (method_exists($className, 'canUserRemove'))
                    {
                        $removable      = $className::canUserRemove();
                    }
                    else
                    {
                        $removable      = $portletsAreRemovable;
                    }
                    $additionalOptionMenuItems = array();
                    if (method_exists($className, 'getAdditionalOptionMenuItems'))
                    {
                        $additionalOptionMenuItems = $className::getAdditionalOptionMenuItems();
                    }
                    if($className == 'RoutesDetailsPortletView') {
						
                        $juiPortletsWidgetItems[$column][$position] = array(
                          'id'                        => $portlet->id,
                          'uniqueId'                  => $portlet->getUniquePortletPageId(),
                          'title'                     => $portlet->getTitle(),
                          'content'                   => $this::renderViewForRouteRelation($portlet),////$portlet->renderContent(),
                          'headContent'               => $portlet->renderHeadContent(),
                          'editable'                  => $portlet->isEditable(),                          
                          'collapsed'                 => $portlet->collapsed,
                          'removable'                 => $removable,
                          'uniqueClass'               => $this->resolveUniqueClass($portlet),
                          'portletParams'             => $portlet->getPortletParams(),
                          'additionalOptionMenuItems' => $additionalOptionMenuItems,
                      );  
                    }
                    else{
                        $juiPortletsWidgetItems[$column][$position] = array(
                            'id'                        => $portlet->id,
                            'uniqueId'                  => $portlet->getUniquePortletPageId(),
                            'title'                     => $portlet->getTitle(),
                            'content'                   => $portlet->renderContent(),
                            'headContent'               => $portlet->renderHeadContent(),
                            'editable'                  => $portlet->isEditable(),							
                            'collapsed'                 => $portlet->collapsed,
                            'removable'                 => $removable,
                            'uniqueClass'               => $this->resolveUniqueClass($portlet),
                            'portletParams'             => $portlet->getPortletParams(),
                            'additionalOptionMenuItems' => $additionalOptionMenuItems,
                        );
                    }                                        
                }
            }   
            $cClipWidget = new CClipWidget();
            $cClipWidget->beginClip("JuiPortlets");
            $cClipWidget->widget('application.core.widgets.JuiPortlets', array(
                'uniqueLayoutId' => $uniqueLayoutId,
                'moduleId'       => $this->moduleId,
                'saveUrl'        => Yii::app()->createUrl($this->moduleId . '/defaultPortlet/SaveLayout'),
                'layoutType'     => $this->getLayoutType(),
                'items'          => $juiPortletsWidgetItems,
                'collapsible'    => $portletsAreCollapsible,
                'movable'        => $portletsAreMovable,
            ));
            $cClipWidget->endClip();
            return $cClipWidget->getController()->clips['JuiPortlets'];
        }  
        
        protected function renderViewForRouteRelation(Portlet $portlet) {
            
            if ($this->params['relationModel']->modelClassNameToBean['Route']->id != null) {
                $id = $this->params['relationModel']->modelClassNameToBean['Route']->id;
                $route_categorys = RouteCategory::getCatByRouteId($id);
                $route_cat_arr = array();
                foreach($route_categorys as $route_category)
                {
                    $route_cat_arr[] = $route_category->category->name;
                }
            
                $tableCreation = '
                            <div class="panel details-table">
                                <div class="panelTitle">Categories Information</div>
                                <table class="form-fields">
                                    <colgroup>
                                        <col class="col-0"><col class="col-1">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>Category</th>
                                            <td colspan="1">'.implode(', ',$route_cat_arr).'</td>
                                        </tr>
                                    </tbody>
                               </table>
                           </div>';
            
                //For the Agreements
                $route_agmts = RouteAgreement::getAgmtByRouteId($id);

                $tableCreation .='<div class="panelTitle">Location Information</div>
                                    <div class="panel" style="padding: 2%;">
                                        <div style = "border : solid #dfdfdf 1px;">
                                            <table class="items" style="padding: 1%; text-align: left; vertical-align: bottom;"  border="0" cellpadding="2" cellspacing="0" width="100%">
                                                <colgroup span="4"></colgroup>
                                                <tbody>
                                                    <tr>';
                                    $i=1;
                                    foreach ($route_agmts as $route_agmt) {                                    
                                        $tableCreation .= '
                                                                <td style="text-align: left;"><b>Location '.$i.'</b> '.$route_agmt->agreement->name.'</td>                                                               
                                                          ';
                                        if($i%2==0)
                                        {
                                            $tableCreation .= '</tr><tr>';
                                        }
                                    $i++;

                                    }
                $tableCreation .= '</tr></tbody></table></div></div></div>';
            
                // For the Trackings
                $route_trackings = RouteTracking::getTrackingByRouteId($id); 
                $tableCreation .='<div class="panelTitle">Route Trackings</div>                    
                                    <div class="form-toolbar clearfix">
                                        <a id="addTracking" name="Add Tracking" class="attachLoading z-button" href="/app/index.php/routes/default/RouteTracking?id=' . $id . '"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">New Route Tracking</span></a>
                                    </div>
                                    <div class="panel" style="padding: 2%;">
                                        <div style = "border : solid #dfdfdf 1px;">
                                            <table class="items" style="padding: 1%; text-align: left; vertical-align: bottom;"  border="0" cellpadding="2" cellspacing="0" width="100%">
                                              <colgroup>
                                                <col class="col-0"><col class="col-1">
                                              </colgroup>
                                                <tbody>
                                                    <tr>
                                                        <td style="text-align: left; width:100px;"><b>Action</b></td>
                                                        <td style="text-align: left;"><b>Route Tracking ID</b></td>
                                                    </tr>';
                            if(count($route_trackings) > 0)
                            {
                                foreach ($route_trackings as $route_tracking) {                                    
                                    $tableCreation .= '<tr>
                                                            <td style="text-align: left; width:45px; font-color:blue;"><a href="/app/index.php/routes/default/deleteTrackingDetails?id=' . $route_tracking->id . '" onclick="return confirm(\'Are you sure to Delete the Tracking?\');"> Delete </a></td>
                                                            <td style="text-align: left;"><a href="/app/index.php/routes/default/routeTrackingDetailsView?id=' . $route_tracking->id . '">' . $route_tracking->name . '</a> </td>
                                                      </tr>';
                                }
                            }
                            else 
                            {
                                $tableCreation .= '<tr>                                                        
                                                        <td style="text-align: left;" colspan="2">No Tracking Found</td>
                                                  </tr>';
                            }

                $tableCreation .= '</tbody></table></div></div></div>';
            
                $content = $portlet->renderContent();        
            
                // Ends Here
                $content .= $tableCreation;
                return $content;            
            } 
            else 
            {
                return $portlet->renderContent();
            }
    }
}
