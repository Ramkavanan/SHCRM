<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class RoutesDefaultController extends ZurmoModuleController {
    /*     * public function filters()  {
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
        $route = new Route(false);
        $searchForm = new RoutesSearchForm($route);
        $dataProvider = $this->resolveSearchDataProvider(
                $searchForm, $pageSize, null, 'RoutesSearchView'
        );

        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view') {
            $mixedView = $this->makeListView(
                    $searchForm, $dataProvider
            );
            $view = new RoutesPageView($mixedView);
        } else {
            $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
            $view = new RoutesPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
        }
        echo $view->render();
    }

    public function actionEdit($id) {
        $isClone = 'edit';
        //get boject by id
        unset($_SESSION['agreementList']);
        $route = Route::getById(intval($id));
		//if tracking is available edit not allowed
        $isRouteTracking = RouteTracking::getTrackingByRouteId($id);
        if(!empty($isRouteTracking)){
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Route cannot be edited once tracking is started.'));
            $this->redirect(Yii::app()->createUrl('routes/default/details?id='.$id));
            Yii::app()->end(false);
        }
        //Security check
        ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($route);
        
        //create view and render
        $this->processEdit($route, $id, $isClone);
    }

    
    public function actionCopy($id,$type=''){
        $isClone = 'clone';
        $copyToRoute = new Route();
        $postVariableName = get_class($copyToRoute);
        $route = Route::getById((int) $id);
        if (!isset($_POST[$postVariableName])) {
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($route);
            ZurmoCopyModelUtil::copy($route, $copyToRoute);
            $this->processEdit($copyToRoute, $id, $isClone, $type);
        }
    }
    
    protected function processEdit(Route $route, $id, $isClone = null, $type=null, $redirectUrl = null) {
        $editAndDetailsView1 = $this->makeEditAndDetailsView($this->attemptToSaveModelFromPost($route), 'Edit');
        $editAndDetailsView = new RouteStep1View($id, $isClone, $type);
        $view = new RoutesPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }
    
    public function actionCreate($id=NULL) {
        $isClone = 'create';
        unset($_SESSION['agreementList']);
        unset($_SESSION['agreementNameList']);
        $view = new RouteStep1View($id, $isClone);
        $trackingView = new RoutesPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $trackingView->render();
    }

    /**
     * Check if form is posted. If form is posted attempt to save. If save is complete, confirm the current
     * user can still read the model.  If not, then redirect the user to the index action for the module.
     */
    protected function attemptToSaveModelFromPost($model, $createStep = 1, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false) {
        assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
        $savedSuccessfully = false;
        $modelToStringValue = null;
        $postVariableName = get_class($model);
        if (isset($_POST[$postVariableName])) {
            $postData = $_POST[$postVariableName];
            $controllerUtil = static::getZurmoControllerUtil();
            $model = $controllerUtil->saveModelFromPost($postData, $model, $savedSuccessfully, $modelToStringValue, $returnOnValidate);
        }
        if ($savedSuccessfully && $redirect) {
            if ($createStep = Route::STEP1) {
                $this->redirect(array($this->getId() . '/createStep2', 'id' => $model->id));
            }

            //$this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
        }
        return $model;
    }

    public function actionCreateStep2($id, $ClonedRouteId, $type) {
        $view = new RouteStep2View($id, $type, $ClonedRouteId);
        $routeView = new RoutesPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $routeView->render();
    }

    public function actionDetails($id) {
        $route = static::getModelAndCatchNotFoundAndDisplayError('Route', intval($id));
        $breadCrumbView = StickySearchUtil::resolveBreadCrumbViewForDetailsControllerAction($this, 'RoutesSearchView', $route);
        ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($route);
        AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($route), 'RoutesModule'), $route);
        $detailsAndRelationsView = $this->makeDetailsAndRelationsView($route, 'RoutesModule',
                                                                          'RoutesDetailsAndRelationsView',
                                                                          Yii::app()->request->getRequestUri(), $breadCrumbView);
        $view = new RoutesPageView(ZurmoDefaultViewUtil::
                                             makeStandardViewForCurrentUser($this, $detailsAndRelationsView));
        echo $view->render();
    }

    //Function for route delete
    public function actionDelete($id, $massDelete=null)
    {
        $route_agmt_prod_id_arr = array();
        $route_agmt_id_arr = array();
        $route = Route::GetById(intval($id));
        if(!empty ($route)){
            $route->delete();       //Delete a route
        }
        $routeAgmnts = RouteAgreement::getAgmtByRouteId(intval($id));
        if(!empty ($routeAgmnts)){
            foreach ($routeAgmnts as $routeAgmnt) {
                $route_agmt_id_arr[] = $routeAgmnt->id;
                $routeAgmnt->delete();      //Delete a route agreement for route
            }
        }
        $routeCategorys = RouteCategory::getCatByRouteId(intval($id));
        if(!empty ($routeCategorys)){
            foreach ($routeCategorys as $routeCategory) {
                $routeCategory->delete();       //Delete a route category for route
            }
        }
        $routeProds = RouteProducts::getRouteProductsByRouteId(intval($id));
        if(!empty ($routeProds)){
            foreach ($routeProds as $routeProd) {
                $route_agmt_prod_id_arr[$routeProd->agreement->id][] = $routeProd->id;
                $routeProd->delete();       //Delete a route products for route
            }
        }
        $RouteTrackings = RouteTracking::getTrackingByRouteId(intval($id));
        if(!empty ($RouteTrackings)){
            $RouteTrackingIds = array();
            foreach ($RouteTrackings as $RouteTracking) {
                $RouteTrackingIds[] = $RouteTracking->id;
                $RouteTrackingProds = RouteTrackingProducts::getTrackingProdByRouteTrackingId(intval($RouteTracking->id));
                if(!empty ($RouteTrackingProds)){
                    foreach ($RouteTrackingProds as $RouteTrackingProd) {
                        $RouteTrackingProd->delete();       //Delete a route tracking products for route
                    }
                }
                $RouteTracking->delete();       //Delete a route tracking for route
            }
        }
        else{
            $this->redirect(array($this->getId() . '/index'));
        }        
        
        // To delete in the agmt tracking tables
        foreach ($RouteTrackingIds as $value) {
            $this->deleteAgmtTracking($value, $route_agmt_prod_id_arr);
        }
        
        if($massDelete != 0){
            return 1;
        }else{
            $this->redirect(array($this->getId() . '/index'));
        }
    }
    
    //Function for route tracking delete 
    public function actionDeleteTrackingDetails($id){
        $route_agmt_prod_id_arr = array();
        $RouteTracking = RouteTracking::GetById(intval($id));
        $RouteTrackingProds = RouteTrackingProducts::getTrackingProdByRouteTrackingId(intval($id));
        if(!empty ($RouteTrackingProds)){
            foreach ($RouteTrackingProds as $RouteTrackingProd) {
                $route_agmt_prod_id_arr[$RouteTrackingProd->agreement->id][] = $RouteTrackingProd->agreementproduct->id;
                $RouteTrackingProd->delete();       //Delete a route tracking products for route
            }
        }
        // To delete in the agmt tracking tables
        $this->deleteAgmtTracking($id, $route_agmt_prod_id_arr);
        
        $RouteTracking->delete();       //Delete a route tracking for route
        $this->redirect(array($this->getId() . '/details?id='.$RouteTracking->route->id.''));
    }

    public function actionMassDelete() {
        $massDelete = 1;
        if(isset($_GET['selectedIds'])){
            $selectedIds = explode(',', $_GET['selectedIds']);
            foreach ($selectedIds as $value) {
                $results[] = $this->actionDelete($value, $massDelete);
            }
        }elseif (isset($_GET['selectAll'])) {
            
        }
        
        if(!empty($results)){
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'Routes are deleted Successfully.'));
            $this->redirect(Yii::app()->createUrl('routes/default'));
            Yii::app()->end(false);
        }
        
