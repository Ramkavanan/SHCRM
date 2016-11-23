<?php

/**
 * Agreement controller to control the route functionality
 *
 * @author Ramachandran.K (ramakavanan@gmail.com)
 */
class AgreementsDefaultController extends ZurmoModuleController {
    /*     * public function filters()
      {
      $modelClassName   = $this->getModule()->getPrimaryModelName();
      $viewClassName    = $modelClassName . 'EditAndDetailsView';
      return array_merge(parent::filters(),
      array(
      array(
      ZurmoBaseController::REQUIRED_ATTRIBUTES_FILTER_PATH . ' + create, createFromRelation, edit',
      'moduleClassName' => get_class($this->getModule()),
      'viewClassName'   => $viewClassName,
      ),
      array(
      ZurmoModuleController::ZERO_MODELS_CHECK_FILTER_PATH . ' + list, index',
      'controller' => $this,
      ),
      )
      );
      } */

    public function actionList() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'listPageSize', get_class($this->getModule()));
        $agreement = new Agreement(false);
        $searchForm = new AgreementsSearchForm($agreement);
        $listAttributesSelector = new ListAttributesSelector('AgreementsListView', get_class($this->getModule()));
        $searchForm->setListAttributesSelector($listAttributesSelector);
        $dataProvider = $this->resolveSearchDataProvider(
                $searchForm, $pageSize, 'AgreementsMetadataAdapter', 'AgreementsSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view') {
            $mixedView = $this->makeListView(
                    $searchForm, $dataProvider
            );
            $view = new AgreementsPageView($mixedView);
        } else {
            $activeActionElementType = $this->resolveActiveElementTypeForKanbanBoard($searchForm);
            $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider, 'AgreementsSecuredActionBarForSearchAndListView', null, $activeActionElementType);
            //$mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
            $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
        }
        echo $view->render();
    }

    public function actionDetails($id, $kanbanBoard = false) {
        // To cehck wether it is deleted or not.
        try {
            $agreement = Agreement::getById($id);
        }catch(Exception $e){
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Record does not exist.'));
            $this->redirect(Yii::app()->createUrl('agreements/default'));
            Yii::app()->end(false);
        }
        // Ends here
        
        if ($agreement->archive != Constant::ARCHIVE) {
            $agmnt = static::getModelAndCatchNotFoundAndDisplayError('Agreement', intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($agmnt);
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($agmnt), 'AgreementsModule'), $agmnt);
            if (KanbanUtil::isKanbanRequest() === false) {
                $breadCrumbView = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'AgreementsSearchView', $agmnt);
                $detailsAndRelationsView = $this->makeDetailsAndRelationsView($agmnt, 'AgreementsModule', 'AgreementDetailsAndRelationsView', Yii::app()->request->getRequestUri(), $breadCrumbView);
                $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                        makeStandardViewForCurrentUser($this, $detailsAndRelationsView));
            } else {
                $view = TasksUtil::resolveTaskKanbanViewForRelation($agmnt, $this->getModule()->getId(), $this, 'TasksForAgreementKanbanView', 'AgreementsPageView');
            }

            //For Hiding The Add Product Button In Agreement Detail View When Approval Process Over All Status Is Pending
            if ($agmnt->Agreement_Type == Constant::CLONEAGREEMENT) {
                $approvalProcess = ApprovalProcess::getAllAppProcessForAgmnt($id);
                if (count($approvalProcess) > 0) {
                    if ($approvalProcess[0]->overallstatus->value == Constant::PENDING) {
                        if ($approvalProcess[1]->assignedto->id != $approvalProcess[1]->actualapprover->id) {
                            Yii::app()->clientScript->registerScript('hideAddProductBtn', '$( document ).ready(function() {
                                    $("#addProductButton").css("display","none")
                                    })');
                        }
                    } else if ($approvalProcess[0]->overallstatus->value == Constant::APPROVED) {
                        if ($approvalProcess[1]->assignedto->id != $approvalProcess[1]->actualapprover->id) {
                            Yii::app()->clientScript->registerScript('hideAddProductBtn', '$( document ).ready(function() {
                                    $("#SubmitForApproval").css("display","none");
                                    $("#addProductButton").css("display","none");
                                  })');
                        } else {
                            Yii::app()->clientScript->registerScript('hideAddProductBtn', '$( document ).ready(function() {
                                    $("#SubmitForApproval").css("display","none")
                                    $("#addProductButton").css("display","none")
                                  })');
                        }
                    }
                }
            }
            echo $view->render();
        } else {
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Record does not exist.'));
            $this->redirect(Yii::app()->createUrl('opportunities/default'));
            Yii::app()->end(false);
        }
    }

    public function actionCreate() {
        Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Not possible to create agreement manually.')
        );
        $this->redirect(array($this->getId() . '/index'));
    }

    public function actionCreateFromRelation($relationAttributeName, $relationModelId, $relationModuleId, $redirectUrl) {
        Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Not possible to create agreement manually.'));
        $this->redirect($redirectUrl);
        Yii::app()->end(false);
    }

    protected function actionCreateByModel(Agreement $agmnt, $redirectUrl = null) {
        $agmtRecordView = new AgmntRecordType($this, NULL);
        $view = new AgrmntRecordModelView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $agmtRecordView));
        echo $view->render();
    }

    public function actionProjectType($redirectUrl = null) {
        Yii::app()->clientScript->registerScript('some-name', '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
        $agmnt = new Agreement();
        $agmnt->RecordType = Constant::PROJECTAGREEMENT;
        $agmnt->ContractTerm = 0;
        $redirectUrl = 'from_create';
        $titleBarAndEditView = new AgreementProjectEditAndDetailsView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($agmnt, $redirectUrl));
        $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $titleBarAndEditView));
        echo $view->render();
    }

    public function actionRecurringType($redirectUrl = null) {
        Yii::app()->clientScript->registerScript('some-name', '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
        $agmnt = new Agreement();
        $agmnt->RecordType = Constant::RECURRINGAGREEMENT;
        $currencies = Currency::getAll();
        $projectAmount = new CurrencyValue();
        $projectAmount->value = 0.0;
        $projectAmount->currency = $currencies[0];
        $agmnt->Project_Agreement_Amount = $projectAmount;
        $redirectUrl = 'from_create';
        $titleBarAndEditView = new AgreementRecurringEditAndDetailsView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($agmnt, $redirectUrl));
        $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $titleBarAndEditView));
        echo $view->render();
    }

    public function actionEdit($id, $redirectUrl = null) {
        $agmnt = Agreement::getById(intval($id));
        if ($agmnt->Status->value != Constant::DEACTIVATED && $agmnt->Status->value != Constant::COMPLETEDAGREEMENT && $agmnt->Status->value != Constant::CLOSED) {
            $appApproval = ApprovalProcess::getAllAppProcessForAgmnt($id);
            if (count($appApproval) > 0 && $appApproval[0]->Status->value == ApprovalProcess::PENDING) { //For checking the current opportunity status to block the edit option
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement Submitted for Approval so you cannot edit the details.')
                );
                $this->actionList();
            } else {
                Yii::app()->clientScript->registerScript('some-name', '$(\'select[id$="_currency_id"]\').each(function() {
                        $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                          });');
                ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($agmnt);
                $this->processEdit($agmnt, $redirectUrl);
            }
        } else {
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Deactivated or closed or Completed Agreements cannot be edited.')
            );
            $this->redirect(array($this->getId() . '/details?id=' . $id));
        }
    }

    public function actionCopy($id) {
        Yii::app()->clientScript->registerScript('some-name', '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
            });');
        $copyToAgreement = new Agreement();
        $postVariableName = get_class($copyToAgreement);
        $agmnt = Agreement::getById((int) $id);
        $copyToAgreement->RecordType = $agmnt->RecordType;
        if (!isset($_POST[$postVariableName])) {
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($agmnt);
            ZurmoCopyModelUtil::copy($agmnt, $copyToAgreement);
        }
        $copyToAgreement->newCurrent_GPM = 0;
        if ($copyToAgreement->RecordType == Constant::RECURRINGAGREEMENT) {
            $currencies = Currency::getAll();
            $projectAmount = new CurrencyValue();
            $projectAmount->value = 0.0;
            $projectAmount->currency = $currencies[0];
            $copyToAgreement->Project_Agreement_Amount = $projectAmount;
        }
        $copyToAgreement->Estimator = $agmnt->Estimator;
        if ($copyToAgreement->RecordType == Constant::PROJECTAGREEMENT) {
            $copyToAgreement->ContractTerm = 0;
        }
        if (!empty($agmnt->Total_MHR)) {
            $copyToAgreement->Total_MHR = $agmnt->Total_MHR;
        } else {
            $copyToAgreement->Total_MHR = 0.0;
        }
        $copyToAgreement->opportunity = $agmnt->opportunity;
        $copyToAgreement->Status->value = Constant::DRAFTAGREEMENT;
        $copyToAgreement->Agreement = $agmnt;
        $copyToAgreement->Agreement_Type->value = Constant::CLONEAGREEMENT;
        $copyToAgreement->name = '';
        $copyToAgreement->Rev_MH = $agmnt->Rev_MH;
        $copyToAgreement->suggestedPrice = $agmnt->suggestedPrice;
        $copyToAgreement->Estimator_Approval = TRUE;
        $copyToAgreement->Total_Available_MHR = $agmnt->Total_Available_MHR;
        $copyToAgreement->Total_Equipment = $agmnt->Total_Equipment;
        $copyToAgreement->Available_Equipment = $agmnt->Available_Equipment;
        $copyToAgreement->Total_Material = $agmnt->Total_Material;
        $copyToAgreement->Available_Material = $agmnt->Available_Material;

        $this->processEdit($copyToAgreement);
    }

    protected function processEdit(Agreement $agmnt, $redirectUrl = null) {
        $redirectUrl = 'from_edit';
        if ($agmnt->RecordType == Constant::RECURRINGAGREEMENT) {
            $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, new AgreementRecurringEditAndDetailsView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($agmnt, $redirectUrl))));
            echo $view->render();
        } else {
            $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, new AgreementProjectEditAndDetailsView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($agmnt, $redirectUrl))));
            echo $view->render();
        }
    }

    protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false) {
        assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
        $savedSuccessfully = false;
        $modelToStringValue = null;
        $postVariableName = get_class($model);
        $oldOwnerId = $model->owner->id;
        if (isset($_POST[$postVariableName])) {
            $postData = $_POST[$postVariableName];
            $controllerUtil = static::getZurmoControllerUtil();
            $model = $controllerUtil->saveModelFromPost($postData, $model, $savedSuccessfully, $modelToStringValue, $returnOnValidate);
        }

        if ($savedSuccessfully && $redirect) {
            if ($model->Agreement_Type->value == Constant::CLONEAGREEMENT) {
                //Deactivate the parent agreement where it from cloned
                $model->Agreement->Status->value = Constant::DEACTIVATED;
                //Update the contract_number in agreement- Sundar P - 10-Sep-2016
                $model->Contract_Number = 'Agmnt-' . $model->id;
                if (!$model->save()) {
                    throw new FailedToSaveModelException();
                } else {
                    $jobs = JobScheduling::getJobsByAgmntId($model->Agreement->id);
                    foreach ($jobs as $job) {
                        $job->status = Constant::DEACTIVATED;
                        $job->save();
                    }
                }
            }
            if (Yii::app()->controller->action->id == 'copy') {
                $this->GetAgreementProducts($model);
            }

            $linkUrl = Yii::app()->getBaseUrl(true) . '/index.php/agreements/default/details?id=' . $model->id;
            if (is_string($redirectUrlParams)) {
                if ($redirectUrlParams == 'from_create') {
                    $subject = '[VERTWARE] A NEW AGREEMENT IS ASSIGNED TO YOU';
                } elseif ($redirectUrlParams == 'from_edit') {
                    $subject = '[VERTWARE] AGREEMENT OWNER CHANGE';
                }
                $redirectUrlParams = '';
                if ($model->owner->id != $oldOwnerId) {
                    $recipients = array();
                    $ownerAccount = User::getById($model->owner->id);
                    if (!empty($ownerAccount->primaryEmail->emailAddress)) {
                        $recipients = array($ownerAccount->primaryEmail->emailAddress);
                    }
                    $agreementAssigner = User::getById(Yii::app()->user->id);
                    if (!empty($agreementAssigner->primaryEmail->emailAddress)) {
                        $fromAddress = $agreementAssigner->primaryEmail->emailAddress;
                    } else {
                        $fromAddress = 'notifications@vertware.net';
                    }
                    $fromAddress = array(
                        'name' => 'VERTWARE',
                        'address' => $fromAddress
                    );

                    $mailContent = array(
                        'subject' => $subject,
                        'content' => 'Hi ' . $ownerAccount->firstName . ', <br> <p> <b>Agreement Details:</b> <br> Name: ' . $model->name . '<br> Assigned by: ' . $agreementAssigner->getFullName() . ' <br> <br> Link: <a href="' . $linkUrl . '">' . $linkUrl . '</a></p> 
                            <hr> Thanks. <br> ShinnedHawks'
                    );
                    if (count($recipients) > 0) {
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                    }
                }
            }
            $this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
        }
        return $model;
    }

    /**
     * Action for displaying a mass edit form and also action when that form is first submitted.
     * When the form is submitted, in the event that the quantity of models to update is greater
     * than the pageSize, then once the pageSize quantity has been reached, the user will be
     * redirected to the makeMassEditProgressView.
     * In the mass edit progress view, a javascript refresh will take place that will call a refresh
     * action, usually massEditProgressSave.
     * If there is no need for a progress view, then a flash message will be added and the user will
     * be redirected to the list view for the model.  A flash message will appear providing information
     * on the updated records.
     * @see Controler->makeMassEditProgressView
     * @see Controller->processMassEdit
     * @see
     */
    public function actionMassEdit() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'massEditProgressPageSize');
        $agmnt = new Agreement(false);
        $activeAttributes = $this->resolveActiveAttributesFromMassEditPost();
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AgreementsSearchForm($agmnt), $pageSize, Yii::app()->user->userModel->id, null, 'AgreementsSearchView');
        $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
        $agmnt = $this->processMassEdit(
                $pageSize, $activeAttributes, $selectedRecordCount, 'AgreementsPageView', $agmnt, AgreementsModule::getModuleLabelByTypeAndLanguage('Plural'), $dataProvider
        );
        $massEditView = $this->makeMassEditView(
                $agmnt, $activeAttributes, $selectedRecordCount, AgreementsModule::getModuleLabelByTypeAndLanguage('Plural')
        );
        $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $massEditView));
        echo $view->render();
    }

    /**
     * Action called in the event that the mass edit quantity is larger than the pageSize.
     * This action is called after the pageSize quantity has been updated and continues to be
     * called until the mass edit action is complete.  For example, if there are 20 records to update
     * and the pageSize is 5, then this action will be called 3 times.  The first 5 are updated when
     * the actionMassEdit is called upon the initial form submission.
     */
    public function actionMassEditProgressSave() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'massEditProgressPageSize');
        $agmnt = new Agreement(false);
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AgreementsSearchForm($agmnt), $pageSize, Yii::app()->user->userModel->id, null, 'AgreementsSearchView'
        );
        $this->processMassEditProgressSave(
                'Agreement', $pageSize, AgreementsModule::getModuleLabelByTypeAndLanguage('Plural'), $dataProvider
        );
    }

    /**
     * Action for displaying a mass delete form and also action when that form is first submitted.
     * When the form is submitted, in the event that the quantity of models to delete is greater
     * than the pageSize, then once the pageSize quantity has been reached, the user will be
     * redirected to the makeMassDeleteProgressView.
     * In the mass delete progress view, a javascript refresh will take place that will call a refresh
     * action, usually makeMassDeleteProgressView.
     * If there is no need for a progress view, then a flash message will be added and the user will
     * be redirected to the list view for the model.  A flash message will appear providing information
     * on the delete records.
     * @see Controler->makeMassDeleteProgressView
     * @see Controller->processMassDelete
     * @see
     */
    public function actionMassDelete() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'massDeleteProgressPageSize');
        $agmnt = new Agreement(false);

        $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AgreementsSearchForm($agmnt), $pageSize, Yii::app()->user->userModel->id, null, 'AgreementsSearchView'
        );
        $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
        $agmnt = $this->processMassDelete(
                $pageSize, $activeAttributes, $selectedRecordCount, 'AgreementsPageView', $agmnt, AgreementsModule::getModuleLabelByTypeAndLanguage('Plural'), $dataProvider
        );
        $massDeleteView = $this->makeMassDeleteView(
                $agmnt, $activeAttributes, $selectedRecordCount, AgreementsModule::getModuleLabelByTypeAndLanguage('Plural')
        );
        $view = new AgreementsPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $massDeleteView));
        echo $view->render();
    }

    /**
     * Action called in the event that the mass delete quantity is larger than the pageSize.
     * This action is called after the pageSize quantity has been delted and continues to be
     * called until the mass delete action is complete.  For example, if there are 20 records to delete
     * and the pageSize is 5, then this action will be called 3 times.  The first 5 are updated when
     * the actionMassDelete is called upon the initial form submission.
     */
    public function actionMassDeleteProgress() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'massDeleteProgressPageSize');
        $agmnt = new Agreement(false);
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AgreementsSearchForm($agmnt), $pageSize, Yii::app()->user->userModel->id, null, 'AgreementsSearchView'
        );
        $this->processMassDeleteProgress(
                'Agreement', $pageSize, AgreementsModule::getModuleLabelByTypeAndLanguage('Plural'), $dataProvider
        );
    }

    public function actionModalList() {
        $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                $_GET['modalTransferInformation']['sourceIdFieldId'], $_GET['modalTransferInformation']['sourceNameFieldId'], $_GET['modalTransferInformation']['modalId']
        );

        if ($_GET['modalTransferInformation']['sourceIdFieldId'] == 'JobScheduling_agreement_id') {
            echo AgreementSearchUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider, 'SelectAgmtMetadataAdapter');
        } else
            echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider);
    }

    public function actionDelete($id) {
        $agmnt = Agreement::GetById(intval($id));
        $jobSchedulings = JobScheduling::getJobsByAgmntId($id);
        foreach ($jobSchedulings as $jobScheduling) {
            $jobScheduling->archive = Constant::ARCHIVE;
            $jobScheduling->save();
        }
        $agmnt->delete();
        $this->redirect(array($this->getId() . '/index'));
    }

    /**
     * Override to provide an agreement specific label for the modal page title.
     * @see ZurmoModuleController::actionSelectFromRelatedList()
     */
    public function actionSelectFromRelatedList($portletId, $uniqueLayoutId, $relationAttributeName, $relationModelId, $relationModuleId, $stateMetadataAdapterClassName = null) {
        parent::actionSelectFromRelatedList($portletId, $uniqueLayoutId, $relationAttributeName, $relationModelId, $relationModuleId);
    }

    protected static function getSearchFormClassName() {
        return 'AgreementsSearchForm';
    }

    public function actionExport() {
        $this->export('AgreementsSearchView');
    }

    public function actionPrintView($id) {
        $printData = '';
        $printView = new AgreementPrintView($printData, $id);
        echo $printView->render();
    }

    public function actionTrackView($id) {
        $printData = '';
        $printView = new AgreementTrackView($printData, $id);
        echo $printView->render();
    }

    public function actionTrackReset($id) {
        $agmntTracking = AgreementTracking::getAgreementTrackingByAgreementId($id);
        if (empty($agmntTracking)) {
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'No tracking details are available to reset.')
            );
            $this->redirect(array($this->getId() . '/details?id=' . $id));
        } else {
            $agt = Agreement::getById(intval($id));
            if ($agt->Status->value != Constant::CLOSED) {
                $agt->Cumulative_Year_to_Date_MHR = $agt->Cumulative_Year_to_Date_MHR + $agt->Year_to_Date_MHR;
                $agt->Cumulative_Year_to_Date_Material = $agt->Cumulative_Year_to_Date_Material + $agt->Material_Year_To_Date;
                $agt->Cumulative_Year_to_Date_Equipment = $agt->Cumulative_Year_to_Date_Equipment + $agt->Equipment_Year_To_Date;

                $agt->MHR_Used_Percentage = 0.0;
                $agt->Total_Available_MHR = $agt->Total_MHR;
                $agt->Used_Material = 0.0;
                $agt->Available_Material = $agt->Total_Material;
                $agt->Used_Equipment = 0.0;
                $agt->Available_Equipment = $agt->Total_Equipment;
                $agt->Year_to_Date_MHR = 0.0;
                $agt->MHR_Used_Percentage = 0.0;
                $agt->Material_Year_To_Date = 0.0;
                $agt->Material_Used_Percentage = 0.0;
                $agt->Equipment_Year_To_Date = 0.0;
                $agt->Equipment_Used_Percentage = 0.0;
                $agt->Used_MHR = 0.0;
                
                // For the New reset functionality
                $agt->reset_count = $agt->reset_count+1;
                $agt->avg_gpm = ($agt->newCurrent_GPM+$agt->newCurrent_GPM)/$agt->reset_count;
                $gpm = $agt->newCurrent_GPM;
                $agt->newCurrent_GPM = 100;

                if (!$agt->save()) {
                    throw new FailedToSaveModelException();
                } else {
                    
                    // To update the reset data
                    $newAgmntReset = new AgreementTrackingReset();
                    $newAgmntReset->reset_number    = $agt->reset_count;
                    $newAgmntReset->reset_date      = date('Y-m-d');
                    $newAgmntReset->gpm             = $gpm;
                    $newAgmntReset->agreement       = $agt;
                    
                    if (!$newAgmntReset->save()) {
                        throw new FailedToSaveModelException();
                    }
                    
                    // To reset the products
                    AgreementTrackingUtils::resetAgmtProductTracking($id);
                    
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Tracking details have been reset.')
                    );
                    $this->redirect(array($this->getId() . '/details?id=' . $id));
                }
            } else {
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Tracking reset cannot be done on closed Agreement.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $id));
            }
        }
    }

    public function actionActivateAgreement($id) {
        $agmnt = Agreement::getById(intval($id));
        switch ($agmnt->Status->value) {
            case Constant::ACTIVEAGREEMENT:
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement already activated.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                break;

            case Constant::DRAFTAGREEMENT:
                if ($agmnt->Agreement_Type->value == Constant::OPPORTUNITYAGREEMENT) {
                    $this->agmntActive($agmnt);                         //For Activate tha Agreement
                } else if ($agmnt->Agreement_Type->value == Constant::CLONEAGREEMENT) {
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Cloned agreement is activated only after estimator approval.')
                    );
                    $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                } else {
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Only Cloned Agreement or Agreement created through Opportunity can be Activated.')
                    );
                    $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                }
                break;

            case Constant::PENDINGAGREEMENT:
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Cloned agreement is activated only after estimator approval.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                break;

            case Constant::COMPLETEDAGREEMENT:
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Completed agreement cannot be activated.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                break;

            case Constant::CLOSED:
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Closed agreement cannot be activated.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                break;

            case Constant::DEACTIVATED:
                if ($agmnt->RecordType == Constant::RECURRINGAGREEMENT) {
                    $this->agmntActive($agmnt);                             //For Activate tha Agreement
                } else {
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'For Project type agreement can not be activated.')
                    );
                    $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                }
                break;

            default :
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Invalid Agreement Status.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                break;
        }
    }

    //For Change The Agreement Status will be Active
    protected function agmntActive($agmnt) {
        $agmnt->Status->value = Constant::ACTIVEAGREEMENT;
        $agmnt->newCurrent_GPM = 100; // To set 100 as default
        $agmnt->ActivatedDate = DateTimeUtil::getTodaysDate();
        if (!$agmnt->save()) {
            throw new FailedToSaveModelException();
        } else {
            if ($agmnt->opportunity) {
                $oppt = Opportunity::getById(intval($agmnt->opportunity->id));
                $oppt->expectedStartDate = DateTimeUtil::getTodaysDate();   //Set Agreement's activated date to Expected Start Date in opportunity
                $oppt->status_changed_date = DateTimeUtil::getTodaysDate(); // For the oppt Won date
                $oppt->stage->value = Constant::WON;
                if (!$oppt->save()) {
                    throw new FailedToSaveModelException();
                }
            }
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement has been activated.')
            );
            $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
        }
    }

    //For Change The Agreement Status will be De-active
    public function actionDeactivateAgreement($id) {
        $agmnt = Agreement::getById(intval($id));
        if ($agmnt->Status->value == Constant::DEACTIVATED) {
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement already De-activate.')
            );
            $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
        } else {
            if ($agmnt->Status->value == Constant::ACTIVEAGREEMENT || $agmnt->Status->value == Constant::DRAFTAGREEMENT) {
                $agmnt->Status->value = Constant::DEACTIVATED;
                if (!$agmnt->save()) {
                    throw new FailedToSaveModelException();
                } else {
                    $jobs = JobScheduling::getJobsByAgmntId($id);
                    foreach ($jobs as $job) {
                        $job->status = Constant::DEACTIVATED;
                        $job->save();
                    }
                }
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement has been De-activated.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
            } else {
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement need to activated before De-activate.')
                );
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
            }
        }
    }

    //For Change The Agreement Status will be Close
    public function actionCloseAgreement($id) {
        $userIdArr = array();
        $AccountManagerGroup = Group::getByName(User::ACCOUNTMANAGER);         //Access Account manager group        
        foreach ($AccountManagerGroup->users as $AccountManagerUserId) {
            $userIdArr[] = $AccountManagerUserId->id;
        }

        $isInAccountManageGroup = FALSE;
        if (in_array(Yii::app()->user->userModel->id, $userIdArr)) {
            $isInAccountManageGroup = TRUE;
        }

        //if user belongs to rootuser or accountmanager group or GM
        if (!empty(Yii::app()->user->userModel->isRootUser) || $isInAccountManageGroup == TRUE || Yii::app()->user->userModel->role->name == Constant::GM) {
            $agmnt = Agreement::getById(intval($id));

            //COMPLETED agreement can only be closed
            if ($agmnt->Status->value == Constant::CLOSED) {
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement already closed.'));
                $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
            } else {
                if ($agmnt->Status->value == Constant::COMPLETEDAGREEMENT) {
                    $agmnt->Status->value = Constant::CLOSED;
                    if (!$agmnt->save()) {
                        throw new FailedToSaveModelException();
                    } else {
                        Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement has been Closed.')
                        );
                        $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                    }
                } else {
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Agreement need to completed before Close.')
                    );
                    $this->redirect(array($this->getId() . '/details?id=' . $agmnt->id));
                }
            }
        }
    }

    public function actionAddNewAssemblyProducts() {
        $agmt_asm_prod = AgreementProduct::getAllByAssembly();
        $updated_agmt_prod_ids = array();
        $updated_agmt_ids = array();

        if (is_array($agmt_asm_prod)) {
            foreach ($agmt_asm_prod as $prod_arr) {
                OpportunityUtils::AssemblyProductSave($prod_arr, $prod_arr->agreement->id, 1);

                $prod_arr->is_assembly_prod_updated = 1;
                $prod_arr->save();

                $updated_agmt_prod_ids[] = $prod_arr->id;
                $updated_agmt_ids[] = $prod_arr->agreement->id;
            }
            echo 'Agmt prod Ids <br>';
            print_r($updated_agmt_prod_ids);
            echo 'Agmt Ids <br>';
            print_r($updated_agmt_ids);
        } else {
            echo 'No records found';
        }
    }

    public function GetAgreementProducts($agmnt) {
        $total_MHR = 0;
        $total_material = 0;
        $total_equipment = 0;
        $oldAgmntProducts = AgreementProduct::getAllProdByAgmntId($agmnt->Agreement->id);
        foreach ($oldAgmntProducts as $oldAgmntProduct) {
            $newAgmntPrdct = new AgreementProduct();
            $newAgmntPrdct->name = $oldAgmntProduct->name;
            $newAgmntPrdct->costbook = $oldAgmntProduct->costbook;
            $newAgmntPrdct->Quantity = $oldAgmntProduct->Quantity;
            if (!empty($oldAgmntProduct->Frequency)) {
                $newAgmntPrdct->Frequency = $oldAgmntProduct->Frequency;
            } else {
                $newAgmntPrdct->Frequency = NULL;
            }
            if (!empty($oldAgmntProduct->Total_MHR)) {
                $newAgmntPrdct->Total_MHR = $oldAgmntProduct->Total_MHR;
            } else {
                $newAgmntPrdct->Total_MHR = 0.0;
            }
            $newAgmntPrdct->Category = $oldAgmntProduct->Category;
            $newAgmntPrdct->Product_Code = $oldAgmntProduct->Product_Code;
            $newAgmntPrdct->Assembly_Product_Code = $oldAgmntProduct->Assembly_Product_Code;
            $newAgmntPrdct->Category_GPM = $oldAgmntProduct->Category_GPM;
            $newAgmntPrdct->Labor_Cost = $oldAgmntProduct->Labor_Cost;
            $newAgmntPrdct->Burden_Cost = $oldAgmntProduct->Burden_Cost;
            $newAgmntPrdct->Equipment_Cost = $oldAgmntProduct->Equipment_Cost;
            $newAgmntPrdct->Suggested_Cost = $oldAgmntProduct->Suggested_Cost;
            $newAgmntPrdct->Materials_Cost = $oldAgmntProduct->Materials_Cost;
            $newAgmntPrdct->Sub_Cost = $oldAgmntProduct->Sub_Cost;
            $newAgmntPrdct->Total_Direct_Cost = $oldAgmntProduct->Total_Direct_Cost;
            $newAgmntPrdct->Other_Cost = $oldAgmntProduct->Other_Cost;
            $newAgmntPrdct->agreement = $agmnt;
            if (!$newAgmntPrdct->save()) {
                throw new Exception();
            }

            // For the Agreement calculation
            if ($oldAgmntProduct->costbook->costofgoodssold->value == Constant::MATERIAL) {
                $total_material += $oldAgmntProduct->Quantity;
            } else if ($oldAgmntProduct->costbook->costofgoodssold->value == Constant::EQUIPMENT) {
                $total_equipment += $oldAgmntProduct->Quantity;
            } else if ($oldAgmntProduct->costbook->costofgoodssold->value == Constant::ASSEMBLY) {

                $assembly_total_arr = OpportunityUtils::AssemblyCalculation($oldAgmntProduct->costbook, $newAgmntPrdct->Quantity, $newAgmntPrdct->Frequency);
                $total_material += $assembly_total_arr['total_material'];
                $total_equipment += $assembly_total_arr['total_equipment'];
            }
            // Ends here	
        }
        // To save the agreement new fields
//            $agmnt->Total_Available_MHR = $agmnt->Total_MHR;
//            $agmnt->Total_Equipment     = $total_equipment;
//            $agmnt->Available_Equipment = $total_equipment;
//            $agmnt->Total_Material      = $total_material;
//            $agmnt->Available_Material  = $total_material;
//            if (!$agmnt->save()) {
//                throw new FailedToSaveModelException();
//            }
    }

    public function actionAutocompleteAgreement() {
        $term = trim($_GET['term']);

        if ($term != '') {
            $agmnts = Agreement::getAllActiveAgmtForJob($term);
            $autoCompleteResults = array();
            foreach ($agmnts as $agmnt) {
                $autoCompleteResults[] = array(
                    'id' => $agmnt->id,
                    'name' => strval($agmnt->name),
                    'label' => strval($agmnt->name)
                );
            }

            if (empty($autoCompleteResults)) {
                $autoCompleteResults = array(array('id' => null,
                        'value' => null,
                        'label' => Zurmo::t('Core', 'No results found')));
            }

            echo CJSON::encode($autoCompleteResults);
        }
    }

    public function actionGetAgmntAmountByStatus() {
        $agreements = Agreement::getTotalAmtForRecAgreements();
        echo json_encode($agreements);
    }

