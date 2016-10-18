<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingDefaultController extends ZurmoModuleController {

    public function actionList() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'listPageSize', get_class($this->getModule()));
        $agreementTracking = new AgreementTracking(false);
        $searchForm = new AgreementTrackingSearchForm($agreementTracking);
        $dataProvider = $this->resolveSearchDataProvider(
                $searchForm, $pageSize, null, 'AgreementTrackingSearchView'
        );
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'list-view') {
            $mixedView = $this->makeListView(
                    $searchForm, $dataProvider
            );
            $view = new AgreementTrackingPageView($mixedView);
        } else {
            $mixedView = $this->makeActionBarSearchAndListView($searchForm, $dataProvider);
            $view = new AgreementTrackingPageView(ZurmoDefaultViewUtil::
                    makeStandardViewForCurrentUser($this, $mixedView));
        }
        echo $view->render();
    }

    public function actionEdit($id) {
//        $agreementTracking = AgreementTracking::getById(intval($id));
////        //Security check
//        ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($agreementTracking);
//
//        //create view and render
//        $editAndDetailsView = $this->makeEditAndDetailsView(
//                $this->attemptToSaveModelFromPost($agreementTracking), 'Edit');
//        $view1 = new AgreementTrackingPageView(ZurmoDefaultViewUtil::
//                makeStandardViewForCurrentUser($this, $editAndDetailsView));
        
        $view = new AgreementTrackingEditView($id);
        $trackingView = new AgreementTrackingCustomView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $trackingView->render();
        //echo $view1->render();
    }

    public function actionCreate($agreementId) {
        $agreement = Agreement::getById($agreementId);
        $editAndDetailsView = $this->makeEditAndDetailsView(
                $this->attemptToSaveModelFromPost(new AgreementTracking()), 'Edit');
        $view = new AgreementTrackingPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $editAndDetailsView));
        echo $view->render();
    }

    public function actionDetails($id) {
        $agreementTracking = static::getModelAndCatchNotFoundAndDisplayError('AgreementTracking', intval($id));
        ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($agreementTracking);
        AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($agreementTracking), 'AgreementTrackingModule'), $agreementTracking);
        $titleBarAndEditView = $this->makeEditAndDetailsView($agreementTracking, 'Details');
        $view = new AgreementTrackingPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $titleBarAndEditView));
        echo $view->render();
    }

    public function actionMassDelete() {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'massDeleteProgressPageSize');
        $agreementTracking = new AgreementTracking(false);

        $activeAttributes = $this->resolveActiveAttributesFromMassDeletePost();
        $dataProvider = $this->getDataProviderByResolvingSelectAllFromGet(
                new AgreementTrackingSearchForm($agreementTracking), $pageSize, Yii::app()->user->userModel->id, null, 'AgreementTrackingSearchView');
        $selectedRecordCount = $this->getSelectedRecordCountByResolvingSelectAllFromGet($dataProvider);
        $agreementTracking = $this->processMassDelete(
                $pageSize, $activeAttributes, $selectedRecordCount, 'AgreementTrackingPageView', $agreementTracking, AgreementTrackingModule::getModuleLabelByTypeAndLanguage('Plural'), $dataProvider
        );
        $massDeleteView = $this->makeMassDeleteView(
                $agreementTracking, $activeAttributes, $selectedRecordCount, AgreementTrackingModule::getModuleLabelByTypeAndLanguage('Plural')
        );
        $view = new AgreementTrackingPageView(ZurmoDefaultViewUtil::
                makeStandardViewForCurrentUser($this, $massDeleteView));
        echo $view->render();
    }

    public function actionExport() {
        $this->export('AgreementTrackingEditAndDetailsView', 'AgreementTracking');
    }

    public function actionModalList() {
        $modalListLinkProvider = new SelectFromRelatedEditModalListLinkProvider(
                $_GET['modalTransferInformation']['sourceIdFieldId'], $_GET['modalTransferInformation']['sourceNameFieldId'], $_GET['modalTransferInformation']['modalId']
        );
        echo ModalSearchListControllerUtil::setAjaxModeAndRenderModalSearchList($this, $modalListLinkProvider);
    }

    public function actionAddNewTracking($agreementId) {
        $view = new AgreementTrackingAddView($agreementId);
        $trackingView = new AgreementTrackingCustomView(ZurmoDefaultViewUtil::makeStandardViewForCurrentUser($this, $view));
        echo $trackingView->render();
    }
    
    public function insertCompletedId($listId) {
        $listIds = array();
        if(isset($listId)){
            $count = 0;
            foreach($listId as $key => $subarray) {
               foreach($subarray as $subkey => $subsubarray) {
                  $listIds[] = $listId[$key][$subkey]['id'];
               }
            }            
            return $listIds;
        }
    }

    public function actionGetAddTracking($trackingInformation) {
        $agreementTracking = json_decode($trackingInformation, TRUE);
        
        $agreementProducts = $agreementTracking['products'];
        
        $cosumedunits = 0;
        foreach ($agreementProducts as $value) {
            $cosumedunits += $value['consumed_unit'];
        }
       
        $newAgreementTracking = new AgreementTracking();
        if($cosumedunits > 0){
            
            // For the Current GPM calculation
            //$agreementTrackingCount = AgreementTracking::getCountByAgmtId($agreementTracking['agreementId']);
           // if($agreementTrackingCount > 0)
                //$hasTracking = 1;
            //else
                //$hasTracking = 0;
            // Ends Here
            
            //$newAgreementTracking->agreement_id = $agreementTracking['agreementId'];
            $newAgreementTracking->agreement = Agreement::GetById(intval($agreementTracking['agreementId']));//$agreementTracking['agreementId'];

            $trackingDate = date('Y-m-d', strtotime($agreementTracking['trackingdate']));
            $newAgreementTracking->tracking_date                = $trackingDate;
            $newAgreementTracking->total_non_agreement_products = $agreementTracking['totalNonAgreementProducts'];

            $newAgreementTracking->name = $agreementTracking['trackingName'];
            $saveAgreementTracking = $newAgreementTracking->save();
        }
       
        
        if (isset($agreementTracking['isCompletedIds'])) {
            $completedProducts = array();
            $completedProductsIds = array();
            foreach ($agreementTracking['isCompletedIds'] as $value) {
                $completedProductsIds[] = AgreementProduct::getProductIdByCategory($value, $agreementTracking['agreementId']);
            }
            $completedProducts = implode(",", $this->insertCompletedId($completedProductsIds));
            if(isset($completedProducts) && $completedProducts > 0){
                AgreementTrackingProducts::getUpdateCompletedProduct($completedProducts);
                AgreementProduct::getUpdateCompletedProduct($completedProducts);
            }
            
        }

        $openProductsCount = AgreementProduct::getProductCompletedStatus($agreementTracking['agreementId']);
        if($openProductsCount == 0){
            $agmnt = Agreement::getById(intval($agreementTracking['agreementId']));
            $agmnt->Status->value = Constant::COMPLETEDAGREEMENT;
            $agmnt->status_changed_date =  DateTimeUtil::getTodaysDate(); // For the agmt completed date
            $agmnt->save();            
            $jobSch = JobScheduling::getJobsByAgmntId(intval($agreementTracking['agreementId']));
            if(isset($jobSch)){
                foreach ($jobSch as $value) {
                    $newStatus = Constant::COMPLETEDAGREEMENT;
                    $value->status = $newStatus; 
                    $value->save();
                }
                
            }    
        }

                
        $agt = Agreement::getById(intval($agreementTracking['agreementId']));
        
        $total_quantity_consumed    = 0;
        $total_mhr_consumed         = 0;
        $total_equipment_consumed   = 0;
        $total_material_consumed    = 0;
        $selected_products_count    = 0;
        $totalDirectCost            = 0;
        $costperunit                = 0;
        $burdenCost                 = 0;
        $laborcost                  = 0;
        
        if (isset($newAgreementTracking->id) && $newAgreementTracking->id > 0 && isset($agreementProducts)) {           
            foreach ($agreementProducts as $trackingProducts) {
                $trackingProduct = new AgreementTrackingProducts();                
                if ($trackingProducts['consumed_unit'] > 0) {
                    
                    // To calculate the total quantity
                    $total_quantity_consumed += round($trackingProducts['consumed_unit'],4);
                    
                    if (strpos($trackingProducts['product_code'], 'L') !== false) {
                        $total_mhr_consumed += $trackingProducts['consumed_unit'];                        
                        
                        $costcatalog=  Costbook::getByProductCode($trackingProducts['product_code']);
                        
                        if(!empty($costcatalog)){
                            $burdenCost = $costcatalog[0]->burdenCost;
                            $laborcost = $costcatalog[0]->laborCost;
                        }
                        
                        $totalDirectCost    +=  ($burdenCost + $laborcost)* $trackingProducts['consumed_unit'];
                         
                    }
                    
                    if (strpos($trackingProducts['product_code'], 'E') !== false) {
                        $total_equipment_consumed += $trackingProducts['consumed_unit'];
                        $costcatalog=  Costbook::getByProductCode($trackingProducts['product_code']);
                        
                        if(!empty($costcatalog)){
                            $costperunit = $costcatalog[0]->costperunit;
                        }
                                                
                        $totalDirectCost    += $costperunit * $trackingProducts['consumed_unit'];
                    }
                    
                    if (strpos($trackingProducts['product_code'], 'M') !== false) {
                        $total_material_consumed += $trackingProducts['consumed_unit'];
                        $costcatalog=  Costbook::getByProductCode($trackingProducts['product_code']);
                        
                        if(!empty($costcatalog)){
                            $costperunit = $costcatalog[0]->costperunit;
                        }
                        
                        $totalDirectCost    += $trackingProducts['consumed_unit'] * $costperunit;
                    }
                    
                    $agmt_prod                  = AgreementProduct::GetById(intval($trackingProducts['agreement_product_id']));
                    $agmt_prod->Consumed_Units  = round($agmt_prod->Consumed_Units+$trackingProducts['consumed_unit'],4);
                    $agmt_prod->save();
                    
                    $trackingProduct->agreementProduct          = $agmt_prod;
                    $trackingProduct->agreement_tracking_id     = $newAgreementTracking->id;
                    $trackingProduct->consumed_unit             = round($trackingProducts['consumed_unit'],4);
                    $trackingProduct->is_agreement_product      = 1;
                    $trackingProduct->agreement                 = $agt;
                    $trackingProduct->save();
                    $selected_products_count++;
                                    
                    $agmttest                  = Agreement::GetById(intval($agreementTracking['agreementId']));
                    $opportunityId =$agmttest->opportunity->id;
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
                                if($totalDirectCost1 > 0)
                                    $totalfinalamount +=	$optpdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)) ;
                            }
                       }
                    }
                   
                }
            }

            
            //For the Current GPM calculation
            $AgmtProductGpm =  AgreementProduct::getAgreementProdByAgreementId($agreementTracking['agreementId']);
            $totalDirectCostData = 0;
            $costperunitData=0;
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
      
            
//            if($hasTracking == 1)
//                $newCurrent_GPMold = $agmttest->newCurrent_GPM;
//            else 
//                $newCurrent_GPMold = 0;
            
           // $newCurrent_GPMround=$newCurrent_GPMold+$newCurrent_GPM;
             //$newCurrent_GPMround=$newCurrent_GPM;
            $agmttest->newCurrent_GPM  =round($newCurrent_GPM,2);
            $agmttest->save();
                    
            $agreementTrackingObj = AgreementTracking::getById($newAgreementTracking->id);
            // To save the other tracking details
            //$agreementTrackingObj->name                     = 'AT'.$newAgreementTracking->id;
            $agreementTrackingObj->total_selected_products  = $selected_products_count;
            $agreementTrackingObj->total_quantity_consumed  = round($total_quantity_consumed,4);
            $agreementTrackingObj->total_mhr                = round($total_mhr_consumed,4);
            $agreementTrackingObj->total_material_units     = round($total_material_consumed,4);
            $agreementTrackingObj->total_equipment_units    = round($total_equipment_consumed,4);
            try{
               $agreementTrackingObj->save();
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
            
            // To update the calculations in the agreement            
            $agt->Used_MHR                  = round($agt->Used_MHR+$total_mhr_consumed,2);
            $agt->Total_Available_MHR       = round($agt->Total_Available_MHR-$total_mhr_consumed,2);
            $agt->Used_Material             = round($agt->Used_Material+$total_material_consumed,2);
            $agt->Available_Material        = round($agt->Available_Material-$total_material_consumed,2);
            $agt->Used_Equipment            = round($agt->Used_Equipment+$total_equipment_consumed,2);
            $agt->Available_Equipment       = round($agt->Available_Equipment-$total_equipment_consumed,2);
            
            $agt->Year_to_Date_MHR          = round($agt->Used_MHR,2);
            
            if($agt->Total_MHR > 0)
                $agt->MHR_Used_Percentage   = round(($agt->Used_MHR/$agt->Total_MHR)*100, 2);
            
            $agt->Material_Year_To_Date     = round($agt->Used_Material,2);
            
            if($agt->Total_Material > 0)
                $agt->Material_Used_Percentage  = round(($agt->Used_Material/$agt->Total_Material)*100, 2);
            
            $agt->Equipment_Year_To_Date    = round($agt->Used_Equipment,2);
            
            if($agt->Total_Equipment > 0)
                $agt->Equipment_Used_Percentage = round(($agt->Used_Equipment/$agt->Total_Equipment)*100, 2);
            
            if (!$agt->save()) {
                throw new FailedToSaveModelException();
            }
        }
        echo json_encode($agreementTracking['agreementId']);
    }

    public function actionGetNonAgreementProducts($category, $costOfGoods, $addProductoptId, $pageOffset, $productName, $sortFor='code', $sortOrder='asc') {
        $pageSize = Yii::app()->pagination->resolveActiveForCurrentUserByType(
                'listPageSize', get_class($this->getModule()));
        $costbookList = AgreementTracking::getAddProductSearchData($category, $costOfGoods, $productName, $pageOffset, $pageSize, $sortFor, $sortOrder);
        $resultArray = array();
        foreach ($costbookList as $key => $costbook) {
            if(AgreementTrackingUtils::addProductFindDuplicate($costbook['Category'], $addProductoptId, $costbook['productcode']) == FALSE){
                if($costbook['CostOfGoodsSold'] == Constant::ASSEMBLY){
                    $costbook = CostbookUtils::getAssemblyUnitDirectCost($costbook);
                }
                array_push($resultArray, $costbook);
            }
        }
        /*$items_per_page = $pageOffset * ($pageSize * 2);
        $offset = 0;
        $output = array_slice($resultArray, $offset, $items_per_page);*/
        echo CJSON::encode($resultArray);
    }

    public function actionaddNonAgreementProducts($ids, $addJsonObj, $agtId, $track_date, $track_name) {
       if($ids != null && $addJsonObj != null) 
       {
            $costbookDatas = json_decode($addJsonObj, TRUE);           
            //Saving Agreement Tracking
            $total_prods = count($costbookDatas);
            
            $newAgreementTracking               = new AgreementTracking();
            $newAgreementTracking->agreement    = Agreement::GetById(intval($agtId));
            $trackingDate                       = date('Y-m-d', strtotime($track_date));
            $newAgreementTracking->tracking_date                = $trackingDate;
            $newAgreementTracking->name                         = $track_name;
            $newAgreementTracking->total_selected_products      = $total_prods;
            $newAgreementTracking->total_non_agreement_products = $total_prods;
            $saveAgreementTracking = $newAgreementTracking->save();
            
            $agt = Agreement::getById(intval($agtId));
           
            $non_agt_totalDirectCost    = 0;
            $total_quantity_consumed    = 0;
            $total_mhr_consumed         = 0;
            $total_equipment_consumed   = 0;
            $total_material_consumed    = 0;
            
            foreach($costbookDatas as $costbookData) {
                // Saving Agreements Products
                $costbook     = Costbook::getById($costbookData['costBookId']);
                $agtRetnArr   = AgreementProductUtils::addAgreementProductsCalculation($costbook, $costbookData['add_Quantity'], '', $agtId, $costbookData['add_Category'], 'non_agt_prod');
                // Saving Agreement Tracking Products                
                if ($costbookData['add_Quantity'] > 0) 
                {                    
                    $trackingProduct = new AgreementTrackingProducts();                    
                    // To calculate the total quantity
                    $total_quantity_consumed    += $costbookData['add_Quantity'];
                    $non_agt_totalDirectCost    += $agtRetnArr['agt_prod_obj']->Total_Direct_Cost->value; 
                                        
                    if (strpos($costbook->productcode, 'L') !== false) {
                        $total_mhr_consumed += $costbookData['add_Quantity'];
                    }                    
                    if (strpos($costbook->productcode, 'E') !== false) {
                        $total_equipment_consumed += $costbookData['add_Quantity'];
                    }                    
                    if (strpos($costbook->productcode, 'M') !== false) {
                        $total_material_consumed += $costbookData['add_Quantity'];
                    }
                    
                    $agmt_prod                  = AgreementProduct::GetById(intval($agtRetnArr['agt_prod_obj']->id));
                    $agmt_prod->Consumed_Units  = round($agmt_prod->Consumed_Units+$total_quantity_consumed,4);
                    $agmt_prod->Is_Non_Agreement_Product = 1;
                    $agmt_prod->save();
                    
                    $trackingProduct->agreementProduct          = $agtRetnArr['agt_prod_obj'];
                    $trackingProduct->agreement_tracking_id     = $newAgreementTracking->id;
                    $trackingProduct->consumed_unit             = round($costbookData['add_Quantity'],4);
                    $trackingProduct->is_agreement_product      = 0;
                    $trackingProduct->agreement                 = $agt;
                    $trackingProduct->is_completed              = 0;
                    $trackingProduct->save();
                }
            }
          
            if (isset($newAgreementTracking->id) && $total_prods > 0) {
                $currencies = Currency::getAll();
                // To save the other tracking details
                //$newAgreementTracking->name                     = 'AT'.$newAgreementTracking->id;
                $newAgreementTracking->total_quantity_consumed  = round($total_quantity_consumed,4);
                $newAgreementTracking->total_mhr                = round($total_mhr_consumed,4);
                $newAgreementTracking->total_material_units     = round($total_material_consumed,4);
                $newAgreementTracking->total_equipment_units    = round($total_equipment_consumed,4);
                $newAgreementTracking->save();
            
                // To update the calculations in the agreement                 
                $agt->Used_MHR                  = round($agt->Used_MHR+$total_mhr_consumed,2);
                $agt->Total_Available_MHR       = round($agt->Total_Available_MHR-$total_mhr_consumed,2);
                $agt->Used_Material             = round($agt->Used_Material+$total_material_consumed,2);
                $agt->Available_Material        = round($agt->Available_Material-$total_material_consumed,2);
                $agt->Used_Equipment            = round($agt->Used_Equipment+$total_equipment_consumed,2);
                $agt->Available_Equipment       = round($agt->Available_Equipment-$total_equipment_consumed,2);
                $agt->Year_to_Date_MHR          = round($agt->MHR_Used_Percentage,2);
                
                $agt->Year_to_Date_MHR     = round($agt->Used_MHR,2);
                
                if($agt->Total_MHR > 0)
                    $agt->MHR_Used_Percentage   = round(($agt->Used_MHR/$agt->Total_MHR)*100, 2);

                $agt->Material_Year_To_Date     = round($agt->Used_Material,2);

                if($agt->Total_Material > 0)
                    $agt->Material_Used_Percentage  = round(($agt->Used_Material/$agt->Total_Material)*100, 2);

                $agt->Equipment_Year_To_Date    = round($agt->Used_Equipment,2);

                if($agt->Total_Equipment > 0)
                    $agt->Equipment_Used_Percentage = round(($agt->Used_Equipment/$agt->Total_Equipment)*100, 2);
                
                $totalDirectCosts               = new CurrencyValue();
                $totalDirectCosts->value        = round($non_agt_totalDirectCost, 2);
                $totalDirectCosts->currency     = $currencies[0];
                $agt->Non_Agmt_Prod_Direct_Cost = $totalDirectCosts;

                if($agt->save()){
                    echo json_encode($agtId);
                }else{
                    return TRUE;
                }
            }
        } 
    }
    
    public function actionGetUpdateTracking($trackingInformation) {
        
        //print_r($trackingInformation);
        
        $agreementTracking          = json_decode($trackingInformation, TRUE);        
        $agreementProducts          = $agreementTracking['products'];
        $nonAgreementProducts       = $agreementTracking['newProducts'];
        //$total_quantity_consumed    = 0;
        $newAgreementTracking       = AgreementTracking::getById($agreementTracking['trackingId']);
       //print_r($newAgreementTracking->agreement);
        $agtId                      = $newAgreementTracking->agreement->id;
        
        // Adding New Agreement prods
        
        $agt = Agreement::getById(intval($agtId));
           
        $non_agt_totalDirectCost    = 0;
        $total_quantity_consumed    = 0;
        $total_mhr_consumed         = 0;
        $total_equipment_consumed   = 0;
        $total_material_consumed    = 0;
        
        if($nonAgreementProducts)
        {
            $total_prods = count($nonAgreementProducts);
            
            $newAgreementTracking->total_selected_products      = $newAgreementTracking->total_selected_products+$total_prods;
            $newAgreementTracking->total_non_agreement_products = $total_prods;
            $saveAgreementTracking = $newAgreementTracking->save(); 
            
            foreach($nonAgreementProducts as $costbookData) {
                // Saving Agreements Products
                $costbook     = Costbook::getById($costbookData['costBookId']);
                $agtRetnArr   = AgreementProductUtils::addAgreementProductsCalculation($costbook, $costbookData['add_Quantity'], '', $agtId, $costbookData['add_Category'], 'non_agt_prod');
                
                // Saving Agreement Tracking Products                
                if ($costbookData['add_Quantity'] > 0) 
                {                    
                    $trackingProduct = new AgreementTrackingProducts();
                    // To calculate the total quantity
                    $total_quantity_consumed    += $costbookData['add_Quantity'];
                    $non_agt_totalDirectCost    += $agtRetnArr['agt_prod_obj']->Total_Direct_Cost->value; 
                                        
                    if (strpos($costbook->productcode, 'L') !== false) {
                        $total_mhr_consumed += $costbookData['add_Quantity'];
                    }                    
                    if (strpos($costbook->productcode, 'E') !== false) {
                        $total_equipment_consumed += $costbookData['add_Quantity'];
                    }                    
                    if (strpos($costbook->productcode, 'M') !== false) {
                        $total_material_consumed += $costbookData['add_Quantity'];
                    }
                    
                    $trackingProduct->agreementProduct      = $agtRetnArr['agt_prod_obj'];
                    $trackingProduct->agreement_tracking_id = $newAgreementTracking->id;
                    $trackingProduct->consumed_unit         = round($costbookData['add_Quantity'],4);
                    $trackingProduct->is_agreement_product  = 0;
                    $trackingProduct->agreement             = $agt;
                    $trackingProduct->is_completed          = 0;
                    $trackingProduct->save();
                }
            }
        }
        
        // For updating the already added agmt prods
        $updated_prod_quantity_consumed = 0;
        $updated_mhr_consumed           = 0;
        $updated_equipment_consumed     = 0;
        $updated_material_consumed      = 0;
        
        $prev_prod_quantity_consumed    = 0;
        $prev_mhr_consumed              = 0;
        $prev_equipment_consumed        = 0;
        $prev_material_consumed         = 0;
        $totalDirectCost            = 0;
        $costperunit                = 0;
        $burdenCost                 = 0;
        $laborcost                  = 0;
        
        if (isset($agreementProducts)) {
            foreach ($agreementProducts as $trackingProducts) {
                $addedTrackingProduct = AgreementTrackingProducts::getAgreementTrackingProductByAgmtTrackingId($trackingProducts['agreement_tracking_product_id']);
                                
                if ($trackingProducts['consumed_unit'] >= 0 && isset($addedTrackingProduct[0])) {
                    $addedTrackingProduct = $addedTrackingProduct[0];
                    
                    // Need to check and re write the code - done for quick fix
                  //  $addedTrackingProduct = AgreementTrackingProducts::getById($addedTrackingProduct->id);

                    //$total_quantity_consumed = $addedTrackingProduct->agreement_tracking_id;                    
                    
                    $updated_prod_quantity_consumed += $trackingProducts['consumed_unit'];                    
                    $prev_prod_quantity_consumed    += $addedTrackingProduct->consumed_unit; 
                    
                    // Updating the units consumed in agmt prods
                    $agmt_prod                  = AgreementProduct::getAgreementProductById(intval($trackingProducts['agreement_product_id']));

                    if(isset($agmt_prod[0]))
                    {
                        $agmt_prod = $agmt_prod[0];
                        
                        $getCostbook     = Costbook::getById($agmt_prod->costbook->id);
                        
                        if (strpos($getCostbook->productcode, 'L') !== false) {
                            $prev_mhr_consumed      += $addedTrackingProduct->consumed_unit;
                            $updated_mhr_consumed   += $trackingProducts['consumed_unit'];
                        }                    
                        if (strpos($getCostbook->productcode, 'E') !== false) {
                            $prev_equipment_consumed    += $addedTrackingProduct->consumed_unit;
                            $updated_equipment_consumed += $trackingProducts['consumed_unit'];
                        }                    
                        if (strpos($getCostbook->productcode, 'M') !== false) {
                            $prev_material_consumed     += $addedTrackingProduct->consumed_unit;
                            $updated_material_consumed  += $trackingProducts['consumed_unit'];
                        }
                    }

                    $agmt_prod_consumed_units_ =  (round($agmt_prod->Consumed_Units,4)+round($trackingProducts['consumed_unit'],4))-round($addedTrackingProduct->consumed_unit,4);
                    $agmt_prod->Consumed_Units  = round($agmt_prod_consumed_units_,4);
                    $agmt_prod->save();
                    
                    
                    $agmttest  = Agreement::GetById(intval($newAgreementTracking->agreement->id));
                    $opportunityId =$agmttest->opportunity->id;
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
                                if($totalDirectCost1 > 0)
                                    $totalfinalamount +=	$optpdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)) ;
                            }
                       }
                    }
                    
