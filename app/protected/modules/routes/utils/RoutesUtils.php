<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RoutesUtils {

    function __construct() {
        
    }
    
    public function createRouteStep1($routeId, $isClone, $isCloneBack) {
        $name = '';
        $crewname = '';
        $content = '<div>';
        $routeDetails = 'create';
        if(isset($routeId)){
            $routeDetails = Route::getById($routeId);
            $name = $routeDetails->name;
            $crewname = $routeDetails->crewname;
            $content .='<input type="hidden" id="edit-route" name="edit-route" value="'. $routeDetails->id.'_'.$isClone.'">';
        }
        else{
            $content .='<input type="hidden" id="edit-route" name="edit-route" value="'.$routeDetails.'_'.$isClone.'">';
        }
        
        $content .='<input type="hidden" id="is_clone_back" name="is_clone_back" value="'.$isCloneBack.'">';
        
        $content .= '<div class="wrapper">
                        <h1><span class="truncated-title" threedots="Create Route - (Step 1 of 4)"><span class="ellipsis-content">Create Route - (Step 1 of 4)</span></span></h1>
                        <div class="wide form">
                            <form id="edit-form" >
                                <div style="display:none"><input type="hidden" ></div>
                                <div class="attributesContainer">
                                    <div class="left-column full-width">
                                        <div class="panel">
                                            <div class="panelTitle">Route Information</div>
                                            <table class="form-fields">
                                               <colgroup>
                                                  <col class="col-0">
                                                  <col class="col-1">
                                               </colgroup>
                                               <tbody>
                                                  <tr>
                                                     <th><label class="required" for="Route_name">Route Name <span class="required">*</span></label></th>
                                                     <td colspan="1"><input type="text" maxlength="100" name="Route[name]" id="Route_routename" value="'.$name.'"> <div id="route_name" style="display:none;" class="errorMessage">Route Name cannot be blank.</div></td>
                                                  </tr>
                                                  <tr>
                                                     <th><label class="required" for="Route_crewname">Crew Name <span class="required">*</span></label></th>
                                                     <td colspan="1"><input type="text" maxlength="100" name="Route[crewname]" id="Route_crewname" value="'.$crewname.'"> <div id="crew_name" style="display:none;" class="errorMessage">Crew Name cannot be blank.</div></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                        </div>
                                        <div class="panel">';
                                        $content .= RoutesUtils::routeCategory($routeId);
                                        $content .='</div>
                                        </div>
                                    <div class="float-bar">
                                        <div class="view-toolbar-container clearfix dock">
                                            <div class="form-toolbar"><a href="/app/index.php/routes/default" class="cancel-button" id="CancelLinkActionElement--30-yt2"><span class="z-label">Cancel</span></a><a href="#"  class="attachLoading z-button" name="next" id="nextyt3" onclick="javascript:createRouteStep1();"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Next</span></a></div>
                                        </div>
                                    </div>
                            </form>
                            <div id="modalContainer-edit-form"></div>
                        </div>
                    </div>
                    </div>';
        return $content;
    }
    
    public function routeCategory($routeId){
        $selectedCategoriesList = '';
        $selectedCategories = array();
        if(isset($routeId)){
            $categories_selected = RouteCategory::getCatByRouteId($routeId);
            foreach ($categories_selected as $values) {
                $selectedCategories[] = $values->category->id;
            }
            $selectedCategoriesList = implode(',', $selectedCategories);
        }
        $categories = Category::getAll();
        $content = '<div class="panelTitle">Categories Information</div>
                        <input type="hidden" id="list-view-selected-categoryIds" name="list-view-selected_categoryIds" value="'. $selectedCategoriesList.'">
                        <div class="table_border_width" id="add_category" style="padding: 0px;">
                            <div class="align_left" style="color:black; background-color:#E0D1D1; color:black; padding:0.5%; font-weight:bold;">Categories</div>
                            <table class="form-fields items">
                               <tbody> <tr>';
                                    $count = 1;
                                    foreach ($categories as $values) {
                                        $content .= '<td>';
                                        if (in_array($values->id, $selectedCategories) && isset($selectedCategories)) {
                                            $content .='<input type="checkbox" id="category_id_'.$count.'"  name="list_category_list[]" value="' . $values->id . '" checked>';
                                        }else{
                                            $content .='<input type="checkbox" id="category_id_'.$count.'"  name="list_category_list[]" value="' . $values->id . '">';
                                        }
                                            $content .= '<label style="float: none;" for="category_id_'.$count.'"> '.$values->name.'</label>
                                        </td>';

                                        if($count % 3 == 0)
                                        {
                                            $content .= '</tr><tr>';
                                        }

                                        $count++;
                                    }
                                $content .= '</tr></tbody>
                            </table>
                        </div>';
        return $content;
    }

    public function routeTracking($data, $routeId) {
        $content    = '';
        $content    .= RoutesUtils::appendLayoutStart($routeId);
        $content    .= RoutesUtils::trackingDate($routeId);
        $content    .= RoutesUtils::appendButton($routeId);
        $content    .= RoutesUtils::agmntProductsView($routeId);
            
            
        return $content;    
    }
    
    public function appendLayoutStart($routeId) {
        $route = Route::getById($routeId);
        $content = '<div class="StarredListView SecuredListView ListView ModelView ConfigurableMetadataView MetadataView">
                        <div class="cgrid-view type-routes">
                            <div class="items-wrapper">
                            <div class="wrapper">
                                <h1>
                                    <span class="truncated-title" threedots="Create Costbook"><span class="ellipsis-content">'.$route->name.'</span></span>
                                </h1>
                                <div class="wide form">
                                    <div class="attributesContainer">
                                        <div class="left-column" style="width:100%;">';
        return $content;
    }

    public function trackingDate($routeId){

        $content = '<div class="routePanelTitle">Route</div>
                    <div class="route-table" style="width:80%; margin-bottom:2%;margin-left:10%;">
                        <div  style="margin-bottom:5%;">
                            <div class="cell" style="float:left; width: 15%; padding-top:0.5%; font-weight:bold; ">Route Name <span class="required">*</span></div>

                            <div class="cell" style="float:left; width:30%;">
                                <div class="has-date-select" style=" width:100%">
                                   <input type="text" name="RouteTracking[name]" value="" id="RouteTracking_name">
                                </div>
                                <div class="errorMessage"style="display:none;" id="RouteTracking_name_Error">Route Name cannot be blank.</div>                                
                            </div>
                            
                            <div class="cell" style="float: left; width: 15%; font-weight: bold; padding-top: 0.5%; margin-left: 5%;">Date Of Service <span class="required">*</span></div>
                            <div class="cell" style="float: left; width: 30%;">
                                <input type="text" name="RouteTracking[date_of_service]" value="" id="RouteTracking_date_of_service">
                                <div class="errorMessage"style="display:none;" id="RouteTracking_date_of_service_Error">Date of service cannot be blank.</div>                                
                            </div>                            
                        </div>

                        <div class="route-table-row" style="margin-top:20%; ">
                            <div class="cell" style="float:left; width: 15%; padding-top:0.5%; font-weight:bold;">Service Start Time<span class="required">*</span></div>
                            <div class="cell" style="float:left; width: 30%;">
                                <div style=" width:100%" class = "route-time-piker">
                                   <input type="text" name="RouteTracking[service_start_time]" value="" id="RouteTracking_service_start_time" class="time">
                                </div>
                                <div class="errorMessage"style="display:none;" id="RouteTracking_service_start_time_Error">Service start time cannot be blank.</div>
                            </div>
                            <div class="cell" style="float:left; width: 15%; margin-left:5%; padding-top:0.5%; font-weight:bold;">Service End Time<span class="required">*</span></div>
                            <div class="cell" style="float:left; width: 30%;">
                                <div class = "route-time-piker">
                                   <input type="text" name="RouteTracking[service_end_time]" value="" id="RouteTracking_service_end_time" class="time">
                                </div>
                                <div class="errorMessage"style="display:none;" id="RouteTracking_service_end_time_Error">Service end time cannot be blank.</div>
                            </div>
                        </div>
                    </div>';
        $agmt_name_arr = RouteAgreement::getAgmtByRouteId($routeId);
        $i =1;
            $content .= '<div class="panel details-table">
                        <div class="panelTitle" style="padding-left: 0px;">Route Location Informations</div>
                            <table class="form-fields">                                
                                <tbody><tr>';
                            foreach ($agmt_name_arr as $agmt_name) {
                                $content .='<td>' .$i.'. '. $agmt_name->agreement->name. '</td>';

                                if($i%3 ==0)
                                {
                                    $content .='</tr><tr>';
                                }                                    
                                $i++;   
                             }  
                                     $content .='</td></tr></tbody>
                             </table>                                 
                        </div>';
        
        return $content;
    }

    
    public function appendButton($routeId) {
        $content = '<div style="margin-left:42%;margin-bottom:2%;">
                        <a href="#" id="Save" onclick="javascript:saveRouteTrackingProducts(\'' . $routeId . '\', this);" class="attachLoading" style="margin-right:1%;">
                            <span class="z-label appendButton">
                                Save
                            </span>
                        </a>
                        <a class="attachLoading cancel-button" id="Cancel" href="/app/index.php/routes/default/details?id='.$routeId.'" style="margin-left:1%;">
                            <span class="z-label">
                                Cancel
                            </span>
                        </a>
                    </div>';
        return $content;
    }
    
    public function agmntProductsView($routeId){
        $content = '';
            $routeAgmts   = RouteAgreement::getAgmtByRouteId($routeId);
            $agmt_id_arr = array();
            $countOfSelectedRow = 0;
            $CategoryKeyCount = 0;
            $countOfRouteAgmnt = 0;
            $content .='<div class="SecuredListView ListView ModelView ConfigurableMetadataView MetadataView" id="RoutesListView">
                        <div id="list-view" class="cgrid-view type-routes">
                            <div class="summary">4 result(s)</div>
                            <div class="items-wrapper">
                                <table class="items">
                                    <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Assembly</th>
                                        <th>Product</th>
                                        <th>UOM</th>';
                                        $j =1;
                                        foreach ($routeAgmts as $agreementKey => $routeAgreement) {
                                            $agmt_id_arr[] = $routeAgreement->agreement->id;
                                            $content .='<th style="text-align:center;  cursor: pointer;" title="'.$routeAgreement->agreement->name.'">' . $j . '</th>
                                            <input value=' .$routeAgreement->agreement->id. ' name="agmntId" id="agmntId_' . $countOfRouteAgmnt . '" type="hidden">';
                                            $countOfRouteAgmnt++;
                                            $j++;
                                        } 
                                    $content .='</tr>';
                                    $agmtProds   = RouteProducts::getRouteProdByAgmtIdRouteId($routeId);
                                    $agreementPdctMap = array();
                                    $agreementPdctIds = array();
                                    foreach ($agmtProds as $agmtProd) {
                                        $agreementPdctIds[$agmtProd->agreementproduct->Category][$agmtProd->agreementproduct->costbook->productcode.'-'.$agmtProd->agreementproduct->Assembly_Product_Code]['present_agmt_ids'][] = $agmtProd->agreement->id;
                                        $agreementPdctMap[$agmtProd->agreementproduct->Category][$agmtProd->agreementproduct->costbook->productcode.'-'.$agmtProd->agreementproduct->Assembly_Product_Code] = $agmtProd;
                                    }
                                    $column_count = 4+count($agmt_id_arr);
                                    foreach ($agreementPdctMap as $CategoryKey => $agreementArray) {
                    $content .='<tbody><tr>
                                    <td colspan="'.$column_count.'" class="align_left" style="background-color:gray; color:white;">' . $CategoryKey . ' </td>
                                </tr>';
                    
                    foreach ($agreementArray as $agreementKey => $agreementpdt) {                     
                        $agreementProduct = 1;                        
                        $content .='<tr>
                                        <td>' . $agreementpdt->agreementproduct->costbook->productcode . '
                                            <input value=' . $agreementpdt->agreementproduct->costbook->id . ' name="productId" id="productId_' . $countOfSelectedRow . '" type="hidden">
                                            <input value=' . $agreementpdt->agreementproduct->id . ' name="agreement_product_id" id="agreement_product_id_' . $countOfSelectedRow . '" type="hidden">
                                            <input value=' . $agreementpdt->agreementproduct->id . ' name="agreement_product_id" id="agreement_category_product_' . $CategoryKeyCount . '" type="hidden">
                                        </td>
                                        <td>' . $agreementpdt->agreementproduct->Assembly_Product_Code . '</td>
                                        <td>' . $agreementpdt->agreementproduct->name . '</td>
                                        <td>' . $agreementpdt->agreementproduct->costbook->unitofmeasure . '</td>';
                                        foreach ($agmt_id_arr as $agmt_id) {
                                            if(in_array($agmt_id, $agreementPdctIds[$CategoryKey][$agreementpdt->agreementproduct->costbook->productcode.'-'.$agreementpdt->agreementproduct->Assembly_Product_Code]['present_agmt_ids']))
                                            {
                                                //To get the Agmtprod Id based on the agmt & costbook Id
                                                $getAgmtProdId   = AgreementProduct::getAgmtProdIdByAgmtIdCostBookId($agmt_id, $agreementpdt->agreementproduct->costbook->id, $agreementpdt->agreementproduct->costbook->productcode, $agreementpdt->agreementproduct->Assembly_Product_Code, $agreementpdt->agreementproduct->Category);
                                                    
                                                $content .='<td style="text-align:center;"> <input type="text" name="route_prod_consumed" id="routeProdConsumed_'.$CategoryKeyCount.'-'.$agmt_id.'-'.$getAgmtProdId->id.'" class="routeProdConsumed" value="0" style="width:50%;"></td>';
                                                
                                            }
                                            else
                                            {
                                                $content .='<td></td>';
                                            }
                                        }                                  
                                    $content .=' </tr>';
                        $countOfSelectedRow++;
                    }
                    
                    $CategoryKeyCount++;
                }
                   $content .= '<input value=' . $countOfSelectedRow . ' name="countOfAgmntProd" id="countOfAgmntProd_id" type="hidden">                 
                                    
                                </tbody>
                            </table>
                     </div>
                     <div id="result_div"></div>';
            return $content;

        
        
        
        }

    public function makeRouteStep3View($routeId, $newClonedRouteId, $isEdit) {
        $editvalue = 0;
        if($isEdit == 'edit'){
            $editvalue = 1;
            $routeAgreements = RouteAgreement::getAgmtByRouteId($routeId);
            if(isset($_SESSION['agreementList']))
            {
                $ids_arr = $_SESSION['agreementList'];
            }else
            {
                if(!empty($routeAgreements)){
                    foreach ($routeAgreements as $value) {
                        $ids_arr[] = $value->agreement->id;
                    }
                }else{
                    $ids_arr = $_SESSION['agreementList'];
                }
            }
        }
        else if($isEdit == 'clone'){
            $editvalue = 1;
            $routeAgreements = RouteAgreement::getAgmtByRouteId($newClonedRouteId);
            if(isset($_SESSION['agreementList']))
            {
                $ids_arr = $_SESSION['agreementList'];
            }else
            {
                if(!empty($routeAgreements)){
                    foreach ($routeAgreements as $value) {
                        $ids_arr[] = $value->agreement->id;
                    }
                }
            }
        }else{
            $ids_arr = $_SESSION['agreementList'];
        }
        $agmt_arr = Agreement::getByInId($ids_arr);
        
        $content = '<div>';
        $content .= '<div class="wrapper">
                        <input type="hidden" id="edit-route" name="edit-route" value="'. $editvalue.'">
                        <h1><span class="truncated-title" threedots="Create Route - (Step 3 of 4)"><span class="ellipsis-content">Create Route - (Step 3 of 4)</span></span></h1>
                        <div class="wide form">
                            <form id="edit-form">                                
                                <div class="attributesContainer">
                                    <div class="left-column full-width">
                                        <div class="panel">
                                            <div class="panelTitle">Select Agreement to Reorder</div>                                            
                                        </div>
                                        
                                        <div class="panel">
                                            <table class="" cellspacing="4" cellpadding="4">
                                                <colgroup>                                                  
                                                  <col class="col-1">
                                                  <col class="col-1">
                                                </colgroup>
                                                <tbody>
                                                    <tr>                                                        
                                                        <td colspan="1">
                                                            <div class="multiselect-holder">
                                                                <div class="">
                                                                    <select name="selectedAgmt" id="selectedAgmt" class="ignore-style multiple" style="width:auto" size="15">';
                                                        foreach ($agmt_arr as $values) {
                                                            $content .= '
                                                                      <option value="'.$values['id'].'">'.$values['name'].'</option>';
                                                        } 
                                                               
                                                    $content .= '     </select>
                                                                </div>
                                                                </div>
                                                                </td>
                                                                <td colspan="1">
                                                                <div class="multiselect-nav multiselect-nav-updown multiselect-holder">
                                                                    <input type="button" value="5" name="yt7" class="white-button icon-up-arrow" id="btn-up">
                                                                    <input type="button" value="6" name="yt8" class="white-button icon-down-arrow" id="btn-down">
                                                                    <a href="JavaScript:void(0);" id="btn-remove"> Remove</a>
                                                               </div>
                                                             
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                              </table>
                                           </div>                                    
                                <div class="float-bar">
                                    <div class="">
                                        <div class="form-toolbar">';
                                        if(!empty ($this->routeId) && empty($this->newClonedRouteId)){                                                    
                                            $content .='<a href="/app/index.php/routes/default/createStep2?id='.$routeId.'&ClonedRouteId='.$newClonedRouteId.'&type='.$isEdit.'" class="cancel-button" id="CancelLinkActionElement--30-yt2"><span class="z-label">Back</span></a>';
                                        }else{
                                            $content .='<a href="/app/index.php/routes/default/createStep2?id='.$routeId.'&ClonedRouteId='.$newClonedRouteId.'&type='.$isEdit.'" class="cancel-button attachLoading z-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Back</span></a>';
                                        }    
                                            $content .='<a href="/app/index.php/routes/default" class="cancel-button" id="CancelLinkActionElement--30-yt2"><span class="z-label">Cancel</span></a>
                                            <a href="#"  class="attachLoading z-button" onclick="javascript:addRouteAgreement(\'' . $routeId .'\', this);" name="next" id="'.$newClonedRouteId.'"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Next</span></a>
                                        </div>
                                    </div>
                                </div>
                            </form>                            
                        </div>
                    </div>
                   </div>';
        return $content;
    }
    
    //Function to Save new route tracking for route
     public function SaveNewRouteTracking($routeTrackingProdData){
         if(!empty($routeTrackingProdData['addRouteTrackingDetails']['date_of_service'])){         
             $newRouteTracking         = new RouteTracking();   
             $newRouteTracking ->date_of_service = $routeTrackingProdData['addRouteTrackingDetails']['date_of_service'];
             $newRouteTracking ->service_start_time = $routeTrackingProdData['addRouteTrackingDetails']['service_start_time'];
             $newRouteTracking ->service_end_time = $routeTrackingProdData['addRouteTrackingDetails']['service_end_time'];
             $newRouteTracking ->route = Route::getById($routeTrackingProdData['addRouteTrackingDetails']['route_id']);
         }  
         if (!$newRouteTracking->save()) {
            throw new FailedToSaveModelException();
         }  else {
             //$newRouteTracking ->name = 'RT-'.$newRouteTracking->id;
             $newRouteTracking ->name = $routeTrackingProdData['addRouteTrackingDetails']['service_name'];
             $newRouteTracking->save();
             // To save in the agreement Tracking
             RoutesUtils::saveNewAgmtTracking($routeTrackingProdData, $newRouteTracking->id);
             RoutesUtils::SaveNewRouteTrackingProducts($routeTrackingProdData,$newRouteTracking->id);
         }
     }
     
     //Function to Save new route tracking products for route
     public function SaveNewRouteTrackingProducts($routeTrackingProdDatas,$newRouteTrackingId){
        foreach ($routeTrackingProdDatas['addRouteTrackingProdDetails'] as $routeTrackingProdData) {
            $consumed_unit = floatval($routeTrackingProdData['consumed_unit']);
             if(!empty($consumed_unit)){
                 $newRouteTrackingProd         = new RouteTrackingProducts();
                 $newRouteTrackingProd->agreementproduct   = AgreementProduct::getById(intval($routeTrackingProdData['agreement_product_id']));
                 $newRouteTrackingProd->consumed_unit   = $consumed_unit;
                 $newRouteTrackingProd->agreement   = Agreement::getById(intval($routeTrackingProdData['agreement_id']));
                 $newRouteTrackingProd->routetracking = RouteTracking::getById(intval($newRouteTrackingId));
                 if (!$newRouteTrackingProd->save()) {
                    throw new FailedToSaveModelException();
                 }
             }  
        }
     }
     
//     // To save the New Agreement Tracking
//     public function saveNewAgmtTracking($trackingInformation, $route_track_id) 
//     {         
//        foreach($trackingInformation['addRouteTrackingProdDetails'] as $routeProdVal)
//        {
//            if(!empty($routeProdVal)){
//                $NewRouteProdArr[$routeProdVal['agreement_id']][] = $routeProdVal;
//            }
//        }
//        
//        foreach($NewRouteProdArr as $agmt_key => $routeProdArr)
//        {
//            $agmt = Agreement::GetById(intval($agmt_key));
//            $newAgreementTracking               = new AgreementTracking();        
//            $newAgreementTracking->agreement    = $agmt;
//            $trackingDate = date('Y-m-d', strtotime($trackingInformation['addRouteTrackingDetails']['date_of_service']));
//            $newAgreementTracking->tracking_date                = $trackingDate;
//            $newAgreementTracking->total_non_agreement_products = 0;
//            $newAgreementTracking->routetracking = RouteTracking::getById($route_track_id);
//            $saveAgreementTracking = $newAgreementTracking->save();        
//        
//            $total_quantity_consumed    = 0;
//            $total_mhr_consumed         = 0;
//            $total_equipment_consumed   = 0;
//            $total_material_consumed    = 0;
//            $selected_products_count    = 0;
//        
//            if (isset($newAgreementTracking->id) && $newAgreementTracking->id > 0 && count($routeProdArr) > 0) {
//         
//                foreach ($routeProdArr as $trackingProducts) {
//                    $trackingProduct = new AgreementTrackingProducts();                
//                    if ($trackingProducts['consumed_unit'] > 0) {
//
//                        // To calculate the total quantity
//                        $total_quantity_consumed += round($trackingProducts['consumed_unit'],4);
//                        
//                        $agmt_prod = AgreementProduct::GetById(intval($trackingProducts['agreement_product_id']));
//
//                        if (strpos($agmt_prod->Product_Code, 'L') !== false) {
//                            $total_mhr_consumed += $trackingProducts['consumed_unit'];
//                        }
//
//                        if (strpos($agmt_prod->Product_Code, 'E') !== false) {
//                            $total_equipment_consumed += $trackingProducts['consumed_unit'];
//                        }
//
//                        if (strpos($agmt_prod->Product_Code, 'M') !== false) {
//                            $total_material_consumed += $trackingProducts['consumed_unit'];
//                        }
//                        
//                        $agmt_prod->Consumed_Units  = round($agmt_prod->Consumed_Units+$trackingProducts['consumed_unit'],4);
//                        $agmt_prod->save();
//
//                        $trackingProduct->agreementProduct          = $agmt_prod;
//                        $trackingProduct->agreement_tracking_id     = $newAgreementTracking->id;
//                        $trackingProduct->consumed_unit             = round($trackingProducts['consumed_unit'],4);
//                        $trackingProduct->is_agreement_product      = 1;
//                        $trackingProduct->agreement                 = $agmt;
//                        $trackingProduct->save();
//                        $selected_products_count++;
//                    }
//                }
//
//                $agreementTrackingObj = AgreementTracking::getById($newAgreementTracking->id);
//                // To save the other tracking details
//                //$agreementTrackingObj->name                     = 'AT'.$newAgreementTracking->id;
//                $agreementTrackingObj->total_selected_products  = $selected_products_count;
//                $agreementTrackingObj->total_quantity_consumed  = round($total_quantity_consumed,4);
//                $agreementTrackingObj->total_mhr                = round($total_mhr_consumed,4);
//                $agreementTrackingObj->total_material_units     = round($total_material_consumed,4);
//                $agreementTrackingObj->total_equipment_units    = round($total_equipment_consumed,4);
//                try{
//                   $agreementTrackingObj->save();
//                } catch (Exception $ex) {
//                    echo $ex->getMessage();
//                }
//
//                // To update the calculations in the agreement            
//                $agmt->Used_MHR                  = round($agmt->Used_MHR+$total_mhr_consumed,2);
//                $agmt->Total_Available_MHR       = round($agmt->Total_Available_MHR-$total_mhr_consumed,2);
//                $agmt->Used_Material             = round($agmt->Used_Material+$total_material_consumed,2);
//                $agmt->Available_Material        = round($agmt->Available_Material-$total_material_consumed,2);
//                $agmt->Used_Equipment            = round($agmt->Used_Equipment+$total_equipment_consumed,2);
//                $agmt->Available_Equipment       = round($agmt->Available_Equipment-$total_equipment_consumed,2);
//
//                $agmt->Year_to_Date_MHR          = round($agmt->Used_MHR,2);
//
//                if($agmt->Total_MHR > 0)
//                    $agmt->MHR_Used_Percentage   = round(($agmt->Used_MHR/$agmt->Total_MHR)*100, 2);
//
//                $agmt->Material_Year_To_Date     = round($agmt->Used_Material,2);
//
//                if($agmt->Total_Material > 0)
//                    $agmt->Material_Used_Percentage  = round(($agmt->Used_Material/$agmt->Total_Material)*100, 2);
//
//                $agmt->Equipment_Year_To_Date    = round($agmt->Used_Equipment,2);
//
//                if($agmt->Total_Equipment > 0)
//                    $agmt->Equipment_Used_Percentage = round(($agmt->Used_Equipment/$agmt->Total_Equipment)*100, 2);
//
//                if (!$agmt->save()) {
//                    throw new FailedToSaveModelException();
//                }
//            }            
//        }
//    }
    
      public function saveNewAgmtTracking($trackingInformation, $route_track_id) 
     {
        foreach($trackingInformation['addRouteTrackingProdDetails'] as $routeProdVal)
        {
            if(!empty($routeProdVal)){
                $NewRouteProdArr[$routeProdVal['agreement_id']][] = $routeProdVal;
                
                    $agmttest  = Agreement::GetById(intval($routeProdVal['agreement_id']));
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
                                $totalfinalamount +=	$optpdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)) ;
                            }
                       }
                    }
            }
        }
         
         $totalDirectCost=0;
        foreach($NewRouteProdArr as $agmt_key => $routeProdArr)
        {
//            // For the Current GPM calculation
//            $agreementTrackingCount = AgreementTracking::getCountByAgmtId($agmt_key);
//            if($agreementTrackingCount > 0)
//                $hasTracking = 1;
//            else
//                $hasTracking = 0;
//            // Ends Here
            
            $agmt = Agreement::GetById(intval($agmt_key));
            $newAgreementTracking               = new AgreementTracking();        
            $newAgreementTracking->agreement    = $agmt;
            $trackingDate = date('Y-m-d', strtotime($trackingInformation['addRouteTrackingDetails']['date_of_service']));
            $newAgreementTracking->tracking_date                = $trackingDate;
            $newAgreementTracking->total_non_agreement_products = 0;
            $newAgreementTracking->routetracking = RouteTracking::getById($route_track_id);
            $saveAgreementTracking = $newAgreementTracking->save();        
        
            $total_quantity_consumed    = 0;
            $total_mhr_consumed         = 0;
            $total_equipment_consumed   = 0;
            $total_material_consumed    = 0;
            $selected_products_count    = 0;
        
            if (isset($newAgreementTracking->id) && $newAgreementTracking->id > 0 && count($routeProdArr) > 0) {
         
                foreach ($routeProdArr as $trackingProducts) {
                    $trackingProduct = new AgreementTrackingProducts();                
                    if ($trackingProducts['consumed_unit'] > 0) {

                        // To calculate the total quantity
                        $total_quantity_consumed += round($trackingProducts['consumed_unit'],4);
                        
                        $agmt_prod = AgreementProduct::GetById(intval($trackingProducts['agreement_product_id']));

                        if (strpos($agmt_prod->Product_Code, 'L') !== false) {
                            $total_mhr_consumed += $trackingProducts['consumed_unit'];
                            
                            $costcatalog=  Costbook::getByProductCode($agmt_prod->Product_Code);
                        
                            if(!empty($costcatalog)){
                                $burdenCost = $costcatalog[0]->burdenCost;
                                $laborcost = $costcatalog[0]->laborCost;
                            }
 
                            $totalDirectCost    +=  ($burdenCost + $laborcost)* $trackingProducts['consumed_unit'];
                        }

                        if (strpos($agmt_prod->Product_Code, 'E') !== false) {
                            $total_equipment_consumed += $trackingProducts['consumed_unit'];
                            
                            $costcatalog=  Costbook::getByProductCode($agmt_prod->Product_Code);
                        
                            if(!empty($costcatalog)){
                                $costperunit = $costcatalog[0]->costperunit;
                            }
                            
                            $totalDirectCost    += $costperunit * $trackingProducts['consumed_unit'];
                        }

                        if (strpos($agmt_prod->Product_Code, 'M') !== false) {
                            $total_material_consumed += $trackingProducts['consumed_unit'];
                            
                            $costcatalog=  Costbook::getByProductCode($agmt_prod->Product_Code);
                        
                            if(!empty($costcatalog)){
                                $costperunit = $costcatalog[0]->costperunit;
                            }
                            
                            $totalDirectCost    += $trackingProducts['consumed_unit'] * $costperunit;
                        }
                        
                        $agmt_prod->Consumed_Units  = round($agmt_prod->Consumed_Units+$trackingProducts['consumed_unit'],4);
                        $agmt_prod->save();

                        $trackingProduct->agreementProduct          = $agmt_prod;
                        $trackingProduct->agreement_tracking_id     = $newAgreementTracking->id;
                        $trackingProduct->consumed_unit             = round($trackingProducts['consumed_unit'],4);
                        $trackingProduct->is_agreement_product      = 1;
                        $trackingProduct->agreement                 = $agmt;
                        $trackingProduct->save();
                        $selected_products_count++;
                    }
                }
//                            $newCurrent_GPM=(($totalfinalamount-$totalDirectCost)/$totalfinalamount)*100;
//                            
//                            if($hasTracking == 1)
//                                $newCurrent_GPMold = $agmt->newCurrent_GPM;
//                            else 
//                                $newCurrent_GPMold = 0;
//                            
//                            $newCurrent_GPMround=$newCurrent_GPMold+$newCurrent_GPM;
//                            $agmt->newCurrent_GPM  =  round($newCurrent_GPMround,2);
//                            $agmt->save();
                
                
                
                //For the Current GPM calculation
            $AgmtProductGpm =  AgreementProduct::getAgreementProdByAgreementId($agmt_key);
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
           $agmt->newCurrent_GPM  =round($newCurrent_GPM,2);
           $agmt->save();
                            

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
                $agmt->Used_MHR                  = round($agmt->Used_MHR+$total_mhr_consumed,2);
                $agmt->Total_Available_MHR       = round($agmt->Total_Available_MHR-$total_mhr_consumed,2);
                $agmt->Used_Material             = round($agmt->Used_Material+$total_material_consumed,2);
                $agmt->Available_Material        = round($agmt->Available_Material-$total_material_consumed,2);
                $agmt->Used_Equipment            = round($agmt->Used_Equipment+$total_equipment_consumed,2);
                $agmt->Available_Equipment       = round($agmt->Available_Equipment-$total_equipment_consumed,2);

                $agmt->Year_to_Date_MHR          = round($agmt->Used_MHR,2);

                if($agmt->Total_MHR > 0)
                    $agmt->MHR_Used_Percentage   = round(($agmt->Used_MHR/$agmt->Total_MHR)*100, 2);

                $agmt->Material_Year_To_Date     = round($agmt->Used_Material,2);

                if($agmt->Total_Material > 0)
                    $agmt->Material_Used_Percentage  = round(($agmt->Used_Material/$agmt->Total_Material)*100, 2);

                $agmt->Equipment_Year_To_Date    = round($agmt->Used_Equipment,2);

                if($agmt->Total_Equipment > 0)
                    $agmt->Equipment_Used_Percentage = round(($agmt->Used_Equipment/$agmt->Total_Equipment)*100, 2);

                if (!$agmt->save()) {
                    throw new FailedToSaveModelException();
                }
            }            
        }
       
        
                    
    }
    
    public function routeTrackingDetail($routeTrackingId){
        $content = RoutesUtils::routeTrackingInfo($routeTrackingId);
        $content .= RoutesUtils::routeTrackingProductInfo($routeTrackingId);
        $content .= '</div></div></div></div></div></div></div></div></div></div>';
        return $content;
    }
    
    public function routeTrackingInfo($routeTrackingId){
        $routeTracking = RouteTracking::getById($routeTrackingId);
        $content ='
            <div class="StarredListView SecuredListView ListView ModelView ConfigurableMetadataView MetadataView">
                        <div class="cgrid-view type-routes">
                            <div class="items-wrapper">
                                <div class="wrapper">
                                    <h1>
                                        <span class="truncated-title" threedots="Create Costbook"><span class="ellipsis-content">'.$routeTracking->name.'</span></span>
                                    </h1>
                                    <div style="margin-left:42%;margin-top:2%;">
                                        <a class="attachLoading cancel-button" id="Cancel" href="/app/index.php/routes/default/details?id='.$routeTracking->route->id.'" style="margin-left:1%;">
                                            <span class="z-label" style = "color:#7CB830">
                                                Go Back
                                            </span>
                                        </a>
                                    </div>    
                                    <div class="wide form">
                                        <div class="attributesContainer">
                                            <div class="left-column" style="width:100%; padding-top: 0px;">
                                            <div class="panel">
                                                <div class="panelTitle" style = "padding-left: 0px; padding-top: 0px;">Route Tracking Information</div>
                                                <table class="form-fields double-column">
                                                   <colgroup>
                                                      <col class="col-0">
                                                      <col class="col-1">
                                                      <col class="col-2">
                                                   </colgroup>
                                                   <tbody>
                                                      <tr>
                                                         <th class="route-bold" style="text-align: right;">Route Tracking Name  </th>
                                                         <td style="padding-left: 10px;"><b>'.$routeTracking->name.'</b></td>
                                                      </tr>
                                                      <tr>
                                                         <th class="route-bold" style="text-align: right;">Date Of Service  </th>
                                                         <td colspan="1" style="padding-left: 10px;"><b>'.$routeTracking->date_of_service.'</b></td>
                                                      </tr>
                                                      <tr>
                                                         <th class="route-bold" style="text-align: right;">Service Start Time  </th>
                                                         <td colspan="1" style="padding-left: 10px;"><b>'.$routeTracking->service_start_time.'</b></td>
                                                      </tr>
                                                      <tr>
                                                         <th class="route-bold" style="text-align: right;">Service End Time  </th>
                                                         <td colspan="1" style="padding-left: 10px;"><b>'.$routeTracking->service_end_time.'</b></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                            </div>
                                            ';
        return $content;

    }
    
    public function routeTrackingProductInfo($routeTrackingId){
        $routeTrackingProds = RouteTrackingProducts::getTrackingProdByRouteTrackingId($routeTrackingId);
        $agreementPdctDetails = array();
        $agreementPdctIds = array();
        $agmt_name_arr = array();
        $agmt_id_arr = array();
        $agreementPdctMap = array();
        $countOfSelectedRow = 0;
        $CategoryKeyCount = 0;
        foreach ($routeTrackingProds as $routeTrackingProd) {
            $agmt_name_arr[$routeTrackingProd->agreement->id] = $routeTrackingProd->agreement->name;
            $agmt_id_arr[$routeTrackingProd->agreement->id] = $routeTrackingProd->agreement->id;
            $agreementPdctMap[$routeTrackingProd->agreementproduct->Category][$routeTrackingProd->agreementproduct->costbook->productcode.'-'.$routeTrackingProd->agreementproduct->Assembly_Product_Code] = $routeTrackingProd;
            $agreementPdctConsumed[$routeTrackingProd->agreementproduct->Category][$routeTrackingProd->agreementproduct->costbook->productcode.'-'.$routeTrackingProd->agreementproduct->Assembly_Product_Code][$routeTrackingProd->agreement->id] = $routeTrackingProd->consumed_unit;
        } 
        $i =1;
        $content = '<div class="panel details-table">
                        <div class="panelTitle" style="padding-left: 0px;">Route Location Informations</div>
                            <table class="form-fields">                                
                                <tbody><tr>';
                            foreach ($agmt_name_arr as $agmt_id => $agmt_name) {
                                $content .='<td>' .$i.'. '. $agmt_name. '</td>';

                                if($i%3 ==0)
                                {
                                    $content .='</tr><tr>';
                                }                                    
                                $i++;   
                             }  
                                     $content .='</td></tr></tbody>
                             </table>                                 
                        </div>';
        
        $content .='<div id="RoutesListView" class="SecuredListView ListView ModelView ConfigurableMetadataView MetadataView">
                    <div id="list-view" class="cgrid-view type-routes">
                    <div class="items-wrapper">
                        <table class="items">
                            <tbody>
                                <tr>
                                <th width="100px">Product Code</th>
                                <th width="100px">Assembly</th>
                                <th width="100px">Product</th>
                                <th width="100px">UOM</th>';
        $j=1;
        foreach ($agmt_name_arr as $agmt_id => $agmt_name) {
            $content .='<th width="75px;" style="cursor: pointer;" title="'.$agmt_name.'">' . $j. '</th>';
            //$content .='<th width="100px">' . $agmt_name. '</th>';
            $j++;
        }
        $content .='</tr>';
        $column_count = 4+count($agmt_id_arr);
        foreach ($agreementPdctMap as $CategoryKey => $agreementArray) {
            $content .='<tr>
                            <td colspan="'.$column_count.'" class="align_left" style="background-color:gray; color:white;">' . $CategoryKey . ' </td>
                        </tr>';
            foreach ($agreementArray as $agreementKey => $agreementpdt) {
                $agreementProduct = 1;                        
                $content .='<tr>
                                <td width="100px">' . $agreementpdt->agreementproduct->costbook->productcode . '</td>
                                <td width="100px">' . $agreementpdt->agreementproduct->Assembly_Product_Code . '</td>
                                <td width="100px">' . $agreementpdt->agreementproduct->name . '</td>
                                <td width="100px">' . $agreementpdt->agreementproduct->costbook->unitofmeasure . '</td>';
                                foreach ($agmt_id_arr as $agmt_id) {
                                    if(isset($agreementPdctConsumed[$CategoryKey][$agreementKey][$agmt_id])){
                                       $content .='<td width="100px"> '.$agreementPdctConsumed[$CategoryKey][$agreementKey][$agmt_id].'</td>';
                                    }
                                    else{
                                        $content .='<td width="100px"></td>';
                                    }
                                }                                  
                $content .=' </tr>';
                $countOfSelectedRow++;
            }

            $CategoryKeyCount++;
        }
            $content .='</tbody></table>';
            return $content;
    }


}   