//    public function actionGetAllRecuringAgmnts() {
//        $headerParams = array('Agreement Name', 'Current GPM', 'Agreement GPM');
//        $recAgreements = Agreement::getActiveAgmntsByType(Constant::RECURRINGAGREEMENT);
//        $tab_content = '';
//        $tab_content .= '<table class="items">';
//        $tab_content .= $this->renderTableHeader($headerParams);
//        foreach ($recAgreements as $recAgreement) {
//            $tab_content .= $this->renderTableBody($recAgreement->name, $recAgreement->newcurrent_gpm, $recAgreement->current_gpm);
//        }
//        $tab_content .= '</table>';
//        echo $tab_content;
//    }

//    public function actionGetAllProjectAgmnts() {
//        $headerParams = array('Agreement Name', 'Current GPM', 'Agreement GPM');
//        $proAgreements = Agreement::getActiveAgmntsByType(Constant::PROJECTAGREEMENT);
//        $tab_content = '';
//        $tab_content .= '<table class="items">';
//        $tab_content .= $this->renderTableHeader($headerParams);
//        foreach ($proAgreements as $proAgreement) {
//            $tab_content .= $this->renderTableBody($proAgreement->name, $proAgreement->newcurrent_gpm, $proAgreement->current_gpm);
//        }
//        $tab_content .= '</table>';
//        echo $tab_content;
//    }
    