//                    $agmt_prod->Consumed_Units  = ($agmt_prod->Consumed_Units+$trackingProducts['consumed_unit'])-$addedTrackingProduct->consumed_unit;
//                    $agmt_prod->save();
                    
                    // Updating the units consumed in Agmt tracking products
                    $updateAgmtTrackingProduct = AgreementTrackingProducts::getById($trackingProducts['agreement_tracking_product_id']);
                    $updateAgmtTrackingProduct->consumed_unit    = round($trackingProducts['consumed_unit'],4);
                    $updateAgmtTrackingProduct->save();
                }
            }

            $AgmtProduct =  AgreementProduct::getAgreementByAgreementId($agtId);
            $totalDirectCost=0;
            foreach ($AgmtProduct as $AgmtProducts) {                
                if(isset($AgmtProducts) && (!empty($AgmtProducts->Consumed_Units)))
                    {
                        $agmtprod = $AgmtProducts;
                        
                        $getCostbook     = Costbook::getById($agmtprod->costbook->id);
                        
                        if (strpos($getCostbook->productcode, 'L') !== false) {                            
                            $costcatalog=  Costbook::getByProductCode($getCostbook->productcode);
                        
                            if(!empty($costcatalog)){
                                $burdenCost = $costcatalog[0]->burdenCost;
                                $laborcost = $costcatalog[0]->laborCost;
                            }
 
                            $totalDirectCost    +=  ($burdenCost + $laborcost)* $trackingProducts['consumed_unit'];
                        }                    
                        if (strpos($getCostbook->productcode, 'E') !== false) {                                                       
                            $costcatalog=  Costbook::getByProductCode($getCostbook->productcode);
                        
                            if(!empty($costcatalog)){
                                $costperunit = $costcatalog[0]->costperunit;
                            }

                            $totalDirectCost    += $costperunit * $trackingProducts['consumed_unit'];
                        }                    
                        if (strpos($getCostbook->productcode, 'M') !== false) {
                            $costcatalog=  Costbook::getByProductCode($getCostbook->productcode);
                        
                            if(!empty($costcatalog)){
                                $costperunit = $costcatalog[0]->costperunit;
                            }

                            $totalDirectCost    += $trackingProducts['consumed_unit'] * $costperunit;
                        }
                    }
            }
            
            
            
