<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CategoriesDefaultController extends ZurmoModuleController {
    /**public function filters()  {
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
        }*/

    protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false)
    {
        assert('$redirectUrlParams == null || is_array($redirectUrlParams)');
        $postVariableName = get_class($model);
        if (isset($_POST[$postVariableName]))
        {
            $model->setAttributes($_POST[$postVariableName]);
            $currencyHelper = Yii::app()->currencyHelper;
            if($model->id > 0 ) {
                $categories = Category::getCategoryByNameEdit($model->name, $model->id);
            } else {
                $categories = Category::getCategoryByName($model->name);
            }
            if ($categories != null)
            {
                $model->addError('name', Zurmo::t('Core', 'Name Already Exists'));
                $currencyHelper->resetErrors();
                return $model;
            }
            if ($model->save())
            {
                $this->redirectAfterSaveModel($model->id, $redirectUrlParams);
            }
        }
        return $model;
    }

    public function actionList()  {
        $pageSize                       = Yii::app()->pagination->resolveActiveForCurrentUserByType(
             'listPageSize', get_class($this->getModule()));
        $categories                     = new Category(false);
        $searchForm                     = new CategoriesSearchForm($categories);
        $dataProvider = $this->resolveSearchDataProvider(
              $searchForm,
              $pageSize,
              null,
              'CategoriesSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')     {
                $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider
                );
                $view = new CategoriesPageView($mixedView);
            }
            else
            {
                $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
                $view = new CategoriesPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
            }
            echo $view->render();
        }

	public function actionEdit($id){
        //get boject by id
        $category = Category::getById(intval($id));
	    //Security check
        ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($category);
        //create view and render
        $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost($category), 'Edit');
	    $view = new CategoriesPageView(ZurmoDefaultViewUtil::
		                                    makeStandardViewForCurrentUser($this,	                                       $editAndDetailsView));
	    echo $view->render();
    	} 

        public function actionCreate() {
            $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost(new Category()), 'Edit');
            $view = new CategoriesPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
            echo $view->render();
        }
        
        public function actionDetails($id) {
            $category = static::getModelAndCatchNotFoundAndDisplayError('Category', intval($id));
            $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'CategoriesSearchView', $category);
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($category);
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($category), 'CategoriesModule'), $category);
            $titleBarAndEditView = $this->makeEditAndDetailsView($category, 'Details');
            $view = new CategoriesPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
        }

        public function actionDelete($id)
        {
            if (!Category::isCategoryInUseById(intval($id)))
            {
                $category = Category::GetById(intval($id));
                ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($category);
                $category->delete();
            }
            else
            {
                Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Not possible to delete. Category is already in use'));
            }
            $this->redirect(array($this->getId() . '/index'));
        }

        public function actionExport(){
            $this->export('CategoryEditAndDetailsView','Category');
        }

        public function actionMassDelete()
        {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                                'massDeleteProgressPageSize');
            $category = new Category(false);
            $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                    new CategoriesSearchForm($category),
                    $pageSize,
                    Yii::app()->user->userModel->id,
                    null,
                    'CategoriesSearchView');         
            $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $category = $this->processMassDelete(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'CategoriesPageView',
                $category,
                CategoriesModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massDeleteView = $this->makeMassDeleteView(
                $category,
                $activeAttributes,
                $selectedRecordCount,
                CategoriesModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new CategoriesPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $massDeleteView));
            echo $view->render();
        }

        public function actionMassDeleteProgress()
        {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
            $category = new Category(false);
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new CategoriesSearchForm($category),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'CategoriesSearchView'
            );
            $this->processMassDeleteProgress(
                'Category',
                $pageSize,
                CategoriesModule::getModuleLabelByTypeAndLanguage('Plural'),
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
}
?>