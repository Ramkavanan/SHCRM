<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AgreementProductsDefaultController extends ZurmoModuleController {
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
        $agmntPrdct                         = new AgreementProduct(false);
        $searchForm                     = new AgreementProductsSearchForm($agmntPrdct);
        $dataProvider = $this->resolveSearchDataProvider(
              $searchForm,
              $pageSize,
              null,
              'AgreementProductsSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view')     {
                $mixedView = $this->makeListView(
                    $searchForm,
                    $dataProvider
                );
                $view = new AgreementProductsPageView($mixedView);
            }
            else
            {
                $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
                $view = new AgreementProductsPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
            }
            echo $view->render();
        }
        
        public function actionCreate() {
            $editAndDetailsView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost(new AgreementProduct()), 'Edit');
            $view = new AgreementProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $editAndDetailsView));
            echo $view->render();
        }
        
        public function actionDetails($id) {
            $agmntPrdct = static::getModelAndCatchNotFoundAndDisplayError('AgreementProduct', intval($id));
            $breadCrumbView          = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'AgreementProductsSearchView', $agmntPrdct);
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($agmntPrdct);
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($agmntPrdct), 'AgreementProductsModule'), $agmntPrdct);
            $titleBarAndEditView = $this->makeEditAndDetailsView($agmntPrdct, 'Details');
            $view = new AgreementProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
        }
        
        public function actionMassDelete()  {
            $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                            'massDeleteProgressPageSize');
            $agmntPrdct = new AgreementProduct(false);

            $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
            $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AgreementProductsSearchForm($agmntPrdct),
                $pageSize,
                Yii::app()->user->userModel->id,
                null,
                'AgreementProductsSearchView');
            $selectedRecordCount = $this->getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
            $agmntPrdct = $this->processMassDelete(
                $pageSize,
                $activeAttributes,
                $selectedRecordCount,
                'AgreementProductsPageView',
                $agmntPrdct,
                AgreementProductsModule::getModuleLabelByTypeAndLanguage('Plural'),
                $dataProvider
            );
            $massDeleteView = $this->makeMassDeleteView(
                $agmntPrdct,
                $activeAttributes,
                $selectedRecordCount,
                AgreementProductsModule::getModuleLabelByTypeAndLanguage('Plural')
            );
            $view = new AgreementProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $massDeleteView));
            echo $view->render();
        }
        
        public function actionExport(){
            $this->export('AgreementProductsSearchView');
        }

	public function actionModalList() {
            $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                                            $_GET['modalTransferInformation']['sourceIdFieldId'],
                                            $_GET['modalTransferInformation']['sourceNameFieldId']
            );
            echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider,
                                                Yii::t('Default', 'AgreementProductModuleSingularLabel Search',
                                                LabelUtil::getTranslationParamsForAllModules()));
        }
	
	//Implementation for creating Agreement product from agreement
	public function actionCreateFromRelation($relationAttributeName, $relationModelId, $relationModuleId, $redirectUrl)     {
            $agmntPrdct             = $this->resolveNewModelByRelationInformation( new AgreementProduct(),
                                                                                $relationAttributeName,
                                                                                (int)$relationModelId,
                                                                                $relationModuleId);
            $this->actionCreateByModel($agmntPrdct, $redirectUrl);
        }

	protected function actionCreateByModel(AgreementProduct $agmntPrdct, $redirectUrl = null)     {
            $titleBarAndEditView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost($agmntPrdct, $redirectUrl), 'Edit');
            $view = new AgreementProductsPageView(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
        }

	public function actionEdit($id, $redirectUrl = null)   {
            $agmntPrdct = AgreementProduct::getById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($agmntPrdct);
            $this->processEdit($agmntPrdct, $redirectUrl);
        }
	
	protected function processEdit(AgreementProduct $agmntPrdct, $redirectUrl = null)   {
            $view    = new AgreementProductsPageView(ZurmoDefaultViewUtil::
                            makeStandardViewForCurrentUser($this,
                            $this->makeEditAndDetailsView(
                                $this->attemptToSaveModelFromPost($agmntPrdct, $redirectUrl), 'Edit')));
            echo $view->render();
        }

	public function actionDelete($id)     {
            $agmntPrdct = AgreementProduct::GetById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($agmntPrdct);
            $agmntPrdct->delete();
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
            
            $modalListLinkProvider = new AgreementProductTemplateSelectFromRelatedListModalListLinkProvider(
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
        
        
        public function actionAddAgreementRelation($relationModuleId, $portletId, $uniqueLayoutId,
                            $id, $relationModelId, $relationAttributeName, $redirect) {
            $agmntProduct = AgreementProduct::GetById(intval($id));
            $agmnt = Agreement::GetById(intval($relationModelId));
            if($agmntProduct ->agreement  != $agmnt ) {
                $agmntProduct ->agreement  = $agmnt;
                $agmntProduct->save();
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
        
        public function actionAddProductsInAgreement($agmntId) {
            $costBookDatas = Costbook::getAll();
            $agmntView = new AddAgreementProductView($costBookDatas, $agmntId, NULL );
            $view =  new MyCustomView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $agmntView));
            echo $view->render();
        }
        
        public function actionGetCostBookProducts($category, $costOfGoods, $addProductoptId, $pageOffset, $productName, $sortFor='code', $sortOrder='asc') {            
            $pageSize    = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                           'listPageSize', get_class($this->getModule()));
            $costbookList = AgreementProduct::getAddProductSearchData($category, $costOfGoods, $productName, $pageOffset, $pageSize, $sortFor, $sortOrder);
            $resultArray = array();
            foreach ($costbookList as $key => $costbook) {
                if(AgreementProductUtils::addProductFindDuplicate($costbook['Category'], $addProductoptId, $costbook['productcode']) == FALSE){
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
        
        public function actionAddAndUpdateAgreementProducts($ids, $addJsonObj, $updateJsonObj, $agmntId, $btnProperty, $finalGpm) {
            $btnProperty = 'Save&Update';
            if(!empty($addJsonObj) && !empty($updateJsonObj) && $btnProperty == 'Save&Update') {
                $addAgreementProduct = $this->actionAddAgreementProducts($ids, $addJsonObj, $agmntId, $btnProperty, $finalGpm);
                $updateAgreementProduct = $this->actionUpdateAgreementProducts($updateJsonObj, $agmntId, $btnProperty, $finalGpm);
                if($addAgreementProduct == TRUE && $updateAgreementProduct == TRUE){
                    echo json_encode($agmntId);
                }
            }

        }

        public function actionAddAgreementProducts($ids, $addJsonObj, $agmntId, $btnProperty, $finalGpm=0, $finalAmnt=0) {
           if($ids != null && $addJsonObj != null) {
               $costbookDatas = json_decode($addJsonObj, TRUE);
               $totalDirectCost = 0;
               $totalMH = 0;
               $totalMHRevenue = 0;
               $suggestedPrice = 0;
                foreach($costbookDatas as $costbookData) {
                    $res = $costbookData['costBookId'];
                    $costbook = Costbook::getById($res);
                    $addProductRes = AgreementProductUtils::addAgreementProductsCalculation($costbook, $costbookData['add_Quantity'], $costbookData['add_Frequency'], $agmntId, $costbookData['add_Category']);
                }
                $agreementProducts = AgreementProduct::getAllProdByAgmntId(intval($agmntId));
                foreach($agreementProducts as $agreementProduct) {
                     $totalDirectCost += $agreementProduct->Total_Direct_Cost->value;
                     
                     // For the MH
                     $totalMH += $agreementProduct->Total_MHR;   
                     
                     $suggestedPrice += $agreementProduct->Suggested_Cost->value;                     
                }
                
               $currencies = Currency::getAll();
               
               if($finalAmnt == 0)
                   $finalAmnt = $totalDirectCost / (1 - ($finalGpm / 100));
               else
                   $finalAmnt = $finalAmnt;
               
               // For the revenue
               if($suggestedPrice > 0 && $totalMH > 0 )
                    $totalMHRevenue = $suggestedPrice/$totalMH;
               else
                   $totalMHRevenue = 0; 
               $agreement = Agreement::getById($agmntId);
               $finalAmount = new CurrencyValue();
               $finalAmount->value = round($finalAmnt, 2);
               $finalAmount->currency = $currencies[0];
               $agreement->Current_Annual_Amount = $finalAmount;
               $agreement->Project_Agreement_Amount = $finalAmount;
               $agreement->Current_GPM = round($finalGpm, 2);
               //$agreement->newCurrent_GPM = round($finalGpm, 2);
               $totalDirectCosts = new CurrencyValue();
               $totalDirectCosts->value = round($totalDirectCost, 2);
               $totalDirectCosts->currency = $currencies[0];
               $agreement->Total_Direct_Costs = $totalDirectCosts;
               $agreement->Total_MHR = round($totalMH, 2);
               $revenueManHour = new CurrencyValue();
               $revenueManHour->value = round($totalMHRevenue, 2);
               $revenueManHour->currency = $currencies[0];
               $agreement->Rev_MH = $revenueManHour;
               $suggestedPriceOpp = new CurrencyValue();
               $suggestedPriceOpp->value = round($suggestedPrice, 2);
               $suggestedPriceOpp->currency = $currencies[0];               
               $agreement->suggestedPrice = $suggestedPriceOpp;                                        
               
               $agreement->save();
               
               if($btnProperty != 'Save&Update'){
                   echo json_encode($agmntId);
               }else{
                   return TRUE;
               }
           }
        }
        
        public function actionUpdateAgreementProducts($updateJsonObj, $agmntId, $btnProperty, $finalGpm=0, $finalAmnt=0) {
           $datas = json_decode($updateJsonObj, TRUE);
           $totalDirectCost = 0;
           $totalMH = 0;
           $totalMHRevenue = 0;
           $suggestedPrice = 0;
           if($datas != null) {
                $agreement  = Agreement::getById($agmntId);
                foreach($datas as $Data) {
                    $agmntpdct = AgreementProduct::getById($Data['product_ids']);                    
                    if($agreement->RecordType == Constant::RECURRINGAGREEMENT){
                      if((!empty($Data['Quantity']) && $Data['Quantity'] > 0)  && (!empty($Data['Frequency']) && $Data['Frequency']> 0)){                        
                          $aupdateProductRes = AgreementProductUtils::updateAgreementProductCalculation($agmntpdct,$Data['Quantity'],$Data['Frequency'], $agreement);
                      } else{                        
                          $aupdateProductRes =  $agmntpdct->delete();
                      }
                    } else if($agreement->RecordType == Constant::PROJECTAGREEMENT) {
                       if(!empty($Data['Quantity']) && $Data['Quantity'] > 0 ){
                           $aupdateProductRes = AgreementProductUtils::updateAgreementProductCalculation($agmntpdct,$Data['Quantity'],$Data['Frequency'], $agreement);
                       } else {
                           $aupdateProductRes =  $agmntpdct->delete(); 
                       }  
                    }
                    $totalDirectCost += $agmntpdct->Total_Direct_Cost->value;
                  
                    // For the MH
                    $totalMH += $agmntpdct->Total_MHR;   
                     
                    $suggestedPrice += $agmntpdct->Suggested_Cost->value;                     
                }
                
                $currencies = Currency::getAll();
                
                if($finalAmnt == 0)
                    $finalAmnt = $totalDirectCost / (1 - ($finalGpm / 100));
                else 
                    $finalAmnt = $finalAmnt;
                
                if($suggestedPrice > 0 && $totalMH > 0 )
                    $totalMHRevenue = $suggestedPrice/$totalMH;
                else
                   $totalMHRevenue = 0; 
                $finalAmount = new CurrencyValue();
                $finalAmount->value = round($finalAmnt, 2);
                $finalAmount->currency = $currencies[0];
                
                $agreement->Current_Annual_Amount = $finalAmount;
                $agreement->Project_Agreement_Amount = $finalAmount;
                
                $agreement->Current_GPM = round($finalGpm, 2);
                $totalDirectCosts = new CurrencyValue();
                $totalDirectCosts->value = round($totalDirectCost, 2);
                $totalDirectCosts->currency = $currencies[0];
                $agreement->Total_Direct_Costs = $totalDirectCosts;
                $agreement->Total_MHR = round($totalMH, 2);
                $agreement->Total_Available_MHR = round($totalMH, 2);
                $revenueManHour = new CurrencyValue();
                $revenueManHour->value = round($totalMHRevenue, 2);
                $revenueManHour->currency = $currencies[0];
                $agreement->Rev_MH = $revenueManHour;
                $suggestedPriceOpp = new CurrencyValue();
                $suggestedPriceOpp->value = round($suggestedPrice, 2);
                $suggestedPriceOpp->currency = $currencies[0];               
                $agreement->suggestedPrice = $suggestedPriceOpp;         
                $agreement->save();

               if($btnProperty != 'Save&Update'){
                   echo json_encode($agmntId);
               }else{
                   return TRUE;
               }
           }
        }
        
}	
?>