//    public function actionGetAllAgmntsForAgmntVsTracking() {
//        $headerParams = array('Agreement Name', 'Agreement Rev/Mhr', 'Tracking Rev/Mhr');
//        $agreements = Agreement::getActiveAgmntsByType();
//        $tab_content = '';
//        $tab_content .= '<table class="items">';
//        $tab_content .= $this->renderTableHeader($headerParams);
//        $agmntDatas = $this->getAgreementRevMhrAndTrackingRevMhr($agreements);
//        foreach ($agmntDatas as $agmntData) {
//            $tab_content .= $this->renderTableBody($agmntData['name'], number_format($agmntData['agmnt_rev_mhr'],2), number_format($agmntData['tracking_rev_mhr'],2));
//        }
//        $tab_content .= '</table>';
//        echo $tab_content;
//    }

//    protected function renderTableHeader($headerParams) {
//        $tab_content = '';
//        $tab_content .= '<thead><tr>';
//        foreach ($headerParams as $headerParam) {
//            $tab_content .= '<th style="width:30%">'.$headerParam.'</th>';
//        }
//
//        $tab_content .= '</tr></thead>';
//        return $tab_content;
//    }

    protected function renderTableBody($name = null, $dataOne = null, $dataTwo = null) {
        $tab_content = '';
        $tab_content .= '<tr><td>'.$name.'</td>';
        if (!empty($dataOne)) {
            $tab_content .= '<td>'.$dataOne.'</td>';
        } else {
            $tab_content .= '<td>-</td>';
        }
        if (!empty($dataTwo)) {
            $tab_content .= '<td>'.$dataTwo.'</td>';
        } else {
            $tab_content .= '<td>-</td>';
        }
        $tab_content .= '</tr>';
        return $tab_content;
    }
    
    protected function renderEmptyRecords(){
        $tab_content = '';
        $tab_content .= '<tbody><tr><td class="empty" colspan="3"><span class="empty"><span class="icon-empty"></span>No results found</span></td></tr></tbody>';
        echo $tab_content;
    }
    
    protected function getAgreementRevMhrAndTrackingRevMhr($agreements){
        foreach ($agreements as $agreement) {
            if($agreement['total_mhr'] > 0){
                if($agreement['recordtype'] == Constant::PROJECTAGREEMENT){
                    $agreementRev_Mhr = $agreement['agmnt_rev_mhr']/$agreement['total_mhr'];
                    $trackingRev_Mhr = $agreement['tracking_rev_mhr'];
//                $renderAgmnts = array('name'=>$agreement->name,'agmnt_rev_mhr'=>$agreementRev_Mhr,'tracking_rev_mhr'=>$trackingRev_Mhr);
                    $renderAgmnts[] = array('name'=>$agreement['name'],'agmnt_rev_mhr'=>$agreementRev_Mhr,'tracking_rev_mhr'=>$trackingRev_Mhr);
                }else{
                    $agreementRev_Mhr = $agreement['agmnt_rev_mhr']/$agreement['total_mhr'];
                    $trackingRev_Mhr = $agreement['tracking_rev_mhr'];
                    $renderAgmnts[] = array('name'=>$agreement['name'],'agmnt_rev_mhr'=>$agreementRev_Mhr,'tracking_rev_mhr'=>$trackingRev_Mhr);
                }
            }
        }
        return $renderAgmnts;
        
    }

}

?>