//        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
//                'massDeleteProgressPageSize');
//        $route = new Route(false);
//        $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
//        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
//                new RoutesSearchForm($route), $pageSize, Yii::app()->user->userModel->id, null, 'RoutesSearchView');
//        $selectedRecordCount = $this->getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
//        $route = $this->processMassDelete(
//                $pageSize, $activeAttributes, $selectedRecordCount, 'RoutesPageView', $route, RoutesModule::getModuleLabelByTypeAndLanguage('Plural'), $dataProvider
//        );
//        $massDeleteView = $this->makeMassDeleteView(
//                $route, $activeAttributes, $selectedRecordCount, RoutesModule::getModuleLabelByTypeAndLanguage('Plural')
//        );
//        $view = new RoutesPageView(ZurmoDefaultViewUtil::
//                makeStandardViewForCurrentUser($this, $massDeleteView));
//        echo $view->render();
    }

    public function actionExport() {
        $this->export('RouteEditAndDetailsView', 'Route');
    }

    public function actionModalList() {
        $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                $_GET['modalTransferInformation']['sourceIdFieldId'], $_GET['modalTransferInformation']['sourceNameFieldId'], $_GET['modalTransferInformation']['modalId']
        );
        echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider);
    }

    public function actionRouteTracking($id) {
        $routeTracking = '';
        $routeTracking = new RouteTrackingView($routeTracking, $id);
        $zurmoView = new RoutesPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $routeTracking));
        echo $zurmoView->render();
    }

    public function actionrouteTrackingDetailsView($id){
        $routeTracking = '';
        $routeTracking = new RouteTrackingDetailView($routeTracking, $id);
        $zurmoView = new RoutesPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $routeTracking));
        echo $zurmoView->render();
    }


    public function actionCreateStep3($id, $ClonedRouteId, $type) {
        $view = new RouteStep3View($id, $ClonedRouteId, $type);
        $routeView = new RoutesPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $routeView->render();
    }

    public function actionGetAddNewRouteAndCategories($routeInformation) {
//        echo "1";exit;
        $clonedRouteInfo = array();
        $routes = json_decode($routeInformation, TRUE);
        if(isset($routes['isEdit'][0]) && $routes['isEdit'][1] == 'edit'){
            $updateRoute = Route::getById($routes['isEdit'][0]);
            $updateRoute->name = $routes['routeName'];
            $updateRoute->crewname = $routes['crewName'];
            $updateRoute->save();
            if (isset($updateRoute->id) && $updateRoute->id > 0) {
                $clonedRouteInfo['newClonedRouteId'] = 0;
                $clonedRouteInfo['type'] = 'edit';
                $clonedRouteInfo['oldRouteId'] = $routes['isEdit'][0];
                $categoryList = explode(',', $routes['totalSelectedCategories']);
                $categoryExist = RouteCategory::getCatByRouteId($routes['isEdit'][0]);
                if(!empty ($categoryExist)){
                    foreach ($categoryExist as $routeCategory) {
                        $routeCategory->id;
                        $routeCategory->delete();
                    }
                foreach ($categoryList as $value) {
//                    if (in_array($value, $existedCategories)){
//                        continue;
//                    }
                    $updateRouteCategory = new RouteCategory();
                    $updateRouteCategory->route = Route::getById($updateRoute->id);
                    $category_details              = Category::getById($value);
                    // For the categroy based Agmts
                    $_SESSION['categoryList'][]    = $category_details->name;                    
                    $updateRouteCategory->category = $category_details;
                    $updateRouteCategory->save();
                }
            }
            echo json_encode($clonedRouteInfo);
            }
        }elseif(!empty($routes['isEdit'][0]) && $routes['isEdit'][1] == 'clone'){
            if(isset($routes['isCloneBack']))
            {
                $cloneEditArr = explode('_', $routes['isCloneBack']);
            }
            else
                $cloneEditArr[0] = 'clone';
            
            if($cloneEditArr[0] == 'edit')
            {
                $updateRoute = Route::getById($routes['isEdit'][0]);
                $updateRoute->name = $routes['routeName'];
                $updateRoute->crewname = $routes['crewName'];
                $updateRoute->save();
                if (isset($updateRoute->id) && $updateRoute->id > 0) {
                    $updatedRouteInfo['newClonedRouteId'] = $routes['isEdit'][0];
                    $updatedRouteInfo['type'] = 'clone';
                    $updatedRouteInfo['oldRouteId'] = $cloneEditArr[1];
                    $categoryList = explode(',', $routes['totalSelectedCategories']);
                    $categoryExist = RouteCategory::getCatByRouteId($routes['isEdit'][0]);
                    if(!empty ($categoryExist)){
                        foreach ($categoryExist as $routeCategory) {
                            $routeCategory->id;
                            $routeCategory->delete();
                        }
                    foreach ($categoryList as $value) {
                        $updateRouteCategory = new RouteCategory();
                        $updateRouteCategory->route = Route::getById($updateRoute->id);
                        $category_details              = Category::getById($value);
                        // For the categroy based Agmts
                        $_SESSION['categoryList'][]    = $category_details->name;                    
                        $updateRouteCategory->category = $category_details;
                        $updateRouteCategory->save();
                    }
                    
                    echo json_encode($updatedRouteInfo);
                }
                }
            }
            else
            {
                $newClonedRoute = new Route();            
                $newClonedRoute->name = $routes['routeName'];
                $newClonedRoute->crewname = $routes['crewName'];
                $newClonedRoute->save();
                if (isset($newClonedRoute->id) && $newClonedRoute->id > 0) {
                    $clonedRouteInfo['newClonedRouteId'] = $newClonedRoute->id;
                    $clonedRouteInfo['type'] = 'clone';
                    $clonedRouteInfo['oldRouteId'] = $routes['isEdit'][0];

                    $categoryList = explode(',', $routes['totalSelectedCategories']);

                    $categoryExist = RouteCategory::getCatByRouteId($routes['isEdit'][0]);

                    foreach ($categoryExist as $values) {
                        $existedCategories[] = $values->category->id;
                    }
                    foreach ($categoryList as $value) {
                        $cloneRouteCategory = new RouteCategory();
                        $cloneRouteCategory->route = Route::getById($newClonedRoute->id);
                        $cloneRouteCategory->category = Category::getById($value);
                        $cloneRouteCategory->save();
                    }
                }
                echo json_encode($clonedRouteInfo);
            }            

        }else{
            $newRoute = new Route();
            $newRoute->name = $routes['routeName'];
            $newRoute->crewname = $routes['crewName'];
            $newRoute->save();
            if (isset($newRoute->id) && $newRoute->id > 0) {
                $clonedRouteInfo['newClonedRouteId'] = 0;
                $clonedRouteInfo['type'] = 'create';
                $clonedRouteInfo['oldRouteId'] = $newRoute->id;
                $categoryList = explode(',', $routes['totalSelectedCategories']);
                foreach ($categoryList as $value) {
                    $newRouteCategory = new RouteCategory();
                    $newRouteCategory->route = Route::getById($newRoute->id);                    
                    $category_details           = Category::getById($value);
                    // For the categroy based Agmts
                    $_SESSION['categoryList'][] = $category_details->name;                    
                    $newRouteCategory->category = $category_details;
                    $newRouteCategory->save();
                }
            }            
            echo json_encode($clonedRouteInfo);
        }
    }
    
    public function actionGetAddNewRouteAgreement($routeAgreementInformation) {
        
        $routAgmntInfo = array();
        $routeAgreements = json_decode($routeAgreementInformation, TRUE);
        $routAgmntInfo['oldRouteId'] = $routeAgreements['routeId'];
        $routAgmntInfo['newClonedRouteId'] = $routeAgreements['newClonedRouteId'];
        $agreementList = explode(',', $routeAgreements['totalSelectedAgreement']);
        $agreementNameList = explode(',', $routeAgreements['totalSelectedAgreementName']);
        
        if(isset($agreementList)){
            $_SESSION['agreementList']     = $agreementList;
            $_SESSION['agreementNameList'] = $agreementNameList;
        }
        
        echo json_encode($routAgmntInfo);
    }

    public function actionTrackView($id) {
        $printData = '';
        $trackingView = new RouteTrackView($printData, $id);
        echo $trackingView->render();
    }

    public function actionGetActiveAgreement($pageOffset, $agmtName, $routeId) {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'listPageSize', get_class($this->getModule()));
        $items_per_page = $pageOffset * ($pageSize * 2);
        $routeCategories = RouteCategory::getCatByRouteId($routeId);
        
        foreach ($routeCategories as $key => $routeCategory) {
             $cat_arr[] = $routeCategory->category->name;
        }
                
        $agmt_arr = AgreementProduct::getAgmtIdByCategory($cat_arr);
        $agmt_ids_arr = array();
        foreach($agmt_arr as $agmt)
        {
           $agmt_ids_arr[] = $agmt['agreement_id'];
        }

        if(count($agmt_ids_arr))
            $agmt_ids_arr = $agmt_ids_arr;
        else
            $agmt_ids_arr = array('0');
        
        $searchData = Agreement::getAllRecurringActiveAgmt(1, $items_per_page, $agmtName, $agmt_ids_arr);
        foreach ($searchData as $key => $val) {
            $format_arr[$key]['account_name'] = $searchData[$key]->account->name;
        }
        echo CJSON::encode($searchData);
    } 
    
    public function actionCreateStep4($id, $ClonedRouteId, $type) {
        $routeAgmnt = RouteAgreement::getAgmtByRouteId($id);
        if(empty($routeAgmnt)){
            Yii::app()->user->setFlash('notification', Zurmo::t('ZurmoModule', 'No agreement added under this route.'));
            $this->redirect(array($this->getId() . '/details?id='.$id));
        }
        $view       = new RouteStep4View($id, $ClonedRouteId, $type);
        $routeView  = new RoutesPageView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $routeView->render();
    }
    
    public function actionAddNewRouteAgreements($routeInformation){
        $existedAgreements = array();
        $routAgmntInfo = array();
        $routes = json_decode($routeInformation, TRUE);        
        if (isset($routes['routeId']) && $routes['routeId'] > 0 ){
            $routAgmntInfo['oldRouteId'] = $routes['routeId'];
            $routAgmntInfo['newClonedRouteId'] = $routes['newClonedRouteId'];
            $agmtExist = RouteAgreement::getAgmtByRouteId($routes['routeId']);
            if($routes['newClonedRouteId'] == 'null' || $routes['newClonedRouteId'] == 0){
                if(!empty ($agmtExist) && isset($routes['isEdit'])){
                    foreach ($agmtExist as $routeAgreement) {
                        $routeAgreement->id;
                        $routeAgreement->delete();
                    }
                }
            }
            if($routes['newClonedRouteId'] == 'null' || $routes['newClonedRouteId'] > 0){
                $agmtExist = RouteAgreement::getAgmtByRouteId($routes['newClonedRouteId']);
                if(!empty ($agmtExist)){
                    foreach ($agmtExist as $routeAgreement) {
                        $routeAgreement->id;
                        $routeAgreement->delete();
                    }
                }
            }
            foreach ($routes['agmt_ids'] as $agmt_value) {
//                if (in_array($agmt_value, $existedAgreements)){
//                    continue;
//                }
                $newRouteAgreement              = new RouteAgreement();
                if($routes['newClonedRouteId'] == 'null' || $routes['newClonedRouteId'] == 0){
                    $newRouteAgreement->route       = Route::getById($routes['routeId']);
                }else{
                    $newRouteAgreement->route       = Route::getById($routes['newClonedRouteId']);
                }
                $newRouteAgreement->agreement   = Agreement::getById($agmt_value);
                $newRouteAgreement->save();
            }
        }
        unset($_SESSION['agreementList']);
        echo json_encode($routAgmntInfo);
    }
     
    public function actionAddRouteAgreementProducts($routeInformation){
        $routAgmntInfo = array();
        $routes = json_decode($routeInformation, TRUE);
        $routAgmntInfo['oldRouteId'] = $routes['routeId'];
        $routAgmntInfo['newClonedRouteId'] = $routes['newClonedRouteId'];

        if (isset($routes['routeId']) && $routes['routeId'] > 0 ){ 
            $productExist = RouteProducts::getRouteProductsByRouteId($routes['routeId']);
            if(!empty ($productExist) && $routes['isEdit'] == 'edit'){
                foreach ($productExist as $routeProduct) {
                    $routeProduct->id;
                    $routeProduct->delete();
                }
            }
            
            foreach ($routes['selected_agmt_prods'] as $agmt_value) {                
                $agmt_detail_arr                        = explode('~',$agmt_value); 
                $newRouteProduct                        = new RouteProducts();
                if($routes['newClonedRouteId'] == 0){
                    $newRouteProduct->route                 = Route::getById($routes['routeId']);
                }else{
                    $newRouteProduct->route                 = Route::getById($routes['newClonedRouteId']);
                }
                $newRouteProduct->agreement             = Agreement::getById($agmt_detail_arr['1']);
                $newRouteProduct->agreementproduct      = AgreementProduct::getById($agmt_detail_arr['0']);
                $newRouteProduct->save();
            }
        }
        echo json_encode($routAgmntInfo);
     }
     
     public function actionAddRouteTrackingProducts($routeTrackingInfo){
         $routeTrackingProdDatas = json_decode($routeTrackingInfo, TRUE);
         foreach ($routeTrackingProdDatas as $key => $routeTrackingProdData) {
             RoutesUtils::SaveNewRouteTracking($routeTrackingProdData);
         }
         echo $routeTrackingProdData['addRouteTrackingDetails']['route_id'];
     }
     
   
