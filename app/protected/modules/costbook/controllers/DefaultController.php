<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookDefaultController
 *
 * @author ideas2it
 */
class CostbookDefaultController  extends ZurmoModuleController{    
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
                    'viewClassName'   => 'CostbookModalCreateView',
               ),
                array(
                    ZurmoModuleController::ZERO_MODELS_CHECK_FILTER_PATH . ' + list',
                    'controller' => $this,
               ),
           )
        );
    }

    public function actionIndex() { 
        $this->clearCaches();
        Yii::app()->custom->resolveIsCustomDataLoaded();
        parent::actionIndex();
    }

    public function actionList() {
        $pageSize                       = Yii::app()->pagination->resolveActiveForCurrentUserByType(
             'listPageSize', get_class($this->getModule()));
        $costbook                       = new Costbook(false);
        $searchForm                     = new CostbookSearchForm($costbook);
        $dataProvider = $this->resolveSearchDataProvider(
              $searchForm,
              $pageSize,
              null,
              'CostbookSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view') {
            $mixedView = $this->makeListView(
                $searchForm,
                $dataProvider
            );
            $view = new CostbookPageView($mixedView);
        } else {
            $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
        }
        echo $view->render();
            
    }

    protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false)
    {
        assert('$redirectUrlParams == null || is_array($redirectUrlParams)');
        $postVariableName = get_class($model);
        if (isset($_POST[$postVariableName]))
        {          
            $model->setAttributes($_POST[$postVariableName]);
            $currencyHelper = Yii::app()->currencyHelper;
            
            if($model->id < 0) {
                $maxproductcode=  Costbook::getByMaxProductCodeInQuery();
                $productcodevalue =$maxproductcode[0]['maxproductcode'];
                $maxproductcodePrefixZero = sprintf("%04d", $maxproductcode[0]['maxproductcode']);
                $maxproductcodePrefixZeroVert='VERT'.$maxproductcodePrefixZero;

                $model->productcode=$maxproductcodePrefixZeroVert;
                $model->productcodevalue=$productcodevalue;
            
                if($model->costofgoodssold->value == 'Labor') {
                    $product_code = 'L-' . $model->productcode;
                }
                if($model->costofgoodssold->value == 'Equipment') {
                    $model->prevcostperunit = $model->unitdirectcost;
                    $product_code = 'E-' . $model->productcode;
                }
                if($model->costofgoodssold->value == 'Material') {
                    $model->prevcostperunit = $model->unitdirectcost;
                    $product_code = 'M-' . $model->productcode;
                }
                if($model->costofgoodssold->value == 'Subcontractor') {
                    $model->prevcostperunit = $model->unitdirectcost;
                    $product_code = 'S-' . $model->productcode;
                }
                if($model->costofgoodssold->value == 'Other') {
                    $model->prevcostperunit = $model->unitdirectcost;
                    $product_code = 'O-' . $model->productcode;
                }
                if($model->costofgoodssold->value == 'Assembly') {
                    $product_code = 'A-' . $model->productcode;
                }
                
                $costbook = Costbook::getByProductCode($product_code);                
                
                if ($costbook != null)
                {
                    $model->addError('productcode', Zurmo::t('Core', 'Product Code Already Exists'));
                    $currencyHelper->resetErrors();
                    return $model;
                }
            }            
            
            if ($model->save())
            {
                if($model->costofgoodssold->value != 'Assembly') {
                    $this->redirectAfterSaveModel($model->id, $redirectUrlParams);
                } else {
                   $this->redirectAfterSaveModel($model->id, array($this->getId() . '/editAssembly', 'id' => $model->id));
                }        
            }
        }
        return $model;
    }

    public function actionCreate()
    {
        $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost(new Costbook()), 'Edit');
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionDetails($id){
	    $Costbook = static::getModelAndCatchNotFoundAndDisplayError("Costbook", intval($id));
          Yii::app()->clientScript->registerScript('append$Symbol',
        '$("table tbody tr th").filter(function() {
                return $.text([this]) == "Cost Rate";}).next().prepend("<span>$</span>");
        ');
        if($Costbook->costofgoodssold == 'Labor') {
            $Costbook->laborCost='$'.$Costbook->departmentreference->laborCost;
            $Costbook->burdenCost='$'.$Costbook->departmentreference->burdonCost;
            $editAndDetailsView = new CostbookLaborView('Details',$this->getId(), $this->getModule()->getId(), $Costbook);
        } else if($Costbook->costofgoodssold == 'Equipment') { 
            $editAndDetailsView = new CostbookEquipmentView('Details',$this->getId(), $this->getModule()->getId(), $Costbook);
        } else if($Costbook->costofgoodssold == 'Material') { 
            $editAndDetailsView = new CostbookMaterialView('Details',$this->getId(), $this->getModule()->getId(), $Costbook);
        } else if($Costbook->costofgoodssold == 'Subcontractor') { 
            $editAndDetailsView = new CostbookSubcontractorView('Details',$this->getId(), $this->getModule()->getId(), $Costbook);
        } else if($Costbook->costofgoodssold == 'Other') { 
            $editAndDetailsView = new CostbookOtherView('Details',$this->getId(), $this->getModule()->getId(), $Costbook);
        } else if($Costbook->costofgoodssold == 'Assembly') { 
            if(isset($_REQUEST['from_finish']))
            {
                if($_REQUEST['from_finish'] == 1)
                {
                    $Costbook->assemblycreatefinal = 1;                
                    if(!$Costbook->save()) {
                        throw new Exception('Exception While saving model');
                    }
                }
            }
            $editAndDetailsView = new CostbookAssemblyView('Details',$this->getId(), $this->getModule()->getId(), $Costbook);
        }    
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    } 

    public function actionEdit($id, $redirectUrl = null) {
        Yii::app()->clientScript->registerScript('productcode',
            '$("label[for=Costbook_departmentreference_id]").append("<span class=required> * </span>");
             $("label[for=Costbook_costperunit]").append("<span class=required> * </span>");
             $("label[for=Costbook_unitofmeasure_value]").append("<span class=required> * </span>");   
        ');
        //get boject by id
        $costbook = Costbook::getById(intval($id));
        //Security check
        ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($costbook);
        //create view and render
        if($costbook->costofgoodssold == 'Labor') {
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this,
                                            new CostbookLaborView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl), 'Edit')));
        } else if($costbook->costofgoodssold == 'Equipment') {
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this,
                                            new CostbookEquipmentView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl), 'Edit')));
        } else if($costbook->costofgoodssold == 'Material') {
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this,
                                            new CostbookMaterialView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl), 'Edit')));
        } else if($costbook->costofgoodssold == 'Subcontractor') {
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this,
                                            new CostbookSubcontractorView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl), 'Edit')));    
        } else if($costbook->costofgoodssold == 'Other') {
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this,
                                            new CostbookOtherView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl), 'Edit')));
        } else if($costbook->costofgoodssold == 'Assembly') {
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                        makeStandardViewForCurrentUser($this,
                                            new CostbookAssemblyView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl), 'Edit')));
        }
        echo $view->render();
    }

    public function actionCopy($id)
    {
        $copyToCostbook  = new Costbook();
        $postVariableName   = get_class($copyToCostbook);
        if (!isset($_POST[$postVariableName])) {
            $costbook        = Costbook::getById((int)$id);
            $copyToCostbook->costofgoodssold->value = $costbook->costofgoodssold->value;
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($costbook);
            ZurmoCopyModelUtil::copy($costbook, $copyToCostbook);
        }
        $this->processEdit($copyToCostbook);
    }

    protected function processEdit(Costbook $costbook, $redirectUrl = null)
    {
        if($costbook->costofgoodssold == 'Labor') {
            $titleBarAndEditView = new CostbookLaborView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl));
        } else if($costbook->costofgoodssold == 'Equipment') {
            $titleBarAndEditView = new CostbookEquipmentView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl));
        } else if($costbook->costofgoodssold == 'Material') {
            $titleBarAndEditView = new CostbookMaterialView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl));
        } else if($costbook->costofgoodssold == 'Subcontractor') {
            $titleBarAndEditView = new CostbookSubcontractorView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl));
        } else if($costbook->costofgoodssold == 'Other') {
            $titleBarAndEditView = new CostbookOtherView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl));
        } else if($costbook->costofgoodssold == 'Assembly') {
            $titleBarAndEditView = new CostbookAssemblyView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, $redirectUrl));
        }
        $view = new CostbookPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $titleBarAndEditView));
        echo $view->render();

    }

    public function actionMassEdit()
    {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massEditProgressPageSize');
        $costbook = new Costbook(false);
        $activeAttributes = $this->resolveActiveAttributesFromMassEditPost();
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
        new CostbookSearchForm($costbook),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'CostbookSearchView');
        $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
        $costbook = $this->processMassEdit(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'CostbookPageView',
                $costbook,
                CostbookModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
        $massEditView = $this->makeMassEditView(
                $costbook,
                $activeAttributes,
                $selectedRecordCount,
                CostbookModule::getModuleLabelByTypeAndLanguage('Plural')
            );
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $massEditView));
        echo $view->render();
    }

    public function actionMassEditProgressSave()
    {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                        'massEditProgressPageSize');
        $costbook = new Costbook(false);
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
            new CostbookSearchForm($costbook),
            $pageSize,
            Yii::app()->user->userModel->id,
            null,
            'CostbookSearchView'
        );
        $this->processMassEditProgressSave(
            'Costbook',
            $pageSize,
            CostbookModule::getModuleLabelByTypeAndLanguage('Plural'),
            $dataProvider
        );
    }

    public function actionDelete($id)
    {
        $costbook = Costbook::GetById(intval($id));
        ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($costbook);
        $costbook->delete();
        $this->redirect(array($this->getId() . '/index'));
    }

    public function actionMassDelete()
    {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
        $costbook = new Costbook(false);

        $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new CostbookSearchForm($costbook),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'CostbookSearchView');
        $selectedRecordCount = static::getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
        $costbook = $this->processMassDelete(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'CostbookPageView',
                $costbook,
                CostbookModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
        $massDeleteView = $this->makeMassDeleteView(
                $costbook,
                $activeAttributes,
                $selectedRecordCount,
                CostbookModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $massDeleteView));
        echo $view->render();
    }

    public function actionMassDeleteProgress()
    {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
        $costbook = new Costbook(false);
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new CostbookSearchForm($costbook),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'CostbookSearchView'
            );
        $this->processMassDeleteProgress(
                'Costbook',
                $pageSize,
                CostbookModule::getModuleLabelByTypeAndLanguage('Plural'),
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

    public function actionModalCreate()
    {
        $costbook = new Costbook();
        $this->validateCreateModalPostData();
        if (isset($_POST['Costbook']) && Yii::app()->request->isAjaxRequest)
        {
            $costbook = $this->attemptToSaveModelFromPost($costbook, null, false);
            if ($costbook->id > 0)
            {
                echo CJSON::encode(array('id' => $costbook->id, 'name' => $costbook->name));
                Yii::app()->end(0, false);
            }
            else
            {
                throw new FailedToSaveModelException();
            }
        }
        echo ModalEditAndDetailsControllerUtil::setAjaxModeAndRenderModalEditAndDetailsView($this,
                                                                                  'CostbookModalCreateView',
                                                                                  $costbook, 'Edit');
    }

    protected static function getSearchFormClassName()
    {
        return 'CostbookSearchForm';
    }

    public function actionExport()
    {
        $this->export('CostbookSearchView');
    }

    protected function validateCreateModalPostData()
    {
        $costbook = new Costbook();
        if (isset($_POST['ajax']) && Yii::app()->request->isAjaxRequest)
        {
            $costbook = $this->attemptToSaveModelFromPost($costbook, null, false, true);
            echo CJSON::encode(ZurmoActiveForm::makeErrorsDataAndResolveForOwnedModelAttributes($costbook));
            Yii::app()->end(0, false);
        }
    }

    protected function clearCaches()
    {
        PermissionsCache::forgetAll();
        RightsCache::forgetAll();
        PoliciesCache::forgetAll();
        AllPermissionsOptimizationCache::forgetAll();
    }

    public function actionLabor()
    {
        $this->clearCaches();
        Yii::app()->custom->resolveIsCustomDataLoaded();
        Yii::app()->clientScript->registerScript('productcode',
            '$("table tbody tr th").filter(function() {
                return $.text([this]) == "Product Code *";}).append("<span class=\"costBookProductCodePrependText\">L -</span>");
                $("label[for=Costbook_departmentreference_id]").append("<span class=required> * </span>");
            ');
        $costbook = new Costbook();
        $costbook->costofgoodssold->value = 'Labor';
	$costbook->unitofmeasure->value='MH';
        $editAndDetailsView = new CostbookLaborView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }
    
    public function actionEquipment()
    {
        $this->clearCaches();
        Yii::app()->custom->resolveIsCustomDataLoaded();
        //  $("label[for='"+$element.attr('id')+"']")     alert($("label[for=Costbook_costperunit]").text());
        Yii::app()->clientScript->registerScript('productcode',
            '$("label:contains(Costbook_costperunit)").text("test");
             $("table tbody tr th").filter(function() {
                return $.text([this]) == "Product Code *";}).append("<span class=\"costBookProductCodePrependText\">E -</span>");
             $("label[for=Costbook_costperunit]").append("<span class=required> * </span>");
             $("label[for=Costbook_unitofmeasure_value]").append("<span class=required> * </span>");
         '); 
        $costbook = new Costbook();
        $costbook->costofgoodssold->value = 'Equipment';
        $editAndDetailsView = new CostbookEquipmentView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();

    }

    public function actionMaterial()
    {
       $this->clearCaches();
        Yii::app()->custom->resolveIsCustomDataLoaded();
        Yii::app()->clientScript->registerScript('productcode',
            '$("table tbody tr th").filter(function() {
                return $.text([this]) == "Product Code *";}).append("<span class=\"costBookProductCodePrependText\" style=\"margin-left:50px;\">M -</span>");
             $("label[for=Costbook_costperunit]").append("<span class=required> * </span>");
             $("label[for=Costbook_unitofmeasure_value]").append("<span class=required> * </span>");   
        ');
        $costbook = new Costbook();
        $costbook->costofgoodssold->value = 'Material';
        $editAndDetailsView = new CostbookMaterialView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionSubcontractor()
    {
        $this->clearCaches();
        Yii::app()->custom->resolveIsCustomDataLoaded();
        Yii::app()->clientScript->registerScript('productcode',
        '$("table tbody tr th").filter(function() {
            return $.text([this]) == "Product Code *";}).append("<span class=\"costBookProductCodePrependText\">S -</span>");
            $("label[for=Costbook_costperunit]").append("<span class=required> * </span>");
            $("label[for=Costbook_unitofmeasure_value]").append("<span class=required> * </span>");
        ');
        $costbook = new Costbook();
        $costbook->costofgoodssold->value = 'Subcontractor';
        $editAndDetailsView = new CostbookSubcontractorView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();

    }

    public function actionOther()
    {
        $this->clearCaches();
        Yii::app()->custom->resolveIsCustomDataLoaded();
        Yii::app()->clientScript->registerScript('productcode',
        '$("table tbody tr th").filter(function() {
            return $.text([this]) == "Product Code *";}).append("<span class=\"costBookProductCodePrependText\" style=\"margin-left:50px;\">O -</span>");
            $("label[for=Costbook_costperunit]").append("<span class=required> * </span>");
            $("label[for=Costbook_unitofmeasure_value]").append("<span class=required> * </span>");
        ');
        $costbook = new Costbook();
        $costbook->costofgoodssold->value = 'Other';
        $editAndDetailsView = new CostbookOtherView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionAssembly()
    {
        $this->clearCaches();
        Yii::app()->custom->resolveIsCustomDataLoaded();
        Yii::app()->clientScript->registerScript('productcode',
        '$("table tbody tr th").filter(function() {
            return $.text([this]) == "Product Code *";}).append("<span class=\"costBookProductCodePrependText\">A -</span>");
            $("label[for=Costbook_costperunit]").append("<span class=required> * </span>");
            $("label[for=Costbook_unitofmeasure_value]").append("<span class=required> * </span>");
        ');
        $costbook = new Costbook();
        $costbook->costofgoodssold->value = 'Assembly';
        $editAndDetailsView = new CostbookAssemblyView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook, 'assemblyStep2'));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionAssemblyStep2($id) {
        if(empty($_SERVER['HTTP_REFERER'])) {
            $this->redirect(array('/costbook/default/Assembly'));
        }
        $costbook = static::getModelAndCatchNotFoundAndDisplayError('Costbook', intval($id));
        $editAndDetailsView = new CostbookAssemblyStepView('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($costbook));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionAssemblyStep3($id) {
        if(empty($_SERVER['HTTP_REFERER'])) {
            $this->redirect(array('/costbook/default/Assembly'));
        }
        $Costbook = static::getModelAndCatchNotFoundAndDisplayError("Costbook", intval($id));
        $editAndDetailsView = new CostbookAssemblyStep3View('Edit', $this->getId(), $this->getModule()->getId(), $this->attemptToSaveModelFromPost($Costbook));
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo  $view->render();
    }

    public function actionCancelAssemblyStep2($id)
    {
        $costbook = Costbook::GetById(intval($id));
        if($costbook->assemblycreatefinal != 1)
        {
         ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($costbook);
            if(!$costbook->delete()) {
                echo 0;
            } else {
                echo 1;
            }
        }
//        ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($costbook);
//        if(!$costbook->delete()) {
//            echo 0;
//        }
        else {
            echo 1;
        }
    }

    public function actionGetDepartmentReferenceLaborCostAndBurdonCostToCopy($id)
    {
        $departmentReference = static::getModelAndCatchNotFoundAndDisplayError('DepartmentReference', intval($id));
        ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($departmentReference);

        $data = array();
        if ($departmentReference->laborCost != null)
        {
            $data['laborCost'] = $departmentReference->laborCost;
        }
        if ($departmentReference->burdonCost != null)
        {
            $data['burdonCost'] = $departmentReference->burdonCost;
        }
        echo CJSON::encode($data);
    }

    public function actionGetAssemblySearchData($category, $costOfGoods, $productId, $productName, $sortFor='code', $sortOrder='asc') {
        $searchData = Costbook::getAssemblySearch($category, $costOfGoods, $productId, $productName, $sortFor, $sortOrder);              
        echo CJSON::encode($searchData);
    }

    public function actionSaveAssemblyStep2($ids, $model_id) {
        if($ids != ''){
            $costbook = Costbook::getById($model_id);
            $searchData = CostbookUtils::updateAssemlblyProductStep2($costbook, $ids, $model_id);
            $costCalculation = CostbookUtils::totalCostCalculation($costbook, $ids);
            if($searchData == 1 && $costCalculation == 1) {
                echo 1; 
            } else {
                echo 0;
            }
        }
    }

    public function actionGetDataByProductCode($productcode) {
        $data = Costbook::getByProductCode($productcode);
        echo CJSON::encode($data[0]->productname.'$##$'.$data[0]->productcode.'$##$'.$data[0]->unitofmeasure);
    }
    
    public function actionEditAssembly($id)
    {
        $costbook           = Costbook::getById(intval($id));
        $title              = Zurmo::t('ZurmoModule', 'User Membership');
       // $breadCrumbLinks    = array(strval($costbook) => array('group/' . static::resolveBreadCrumbActionByGroup($costbook),  'id' => $id), $title);
            
        $membershipForm     = CostbookAssemblyFormUtil::makeFormFromGroup($costbook);
        $postVariableName   = get_class($membershipForm);
        if (isset($_POST[$postVariableName]))
        {          
          
            if(isset($_POST[$postVariableName]['selectedAssemblyProducts']))
            {
               $_POST[$postVariableName]['assemblydetail'] = $_POST[$postVariableName]['selectedAssemblyProducts'];
                             
                             
                if(count($_POST[$postVariableName]['assemblydetail']) > 0)
                {
                   $assembluProducts = implode(';',array_filter($_POST[$postVariableName]['assemblydetail']));
                 
                   if ($assembluProducts != '') {

                       
                       $costCalculation = CostbookUtils::totalCostCalculation($costbook, $assembluProducts);
                       $searchData = CostbookUtils::updateAssemlblyProductNew($costbook, $assembluProducts, $id);
                    
                    }

                   
                    $this->redirect(array($this->getId() . '/AssemblyStep2', 'id' => $costbook->id));
                    Yii::app()->end(0, false);
                    
                } 
            }
            else {
                $assembluProducts = '';    
                $searchData = CostbookUtils::updateAssemlblyProductNew($costbook, $assembluProducts, $id);
     
                    $this->redirect(array($this->getId() . '/AssemblyStep2', 'id' => $costbook->id));
                    Yii::app()->end(0, false); 

              }
        }
        $titleBarAndEditView = new CostbookAssemblyEditView(
                                        $this->getId(),
                                        $this->getModule()->getId(),
                                        $membershipForm,
                                        $costbook,
                                        $this->getModule()->getPluralCamelCasedName());
       // $view                = new CostbookAssemblyView(ZurmoDefaultAdminViewUtil::
                                   //  makeStandardForCurrentUser($this, $titleBarAndEditView));
       // echo $titleBarAndEditView->render();
        $view = new CostbookPageView(ZurmoDefaultViewUtil::
                                     makeStandardViewForCurrentUser($this, $titleBarAndEditView));
        echo  $view->render();
    }
    
    public function actionGetCostBookDataByAssemblyCode($productcode) {
        $pieces = explode(",", rtrim($productcode, ','));
        
        foreach ($pieces as $value) {
            $varname = explode("|", $value);
            $assemblyArray[] = $varname[0];
        }
        $code = implode("','", $assemblyArray);
        $data = Costbook::getByProductCodeInQuery("'$code'");
        foreach ($data as $value) {
            if(empty($value["description"])){
                $value["description"] = "No Description is Available";
            }
            $productInfo = $value["productcode"].' - '.$value["description"].' </br> ';
            $dataArray[] = $productInfo;
        }
        echo CJSON::encode($dataArray);
    }
    
    protected function rempty($var) {
        return !($var == "" || $var == null);
    }
}

Yii::app()->clientScript->registerScript('some-name',
    '$("#Costbook_costofgoodssold_value").change(function(){
        var strSel =  this.value;
        window.location.href="'.Yii::app()->baseUrl.'/index.php/costbook/default/"+strSel;
    });');
