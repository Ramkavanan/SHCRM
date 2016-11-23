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

    class AccountsDefaultController extends ZurmoModuleController
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
                        ZurmoBaseController::REQUIRED_ATTRIBUTES_FILTER_PATH . ' + modalCreate',
                        'moduleClassName' => get_class($this->getModule()),
                        'viewClassName'   => 'AccountModalCreateView',
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
            $pageSize                       = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                                              'listPageSize', get_class($this->getModule()));
            $account                        = new Account(false);
            $searchForm                     = new AccountsSearchForm($account);
            $listAttributesSelector         = new ListAttributesSelector('AccountsListView', get_class($this->getModule()));
            $searchForm->setListAttributesSelector($listAttributesSelector);
            $dataProvider = $this->resolveSearchDataProvider(
                $searchForm,
                $pageSize,
                null,
                'AccountsSearchView'
            );
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')
            {
                $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider
                );
                $view = new AccountsPageView($mixedView);
            }
            else
            {
                $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider,
                                                                    'SecuredActionBarForAccountsSearchAndListView');
                $view = new AccountsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $mixedView));
            }
            echo $view->render();
        }

        public function actionDetails($id)
        {
            $account = static::getModelAndCatchNotFoundAndDisplayError('Account', intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($account);
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($account), 'AccountsModule'), $account);
            if (KanbanUtil::isKanbanRequest() === false)
            {
                $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'AccountsSearchView', $account);
                $detailsAndRelationsView = $this->makeDetailsAndRelationsView($account, 'AccountsModule',
                                                                              'AccountDetailsAndRelationsView',
                                                                              Yii::app()->request->getRequestUri(),
                                                                              $breadCrumbView);
                $view                    = new AccountsPageView(ZurmoDefaultViewUtil::
                                                                    makeStandardViewForCurrentUser($this, $detailsAndRelationsView));
            }
            else
            {
                $view = TasksUtil::resolveTaskKanbanViewForRelation($account, $this->getModule()->getId(), $this,
                                                                        'TasksForAccountKanbanView', 'AccountsPageView');
            }
            echo $view->render();
        }

        public function actionCreate()
        {
            Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
            $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost(new Account(), 'from_create'), 'Edit');
            $view = new AccountsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
            echo $view->render();
        }

        public function actionEdit($id, $redirectUrl = null)
        {
            Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
            $account = Account::getById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($account);
            $this->processEdit($account, $redirectUrl);
        }

        public function actionCopy($id)
        {
            $copyToAccount  = new Account();
            $postVariableName   = get_class($copyToAccount);
            if (!isset($_POST[$postVariableName]))
            {
                $account        = Account::getById((int)$id);
                ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($account);
                ZurmoCopyModelUtil::copy($account, $copyToAccount);
            }
            $this->processEdit($copyToAccount);
        }

        protected function processEdit(Account $account, $redirectUrl = null)
        {
            $view = new AccountsPageView(ZurmoDefaultViewUtil::
                            makeStandardViewForCurrentUser($this,
                            $this->makeEditAndDetailsView(
                                $this->attemptToSaveModelFromPost($account, 'from_edit'), 'Edit')));
            echo $view->render();
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
            $account = new Account(false);
            $activeAttributes = $this->resolveActiveAttributesFromMassEditPost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AccountsSearchForm($account),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'AccountsSearchView');
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $account = $this->processMassEdit(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'AccountsPageView',
                $account,
                AccountsModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massEditView = $this->makeMassEditView(
                $account,
                $activeAttributes,
                $selectedRecordCount,
                AccountsModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new AccountsPageView(ZurmoDefaultViewUtil::
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
            $account = new Account(false);
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AccountsSearchForm($account),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'AccountsSearchView'
            );
            $this->processMassEditProgressSave(
                'Account',
                $pageSize,
                AccountsModule::getModuleLabelByTypeAndLanguage('Plural'),
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
         * @see Controller->makeMassDeleteProgressView
         * @see Controller->processMassDelete
         * @see
         */
        public function actionMassDelete()
        {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
            $account = new Account(false);

            $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AccountsSearchForm($account),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'AccountsSearchView');
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $account = $this->processMassDelete(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'AccountsPageView',
                $account,
                AccountsModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massDeleteView = $this->makeMassDeleteView(
                $account,
                $activeAttributes,
                $selectedRecordCount,
                AccountsModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new AccountsPageView(ZurmoDefaultViewUtil::
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
            $account = new Account(false);
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AccountsSearchForm($account),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'AccountsSearchView'
            );
            $this->processMassDeleteProgress(
                'Account',
                $pageSize,
                AccountsModule::getModuleLabelByTypeAndLanguage('Plural'),
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
            echo ModalSearchListControllerUtil::
                 setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider);
        }

        public function actionDelete($id)
        {
            $account = Account::GetById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($account);
            $account->delete();
            $this->redirect(array($this->getId() . '/index'));
        }

        protected static function getSearchFormClassName()
        {
            return 'AccountsSearchForm';
        }

        public function actionExport()
        {
            $this->export('AccountsSearchView');
        }

        /**
         * Modal create for account
         */
        public function actionModalCreate()
        {
            $account = new Account();
            $this->validateCreateModalPostData();
            if (isset($_POST['Account']) && Yii::app()->request->isAjaxRequest)
            {
                $account = $this->attemptToSaveModelFromPost($account, null, false);
                if ($account->id > 0)
                {
                    echo CJSON::encode(array('id' => $account->id, 'name' => $account->name));
                    Yii::app()->end(0, false);
                }
                else
                {
                    throw new FailedToSaveModelException();
                }
            }
            echo ModalEditAndDetailsControllerUtil::setAjaxModeAndRenderModalEditAndDetailsView($this,
                                                                                      'AccountModalCreateView',
                                                                                      $account, 'Edit');
        }

        /**
         * Modal validate for account
         */
        protected function validateCreateModalPostData()
        {
            $account = new Account();
            if (isset($_POST['ajax']) && Yii::app()->request->isAjaxRequest)
            {
                $account = $this->attemptToSaveModelFromPost($account, null, false, true);
                echo CJSON::encode(ZurmoActiveForm::makeErrorsDataAndResolveForOwnedModelAttributes($account));
                Yii::app()->end(0, false);
            }
        }

        /**
         * Overriding to implement the dedupe action for new leads
         */
        public function actionSearchForDuplicateModels($attribute, $value)
        {
            assert('is_string($attribute)');
            assert('is_string($value)');
            $model          = new Account();
            $depudeRules    = DedupeRulesFactory::createRulesByModel($model);
            $viewClassName  = $depudeRules->getDedupeViewClassName();
            $searchResult   = $depudeRules->searchForDuplicateModels($attribute, $value);
            ob_end_clean();
            if ($searchResult != null)
            {
                $summaryView    = new $viewClassName($this->id, $this->module->id, $model, $searchResult['matchedModels']);
                $content        = $summaryView->render();
                $message        = $searchResult['message'];
                echo CJSON::encode(array('content' => $content, 'message' => $message));
            } 
        }

        /**
         * List view merge for accounts
         */
        public function actionListViewMerge()
        {
            $this->processListViewMerge('Account',
                                        'AccountsListDuplicateMergedModelForm',
                                        'AccountsMerged', 'AccountsPageView',
                                        '/accounts/default/list');
        }

        public function actionOrgGraph()
        {
            $renderOrgGraphModalView = new AccountOrgChartModalView();
            $view = new ModalView($this, $renderOrgGraphModalView);
            return $view->render();
        }
        
        public function actionPrintView($id)
        {
            $printData = '';
            $printView =   new AccountPrintView($printData, $id);
            echo $printView->render();
        }
        
        /**
         * Check if form is posted. If form is posted attempt to save. If save is complete, confirm the current
         * user can still read the model.  If not, then redirect the user to the index action for the module.
         */
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
                $linkUrl = Yii::app()->getBaseUrl(true).'/index.php/accounts/default/details?id='.$model->id;
                if(is_string($redirectUrlParams))
                {
                    if($redirectUrlParams == 'from_create')
                    {
                        $subject = '[VERTWARE] A NEW ACCOUNT IS ASSIGNED TO YOU';
                    }elseif ($redirectUrlParams == 'from_edit') {
                        $subject = '[VERTWARE] ACCOUNT OWNER CHANGE';
                    }
                        $redirectUrlParams = '';
                        if($model->owner->id != $oldOwnerId)
                        {
                            $recipients = array();
                            $ownerAccount  = User::getById($model->owner->id);
                            if (!empty($ownerAccount->primaryEmail->emailAddress)){
                                $recipients = array($ownerAccount->primaryEmail->emailAddress);
                            }
                            $accountAssigner     = User::getById(Yii::app()->user->id);
                            if (!empty($accountAssigner->primaryEmail->emailAddress)) {
                                $fromAddress = $accountAssigner->primaryEmail->emailAddress;
                            } else {
                                $fromAddress = Constant::DEFAULT_FROM_EMAIL;
                            }                                   
                            $fromAddress = array(
                                'name'      => 'VERTWARE',
                                'address'   => $fromAddress
                            );
                            
                            $mailContent = array(
                                'subject' => $subject,
                                'content' => 'Hi '.$ownerAccount->firstName .', <br> <p> <b>Account Details:</b> <br> Name: '.$model->name.'<br> Assigned by: '.$accountAssigner->getFullName().' <br> <br> Link: <a href="'.$linkUrl.'">'.$linkUrl.'</a></p> 
                                <hr> Thanks. <br> ShinnedHawks'
                            );
                            if(count($recipients) > 0){
                                ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                            }
                        }
                }
                $this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
            }
            return $model;
        }
    }
?>
