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
class AgreementRelationsSecuredPortletFrameView extends ModelRelationsSecuredPortletFrameView {

    private $showAsTabs;

    public function __construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible = true, $portletsAreMovable = true, $showAsTabs = false, $layoutType = '100', $portletsAreRemovable = true) {
        parent::__construct($controllerId, $moduleId, $uniqueLayoutId, $params, $metadata, $portletsAreCollapsible, $portletsAreMovable, $showAsTabs, $layoutType, $portletsAreRemovable);
    }

    //put your code here
    protected function renderPortlets($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true) {
        if (!$this->showAsTabs) {
            return $this->renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible, $portletsAreMovable, $portletsAreRemovable);
        }
        assert('is_bool($portletsAreCollapsible) && $portletsAreCollapsible == false');
        assert('is_bool($portletsAreMovable) && $portletsAreMovable == false');
        return $this->renderPortletsTabbed();
    }

    protected function renderPortletsTabbed() {
        assert('count($this->portlets) == 1 || count($this->portlets) == 0');
        if (count($this->portlets) == 1) {
            $tabItems = array();
            foreach ($this->portlets[1] as $noteUsed => $portlet) {
                $tabItems[$portlet->getTitle()] = array(
                    'id' => $portlet->getUniquePortletPageId(),
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

    protected function renderPortletHelper($uniqueLayoutId, $portletsAreCollapsible = true, $portletsAreMovable = true, $portletsAreRemovable = true) {
        assert('is_string($uniqueLayoutId)');
        assert('is_bool($portletsAreCollapsible)');
        assert('is_bool($portletsAreMovable)');
        assert('is_bool($portletsAreRemovable)');
        $juiPortletsWidgetItems = array();
        foreach ($this->portlets as $column => $columnPortlets) {
            foreach ($columnPortlets as $position => $portlet) {
                $className = get_class($portlet->getView());
                if (method_exists($className, 'canUserRemove')) {
                    $removable = $className::canUserRemove();
                } else {
                    $removable = $portletsAreRemovable;
                }
                $additionalOptionMenuItems = array();
                if (method_exists($className, 'getAdditionalOptionMenuItems')) {
                    $additionalOptionMenuItems = $className::getAdditionalOptionMenuItems();
                }
                if ($className == 'AgreementDetailsPortletView') {
                    $juiPortletsWidgetItems[$column][$position] = array(
                        'id' => $portlet->id,
                        'uniqueId' => $portlet->getUniquePortletPageId(),
                        'title' => $portlet->getTitle(),
                        'content' => $this::renderViewForAgreementWithAgmntProduct($portlet).$this::renderViewForAgreementWithJobs($portlet), ////$portlet->renderContent(),
                        'headContent' => $portlet->renderHeadContent(),
                        'editable' => $portlet->isEditable(),
                        'collapsed' => $portlet->collapsed,
                        'removable' => $removable,
                        'uniqueClass' => $this->resolveUniqueClass($portlet),
                        'portletParams' => $portlet->getPortletParams(),
                        'additionalOptionMenuItems' => $additionalOptionMenuItems,
                    );
                } else {
                    $juiPortletsWidgetItems[$column][$position] = array(
                        'id' => $portlet->id,
                        'uniqueId' => $portlet->getUniquePortletPageId(),
                        'title' => $portlet->getTitle(),
                        'content' => $portlet->renderContent(),
                        'headContent' => $portlet->renderHeadContent(),
                        'editable' => $portlet->isEditable(),
                        'collapsed' => $portlet->collapsed,
                        'removable' => $removable,
                        'uniqueClass' => $this->resolveUniqueClass($portlet),
                        'portletParams' => $portlet->getPortletParams(),
                        'additionalOptionMenuItems' => $additionalOptionMenuItems,
                    );
                }
            }
        }
        $cClipWidget = new CClipWidget();
        $cClipWidget->beginClip("JuiPortlets");
        $cClipWidget->widget('application.core.widgets.JuiPortlets', array(
            'uniqueLayoutId' => $uniqueLayoutId,
            'moduleId' => $this->moduleId,
            'saveUrl' => Yii::app()->createUrl($this->moduleId . '/defaultPortlet/SaveLayout'),
            'layoutType' => $this->getLayoutType(),
            'items' => $juiPortletsWidgetItems,
            'collapsible' => $portletsAreCollapsible,
            'movable' => $portletsAreMovable,
        ));
        $cClipWidget->endClip();
        return $cClipWidget->getController()->clips['JuiPortlets'];
    }

        protected function renderViewForAgreementWithJobs(Portlet $portlet) {
            $agmntId = $this->params['relationModel']->modelClassNameToBean['Agreement']->id;
            $jobSchedules = JobScheduling::getJobsByAgmntId($agmntId);
            $AccountManagerGroup = Group::getByName(User::ACCOUNTMANAGER);
            $userIdArr = array();
            foreach($AccountManagerGroup->users as $AccountManagerUserId)
            {
                $userIdArr[] = $AccountManagerUserId->id;
            }
            $isInAccountManageGroup = FALSE;
            if(in_array(Yii::app()->user->userModel->id, $userIdArr)){
                $isInAccountManageGroup = TRUE;
            }
            $jobTable = '';
           
            if ($this->params['relationModel']->modelClassNameToBean['Agreement']->id != null) {
                $id = $this->params['relationModel']->modelClassNameToBean['Agreement']->id;
                $agreement = Agreement::getById($id);
            }
//            $jobContent = $portlet->renderContent();
//            $jobTable = '<div class="panelTitle">Jobs</div>';
            
            $jobTable = '<div class="form-toolbar clearfix" id = "createButton">';
            
            if(!empty(Yii::app()->user->userModel->isRootUser) || $isInAccountManageGroup == TRUE || Yii::app()->user->userModel->role->name == Constant::GM){
//                if (($agreement->Status->value == Constant::DRAFTAGREEMENT && $agreement->Agreement_Type->value == 'Opportunity' )|| $agreement->Status->value == Constant::ACTIVEAGREEMENT ) {
                if ($agreement->Status->value == Constant::ACTIVEAGREEMENT ) {
                    if ($agreement->Total_MHR > 0) {
                        $jobTable .= '<a id="createJobsInAgmnt" name="createJobs" class="attachLoading z-button" href="/app/index.php/jobScheduling/default/createStep1?agreementId=' . $id . '">
                            <span class="z-spinner"></span>
                            <span class="z-icon"></span>
                            <span class="z-label">
                                Schedule a Job
                            </span>
                        </a>';
                    }
                }                   
            }  
            $jobTable .= '</div> <div class="view-toolbar-container clearfix"><div class="panelTitle">Job details</div><div class="panel" style = "padding: 2%;">';

            $jobTable .= '<div style = "border : solid #dfdfdf 1px;"><table class="items" cellpadding="2" cellspacing="0"><colgroup span="5"></colgroup>';

            $jobTable .= '<thead style="font-weight: bold; color : #999; vertical-align: inherit;">
                                        <th style="font-weight: bold;">Job Name</th>
                                        <th style="font-weight: bold;">Crew Name</th>
                                        <th style="font-weight: bold;">Status</th>
                                        ';
            $jobTable .= ' </thead><tbody>';
            foreach ($jobSchedules as $jobSchedule) {
                if($jobSchedule->archive != Constant::ARCHIVE){
                    $jobTable .= '<tr>';
                    foreach($AccountManagerGroup->users as $AccountManagerUserId)
                    {
                        $userIdArr[] = $AccountManagerUserId->id;
                    }
                    $isInAccountManageGroup = FALSE;
                    if(in_array(Yii::app()->user->userModel->id, $userIdArr)){
                        $isInAccountManageGroup = TRUE;
                    }
                    //For normal user job name not clickabel
                    if(empty(Yii::app()->user->userModel->isRootUser) && $isInAccountManageGroup == FALSE && Yii::app()->user->userModel->role->name !== Constant::GM){
                        $jobTable .='<td style="width: 25%;  padding-top: 2px; text-align: left;">' . $jobSchedule->name . '</td>';                        
                    } 
                    //For super user, Account Manager(Group) and General manager(Role) job name id clickabel
                    else{    
                        $jobTable .='<td style="width: 25%;  padding-top: 2px; text-align: left;"><a href="/app/index.php/jobScheduling/default/details?id='.$jobSchedule->id.'">' . $jobSchedule->name . '</a></td>';
                    }
                        $jobTable .='<td style="width: 15%;  padding-top: 2px; text-align: left;">' . $jobSchedule->crewName . '</td>
                                            <td style="width: 25%;  padding-top: 2px; text-align: left;">' . $jobSchedule->status . '</td></tr>';

                    
                }         
            }
            $jobTable .= '</tbody></table></div></div></div>';
            $jobContent = $jobTable;
            return $jobContent;
        }
            
    
    protected function renderViewForAgreementWithAgmntProduct(Portlet $portlet) {
        if ($this->params['relationModel']->modelClassNameToBean['Agreement']->id != null) {
            $id = $this->params['relationModel']->modelClassNameToBean['Agreement']->id;
            $agreement = Agreement::getById($id);

            $data = AgreementProduct::getAllAgmntProducts($id);
            //$data = AgreementProduct::getAllByAgmntId($id);
            $tableCreation = '';
            $addproductBtn = '';
            $approvalProcess = '';
            $approvalProcess .= '<div id="approvalProcess">' . ApprovalProcessUtils::generateHTMLForApprovalInAgmnt($id) . '</div>';
            if ($agreement->Agreement_Type->value == Constant::CLONEAGREEMENT) {
                
                if($agreement->Status->value != Constant::DEACTIVATED)
                {
                    $addproductBtn = '<div class="form-toolbar clearfix" id = "addProductButton">
                                                   <a id="addProductInAgmnt" name="Add Products" class="attachLoading z-button" href="/app/index.php/agreementProducts/default/AddProductsInAgreement?agmntId=' . $id . '">
                                                       <span class="z-spinner"></span>
                                                       <span class="z-icon"></span>
                                                       <span class="z-label">
                                                           Add Products
                                                       </span>
                                                   </a>
                                   </div>';
                }
            }
            if (count($data) > 0) {
                $content = $portlet->renderContent();
                // For the Charts                
                if($agreement->Total_MHR > 0){
                    $tableCreation .= '<div class="panelTitle">Man Hour</div>';
                    $tableCreation .= $this::agreementChartView($id);
                    $tableCreation .= '<hr>';
                }
                
                if($agreement->Total_Equipment > 0){
                    $tableCreation .= '<div class="panelTitle">Equipment</div>';
                    $tableCreation .= $this::agreementChartEquipmentView($id);
                    $tableCreation .= '<hr>';
                }    
                    
                if($agreement->Total_Material > 0){
                    $tableCreation .= '<div class="panelTitle">Material</div>';
                    $tableCreation .= $this::agreementChartMaterialView($id);
                }    
                // Ends Here
                $tableCreation .= $addproductBtn;
                $tableCreation .= '<div class="view-toolbar-container clearfix"><div class="panelTitle">Agreement Products</div><div class="panel" style = "padding: 2%;">';
                $tableCreation .= '<div style = "border : solid #dfdfdf 1px;"><table class="items" cellpadding="2" cellspacing="0"><colgroup span="5"></colgroup>';

                $tableCreation .= '<thead style="font-weight: bold; color : #999; vertical-align: inherit;">
                                            <th style="font-weight: bold;">Product Code</th>
                                            <th style="font-weight: bold;">Product Name</th>
                                            <th style="font-weight: bold; text-align: center;">Category</th>
                                            <th style="font-weight: bold; text-align: center;">Quantity</th>';
                if ($agreement->RecordType == OpportunityUtils::RECURRINGAGREEMENT) {
                    $tableCreation .= '<th style="font-weight: bold;">Frequency</th>';
                }

                $tableCreation .= '<th style="font-weight: bold;">Unit of Measure</th>
                                       </thead><tbody>';
                foreach ($data as $row) {
                    $tableCreation .= '<tr><td style="width: 10%;  padding-top: 2px; text-align: left;">' . $row->costbook->productcode . '</td>
                                               <td style="width: 25%;  padding-top: 2px; text-align: left;">' . $row->name . '</td>
                                               <td style="width: 25%;  padding-top: 2px; text-align: center;">' . $row->Category . '</td>
                                               <td style="width: 15%;  padding-top: 2px; text-align: center;">' . $row->Quantity . '</td>';
                    if ($agreement->RecordType == OpportunityUtils::RECURRINGAGREEMENT) {
                        $tableCreation .= '<td style="width: 15%;  padding-top: 2px; text-align: center;">' . $row->Frequency . '</td>';
                    }

                    $tableCreation .= '<td style="width: 10%;  padding-top: 2px; text-align: center;">' . $row->costbook->unitofmeasure . '</td>
                                            </tr>';
                }
                $tableCreation .= '</tbody></table></div></div></div>';
                $tableCreation .= $this->renderScripts();

                //For Approval Process View
                if ($agreement->Agreement_Type->value == Constant::CLONEAGREEMENT) {
                    if($agreement->Status->value != Constant::DEACTIVATED)
                    {
                        $tableCreation .= $approvalProcess;
                    }
                }
                //Ends Here
                // For the Tracking View
                $tableCreation .= $this::agreementTrackingView($id, $agreement->Status->value);

                // Ends Here
                $content .= $tableCreation;

                return $content;
            } else {
                return $portlet->renderContent();
            }
        } else {
            return $portlet->renderContent();
        }
    }

    protected function getAllAgreementProducts($id) {
        $mysql = 'SELECT * FROM agreementproduct WHERE agreement_id =\'' . intval($id) . '\'';
        $rows = ZurmoRedBean::getAll($mysql);
        return $rows;
    }

    protected function agreementChartView($id) {
        $chart_data = new AgreementByTotalManHourChartView('AgreementByTotalMHR', null, null);
        return $chart_data->setAgId($id);
    }

    protected function agreementChartEquipmentView($id) {
        $eq_chart_data = new AgreementByTotalEquipmentChartView('AgreementByTotalEquipment', null, null);
        return $eq_chart_data->setAgreementId($id);
    }

    protected function agreementChartMaterialView($id) {
        $mat_chart_data = new AgreementByTotalMaterialChartView('AgreementByTotalMaterial', null, null);
        return $mat_chart_data->setAgreementId($id);
    }

    protected function agreementTrackingView($id, $status) {

        $data = AgreementTracking::getAgreementTrackingByAgreementId($id);

        $tableCreation = '<div class="view-toolbar-container clearfix">';
        if ($status == Constant::ACTIVEAGREEMENT) {
            $tableCreation .= '<div class="form-toolbar clearfix padding-2">
                            <a id="addTracking" name="Add Tracking" class="attachLoading z-button" href="/app/index.php/agreementTracking/default/AddNewTracking?agreementId=' . $id . '"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">New Agreement Tracking</span></a>
                        </div>';
        }

        if (isset($data) && !empty($data)) {
            $tableCreation .='<div class="panelTitle">Agreement Tracking</div>
                                <div class="panel" style="padding: 2%;">
                               <div style = "border : solid #dfdfdf 1px;"><table class="items" style="padding: 1%; text-align: left; vertical-align: bottom; font-size: 11px;"  border="0" cellpadding="2" cellspacing="0" width="100%">

                        <colgroup span="7"></colgroup>';

            $tableCreation .= '<thead style="font-weight: bold; color : #999; vertical-align: inherit;">
                                            <th style="font-weight: bold;">Id</th>
                                            <th style="font-weight: bold;">Name</th>
                                            <th style="font-weight: bold;">Tracking Date</th>
                                            <th style="font-weight: bold;">Quantity Consumed</th>
                                            <th style="font-weight: bold;">Selected Products</th>  
                                            <th style="font-weight: bold;">Non-Agreement Products</th>   
                                            <th style="font-weight: bold;">MHR</th>
                                       </thead><tbody>';
            foreach ($data as $row) {
                $tableCreation .= '<tr>
                                                <td style="text-align: left;"> <a href="/app/index.php/agreementTracking/default/details?id=' . $row->id . '"> AT' . $row->id . ' </a></td>';
                                                
                if($row->name == 'AT'.$row->id)
                    $tableCreation .= '<td style="text-align: left;"> <a href="/app/index.php/agreementTracking/default/details?id=' . $row->id . '"></a></td>';
                else
                    $tableCreation .= '<td style="text-align: left;"> <a href="/app/index.php/agreementTracking/default/details?id=' . $row->id . '"> '.$row->name.'</a></td>';
                
                                               $tableCreation .= '<td style="text-align: center;"> <a href="/app/index.php/agreementTracking/default/edit?id=' . $row->id . '">' . DateTimeUtil::resolveValueForDateLocaleFormattedDisplay($row->tracking_date) . '</a> </td><td style="text-align: center;">' . $row->total_quantity_consumed . '</td>
                                                <td style="text-align: center;">' . $row->total_selected_products . '</td>
                                                <td style="text-align: center;">' . $row->total_non_agreement_products . '</td>
                                                <td style="text-align: center;">' . $row->total_mhr . '</td>
                                          </tr>';
            }
            $tableCreation .= '</tbody></table></div></div></div>';
        }
        return $tableCreation;
    }

    //For rendering the approval process js file
    protected function renderScripts() {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                        Yii::getPathOfAlias('application.modules.approvalProcess.elements.assets')) . '/ApprovalProcessTemplateUtils.js', CClientScript::POS_END);
    }

}
