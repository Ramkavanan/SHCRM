<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class UnitofmeasuresDefaultController extends ZurmoModuleController {
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
                $unitofmeasures = Unitofmeasure::getUnitofmeasureByNameEdit($model->name, $model->id);
            } else {
                $unitofmeasures = Unitofmeasure::getUnitofmeasureByName($model->name);
            }
            if ($unitofmeasures != null)
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
        $unitofmeasures                 = new Unitofmeasure(false);
        $searchForm                     = new UnitofmeasuresSearchForm($unitofmeasures);
        $dataProvider = $this->resolveSearchDataProvider(
              $searchForm,
              $pageSize,
              null,
              'UnitofmeasuresSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view') {
            $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider
                );
                $view = new UnitofmeasuresPageView($mixedView);
        }
        else
        {
            $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
            $view = new UnitofmeasuresPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
         }
            echo $view->render();
    }

    public function actionEdit($id){
        //get boject by id
        $unitofmeasure = Unitofmeasure::getById(intval($id));
        //Security check
        ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($unitofmeasure);
        //create view and render
        $editAndDetailsView = $this->makeEditAndDetailsView(
                                        $this->attemptToSaveModelFromPost($unitofmeasure), 'Edit');
        $view = new UnitofmeasuresPageView(ZurmoDefaultViewUtil::
                                                makeStandardViewForCurrentUser($this, $editAndDetailsView));
		echo $view->render();
    } 

    public function actionCreate() {
        $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost(new Unitofmeasure()), 'Edit');
        $view = new UnitofmeasuresPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }
        
    public function actionDetails($id) {
        $unitofmeasure = static::getModelAndCatchNotFoundAndDisplayError('Unitofmeasure', intval($id));
        $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'UnitofmeasuresSearchView', $unitofmeasure);
        ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($unitofmeasure);
        AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($unitofmeasure), 'UnitofmeasuresModule'), $unitofmeasure);
        $titleBarAndEditView = $this->makeEditAndDetailsView($unitofmeasure, 'Details');
        $view = new UnitofmeasuresPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $titleBarAndEditView));
        echo $view->render();
    }

    public function actionDelete($id)
    {
        if (!Unitofmeasure::isUnitofmeasureInUseById(intval($id))) {
            $unitofmeasures = Unitofmeasure::GetById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($unitofmeasures);
            $unitofmeasures->delete();
        }
        else
        {
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Not possible to delete. Unit Of Measure is already in use'));
        }
        $this->redirect(array($this->getId() . '/index'));
    }

    public function actionExport(){
        $this->export('UnitofmeasureEditAndDetailsView','Unitofmeasure');
    }
    public function actionMassDelete()
    {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
        $unitofmeasure = new Unitofmeasure(false);

        $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new UnitofmeasuresSearchForm($unitofmeasure),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'UnitofmeasuresSearchView');
        $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
        $unitofmeasure = $this->processMassDelete(
            $pageSize,
            $activeAttributes,
            $selectedRecordCount,
            'UnitofmeasuresPageView',
            $unitofmeasure,
            UnitofmeasuresModule::getModuleLabelByTypeAndLanguage('Plural'),
            $dataProvider
        );
        $massDeleteView = $this->makeMassDeleteView(
            $unitofmeasure,
            $activeAttributes,
            $selectedRecordCount,
            UnitofmeasuresModule::getModuleLabelByTypeAndLanguage('Plural')
        );
        $view = new UnitofmeasuresPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $massDeleteView));
        echo $view->render();
    }
    public function actionMassDeleteProgress()
    {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                        'massDeleteProgressPageSize');
        $unitofmeasure = new Unitofmeasure(false);
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
            new UnitofmeasuresSearchForm($unitofmeasure),
            $pageSize,
            Yii::app()->user->userModel->id,
            null,
            'UnitofmeasuresSearchView'
        );
        $this->processMassDeleteProgress(
            'Unitofmeasure',
            $pageSize,
            UnitofmeasuresModule::getModuleLabelByTypeAndLanguage('Plural'),
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