//     public function deleteAgmtTracking($route_tracking_id, $agmt_prod_ids)
//    {
//        $agmt_track_details_arr = array();
//        $agreementTracking = AgreementTracking::getAgmtTrackingByRouteTrackingId($route_tracking_id);
//        foreach($agreementTracking as $agmt_track_key => $agmt_track_arr)
//        {
//            $agmt_track_details_arr[$agmt_track_arr->agreement->id] = $agmt_track_arr->id;
//        }        
//        foreach($agmt_track_details_arr as $agmt_id=>$agmt_track_id)
//        {
//            // Adding New Agreement prods
//            $agt = Agreement::getById(intval($agmt_id));
//            // For updating the already added agmt prods
//            $prev_prod_quantity_consumed    = 0;
//            $prev_mhr_consumed              = 0;
//            $prev_equipment_consumed        = 0;
//            $prev_material_consumed         = 0;
//            $addedTrackingProducts = AgreementTrackingProducts::getAgmtTrackingProductsByAgmtTrackingId($agmt_track_id);
//
//            // Updating the units consumed in agmt prods
//            foreach ($addedTrackingProducts as $trackingProduct) {                    
//                $agmt_prod = AgreementProduct::getById(intval($trackingProduct->agreementProduct->id));
//                if (strpos($agmt_prod->Product_Code, 'L') !== false) {
//                    $prev_mhr_consumed      += $trackingProduct->consumed_unit;
//                }                    
//                if (strpos($agmt_prod->Product_Code, 'E') !== false) {
//                    $prev_equipment_consumed    += $trackingProduct->consumed_unit;
//                }                    
//                if (strpos($agmt_prod->Product_Code, 'M') !== false) {
//                    $prev_material_consumed     += $trackingProduct->consumed_unit;
//                }
//
//                $agmt_prod_consumed_units_ =  round($agmt_prod->Consumed_Units,4)-round($trackingProduct->consumed_unit,4);
//                $agmt_prod->Consumed_Units  = round($agmt_prod_consumed_units_,4);
//                if(!$agmt_prod->save()){
//                    throw new FailedToSaveModelException();
//                    die;
//                }
//            }
//            $agmntTrackingObj = AgreementTracking::getById($agmt_track_id);
//            $agmntTrackingObj->delete();   //Agreement tracking delete
//        
//            // To update the calculations in the agreement
//            $agt->Used_MHR                  = round($agt->Used_MHR-$prev_mhr_consumed,2);
//            $agt->Total_Available_MHR       = round($agt->Total_Available_MHR+$prev_mhr_consumed,2);
//            $agt->Used_Material             = round($agt->Used_Material-$prev_material_consumed,2);
//            $agt->Available_Material        = round($agt->Available_Material+$prev_material_consumed,2);
//            $agt->Used_Equipment            = round($agt->Used_Equipment-$prev_equipment_consumed,2);
//            $agt->Available_Equipment       = round($agt->Available_Equipment+$prev_equipment_consumed,2);
//            $agt->Year_to_Date_MHR          = round($agt->Used_MHR,2);
//
//            if($agt->Total_MHR > 0)
//                $agt->MHR_Used_Percentage   = round(($agt->Used_MHR/$agt->Total_MHR)*100, 2);
//
//            $agt->Material_Year_To_Date     = round($agt->Used_Material,2);
//
//            if($agt->Total_Material > 0)
//                $agt->Material_Used_Percentage  = round(($agt->Used_Material/$agt->Total_Material)*100, 2);
//
//            $agt->Equipment_Year_To_Date    = round($agt->Used_Equipment,2);
//
//            if($agt->Total_Equipment > 0)
//                $agt->Equipment_Used_Percentage = round(($agt->Used_Equipment/$agt->Total_Equipment)*100, 2);
//
//            if(!$agt->save()){
//                throw new FailedToSaveModelException();
//                die;
//            } 
//        }
//    }
    
    public function deleteAgmtTracking($route_tracking_id, $agmt_prod_ids)
    {
        $agmt_track_details_arr = array();
        $agreementTracking = AgreementTracking::getAgmtTrackingByRouteTrackingId($route_tracking_id);
        foreach($agreementTracking as $agmt_track_key => $agmt_track_arr)
        {
            $agmt_track_details_arr[$agmt_track_arr->agreement->id] = $agmt_track_arr->id;
        }        
        foreach($agmt_track_details_arr as $agmt_id1=>$agmt_track_id)
        {
                $agt1 = Agreement::getById(intval($agmt_id1));
                $opportunityId =$agt1->opportunity->id;
                   $opportunity = Opportunity::getById($opportunityId);

                   $opptProducts = OpportunityProduct::getAllByOpptId(intval($opportunityId));
                   $opptPdctMap;
                   $totalDirectCost1=$totalfinalamount=0;
                   if(count($opptProducts) > 0) {
                       foreach($opptProducts as $row) {
                           $opptPdctMap[$row->Category][] = $row;
                       }

                       foreach ($opptPdctMap as $key1 => $optpdctArray1)  {                
                          foreach ($optpdctArray1 as $optKey1 => $optpdt1){
                             $totalDirectCost1 += $optpdt1->Total_Direct_Cost->value;                                   
                           }
                      }

                      foreach ($opptPdctMap as $key => $optpdctArray)  {                 
                           foreach ($optpdctArray as $optKey => $optpdt){  
                               $totalfinalamount +=	$optpdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)) ;
                           }
                      }
                   }
        }
        $totalDirectCost=0;
        
        foreach($agmt_track_details_arr as $agmt_id=>$agmt_track_id)
        {
            // Adding New Agreement prods
            $agt = Agreement::getById(intval($agmt_id));
            // For updating the already added agmt prods
            $prev_prod_quantity_consumed    = 0;
            $prev_mhr_consumed              = 0;
            $prev_equipment_consumed        = 0;
            $prev_material_consumed         = 0;
            $addedTrackingProducts = AgreementTrackingProducts::getAgmtTrackingProductsByAgmtTrackingId($agmt_track_id);

            // Updating the units consumed in agmt prods
            foreach ($addedTrackingProducts as $trackingProduct) {                    
                $agmt_prod = AgreementProduct::getById(intval($trackingProduct->agreementProduct->id));
                if (strpos($agmt_prod->Product_Code, 'L') !== false) {
                    $prev_mhr_consumed      += $trackingProduct->consumed_unit;
                    $costcatalog=  Costbook::getByProductCode($agmt_prod->Product_Code);
                    if(!empty($costcatalog)){
                        $burdenCost = $costcatalog[0]->burdenCost;
                        $laborcost = $costcatalog[0]->laborCost;
                    }

                    $totalDirectCost    +=  ($burdenCost + $laborcost)* $trackingProduct->consumed_unit;
                }                    
                if (strpos($agmt_prod->Product_Code, 'E') !== false) {
                    $prev_equipment_consumed    += $trackingProduct->consumed_unit;
                    $costcatalog=  Costbook::getByProductCode($agmt_prod->Product_Code);
                        
                    if(!empty($costcatalog)){
                        $costperunit = $costcatalog[0]->costperunit;
                    }

                    $totalDirectCost    += $costperunit * $trackingProduct->consumed_unit;
                }                    
                if (strpos($agmt_prod->Product_Code, 'M') !== false) {
                    $prev_material_consumed     += $trackingProduct->consumed_unit;
                    $costcatalog=  Costbook::getByProductCode($agmt_prod->Product_Code);
                        
                    if(!empty($costcatalog)){
                        $costperunit = $costcatalog[0]->costperunit;
                    }

                    $totalDirectCost    += $trackingProduct->consumed_unit * $costperunit;
                }

                $agmt_prod_consumed_units_ =  round($agmt_prod->Consumed_Units,4)-round($trackingProduct->consumed_unit,4);
                $agmt_prod->Consumed_Units  = round($agmt_prod_consumed_units_,4);
                if(!$agmt_prod->save()){
                    throw new FailedToSaveModelException();
                    die;
                }
            }
            
           // $newCurrent_GPM=(($totalfinalamount-$totalDirectCost)/$totalfinalamount)*100;
            //$newCurrent_GPMold =$agt->newCurrent_GPM;
            //$agt->newCurrent_GPM  = round($newCurrent_GPM,2);
            //$agt->save();
            
            //For the Current GPM calculation
            $AgmtProductGpm =  AgreementProduct::getAgreementProdByAgreementId($agmt_id);
            $totalDirectCostData = 0;
            foreach ($AgmtProductGpm as $AgmtProductsGpm) {                
                if(isset($AgmtProductsGpm) && (!empty($AgmtProductsGpm->Consumed_Units)))
                    {
                        $agmtprodData = $AgmtProductsGpm;
                        
                        $getCostbookData     = Costbook::getById($agmtprodData->costbook->id);
                        
                        if (strpos($getCostbookData->productcode, 'L') !== false) {                            
                            $costcatalogData =  Costbook::getByProductCode($getCostbookData->productcode);
                        
                            if(!empty($costcatalogData)){
                                $burdenCostData = $costcatalogData[0]->burdenCost;
                                $laborcostData = $costcatalogData[0]->laborCost;
                            }
 
                            $totalDirectCostData    +=  ($burdenCostData + $laborcostData)* $AgmtProductsGpm->Consumed_Units;
                        }                    
                        if (strpos($getCostbookData->productcode, 'E') !== false) {                                                       
                            $costcatalogData=  Costbook::getByProductCode($getCostbookData->productcode);
                        
                            if(!empty($costcatalogData)){
                                $costperunitData = $costcatalogData[0]->costperunit;
                            }

                            $totalDirectCostData    += $costperunitData * $AgmtProductsGpm->Consumed_Units;
                        }                    
                        if (strpos($getCostbookData->productcode, 'M') !== false) {
                            $costcatalogData =  Costbook::getByProductCode($getCostbookData->productcode);
                        
                            if(!empty($costcatalog)){
                                $costperunitData = $costcatalogData[0]->costperunit;
                            }

                            $totalDirectCostData    += $AgmtProductsGpm->Consumed_Units * $costperunitData;
                        }
                    }
            }
            
           $newCurrent_GPM=(($totalfinalamount-$totalDirectCostData)/$totalfinalamount)*100;
           $agt->newCurrent_GPM  =round($newCurrent_GPM,2);
           $agt->save();
            
            $agmntTrackingObj = AgreementTracking::getById($agmt_track_id);
            $agmntTrackingObj->delete();   //Agreement tracking delete
        
            // To update the calculations in the agreement
            $agt->Used_MHR                  = round($agt->Used_MHR-$prev_mhr_consumed,2);
            $agt->Total_Available_MHR       = round($agt->Total_Available_MHR+$prev_mhr_consumed,2);
            $agt->Used_Material             = round($agt->Used_Material-$prev_material_consumed,2);
            $agt->Available_Material        = round($agt->Available_Material+$prev_material_consumed,2);
            $agt->Used_Equipment            = round($agt->Used_Equipment-$prev_equipment_consumed,2);
            $agt->Available_Equipment       = round($agt->Available_Equipment+$prev_equipment_consumed,2);
            $agt->Year_to_Date_MHR          = round($agt->Used_MHR,2);

            if($agt->Total_MHR > 0)
                $agt->MHR_Used_Percentage   = round(($agt->Used_MHR/$agt->Total_MHR)*100, 2);

            $agt->Material_Year_To_Date     = round($agt->Used_Material,2);

            if($agt->Total_Material > 0)
                $agt->Material_Used_Percentage  = round(($agt->Used_Material/$agt->Total_Material)*100, 2);

            $agt->Equipment_Year_To_Date    = round($agt->Used_Equipment,2);

            if($agt->Total_Equipment > 0)
                $agt->Equipment_Used_Percentage = round(($agt->Used_Equipment/$agt->Total_Equipment)*100, 2);

            if(!$agt->save()){
                throw new FailedToSaveModelException();
                die;
            } 
        }
    }
    
    public function actionRouteProduct($id) {
        // For the route product page
    }
}
?>
