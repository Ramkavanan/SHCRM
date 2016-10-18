<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class OpportunityProductsDefaultController extends ZurmoModuleController {
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
        $opportunity                         = new OpportunityProduct(false);
        $searchForm                     = new OpportunityProductsSearchForm($opportunity);
        $dataProvider = $this->resolveSearchDataProvider(
              $searchForm,
              $pageSize,
              null,
              'OpportunityProductsSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')     {
                $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider
                );
                $view = new OpportunityProductsPageView($mixedView);
            }
            else
            {
                $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
                $view = new OpportunityProductsPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
            }
            echo $view->render();
        }
        
        public function actionCreate() {
            $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost(new OpportunityProduct()), 'Edit');
             Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
            $view = new OpportunityProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
            echo $view->render();
        }
        
        public function actionDetails($id) {
            $deptReference = static::getModelAndCatchNotFoundAndDisplayError('OpportunityProduct', intval($id));
            $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'OpportunityProductsSearchView', $deptReference);
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($deptReference);
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($deptReference), 'OpportunityProductsModule'), $deptReference);
            $titleBarAndEditView = $this->makeEditAndDetailsView($deptReference, 'Details');
            $view = new OpportunityProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
        }
        
        public function actionMassDelete()  {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
            $deptReference = new OpportunityProduct(false);

            $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new OpportunityProductsSearchForm($deptReference),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'OpportunityProductsSearchView');
            $selectedRecordCount = $this->getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $deptReference = $this->processMassDelete(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'OpportunityProductsPageView',
                $deptReference,
                OpportunityProductsModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massDeleteView = $this->makeMassDeleteView(
                $deptReference,
                $activeAttributes,
                $selectedRecordCount,
                OpportunityProductsModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new OpportunityProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $massDeleteView));
            echo $view->render();
        }
        
        public function actionExport(){
            $this->export('OpportunityProductsSearchView');
        }

	public function actionModalList() {
            $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                                            $_GET['modalTransferInformation']['sourceIdFieldId'],
                                            $_GET['modalTransferInformation']['sourceNameFieldId']
            );
            echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider,
                                                Yii::t('Default', 'OpportunityProductModuleSingularLabel Search',
                                                LabelUtil::getTranslationParamsForAllModules()));
        }
	
	//Implementation for creating Opportunity product from opportunity
	public function actionCreateFromRelation($relationAttributeName, $relationModelId, $relationModuleId, $redirectUrl)     {
            $opportunityProduct             = $this->resolveNewModelByRelationInformation( new OpportunityProduct(),
                                                                                $relationAttributeName,
                                                                                (int)$relationModelId,
                                                                                $relationModuleId);
            $this->actionCreateByModel($opportunityProduct, $redirectUrl);
        }

	protected function actionCreateByModel(OpportunityProduct $opportunityProduct, $redirectUrl = null)     {
            $titleBarAndEditView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost($opportunityProduct, $redirectUrl), 'Edit');
            $view = new OpportunityProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
        }

	public function actionEdit($id, $redirectUrl = null)   {
             Yii::app()->clientScript->registerScript('replaceDollarByUSD',
                '$(\'select[id$="_currency_id"]\').each(function() {
                    $(this).parent().parent().replaceWith($("<div>$</div>").toggleClass( "replaceDoller" ));
                    });');
            $opportunityProduct = OpportunityProduct::getById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($opportunityProduct);
            $this->processEdit($opportunityProduct, $redirectUrl);
        }
	
	protected function processEdit(OpportunityProduct $opportunityProduct, $redirectUrl = null)   {
            $view    = new OpportunityProductsPageView(ZurmoDefaultViewUtil::
                            makeStandardViewForCurrentUser($this,
                            $this->makeEditAndDetailsView(
                                $this->attemptToSaveModelFromPost($opportunityProduct, $redirectUrl), 'Edit')));
            echo $view->render();
        }

	public function actionDelete($id)     {
            $opptProduct = OpportunityProduct::GetById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($opptProduct);
            $opptProduct->delete();
            $this->redirect(array($this->getId() . '/index'));
        }

	//Override the parent class

	public function actionSelectFromRelatedList($portletId,
                                                    $uniqueLayoutId,
                                                    $relationAttributeName,
                                                    $relationModelId,
                                                    $relationModuleId,
                                                    $stateMetadataAdapterClassName = null)
        {
            $portlet               = Portlet::getById((int)$portletId);
            
            $modalListLinkProvider = new OpportunityProductTemplateSelectFromRelatedListModalListLinkProvider(
                                            $relationAttributeName,
                                            (int)$relationModelId,
                                            $relationModuleId,
                                            $portlet->getUniquePortletPageId(),
                                            $uniqueLayoutId,
                                            (int)$portlet->id,
                                            $this->getModule()->getId()
            );
  
            echo ModalSearchListControllerUtil::
                 setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider, $stateMetadataAdapterClassName);
        }
        
        
        public function actionAddOpportunityRelation($relationModuleId, $portletId, $uniqueLayoutId,
                            $id, $relationModelId, $relationAttributeName, $redirect) {
            $opportunityProduct = OpportunityProduct::GetById(intval($id));
            $opportunity = Opportunity::GetById(intval($relationModelId));
            if($opportunityProduct ->opportunity  != $opportunity ) {
                $opportunityProduct ->opportunity  = $opportunity;
                $opportunityProduct->save();
            }
            if((bool) $redirect){
                $isViewLocked = ZurmoDefaultViewUtil::getLockKeyForDetailsAndRelationsView('lockPortletsForDetailsAndRelationsView');
                $redirectUrl  = Yii::app()->createUrl('/' . $relationModuleId . '/default/details', array('id' => $relationModelId));
                $this->redirect(array('/' . $relationModuleId . '/defaultPortlet/modalRefresh',
                                        'portletId'            => $portletId,
                                        'uniqueLayoutId'       => $uniqueLayoutId,
                                        'redirectUrl'          => $redirectUrl,
                                        'portletParams'        => array(  'relationModuleId' => $relationModuleId,
                                                                          'relationModelId'  => $relationModelId),
                                        'portletsAreRemovable' => !$isViewLocked));
            }
        }

        public function actionAddProductsInOpportunity($optId) {
            $data = Costbook::getAll();
            $view = new AddProductsView($data, $optId, NULL );
            $view1 =  new MyAddProductsView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
            echo $view1->render();
        }

        public function actionEstimateSummaryInOpportunity($optId) {
            $data = '';
            
            $view = new EstimateSummaryView($data, $optId, NULL );
            $view1 =  new MyEstimateSummaryView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
            echo $view1->render();
        }
        
        public function actionAddAndUpdateOpportunityProducts($ids, $addJsonObj, $updateJsonObj, $optId, $btnProperty, $finalGpm) {
            $btnProperty = 'Save&Update';
            if(!empty($addJsonObj) && !empty($updateJsonObj) && $btnProperty == 'Save&Update') {
                $addOpportunityProduct = $this->actionAddOpportunityProducts($ids, $addJsonObj, $optId, $btnProperty, $finalGpm);
                $updateOpportunityProduct = $this->actionUpdateOpportunityProducts($updateJsonObj, $optId, $btnProperty, $finalGpm);
                if($addOpportunityProduct == TRUE && $updateOpportunityProduct == TRUE){
                    echo json_encode($optId);
                }
            }

        }

        public function actionAddOpportunityProducts($ids, $addJsonObj, $optId, $btnProperty, $finalGpm=0, $finalAmnt=0) {
           if($ids != null && $addJsonObj != null) {
               $costbookDatas = json_decode($addJsonObj, TRUE);
               $totalDirectCost = 0;
               $totalMH = 0;
               $totalMHRevenue = 0;
               $suggestedPrice = 0;
                foreach($costbookDatas as $costbookData) {
                    $res = $costbookData['costBookId'];
                    $costbook = Costbook::getById($res);
                    $addProductRes = OpportunityProductUtils::addOpportunityProductsCalculation($costbook, $costbookData['add_Quantity'], $costbookData['add_Frequency'], $optId, $costbookData['add_Category']);
                }
                $opportunityProducts = OpportunityProduct::getAllByOpptId(intval($optId));
                foreach($opportunityProducts as $opportunityProduct) {
                     $totalDirectCost += $opportunityProduct->Total_Direct_Cost->value;
                     
                     // For the MH
                     $totalMH += $opportunityProduct->Total_MHR;   
                     
                     $suggestedPrice += $opportunityProduct->Final_Cost->value;                     
                }
                
               $currencies = Currency::getAll();
               
                if($finalAmnt == 0)
                    $finalAmnt = $totalDirectCost/(1-($finalGpm/100));
                else
                   $finalAmnt = $finalAmnt;
               
                // For the revenue
               if($suggestedPrice > 0 && $totalMH > 0 )
                    $totalMHRevenue = $suggestedPrice/$totalMH;
               else
                   $totalMHRevenue = 0; 
               $opportunity = Opportunity::getById($optId);
               $finalAmount = new CurrencyValue();
               $finalAmount->value = round($finalAmnt, 2);
               $finalAmount->currency = $currencies[0];
               $opportunity->finalAmount = $finalAmount;
               $opportunity->aggregateGPM = round($finalGpm, 2);
               $totalDirectCosts = new CurrencyValue();
               $totalDirectCosts->value = round($totalDirectCost, 2);
               $totalDirectCosts->currency = $currencies[0];
               $opportunity->totalDirectCosts = $totalDirectCosts;
               $opportunity->totalMHR = round($totalMH, 2);
               $revenueManHour = new CurrencyValue();
               $revenueManHour->value = round($totalMHRevenue, 2);
               $revenueManHour->currency = $currencies[0];
               $opportunity->revenueMHR = $revenueManHour;
               $suggestedPriceOpp = new CurrencyValue();
               $suggestedPriceOpp->value = round($suggestedPrice, 2);
               $suggestedPriceOpp->currency = $currencies[0];               
               $opportunity->suggestedPrice = $suggestedPriceOpp;                                        
               
               $opportunity->save();
               
               if($btnProperty != 'Save&Update'){
                   echo json_encode($optId);
               }else{
                   return TRUE;
               }
           }
        }
        
        public function actionUpdateOpportunityProducts($updateJsonObj, $optId, $btnProperty, $finalGpm=0, $finalAmnt=0) {
           $datas = json_decode($updateJsonObj, TRUE);
           $totalDirectCost = 0;
           $totalMH = 0;
           $totalMHRevenue = 0;
           $suggestedPrice = 0;
           if($datas != null) {
                $opportunity  = Opportunity::getById($optId);
                foreach($datas as $Data) {
                    $optpdct = OpportunityProduct::getById($Data['product_ids']);                    
                    if($opportunity->recordType->value == OpportunityProductUtils::RECURRINGFINAL){
                      if((!empty($Data['Quantity']) && $Data['Quantity'] > 0)  && (!empty($Data['Frequency']) && $Data['Frequency']> 0)){                        
                          $aupdateProductRes = OpportunityProductUtils::updateOpportunityProduct($optpdct,$Data['Quantity'],$Data['Frequency']);
                      } else{                        
                          $aupdateProductRes =  $optpdct->delete();
                      }
                    } else if($opportunity->recordType->value == OpportunityProductUtils::PROJECTFINAL) {
                       if(!empty($Data['Quantity']) && $Data['Quantity'] > 0 ){
                           $aupdateProductRes = OpportunityProductUtils::updateOpportunityProduct($optpdct,$Data['Quantity'],$Data['Frequency']);
                       } else {
                           $aupdateProductRes =  $optpdct->delete(); 
                       }  
                    }
                  
                }
                $opportunityProducts = OpportunityProduct::getAllByOpptId(intval($optId));
//                if(!empty($opportunityProducts)){
                    foreach($opportunityProducts as $opportunityProduct) {
                         $totalDirectCost += $opportunityProduct->Total_Direct_Cost->value;

                         // For the MH
                         $totalMH += $opportunityProduct->Total_MHR;   

                         $suggestedPrice += $opportunityProduct->Final_Cost->value;                     
                    }
//                }
//                $opportunityProducts = OpportunityProduct::getAllByOpptId(intval($optId));
//                if(!empty($opportunityProducts)){
//                    foreach($opportunityProducts as $opportunityProduct) {
//                        $opportunityProduct->Category_GPM = round($finalGpm, 2);
//                        $opportunityProduct->save();
//                        $finalCost = new CurrencyValue();
//                        
//                        $finalCost->value             =  round(($opportunityProduct->Total_Direct_Cost->value/(1-($opportunityProduct->Category_GPM/100))),2);            
//                        $opportunityProduct->Final_Cost = $finalCost;
//                        $opportunityProduct->save();
//                    }
//                }
                
                $currencies = Currency::getAll();
                if($finalAmnt == 0)
                    $finalAmnt = $totalDirectCost/(1-($finalGpm/100));
                else
                   $finalAmnt = $finalAmnt;
                   
                if($suggestedPrice > 0 && $totalMH > 0 )
                    $totalMHRevenue = $suggestedPrice/$totalMH;
                else
                   $totalMHRevenue = 0; 
                $finalAmount = new CurrencyValue();
                $finalAmount->value = round($finalAmnt, 2);
                $finalAmount->currency = $currencies[0];
                $opportunity->finalAmount = $finalAmount;
                $opportunity->aggregateGPM = round($finalGpm, 2);
                $totalDirectCosts = new CurrencyValue();
                $totalDirectCosts->value = round($totalDirectCost, 2);
                $totalDirectCosts->currency = $currencies[0];
                $opportunity->totalDirectCosts = $totalDirectCosts;
                $opportunity->totalMHR = round($totalMH, 2);
                $revenueManHour = new CurrencyValue();
                $revenueManHour->value = round($totalMHRevenue, 2);
                $revenueManHour->currency = $currencies[0];
                $opportunity->revenueMHR = $revenueManHour;
                $suggestedPriceOpp = new CurrencyValue();
                $suggestedPriceOpp->value = round($suggestedPrice, 2);
                $suggestedPriceOpp->currency = $currencies[0];               
                $opportunity->suggestedPrice = $suggestedPriceOpp;         
                $opportunity->save();

               if($btnProperty != 'Save&Update'){
                   echo json_encode($optId);
               }else{
                   return TRUE;
               }
           }
        }

        public function actionGetAddProductSearch($category, $costOfGoods, $addProductoptId, $pageOffset, $productName, $sortFor='code', $sortOrder='asc') {            
            $pageSize    = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                           'listPageSize', get_class($this->getModule()));
            $costbookList = OpportunityProduct::getAddProductSearchData($category, $costOfGoods, $productName, $pageOffset, $pageSize, $sortFor, $sortOrder);
            $resultArray = array();
            foreach ($costbookList as $key => $costbook) {
                if(OpportunityProductUtils::addProductFindDuplicate($costbook['Category'], $addProductoptId, $costbook['productcode']) == FALSE){
                    if($costbook['CostOfGoodsSold'] == Constant::ASSEMBLY){
                        $costbook = CostbookUtils::getAssemblyUnitDirectCost($costbook);
                    }
                    array_push($resultArray, $costbook);
                }
            }
            /*$items_per_page = $pageOffset * ($pageSize*2);
            $offset = 0;
            $output = array_slice($resultArray, $offset, $items_per_page);*/
            echo CJSON::encode($resultArray);
        }

        public function actionUnlink($id)
        {
            $relationModelClassName    = ArrayUtil::getArrayValue(GetUtil::getData(), 'relationModelClassName');
            $relationModelId           = ArrayUtil::getArrayValue(GetUtil::getData(), 'relationModelId');
            $relationModelRelationName = ArrayUtil::getArrayValue(GetUtil::getData(), 'relationModelRelationName');
            if ($relationModelClassName == null || $relationModelId == null || $relationModelRelationName == null)
            {
                throw new NotSupportedException();
            }
            $relationModel  = $relationModelClassName::GetById(intval($relationModelId));
            if ($relationModel->getRelationType($relationModelRelationName) != RedBeanModel::HAS_MANY &&
                       $relationModel->getRelationType($relationModelRelationName) != RedBeanModel::MANY_MANY)
            {
                throw new NotSupportedException();
            }
            $modelClassName = $relationModel->getRelationModelClassName($relationModelRelationName);
            $model          = $modelClassName::getById((int)$id);
            ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($model);
            ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($relationModel);
            $relationModel->$relationModelRelationName->remove($model);
            $saved          = $relationModel->save();
            if (!$saved)
            {
                throw new FailedToSaveModelException();
            }
        }
        
    public function actionchangePriceForOpportunity($optId) {
        $data = Costbook::getAll();
        $view = new changePriceView($data, $optId, NULL );
        $view1 =  new MyAddProductsView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $view1->render();
    }
    
    public function actionupdateOpportunityPrice($optId, $finalGpm, $finalAmnt) {    
        if($optId != null) 
        {
            $currencies  = Currency::getAll();              
            $opportunity = Opportunity::getById($optId);
            $opportunity->oldFinalAmount = $opportunity->finalAmount->value;
            $opportunity->oldaggregateGPM = $opportunity->aggregateGPM;            
            $finalAmount = new CurrencyValue();
            $finalAmount->value = round($finalAmnt, 2);
            $finalAmount->currency = $currencies[0];
            $opportunity->finalAmount = $finalAmount;
            $opportunity->aggregateGPM = round($finalGpm, 2);
            
//            $opportunityProducts = OpportunityProduct::getAllByOpptId(intval($optId));
//            if(!empty($opportunityProducts)){
//                foreach($opportunityProducts as $opportunityProduct) {                
//                    $opportunityProduct->Category_GPM = round($finalGpm, 2);
//                    $opportunityProduct->save();
//                    
//                    $finalCost = new CurrencyValue();
//                    $finalCost->value             =  round(($opportunityProduct->Total_Direct_Cost->value/(1-($opportunityProduct->Category_GPM/100))),2);
//                    $opportunityProduct->Final_Cost = $finalCost;
//                    $opportunityProduct->save();
//                }            
//            }
            
            if($opportunity->save()){
                echo json_encode($optId);
            }else{
                return TRUE;
            }
        }        
    }
}	
?>
