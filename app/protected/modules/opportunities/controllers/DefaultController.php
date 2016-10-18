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

    class OpportunitiesDefaultController extends ZurmoModuleController
    {
        public function filters()
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
        }

        public function actionList()
        {
            $pageSize    = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                           'listPageSize', get_class($this->getModule()));
            $opportunity = new Opportunity(false);
            $searchForm  = new OpportunitiesSearchForm($opportunity);
            $listAttributesSelector = new ListAttributesSelector('OpportunitiesListView', get_class($this->getModule()));
            $searchForm->setListAttributesSelector($listAttributesSelector);
            $dataProvider = $this->resolveSearchDataProvider(
                $searchForm,
                $pageSize,
                'OpportunitiesMetadataAdapter',
                'OpportunitiesSearchView'
            );
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')
            {
                $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider
                );
                $view = new OpportunitiesPageView($mixedView);
            }
            else
            {
                $activeActionElementType = $this->resolveActiveElementTypeForKanbanBoard($searchForm);
                $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider,
                             'OpportunitiesSecuredActionBarForSearchAndListView', null, $activeActionElementType);
                $view      = new OpportunitiesPageView(ZurmoDefaultViewUtil::
                             makeStandardViewForCurrentUser($this, $mixedView));
            }
            echo $view->render();
        }

        public function actionDetails($id, $kanbanBoard = false)
        {
            $opt = Opportunity::getById($id);
            if($opt->archive != Constant::ARCHIVE){
                $opportunity = static::getModelAndCatchNotFoundAndDisplayError('Opportunity', intval($id));
                ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($opportunity);
                AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($opportunity), 'OpportunitiesModule'), $opportunity);
                if (KanbanUtil::isKanbanRequest() === false)
                {
                    $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'OpportunitiesSearchView', $opportunity);
                    $detailsAndRelationsView = $this->makeDetailsAndRelationsView($opportunity, 'OpportunitiesModule',
                                                                              'OpportunityDetailsAndRelationsView',
                                                                              Yii::app()->request->getRequestUri(), $breadCrumbView);
                    $view = new OpportunitiesPageView(ZurmoDefaultViewUtil::
                                                 makeStandardViewForCurrentUser($this, $detailsAndRelationsView));
                }
                else
                {
                    $view = TasksUtil::resolveTaskKanbanViewForRelation($opportunity, $this->getModule()->getId(), $this,
                                                                            'TasksForOpportunityKanbanView', 'OpportunitiesPageView');
                }
                echo $view->render();
            }else{
                Yii::app()->user->setFlash('notification',
                    Zurmo::t('ZurmoModule', 'Record does not exist.'));
                $this->redirect(Yii::app()->createUrl('opportunities/default'));
                Yii::app()->end(false);
            }    
        }

        public function actionCreate()
        {
            $this->actionCreateByModel(new Opportunity());
        }

        public function actionCreateFromRelation($relationAttributeName, $relationModelId, $relationModuleId, $redirectUrl)
        {
            if($relationModuleId == 'contacts' ){
                $contact = Contact::getById((int)$relationModelId);
                if(($contact->account->id) < 0){
                    Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Account required to create the opportunity.'));
                    $redirectUrl = Yii::app()->createUrl('contacts' . '/' . 'default' . '/details/', array('id' => $_REQUEST['relationModelId']));
                    $this->redirect($redirectUrl);
                    Yii::app()->end(false);
                } 
            } 
            $opportunity = $this->resolveNewModelByRelationInformation( new Opportunity(),
                                                                                $relationAttributeName,
                                                                                (int)$relationModelId,
                                                                                $relationModuleId);
            $this->actionCreateByModel($opportunity, $redirectUrl);
        }

        protected function actionCreateByModel(Opportunity $opportunity, $redirectUrl = null)
        {
            $opptRecordView = new OpptRecordType($this, NULL );
            $view =  new MyOpportunityView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $opptRecordView));
            echo $view->render();
        }

        public function actionProjectType($redirectUrl = null) {	
            if(isset($_REQUEST['relationModelId']) && isset($_REQUEST['relationModuleId'])){
                if( $_REQUEST['relationModuleId'] == 'accounts' ){
                    $accountId = $_REQUEST['relationModelId'];    
                } else if ($_REQUEST['relationModuleId'] == 'contacts') {
                    $contact = Contact::getById((int)$_REQUEST['relationModelId']);
                    $accountId = $contact->account->id;
                }
                $opportunity = $this->resolveNewModelByRelationInformation( new Opportunity(),                                                                                
                                                                                    'account',
                                                                                    $accountId,
                                                                                    'accounts');
            }
            else{
                $opportunity = new Opportunity();            
            }
            $opportunity->recordType->value = 'Project Final';
            $redirectUrl = 'from_create';

            $titleBarAndEditView = new OpportunityProjectEditAndDetailsView('Edit', $this->getId(), 
                                    $this->getModule()->getId(), $this->attemptToSaveModelFromPost($opportunity, $redirectUrl));

            // Script registered for Replacing $ symbol insteed of USD in Create or Edit screen
            Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
            $view = new OpportunitiesPageView(ZurmoDefaultViewUtil::
                                    makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();           
	}

	public function actionRecurringType($redirectUrl = null) {           
            if(isset($_REQUEST['relationModelId']) && isset($_REQUEST['relationModuleId'])){
                if( $_REQUEST['relationModuleId'] == 'accounts' ){
                    $accountId = $_REQUEST['relationModelId'];    
                } else if ($_REQUEST['relationModuleId'] == 'contacts') {
                    $contact = Contact::getById((int)$_REQUEST['relationModelId']);
                    $accountId = $contact->account->id;
                }
                $opportunity = $this->resolveNewModelByRelationInformation( new Opportunity(),                                                                                
                                                                                'account',
                                                                                $accountId,
                                                                                'accounts');
            }
            else{
                $opportunity = new Opportunity();            
            }              
            $opportunity->recordType->value = 'Recurring Final';
            $redirectUrl = 'from_create';
            $titleBarAndEditView = new OpportunityRecurringEditAndDetailsView('Edit', $this->getId(), 
                                    $this->getModule()->getId(), $this->attemptToSaveModelFromPost($opportunity, $redirectUrl));
            // Script registered for Replacing $ symbol insteed of USD in Create or Edit screen 
            Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
            $view = new OpportunitiesPageView(ZurmoDefaultViewUtil::
                                    makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
	}
        
        protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false)
        {
            assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
            $savedSuccessfully   = false;
            $modelToStringValue = null;
            $postVariableName   = get_class($model);
            $oldOwnerId = $model->owner->id;
            if (isset($_POST[$postVariableName]))
            {
                $postData = $_POST[$postVariableName];
                $controllerUtil   = static::getZurmoControllerUtil();
                $model            = $controllerUtil->saveModelFromPost($postData, $model, $savedSuccessfully,
                                                                       $modelToStringValue, $returnOnValidate);
            }
            
            if ($savedSuccessfully && $redirect)
            {
               if($model->stage->value == OpportunityUtils::AGREEMENT && Yii::app()->controller->action->id != 'copy') {
                    OpportunityUtils::convertOpportunityToAgreement(intval($model->id));
               }
               
               if (Yii::app()->controller->action->id == 'copy') {
                   OpportunityProductUtils::cloneOpportunityProducts($model);
               }
   
               $linkUrl = Yii::app()->getBaseUrl(true).'/index.php/opportunities/default/details?id='.$model->id;
               if(is_string($redirectUrlParams))
               {
                    if($redirectUrlParams == 'from_create')
                    {
                        $subject = '[VERTWARE] A NEW OPPORTUNITY IS ASSIGNED TO YOU';
                    }elseif ($redirectUrlParams == 'from_edit') {
                        $subject = '[VERTWARE] OPPORTUNITY OWNER CHANGE';
                    }
                        $redirectUrlParams = '';
                        if($model->owner->id != $oldOwnerId)
                        {
                            $ownerAccount  = User::getById($model->owner->id);
                            $recipients = array();
                            if (!empty($ownerAccount->primaryEmail->emailAddress)){
                                $recipients = array($ownerAccount->primaryEmail->emailAddress);
                            }                           
                            $opportunityAssigner     = User::getById(Yii::app()->user->id);
                            if (!empty($opportunityAssigner->primaryEmail->emailAddress)){
                                $fromAddress = $opportunityAssigner->primaryEmail->emailAddress;
                            } else {                                   
                                $fromAddress = 'notifications@vertware.net';
                            }
                            $fromAddress = array(
                                'name'      => 'VERTWARE',
                                'address'   => $fromAddress
                            );
                            
                            $mailContent = array(
                                'subject' => $subject,
                                'content' => 'Hi '.$ownerAccount->firstName .', <br> <p> <b>Opportunity Details:</b> <br> Name: '.$model->name.'<br> Assigned by: '.$opportunityAssigner->getFullName().' <br> <br> Link: <a href="'.$linkUrl.'">'.$linkUrl.'</a></p> 
                                <hr> Thanks. <br> Vertware'
                            );
                            
                            ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                        }
               }
               $this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
            }
            return $model;
        }

        public function actionEdit($id, $redirectUrl = null)
        {			
            $appApproval = ApprovalProcess::getAllAppProcess($id);
            $opportunity = Opportunity::getById($id);
            $opportunityProducts = OpportunityProduct::getAllByOpptId($id);
            if(count($opportunityProducts) > 0) {
                Yii::app()->session->add('IsRecordTypeEditable', FALSE);
            } else {
                Yii::app()->session->add('IsRecordTypeEditable', TRUE);
            }

            if(count($appApproval)>0 && $appApproval[0]->Status->value == ApprovalProcess::PENDING ) { //For checking the current opportunity status to block the edit option
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Opportunity Submitted for Approval and cannot be edited.'));
                $this->actionList();	                
            }
            else if ($opportunity->stage->value == OpportunityUtils::AGREEMENT){
                Yii::app()->user->setFlash('notification',
                Zurmo::t('ZurmoModule', 'Opportunity converted to agreement and cannot be edited.'));
                $this->redirect(Yii::app()->createUrl('opportunities/default/index'));
                Yii::app()->end(false);
            }
            else if ($opportunity->stage->value == OpportunityUtils::WON){
                Yii::app()->user->setFlash('notification',
                Zurmo::t('ZurmoModule', 'Opportunity has won and cannot be edited.'));
                $this->redirect(Yii::app()->createUrl('opportunities/default/index'));
                Yii::app()->end(false);
            }else{			
                // Script registered for Replacing $ symbol insteed of USD in Create or Edit screen
                Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                        '$(\'select[id$="_currency_id"]\').each(function() {
                                $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                                });');
                $opportunity = Opportunity::getById(intval($id));
                ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($opportunity);
                $this->processEdit($opportunity, $redirectUrl);
                $this->attemptToSaveModelFromPost($opportunity, $redirectUrl);
            }
        }

        public function actionCopy($id)
        {
             Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                        '$(\'select[id$="_currency_id"]\').each(function() {
                                $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                                });');
            $copyToOpportunity  = new Opportunity();
            $postVariableName   = get_class($copyToOpportunity);
            $opportunity    = Opportunity::getById((int)$id);
            if (!isset($_POST[$postVariableName]))
            {
                ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($opportunity);
                ZurmoCopyModelUtil::copy($opportunity, $copyToOpportunity);
            }
            $copyToOpportunity->recordType->value = $opportunity->recordType->value;
            $copyToOpportunity->Opportunity = $opportunity;
            $copyToOpportunity->name = "";