//            $newCurrent_GPM=(($totalfinalamount-$totalDirectCost)/$totalfinalamount)*100;
//            $newCurrent_GPMold =$agmttest->newCurrent_GPM;
//            $agmttest->newCurrent_GPM  = round($newCurrent_GPM,2);
//            $agmttest->save();
            
            
            //For the Current GPM calculation
            $AgmtProductGpm =  AgreementProduct::getAgreementProdByAgreementId($agtId);
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
      
            
//            if($hasTracking == 1)
//                $newCurrent_GPMold = $agmttest->newCurrent_GPM;
//            else 
//                $newCurrent_GPMold = 0;
            
           // $newCurrent_GPMround=$newCurrent_GPMold+$newCurrent_GPM;
             //$newCurrent_GPMround=$newCurrent_GPM;
            $agmttest->newCurrent_GPM  =round($newCurrent_GPM,2);
            $agmttest->save();
            
        }  
        
        // To save the other tracking details
        $trackingDate = date('Y-m-d', strtotime($agreementTracking['trackingdate']));
        $newAgreementTracking->tracking_date            = $trackingDate;
        $newAgreementTracking->name            = $agreementTracking['trackingName'];
        
        $tot_quant_consumed_calc = ($total_quantity_consumed+$updated_prod_quantity_consumed)-$prev_prod_quantity_consumed;
        $newAgreementTracking->total_quantity_consumed  = $updated_prod_quantity_consumed+$total_quantity_consumed;
        
        $tot_mhr_consumed_calc = ($total_mhr_consumed+$updated_mhr_consumed)-$prev_mhr_consumed;
        $newAgreementTracking->total_mhr  = $updated_mhr_consumed+$total_mhr_consumed;
        
        $tot_equipment_consumed_calc = ($total_equipment_consumed+$updated_equipment_consumed)-$prev_equipment_consumed;
        $newAgreementTracking->total_equipment_units  = $updated_equipment_consumed+$total_equipment_consumed;
        
        $tot_material_consumed_calc = ($total_material_consumed+$updated_material_consumed)-$prev_material_consumed;
        $newAgreementTracking->total_material_units  = $updated_material_consumed+$total_material_consumed;
        
        $newAgreementTracking->save(); 
        
        // To update the calculations in the agreement  q
        $agt->Used_MHR                  = round($agt->Used_MHR+$tot_mhr_consumed_calc,2);
        $agt->Total_Available_MHR       = round($agt->Total_Available_MHR-$tot_mhr_consumed_calc,2);
        $agt->Used_Material             = round($agt->Used_Material+$tot_material_consumed_calc,2);
        $agt->Available_Material        = round($agt->Available_Material-$tot_material_consumed_calc,2);
        $agt->Used_Equipment            = round($agt->Used_Equipment+$tot_equipment_consumed_calc,2);
        $agt->Available_Equipment       = round($agt->Available_Equipment-$tot_equipment_consumed_calc,2);
        $agt->Year_to_Date_MHR          = round($agt->MHR_Used_Percentage,2);

        $agt->Year_to_Date_MHR     = round($agt->Used_MHR,2);
        
        if($agt->Total_MHR > 0)
            $agt->MHR_Used_Percentage   = round(($agt->Used_MHR/$agt->Total_MHR)*100, 2);

        $agt->Material_Year_To_Date     = round($agt->Used_Material,2);

        if($agt->Total_Material > 0)
            $agt->Material_Used_Percentage  = round(($agt->Used_Material/$agt->Total_Material)*100, 2);

        $agt->Equipment_Year_To_Date    = round($agt->Used_Equipment,2);

        if($agt->Total_Equipment > 0)
            $agt->Equipment_Used_Percentage = round(($agt->Used_Equipment/$agt->Total_Equipment)*100, 2);
        
        $currencies = Currency::getAll();
        $totalDirectCosts               = new CurrencyValue();
        $totalDirectCosts->value        = round($non_agt_totalDirectCost, 2);
        $totalDirectCosts->currency     = $currencies[0];
        $agt->Non_Agmt_Prod_Direct_Cost = $totalDirectCosts;

        if($agt->save()){
            echo json_encode($agtId);
        }else{
            return TRUE;
        }
    }   
 }

?>
