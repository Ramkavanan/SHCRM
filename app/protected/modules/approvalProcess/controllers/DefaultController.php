<?php

/*
 * List of Common Functions For Opportunity and Agreement
 *    actionApprovalProcessing();
 * 
 */

class ApprovalProcessDefaultController extends ZurmoModuleController {

    public function actionList() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'listPageSize', get_class($this->getModule()));
        $approvalProcess = new ApprovalProcess(false);
        $searchForm = new ApprovalProcessSearchForm($approvalProcess);
        $dataProvider = $this->resolveSearchDataProvider(
                $searchForm, $pageSize, null, 'ApprovalProcessSearchView'
        );
//        $listViewClassName = 'ApprovalProcessListView';
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view') {
            $mixedView = $this->makeListView(
                    $searchForm, $dataProvider
            );
            $view = new ApprovalProcessPageView($mixedView);
        } else {
            $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
            $view = new ApprovalProcessPageView(ZurmoDefaultViewUtil::
                            makeStandardViewForCurrentUser($this, $mixedView));
        }
        echo $view->render();
    }

    public function actionEdit($id) {
        //get boject by id
        $deptReference = ApprovalProcess::getById(intval($id));

        //Security check
        ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($deptReference);

        //create view and render
        $editAndDetailsView = $this->makeEditAndDetailsView(
                $this->attemptToSaveModelFromPost($deptReference), 'Edit');
        $view = new ApprovalProcessPageView(ZurmoDefaultViewUtil::
                        makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionReassign($id) {
        $approvalProcess = ApprovalProcess::getById(intval($id));
        // ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($deptReference);
        //create view and render        
        $approvalProcess->Status->value = ApprovalProcess::REASSIGNED;
        
        $opptId = $approvalProcess->opportunity->id;        
        $oppDetails = Opportunity::getById(intval($opptId));
        
        if($approvalProcess->agreement->id > 0){
            $reassignView = new AgreementApprovalReassignEditAndDetailsView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($approvalProcess));
        }
        else if($oppDetails->stage->value == 'Final Proposal') {
            $reassignView = new ApprovalGmReassignEditAndDetailsView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($approvalProcess));
        }
        else {
            $reassignView = new ApprovalReassignEditAndDetailsView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($approvalProcess));
        }

        $view = new ApprovalProcessPageView(ZurmoDefaultViewUtil::
                        makeStandardViewForCurrentUser($this, $reassignView));
        echo $view->render();
    }

    protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false) {
        assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
        $savedSuccessfully = false;
        $modelToStringValue = null;
        $postVariableName = get_class($model);
        if (isset($_POST[$postVariableName])) {
            if ($model->Status->value == ApprovalProcess::REASSIGNED) {
                $model->action = '';
            }
            $postData = $_POST[$postVariableName];
            $controllerUtil = static::getZurmoControllerUtil();
            $model = $controllerUtil->saveModelFromPost($postData, $model, $savedSuccessfully, $modelToStringValue, $returnOnValidate);
        }
        if ($savedSuccessfully && $redirect) {
            if ($model->Status->value == ApprovalProcess::REASSIGNED && $redirect) {
                $opptId = $model->opportunity->id;        
                $oppDetails = Opportunity::getById(intval($opptId));
                
                $newApprovalProcess = new ApprovalProcess();
                $newApprovalProcess->date = $model->date;
                $newApprovalProcess->action = 'Step:Step'; //$model->action;
                //$newApprovalProcess->comments = $model->comments;
                
                if($model->agreement->id > 0){
                    $newApprovalProcess->agreement = $model->agreement;
                    $new_redirect_url = Yii::app()->getBaseUrl(true) . '/index.php/agreements/default/details?id=' . $model->agreement->id;                    
                    
                    // For the notify mail link
                    $new_content = 'The Agreement is Re-assigned for Estimator approval. <br><br> <b>Agreement Details:</b> <br>Name: ' . $newApprovalProcess->agreement->name . '<br>Type: ' . $newApprovalProcess->agreement->RecordType . '  <br>Link: <a href="' . $new_redirect_url . '">' . $new_redirect_url . '</a><br> </p> <hr> <br> Thanks. <br> ShinnedHawks';
                    $urlParams = array('/agreements/default/details', 'id' => $newApprovalProcess->agreement->id);
                    $newApprovalProcess->actualapprover = $model->actualgmapprover;
                    $newApprovalProcess->actualgmapprover = $model->actualgmapprover;
                }
                else if($oppDetails->stage->value == 'Final Proposal') {
                    $newApprovalProcess->actualapprover = $model->actualgmapprover;
                    $newApprovalProcess->actualgmapprover = $model->actualgmapprover;
                    $newApprovalProcess->opportunity = $model->opportunity;
                    $new_redirect_url = Yii::app()->getBaseUrl(true) . '/index.php/opportunities/default/details?id=' . $model->opportunity->id;
                    
                    $new_content = 'The Opportunity is Re-assigned for Manager approval. <br><br> <b>Opportunity Details:</b> <br>Name: ' . $newApprovalProcess->opportunity->name . '<br>Type: ' . $newApprovalProcess->opportunity->recordType . '  <br>Link: <a href="' . $new_redirect_url . '">' . $new_redirect_url . '</a><br> </p> <hr> <br> Thanks. <br> ShinnedHawks';
                    $urlParams = array('/opportunities/default/details', 'id' => $newApprovalProcess->opportunity->id);
                }
                else {
                    $newApprovalProcess->opportunity = $model->opportunity;
                    $new_redirect_url = Yii::app()->getBaseUrl(true) . '/index.php/opportunities/default/details?id=' . $model->opportunity->id;
                    
                    $new_content = 'The Opportunity is Re-assigned for Estimator approval. <br><br> <b>Opportunity Details:</b> <br>Name: ' . $newApprovalProcess->opportunity->name . '<br>Type: ' . $newApprovalProcess->opportunity->recordType . '  <br>Link: <a href="' . $new_redirect_url . '">' . $new_redirect_url . '</a><br> </p> <hr> <br> Thanks. <br> ShinnedHawks';
                    $urlParams = array('/opportunities/default/details', 'id' => $newApprovalProcess->opportunity->id);
                    $newApprovalProcess->actualapprover = $model->actualapprover;
                }
                
                $newApprovalProcess->assignedto = $model->assignedto;                
                $newApprovalProcess->Status->value = ApprovalProcess::PENDING;
                $newApprovalProcess->overallstatus->value = $model->overallstatus->value;
                $newApprovalProcess->save();
                
                $actualApproverAccount = User::getById(Yii::app()->user->id);
                $assignedAccount = User::getById($newApprovalProcess->actualapprover->id);

                if (!empty($actualApproverAccount->primaryEmail->emailAddress)){
                    $fromAddress = $actualApproverAccount->primaryEmail->emailAddress;
                } else {
                    $fromAddress = '';
                }
                $recipients = array();
                if (!empty($assignedAccount->primaryEmail->emailAddress)){
                    $recipients = array($assignedAccount->primaryEmail->emailAddress);
                }
                $mailContent = array(
                    'subject' => '[VERTWARE] REQUEST FOR RE-ASSIGNED APPROVAL',
                    'content' => 'Hi ' . $assignedAccount->firstName . ', <br> <p> '.$new_content
                );

                $fromAddress = array(
                    'name' => 'VERTWARE',
                    'address' => $fromAddress
                );
                if(count($recipients) > 0){
                    ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                }

                // $this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
                
                $this->redirect($urlParams);
            } else {
                $this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
            }
        }
        return $model;
    }

    public function actionCreate() {
        $editAndDetailsView = $this->makeEditAndDetailsView(
                $this->attemptToSaveModelFromPost(new ApprovalProcess()), 'Edit');
        $view = new ApprovalProcessPageView(ZurmoDefaultViewUtil::
                        makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionDetails($id) {
        $deptReference = static::getModelAndCatchNotFoundAndDisplayError('ApprovalProcess', intval($id));
        $breadCrumbView = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'ApprovalProcessSearchView', $deptReference);
        ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($deptReference);
        AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($deptReference), 'ApprovalProcessModule'), $deptReference);
        $titleBarAndEditView = $this->makeEditAndDetailsView($deptReference, 'Details');
        $view = new ApprovalProcessPageView(ZurmoDefaultViewUtil::
                        makeStandardViewForCurrentUser($this, $titleBarAndEditView));
        echo $view->render();
    }

    public function actionMassDelete() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'massDeleteProgressPageSize');
        $deptReference = new ApprovalProcess(false);

        $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new ApprovalProcessSearchForm($deptReference), $pageSize, Yii::app()->user->userModel->id, null, 'ApprovalProcessSearchView');
        $selectedRecordCount = $this->getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
        $deptReference = $this->processMassDelete(
                $pageSize, $activeAttributes, $selectedRecordCount, 'ApprovalProcessPageView', $deptReference, ApprovalProcessModule::getModuleLabelByTypeAndLanguage('Plural'), $dataProvider
        );
        $massDeleteView = $this->makeMassDeleteView(
                $deptReference, $activeAttributes, $selectedRecordCount, ApprovalProcessModule::getModuleLabelByTypeAndLanguage('Plural')
        );
        $view = new ApprovalProcessPageView(ZurmoDefaultViewUtil::
                        makeStandardViewForCurrentUser($this, $massDeleteView));
        echo $view->render();
    }

    public function actionExport() {
        $this->export('ApprovalProcessEditAndDetailsView', 'ApprovalProcess');
    }

    public function actionModalList() {
        $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                        $_GET['modalTransferInformation']['sourceIdFieldId'],
                        $_GET['modalTransferInformation']['sourceNameFieldId'],
                        $_GET['modalTransferInformation']['modalId']
        );
        echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider);
    }

    
    //For Submit For Approval And Recall In Agreement Approval Process
    public function actionNewApprovalRequestForAgreement($agmntId, $typ) {
        $isAgreement = TRUE;
        if ($typ != null && $typ != '' && $typ == 1) {
            $agmnt = Agreement::getById($agmntId);
            $linkUrl = Yii::app()->getBaseUrl(true) . '/index.php/agreements/default/details?id=' . $agmntId;
            // To get the Estimator
            $assignedTo = NULL;
            $actualApprovar = NULL;
            if (!empty($agmnt->Estimator)) {
                $assignedTo = $agmnt->Estimator;
                $actualApprovar = $agmnt->Estimator;
            } else {
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Estimator is not assigned to this Agreement.'));
                Yii::app()->end(false);
            }
            $apForSubmit = new ApprovalProcess();
            $apForSubmit->date = date("Y-m-d H:i:s");
            $apForSubmit->assignedto = Yii::app()->user->userModel; //$assignedTo;
            $apForSubmit->actualapprover = $actualApprovar;
            $apForSubmit->agreement = $agmnt;
            $apForSubmit->Status->value = Constant::SUBMITTED;
            if ($apForSubmit->save()) {
                $approvalProcess = new ApprovalProcess();
                $approvalProcess->date = date("Y-m-d H:i:s");
                $approvalProcess->assignedto = $assignedTo;
                $approvalProcess->actualapprover = $actualApprovar;
                $approvalProcess->agreement = $agmnt;
                $approvalProcess->action = 'Step:Step 1';
                $approvalProcess->Status->value = Constant::PENDING;
                $approvalProcess->overallstatus->value = Constant::PENDING;
                if($approvalProcess->save()){
                    $agmnt->Status->value = Constant::PENDINGAGREEMENT;
                    $agmnt->Estimator_Approval_Date = date("Y-m-d H:i:s");
                    $agmnt->save();
                }
                $userAccount = User::getById($agmnt->Estimator->id);
                $recipients = array();
                if (!empty($userAccount->primaryEmail->emailAddress)) {
                    $recipients = array($userAccount->primaryEmail->emailAddress);
                } 
                $mailContent = array(
                    'subject' => '[VERTWARE] REQUEST FOR AGREEMENT ESTIMATOR APPROVAL',
                    'content' => 'Hi ' . $userAccount->firstName . ', <br> <p> The Agreement is submitted for Estimator approval. <br><br> <b>Agreement Details:</b><br>Name: ' . $agmnt->name . '<br>Type: ' . $agmnt->RecordType . '<br>Submitted By: ' . $agmnt->owner . ' <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a><br> </p> <hr> <br> Thanks. <br> ShinnedHawks'
                );

                $fromAddress = array(
                    'name' => 'VERTWARE',
                    'address' => 'notifications@vertware.net'
                );
                if(count($recipients) > 0){
                    ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                }
            } else {
                var_dump($apForSubmit->getErrors());
                die;
            }
        } else {
            ApprovalProcessUtils::changeRecalledStatus($agmntId, $isAgreement);
        }
        echo ApprovalProcessUtils::generateHTMLForApprovalInAgmnt($agmntId);
    }

    //For new approval request
    public function actionNewApprovalRequest($optId, $typ, $stats) {
        $isAgreement = FALSE;
        //Initialization

        if ($typ != null && $typ != '' && $typ == 1) {
            $opt = Opportunity::getById($optId);
            $linkUrl = Yii::app()->getBaseUrl(true) . '/index.php/opportunities/default/details?id=' . $optId;
            // To get the manager
            $gmUserData = NULL;
            $assignedTo = NULL;
            $actualApprovar = NULL;
            if ($opt->managementPricingApproval) {
                $allGmUserData = User::getById($opt->gm->id);//User::getEmailByGmRole();
              // if (count($allGmUserData) > 0) {
                    $gmUserData = $allGmUserData;
              //  }
            }
            if ($opt->stage->value == 'Estimate') {
                $assignedTo = $opt->estimator;
                $actualApprovar = $opt->estimator;
            } else if ($opt->stage->value == 'Final Proposal') {
                if (isset($gmUserData)) {
                    $assignedTo = $gmUserData;
                    $actualApprovar = $gmUserData;
                } else {
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'GM is not assigned to this Organization.'));
                    Yii::app()->end(false);
                }
            }
            $apForSubmit = new ApprovalProcess();
            $apForSubmit->date = date("Y-m-d H:i:s");
            $apForSubmit->assignedto = Yii::app()->user->userModel; //$assignedTo;
            $apForSubmit->actualapprover = $actualApprovar;
            $apForSubmit->opportunity = $opt;
            $apForSubmit->Status->value = ApprovalProcess::SUBMITTED;
            if ($submitApproval = $apForSubmit->save()) {
                $approvalProcess = new ApprovalProcess();
                $approvalProcess->date = date("Y-m-d H:i:s");
                $approvalProcess->assignedto = $assignedTo;
                $approvalProcess->actualapprover = $actualApprovar;
                $approvalProcess->opportunity = $opt;

                if ($opt->stage->value == 'Estimate'){
                    $approvalProcess->action = 'Step:Step 1';
                }
                else if ($opt->stage->value == 'Final Proposal'){
                    $approvalProcess->action = 'Step:Step 2';
                    $approvalProcess->actualgmapprover = $actualApprovar;
                }

                $approvalProcess->Status->value = ApprovalProcess::PENDING;
                $approvalProcess->overallstatus->value = ApprovalProcess::PENDING;
                $approvalProcess->save();
                $userAccount = User::getById($opt->estimator->id);
                $recipients = array();
                if ($opt->stage->value == 'Estimate') {
                    if (!empty($userAccount->primaryEmail->emailAddress)){
                        $recipients = array($userAccount->primaryEmail->emailAddress);
                    }
                    $mailContent = array(
                        'subject' => '[VERTWARE] REQUEST FOR ESTIMATOR APPROVAL',
                        'content' => 'Hi ' . $userAccount->firstName . ', <br> <p> The Opportunity is submitted for Estimator approval. <br><br> <b>Opportunity Details:</b><br>Name: ' . $opt->name . '<br>Type: ' . $opt->recordType . '<br>Submitted By: ' . $opt->owner . ' <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a><br> </p> <hr> <br> Thanks. <br> ShinnedHawks'
                    );

                    $fromAddress = array(
                        'name' => 'VERTWARE',
                        'address' => 'notifications@VERTWARE.com'
                    );
                    if(count($recipients) > 0){
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                    }
                }
                else if ($opt->stage->value == 'Final Proposal') {
                    if ($opt->managementPricingApproval) {
                        // Commented and added to get the manager email

                        $name = $gmUserData->firstName;
                        if (!empty($gmUserData->primaryEmail->emailAddress)){
                            $recipients = array($gmUserData->primaryEmail->emailAddress);
                        }
                    } else {
                        $name = $userAccount->firstName;
                        if (!empty($userAccount->primaryEmail->emailAddress)){
                            $recipients = array($userAccount->primaryEmail->emailAddress);
                        }
                    }
                    $mailContent = array(
                        'subject' => '[VERTWARE] REQUEST FOR MANAGER APPROVAL',
                        'content' => 'Hi ' . $name . ', <br> <p> The Opportunity is submitted for Manager Approval. <br><br> <b>Opportunity Details:</b><br>Name: ' . $opt->name . '<br>Type: ' . $opt->recordType . '<br>Submitted By: ' . $opt->owner . ' <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a><br> </p> <hr> <br> Thanks. <br> ShinnedHawks'
                    );
                    $fromAddress = array(
                        'name' => 'VERTWARE',
                        'address' => 'notifications@VERTWARE.com'
                    );
                    if(count($recipients) > 0){
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);    
                    }
                }
            } else {
                var_dump($apForSubmit->getErrors());
                die;
             }
        } else {
            ApprovalProcessUtils::changeRecalledStatus($optId, $isAgreement);
          }
        echo ApprovalProcessUtils::generateHTMLForApprovalInOppt($optId);
    }

    protected function resolvePersonOrAccountFromGet($relatedId = null, $relatedModelClassName = null) {
        $personOrAccount = null;
        if ($relatedId != null && $relatedModelClassName != null) {
            $personOrAccount = $relatedModelClassName::getById((int) $relatedId);
            //Only attempt to populate email if the user has write permissions
            if ($relatedModelClassName == 'Contact' &&
                    $personOrAccount->primaryEmail->emailAddress == null &&
                    ControllerSecurityUtil::doesCurrentUserHavePermissionOnSecurableItem($personOrAccount, Permission::WRITE)) {
                $this->redirect(array($this->getId() . '/populateContactEmailBeforeCreating',
                    'id' => $personOrAccount->id));
                Yii::app()->end(false);
            }
        }
        return $personOrAccount;
    }

    //For approval history part
    public function AppProcessHistory($optId, $topVl, $appApproval) {

        $statusArr = array(1 => 'Pending', 2 => 'Approved', 3 => 'Rejected', 4 => 'Reassigned', 5 => 'Recalled');

        $apTab = '<hr style="color:#eee;"><div class="view-toolbar-container clearfix">
								<div class="panel">
									<div class="panelTitle">
										Approval History 
                                     </div><div id="approvalHistoryListMsgId" style="margin:10px 0px 10px 350px; color:green; font-weight:bold;"></div>';
        $apTab .= '<hr>';



        if (($topVl == 2) || ($topVl == 3) || ($topVl == 5) || (count($appApproval) == 0)) { //For checking the current opportunity status
            $apTab .= '<div class="form-toolbar clearfix">
							<a id="subApprovalProces" name="Submit for Approval" class="attachLoading z-button" href="#approvalHistoryListId"  onClick="javascript:searchProducts(\'' . $optId . '\',2,1);">
								<span class="z-spinner"></span>
								<span class="z-icon"></span>
								<span class="z-label">Submit for Approval</span>
							</a>       
							</div><br />';
        } else {
            $apTab .= '<div class="form-toolbar clearfix">
							<a id="subApprovalProces" name="Recall the Approval" class="attachLoading z-button" href="#approvalHistoryListId"  onClick="javascript:searchProducts(\'' . $optId . '\',2,5);">
								<span class="z-spinner"></span>
								<span class="z-icon"></span>
								<span class="z-label">Recall the Request</span>
							</a>       
							</div><br />';
        }

        $apTab .= '<table class="items selected_products_table">
						   <colgroup span="7"></colgroup>';

        $apTab .= '<thead style="font-weight: bold; background-color:#E6E6E6; color: #999;vertical-align: inherit; padding: 5px;">
                              <th style="width: 10%; font-weight: bold;">Action</th>
                              <th style="width: 20%; font-weight: bold;">Date</th>
                              <th style="width: 10%; font-weight: bold;">Status</th>
                              <th style="width: 15%; font-weight: bold;">Assigned To</th>
                              <th style="width: 15%; font-weight: bold;">Actual Approver</th>
                              <th style="width: 20%; font-weight: bold;">Comments</th>
                              <th style="width: 15%;font-weight: bold;">Overall Status</th>
                           </thead><tbody>';

        if (count($appApproval) > 0) {
            $titl = 0;
            $showStat = $showStatTmp = array();
            foreach ($appApproval as $appApprovalTmp) {

                if ($titl != $appApprovalTmp->action) {
                    $titl = $appApprovalTmp->action;
                }

                $date = new DateTime($appApprovalTmp->date);
                $dtmp = $date->format("m/d/Y h:m a");

                $showStatTmp['Date'] = $dtmp;
                $showStatTmp['Status'] = $statusArr[$appApprovalTmp->status];
                $showStatTmp['AssignedTo'] = $appApprovalTmp->assignedto;
                $showStatTmp['ActualApprover'] = $appApprovalTmp->actualapprover;
                $showStatTmp['Comments'] = str_replace('#@#', '', $appApprovalTmp->comments);
                $showStatTmp['OverallStatus'] = $statusArr[$appApprovalTmp->status];

                $showStat['Step' . $appApprovalTmp->action][] = $showStatTmp;
                $showStat['Step' . $appApprovalTmp->action]['ols'] = $statusArr[$appApprovalTmp->status];
            }

            foreach ($showStat as $sKey => $sValArr) {

                $spanClrTmp = "#fff";
                if ($showStat[$sKey]['ols'] == "Approved") {
                    //$spanClrTmp = "#619025";
                    $spanClrTmp = "green";
                } elseif ($showStat[$sKey]['ols'] == "Pending") {
                    $spanClrTmp = "#262770";
                } else {
                    $spanClrTmp = "#a82d31";
                    //$spanClrTmp = "red";
                }

                $apTab .= '<th colspan="6" style="background-color:gray; color:white; font-weight:bold;">Step:' . $sKey . '</th><th style="background-color:gray; color:#fff; font-weight:bold;"><span style="background-color:' . $spanClrTmp . '; padding:2px 5px; width:150px;">' . $showStat[$sKey]['ols'] . '</span></th>';

                for ($j = (count($sValArr) - 2); $j >= 0; $j--) {

                    $olStat = '';
                    if ($sValArr[$j]['OverallStatus'] == 'Recalled') {
                        $olStat = $sValArr[$j]['OverallStatus'];
                    }

                    $spanClr = "#fff";
                    if ($sValArr[$j]['Status'] == "Approved") {
                        //$spanClr = "#619025";
                        $spanClr = "green";
                    } else {
                        //$spanClr = "#a82d31";
                        $spanClr = "red";
                    }

                    $userAT = $userAA = array();
                    $userATStr = $userAAStr = '';

                    $userAT = User::getById(intval($sValArr[$j]['AssignedTo']));
                    $userATStr = $userAT->attributeNameToBeanAndClassName['department'][0]->firstname . " " . $userAT->attributeNameToBeanAndClassName['department'][0]->lastname;

                    $userAA = User::getById(intval($sValArr[$j]['ActualApprover']));
                    $userAAStr = $userAA->attributeNameToBeanAndClassName['department'][0]->firstname . " " . $userAA->attributeNameToBeanAndClassName['department'][0]->lastname;


                    $apTab .= '<tr style="padding-top: 2px; text-align: left;">
                                    <td ></td>
                                    <td >' . $sValArr[$j]['Date'] . '</td>
                                    <td >' . $sValArr[$j]['Status'] . '</td>
                                    <td >' . $userATStr . '</td>
                                    <td >' . $userAAStr . '</td>
                                    <td >' . $sValArr[$j]['Comments'] . '</td>
                                    <td style="color:' . $spanClr . ';">' . $olStat . '</td>
                               </tr>';
                }
            }
        } else {
            $apTab .= '<th colspan="7" style="background-color:gray; color:white; font-weight:bold;"><span class="empty">No results found</span></th>';
        }

        $apTab .= '</tbody></table></div></div>';

        echo $apTab;
    }

    //For approval processing (accept or reject request) For Opportunity and Agreement 
    public function actionApprovalProcessing($optId = null, $agmntId = null, $apId, $redURL=null) {
        if (!empty($optId) && empty($agmntId)) {
            // To validate the user            
            $optData = Opportunity::getById($optId);
            if ($optData->stage->value == Constant::FINALPROPOSAL) {
                $appProcessData = ApprovalProcess::getAllAppProcess($optId);
                if (count($appProcessData) > 0) {
                    if ($appProcessData[0]->action != 'Step:Step 1') {
                        if ($appProcessData[0]->overallstatus->value == Constant::PENDING) {
                            if ($appProcessData[0]->assignedto->id != Yii::app()->user->id) {
                                Yii::app()->user->setFlash('notification',
                                Zurmo::t('ZurmoModule', 'Permission not allowed.'));
                                $this->redirect(Yii::app()->createUrl('/opportunities/default/details?id='.$optId));
                                Yii::app()->end(false);
                            }
                        }
                    }
                    else
                    {
                        Yii::app()->user->setFlash('notification',
                        Zurmo::t('ZurmoModule', 'Permission not allowed.'));
                        $this->redirect(Yii::app()->createUrl('/opportunities/default/details?id='.$optId));
                        Yii::app()->end(false);
                    }
                }                
            }            
            // Ends Here
            
            $opportunityReqView = new ApprovalProcessRequestView($optId, $agmntId, $apId, NULL);
            $zurmoView = new ApprovalProcessPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $opportunityReqView));
            echo $zurmoView->render();
        }else if(empty($optId) && !empty($agmntId)){
            $agreementReqView = new ApprovalProcessRequestView($optId, $agmntId, $apId, NULL);
            $zurmoView = new ApprovalProcessPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $agreementReqView));
            echo $zurmoView->render();            
        }
         else {
            $this->redirect($redURL);
        }
    }
    
    public function actionAcceptOrRejectapprovalprocessForAgmnt($comment, $status, $agmntId, $apId){
        $agreement = Agreement::getById($agmntId);
        $linkUrl = Yii::app()->getBaseUrl(true) . '/index.php/agreements/default/details?id=' . $agmntId;
        $ownerAccount = User::getById($agreement->owner->id);
        $recipients = array();
        if (!empty($ownerAccount->primaryEmail->emailAddress)){
            $recipients = array($ownerAccount->primaryEmail->emailAddress);
        }
        $fromAddress = array(
            'name' => 'VERTWARE',
            'address' => 'notifications@VERTWARE.com'
        );
        if ($apId != null) {
            $apprlPrcs = ApprovalProcess::getById($apId);
            if ($apprlPrcs != null) {
                $apprlPrcs->comments = $comment;
                if ($status != null && $status == 'accept') {
                    $apprlPrcs->Status->value = Constant::APPROVED;
                    $apprlPrcs->overallstatus->value = Constant::APPROVED;
                    $agmnt = Agreement::getById($apprlPrcs->agreement->id);
                    $agmnt->Status->value = Constant::ACTIVEAGREEMENT;
                    $agmnt->newCurrent_GPM = 100; // To set 100 as default
                    $agmnt->save();
                    $mailContent = array(
                            'subject' => '[VERTWARE] AGREEMENT ESTIMATOR  APPROVAL',
                            'content' => 'Hi ' . $ownerAccount->firstName . ', <br> <p> Your estimator submission <b>' . $agreement->name . '</b>  has been Approved <br> <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a> <br><hr>Thanks. <br> ShinnedHawks</p>'
                        );
                    if(count($recipients) > 0){
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                    }
                }else {
                    $apprlPrcs->Status->value = Constant::REJECTED;
                    $apprlPrcs->overallstatus->value = Constant::REJECTED;

                    // To send the mail
                    $mailContent = array(
                        'subject' => '[VERTWARE] AGREEMENT ESTIMATOR REJECTION',
                        'content' => 'Hi ' . $ownerAccount->firstName . ', <br> <p> Your estimator submission <b>' . $agreement->name . '</b>  has been rejected, the agreement status has been reverted to Estimate. Please discuss with ' . $agreement->Estimator . ' and resubmit.  <br> <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a> <br></p> 
                                    <hr> <br>Thanks. <br>ShinnedHawks'
                    );
                    if(count($recipients) > 0){
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                    }
                    // Ends Here
                }
            }else {
                echo 'Failed';
            }
       }
       if ($apprlPrcs->save(false)) {
            $this->redirect('/app/index.php/agreements/default/details?id=' . $agmntId);
        } else {
            $this->redirect('/app/index.php/agreements/default/details?id=' . $agmntId);
        }
    }    

    public function actionAcceptOrRejectapprovalprocess($comment, $status, $optId, $apId, $job = 'false', $renDate = null) {
        $ren_Date = date("Y-m-d", strtotime($renDate));
        $opportunity = Opportunity::getById($optId);
        $linkUrl = Yii::app()->getBaseUrl(true) . '/index.php/opportunities/default/details?id=' . $optId;
        $ownerAccount = User::getById($opportunity->owner->id);
        $recipients = array();
        if (!empty($ownerAccount->primaryEmail->emailAddress)){
            $recipients = array($ownerAccount->primaryEmail->emailAddress);
        }
        $fromAddress = array(
            'name' => 'VERTWARE',
            'address' => 'notifications@VERTWARE.com'
        );
        if ($apId != null) {
            $apprlPrcs = ApprovalProcess::getById($apId);
            if ($apprlPrcs != null) {
                $apprlPrcs->comments = $comment;
                if ($status != null && $status == 'accept') {
                    $apprlPrcs->Status->value = Constant::APPROVED;
                    $apprlPrcs->overallstatus->value = Constant::APPROVED;
                    
                    
                    $opprt = Opportunity::getById($apprlPrcs->opportunity->id);

                    if ($opprt->stage->value == Constant::ESTIMATE) {
                        $opprt->stage->value = Constant::FINALPROPOSAL;
                        $opprt->managementPricingApproval = TRUE;
                        $opprt->createAgreement = TRUE;
                        $opprt->estimatorApprovalDate = DateTimeUtil::getTodaysDate();
                    } else if ($opprt->stage->value == Constant::FINALPROPOSAL) {
                        $opprt->stage->value = Constant::AGREEMENT;
                        $opprt->managementPricingApprovalDate = DateTimeUtil::getTodaysDate();
                    }
                    $opprt->save();
                    if ($opportunity->stage->value == 'Final Proposal') {
                        $mailContent = array(
                            'subject' => '[VERTWARE] OPPORTUNITY ESTIMATOR  APPROVAL',
                            'content' => 'Hi ' . $ownerAccount->firstName . ', <br> <p> Your estimator submission <b>' . $opportunity->name . '</b>  has been Approved ! The opportunity is now available for Management Approval.<br> <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a> <br><hr>Thanks. <br> ShinnedHawks</p>'
                        );
                    } else if ($opportunity->stage->value == 'Agreement') {
                        $mailContent = array(
                            'subject' => '[VERTWARE] OPPORTUNITY MANAGEMENT APPROVAL',
                            'content' => 'Hi ' . $ownerAccount->firstName . ', <br> <p> Your proposal for <b>' . $opportunity->name . '</b>  has been approved. <br> <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a> <br><hr>Thanks. <br> ShinnedHawks</p>'
                        );
                    }
                    if(count($recipients) > 0){
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                    }
                    if ($opprt->stage->value == Constant::AGREEMENT && $opprt->createAgreement) {
                        OpportunityUtils::convertOpportunityToAgreement(intval($opprt->id));
                        if(!empty($opprt->agreement->id)){
                            $agmnt = Agreement::getById($opprt->agreement->id);
                            $agmnt->Agreement_Expiration = $ren_Date;
                            if (!$agmnt->save()) {
                                throw new FailedToSaveModelException();
                            }
                        }
                    }
                } else {
                    $apprlPrcs->Status->value = Constant::REJECTED;
                    $apprlPrcs->overallstatus->value = Constant::REJECTED;

                    // To send the mail
                    $mailContent = array(
                        'subject' => '[VERTWARE] OPPORTUNITY ESTIMATOR REJECTION',
                        'content' => 'Hi ' . $ownerAccount->firstName . ', <br> <p> Your estimator submission <b>' . $opportunity->name . '</b>  has been rejected, the opportunity status has been reverted to Estimate. Please discuss with ' . $opportunity->estimator . ' and resubmit.  <br> <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a> <br></p> 
                                    <hr> <br>Thanks. <br>ShinnedHawks'
                    );
                    if(count($recipients) > 0){
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                    }
                    // Ends Here
                }
            } else {
                echo 'Failed';
            }
        }
        
        if ($apprlPrcs->save()) {
            if($job == 'true'){
                if(!empty($opportunity->agreement->Total_MHR)){
                    $this->redirect(Yii::app()->getBaseUrl(true) . '/index.php/jobScheduling/default/CreateStep1?agreementId='.$opportunity->agreement->id);
                }else{
                    Yii::app()->user->setFlash('notification',
                        Zurmo::t('ZurmoModule', 'No Labour Products Available.'));
                    $this->redirect(Yii::app()->createUrl('/agreements/default/details?id='.$opportunity->agreement->id));
                    Yii::app()->end(false);
                }    
            }else{
                $this->redirect('/app/index.php/opportunities/default/details?id=' . $optId);
            }
        }  else {
                $this->redirect('/app/index.php/opportunities/default/details?id=' . $optId);
        }
    }

}

?>