//            $copyToOpportunity->stage->value = Constant::QUALIFICATIONANDEDUCATION;
            $copyToOpportunity->stage->value = Constant::CONSULTING;
            $copyToOpportunity->totalMHR = $opportunity->totalMHR;
            $copyToOpportunity->revenueMHR = $opportunity->revenueMHR;
            $copyToOpportunity->suggestedPrice = $opportunity->suggestedPrice;
            $copyToOpportunity->totalDirectCosts = $opportunity->totalDirectCosts;
            $copyToOpportunity->aggregateGPM = $opportunity->aggregateGPM;
            $this->processEdit($copyToOpportunity);
        }

        protected function processEdit(Opportunity $opportunity, $redirectUrl = null)
        {
            $redirectUrl = 'from_edit';
            if($opportunity->recordType->value == 'Recurring Final') {
                $titleBarAndEditView = new OpportunityRecurringEditAndDetailsView('Edit', $this->getId(), 
					$this->getModule()->getId(), $this->attemptToSaveModelFromPost($opportunity, $redirectUrl));
           	$view = new OpportunitiesPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this, $titleBarAndEditView));
		echo $view->render();
            } else {
				
                $titleBarAndEditView = new OpportunityProjectEditAndDetailsView('Edit', $this->getId(), 
					$this->getModule()->getId(), $this->attemptToSaveModelFromPost($opportunity, $redirectUrl));
           	$view = new OpportunitiesPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this, $titleBarAndEditView));
		echo $view->render();
            }
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
        public function actionMassEdit()
        {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massEditProgressPageSize');
            $opportunity = new Opportunity(false);
            $activeAttributes = $this->resolveActiveAttributesFromMassEditPost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new OpportunitiesSearchForm($opportunity),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'OpportunitiesSearchView');
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $opportunity = $this->processMassEdit(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'OpportunitiesPageView',
                $opportunity,
                OpportunitiesModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massEditView = $this->makeMassEditView(
                $opportunity,
                $activeAttributes,
                $selectedRecordCount,
                OpportunitiesModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new OpportunitiesPageView(ZurmoDefaultViewUtil::
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
        public function actionMassEditProgressSave()
        {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massEditProgressPageSize');
            $opportunity = new Opportunity(false);
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new OpportunitiesSearchForm($opportunity),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'OpportunitiesSearchView'
            );
            $this->processMassEditProgressSave(
                'Opportunity',
                $pageSize,
                OpportunitiesModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
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
        public function actionMassDelete()
        {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
            $opportunity = new Opportunity(false);

            $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new OpportunitiesSearchForm($opportunity),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'OpportunitiesSearchView'
            );
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $opportunity = $this->processMassDelete(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'OpportunitiesPageView',
                $opportunity,
                OpportunitiesModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massDeleteView = $this->makeMassDeleteView(
                $opportunity,
                $activeAttributes,
                $selectedRecordCount,
                OpportunitiesModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new OpportunitiesPageView(ZurmoDefaultViewUtil::
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
        public function actionMassDeleteProgress()
        {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
            $opportunity = new Opportunity(false);
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new OpportunitiesSearchForm($opportunity),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'OpportunitiesSearchView'
            );
            $this->processMassDeleteProgress(
                'Opportunity',
                $pageSize,
                OpportunitiesModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
        }

        public function actionModalList()
        {
            $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                                            $_GET['modalTransferInformation']['sourceIdFieldId'],
                                            $_GET['modalTransferInformation']['sourceNameFieldId'],
                                            $_GET['modalTransferInformation']['modalId']
            );
            echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider);
        }

        public function actionDelete($id)
        {            
            $appApproval = ApprovalProcess::getAllAppProcess($id);
            $opportunity = Opportunity::getById($id);            

            if(count($appApproval)>0 && $appApproval[0]->Status->value == ApprovalProcess::PENDING ) {
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Opportunity Submitted for Approval and cannot be deleted.'));
                $this->redirect(Yii::app()->createUrl('opportunities/default/details?id='.$id));
                Yii::app()->end(false);
            }
            else if ($opportunity->stage->value == OpportunityUtils::AGREEMENT){
                Yii::app()->user->setFlash('notification',
                Zurmo::t('ZurmoModule', 'Opportunity converted to agreement and cannot be deleted.'));
                $this->redirect(Yii::app()->createUrl('opportunities/default/details?id='.$id));
                Yii::app()->end(false);
            }
            else if ($opportunity->stage->value == OpportunityUtils::WON){
                Yii::app()->user->setFlash('notification',
                Zurmo::t('ZurmoModule', 'Opportunity has won and cannot be deleted.'));
                $this->redirect(Yii::app()->createUrl('opportunities/default/details?id='.$id));
                Yii::app()->end(false);
            }else{
                $opportunity->delete();
                $this->redirect(array($this->getId() . '/index'));
            }
        }

        /**
         * Override to provide an opportunity specific label for the modal page title.
         * @see ZurmoModuleController::actionSelectFromRelatedList()
         */
        public function actionSelectFromRelatedList($portletId,
                                                    $uniqueLayoutId,
                                                    $relationAttributeName,
                                                    $relationModelId,
                                                    $relationModuleId,
                                                    $stateMetadataAdapterClassName = null)
        {
            parent::actionSelectFromRelatedList($portletId,
                                                    $uniqueLayoutId,
                                                    $relationAttributeName,
                                                    $relationModelId,
                                                    $relationModuleId);
        }

        protected static function getSearchFormClassName()
        {
            return 'OpportunitiesSearchForm';
        }

        public function actionExport()
        {
            $this->export('OpportunitiesSearchView');
        }
        
        public function actionPrintView($id)
        {
            $printData = '';
            $printView =   new OppurtunityPrintView($printData, $id);
            echo $printView->render();
        }
        
        public function actionArchive($id)
        {
            $approvalProcess = ApprovalProcess::getAllAppProcess($id);
            $opt = Opportunity::getById($id);
            if(count($approvalProcess) > 0 && $approvalProcess[0]->Status->value == Constant::PENDING){
                Yii::app()->user->setFlash('notification',
                    Zurmo::t('ZurmoModule', 'Opportunity Submitted for Approval and cannot be Archived.'));
                $this->redirect(Yii::app()->createUrl('opportunities/default/details?id='.$id));
                Yii::app()->end(false);
            }else{
                if ((Yii::app()->user->userModel->role->name == Constant::GM) || ($opt->owner->id == Yii::app()->user->userModel->id)) {
                    $agmntId = $opt->agreement->id;
                    if($agmntId > 0){
                        $agmnt = Agreement::getById($agmntId);
                        $agmnt->archive = Constant::ARCHIVE;
                        if (!$agmnt->save()) {
                            throw new FailedToSaveModelException();
                        }else{
                            $jobs = JobScheduling::getJobsByAgmntId($agmntId);
                            if(!empty($jobs)){
                                foreach ($jobs as $job) {
                                    $job->archive = Constant::ARCHIVE;
                                    $job->save();
                                }
                            }
                        }
                    }
                    $opt->archive = Constant::ARCHIVE;
                    $opt->save(false); // To skip the validation
                    Yii::app()->user->setFlash('notification',
                        Zurmo::t('ZurmoModule', 'Opportunity and related Agreement archived successfully.'));
                    $this->redirect(Yii::app()->createUrl('opportunities/default'));
                    Yii::app()->end(false);
                }  else {
                    Yii::app()->user->setFlash('notification',
                        Zurmo::t('ZurmoModule', 'GM or Owner can only archieve.'));
                    $this->redirect(Yii::app()->createUrl('opportunities/default/details?id='.$id));
                    Yii::app()->end(false);
                }
            }    
        }
        
        public function actionEstimationPrintView($id)
        {
            $printData = '';
            $printView =   new OppurtunityEstimationPrintView($printData, $id);
            echo $printView->render();
        }
        
        public function actionGetoptFinalAmtTotalByStage()
        {
            $stages = array('estimate'=>Constant::ESTIMATE, 'proposal'=>Constant::FINALPROPOSAL);
            $totFinalAmt = Opportunity::getTotalFinalAmt($stages);
            echo json_encode($totFinalAmt);
        }
        
        public function actionGetPipelineChartData(){
            $opportunities = Opportunity::getPipeLineReport();
            $makePipelineChartData = OpportunityUtils::getChartData($opportunities);
            echo json_encode($makePipelineChartData);
        }
        
        public function actionGetClosedSalesChartData(){
            $opportunities = Opportunity::getClosedSaleReport();
            $makeClosedSaleChartData = OpportunityUtils::getChartData($opportunities);
            echo json_encode($makeClosedSaleChartData);
        }

        public function actionGetCurrentyearWonOpportunities()
        {
            $stages = array('won'=>  Constant::WON);
            $totFinalAmt = Opportunity::getTotalFinalAmt($stages,$whereCon = 'YEAR(status_changed_date) = YEAR(CURDATE())');
            echo json_encode($totFinalAmt);
        }
        
        /**
         * Update status_changed_date field
         * @param Null
         * @return Null
         * @author Sundar P - 04-Oct-2016         
         * Describtion: To update the status changed date for the records which is not updated at the time of own stage.
         */
        public function actionUpdateOwnStageDate(){
            
          $result = Opportunity::updateOwnDate();
          if($result > 0){
            Yii::app()->user->setFlash('notification',
                        Zurmo::t('OpportunityModule', 'Date updated successfully.')
                    );
          }else{
            Yii::app()->user->setFlash('notification',
                        Zurmo::t('OpportunityModule', 'No records found.')
                    );
          }
          $this->redirect(Yii::app()->createUrl('opportunities/default'));
                
        }
        
        public function actionOppPipeLineReports() {
            $view           = new OpportunityReportView('Pipeline');
            $oppReportView  = new OpportunityPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
            echo $oppReportView->render();
        }
        
        public function actionOppPipeLineReportsByUser($id) {
            $view           = new OpportunityReportView('user', $id);
            $oppReportView  = new OpportunityPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
            echo $oppReportView->render();
        }
        
        public function actionOppPipeLineReportsExport() {
            
            $oppData = Opportunity::getOppourtunityPipeLineReport();
            
            if(empty($oppData))
                return $this->generateNoRecordsContentHTML('Pipeline');
            
            $lastArr = count($oppData)-1;
            $oppArr = array();
            $randNumber = time();
            foreach($oppData as $oppDataKey=>$oppDataArr)
            {
                $oppArr[$oppDataArr['user_id']]['userName'] = $oppDataArr['sales_person'];
                
                // To assign the names
                if(!isset($oppArr[$oppDataArr['user_id']]['Consulting_Project Final']))
                    $oppArr[$oppDataArr['user_id']]['Consulting_Project Final'] = 0;
                
                if(!isset($oppArr[$oppDataArr['user_id']]['Estimate_Project Final']))
                    $oppArr[$oppDataArr['user_id']]['Estimate_Project Final'] = 0;
                
                if(!isset($oppArr[$oppDataArr['user_id']]['Final Proposal_Project Final']))
                    $oppArr[$oppDataArr['user_id']]['Final Proposal_Project Final'] = 0;
                
                if(!isset($oppArr[$oppDataArr['user_id']]['Consulting_Recurring Final']))
                    $oppArr[$oppDataArr['user_id']]['Consulting_Recurring Final'] = 0;
                
                if(!isset($oppArr[$oppDataArr['user_id']]['Estimate_Recurring Final']))
                    $oppArr[$oppDataArr['user_id']]['Estimate_Recurring Final'] = 0;
                
                if(!isset($oppArr[$oppDataArr['user_id']]['Final Proposal_Recurring Final']))
                    $oppArr[$oppDataArr['user_id']]['Final Proposal_Recurring Final'] = 0;
                
                if(!isset($oppArr[$oppDataArr['user_id']]['Consulting_Project Final']))
                    $oppArr[$oppDataArr['user_id']]['Consulting_Project Final'] = 0;
                
                $oppArr[$randNumber]['userName'] = 'Total';
                
                if(!isset($oppArr[$randNumber]['Consulting_Project Final']))
                    $oppArr[$randNumber]['Consulting_Project Final'] = 0;
                
                if(!isset($oppArr[$randNumber]['Estimate_Project Final']))
                    $oppArr[$randNumber]['Estimate_Project Final'] = 0;
                
                if(!isset($oppArr[$randNumber]['Final Proposal_Project Final']))
                    $oppArr[$randNumber]['Final Proposal_Project Final'] = 0;
                
                if(!isset($oppArr[$randNumber]['Consulting_Recurring Final']))
                    $oppArr[$randNumber]['Consulting_Recurring Final'] = 0;
                
                if(!isset($oppArr[$randNumber]['Estimate_Recurring Final']))
                    $oppArr[$randNumber]['Estimate_Recurring Final'] = 0;
                
                if(!isset($oppArr[$randNumber]['Final Proposal_Recurring Final']))
                    $oppArr[$randNumber]['Final Proposal_Recurring Final'] = 0;
                
                if(isset($oppArr[$oppDataArr['user_id']][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']]))
                    $oppArr[$oppDataArr['user_id']][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] += $oppDataArr['price'];
                else
                   $oppArr[$oppDataArr['user_id']][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] = $oppDataArr['price'];
                
                // For the Colom wise calculations
                if(isset($oppArr[$randNumber][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']]))
                    $oppArr[$randNumber][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] += $oppDataArr['price'];
                else
                {                   
                   $oppArr[$randNumber][$oppDataArr['stage'].'_'.$oppDataArr['Recordtype']] = $oppDataArr['price'];
                }                
            }            
            
            // To move that total arr element to the last of the array
            $totalArr = $oppArr[$randNumber];
            unset($oppArr[$randNumber]);
            $totalResult = count($oppArr);
            $oppArr[$randNumber] = $totalArr;            
            
            $fileName = 'pipeline_report' . ".csv";
            ExportItemToCsvFileUtil::export($oppArr, array('Salesperson', 'Project Final - Consulting','Project Final - Estimate', 'Project Final - Finalproposal', 'Recurring Final - Consulting','Recurring Final - Estimate', 'Recurring Final - Finalproposal' ), $fileName, true);
        }
    }
?>
