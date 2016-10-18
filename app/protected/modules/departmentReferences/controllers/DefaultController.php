<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class DepartmentReferencesDefaultController extends ZurmoModuleController {
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
        
    public function actionList()  {
        $pageSize                       = Yii::app()->pagination->resolveActiveForCurrentUserByType(
             'listPageSize', get_class($this->getModule()));
        $departmentReferences                         = new DepartmentReference(false);
        $searchForm                     = new DepartmentReferencesSearchForm($departmentReferences);
        $dataProvider = $this->resolveSearchDataProvider(
              $searchForm,
              $pageSize,
              null,
              'DepartmentReferencesSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')     {
                $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider
                );
                $view = new DepartmentReferencesPageView($mixedView);
            }
            else
            {
                $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
                $view = new DepartmentReferencesPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
            }
            echo $view->render();
        }

		public function actionEdit($id){
			   //get boject by id
		    $deptReference = DepartmentReference::getById(intval($id));
            $deptReference->prevBurdonCost = $deptReference->burdonCost;
            $deptReference->prevLaborCost = $deptReference->laborCost;
		    //Security check
		    ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($deptReference);

		    //create view and render
			$editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost($deptReference), 'Edit');
	        $view = new DepartmentReferencesPageView(ZurmoDefaultViewUtil::
		                                    makeStandardViewForCurrentUser($this,	                                       $editAndDetailsView));
		    echo $view->render();
    	} 
        
        public function actionCreate() {
            $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost(new DepartmentReference()), 'Edit');
            $view = new DepartmentReferencesPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
            echo $view->render();
        }
        
        public function actionDetails($id) {
            $deptReference = static::getModelAndCatchNotFoundAndDisplayError('DepartmentReference', intval($id));
            $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'DepartmentReferencesSearchView', $deptReference);
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($deptReference);
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($deptReference), 'DepartmentReferencesModule'), $deptReference);
            $titleBarAndEditView = $this->makeEditAndDetailsView($deptReference, 'Details');
            $view = new DepartmentReferencesPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
        }
        
        public function actionMassDelete()  {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
            $deptReference = new DepartmentReference(false);

            $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new DepartmentReferencesSearchForm($deptReference),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'DepartmentReferencesSearchView');
            $selectedRecordCount = $this->getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $deptReference = $this->processMassDelete(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'DepartmentReferencesPageView',
                $deptReference,
                DepartmentReferencesModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massDeleteView = $this->makeMassDeleteView(
                $deptReference,
                $activeAttributes,
                $selectedRecordCount,
                DepartmentReferencesModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new DepartmentReferencesPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $massDeleteView));
            echo $view->render();
        }
        
        protected static function getSearchFormClassName()
        {
            return 'DepartmentReferencesSearchForm';
        }
        
        public function actionExport(){
            $this->export('DepartmentReferencesSearchView');
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
