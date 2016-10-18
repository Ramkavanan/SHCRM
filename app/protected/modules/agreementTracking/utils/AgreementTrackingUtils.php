<?php

/**
 * Class utilized by opportunity product selection
 * 
 * @author Ramachandran.K 
 */
class AgreementTrackingUtils {
    
    public function addProductFindDuplicate($category, $addProductoptId, $productCode){
        $opptproducts = AgreementProduct::getagmntProductByproductCode($category,$addProductoptId, $productCode);  
        if($opptproducts != null){
            return TRUE;
        }else {
            return FALSE;
        }
    }

    public function makeCostBookProductSelection($datas, $opportunityId) {

        $content = AgreementProductUtils::appendButton($opportunityId);

        $content .= '<hr/><div class="cgrid-view type-opportunityProducts" id="list-view">
				<div class="summary">5 result(s)</div>
				<div class="items-wrapper">
				<table class="items">
				<thead>
				<tr>
				<th class="checkbox-column" id="list-view-rowSelector"><label class="hasCheckBox"><input value="1" name="list-view-rowSelector_all" id="list-view-rowSelector_all" type="checkbox"></label></th><th id="list-view_c1">Product Code</th><th id="list-view_c2"><a class="sort-link" href="/app/index.php/opportunityProducts/default/index?OpportunityProduct_sort=name">Product Name</a></th><th id="list-view_c3">Unit of Measure</th>
				<th id="list-view_c4">Unit Direct Cost</th><th id="list-view_c5">Quantity</th><th id="list-view_c6">Frequency</th><th id="list-view_c7">Category</th>
				</tr>
				</thead>
				<tbody>';
        $count = 0;
        $content1 = '';
        foreach ($datas as $data) {
            $content .= '<tr class="odd"><td class="checkbox-column"><label class="hasCheckBox"><input value="' . $data->id . '" id="list-view-rowSelector_' . $count . '" name="list-view-rowSelector[]" type="checkbox"></label></td><td>' . $data->productcode . '</td><td>' . $data->productkey . '</td><td>' . $data->unitofmeasure . '</td><td>' . $data->unitdirectcost . '</td><td><input type="text" id="quantity_' . $count . '" value="1.0"></td><td><input type="text" id="frequency_' . $count . '" value="1"></td><td>' . $data->category . '</td></tr>';
            $count++;
        }
        $content .= '</tbody></table><input value="" name="list-view-selectedIds" id="list-view-selectedIds" type="hidden"></div></div>';
        return $content;
    }

    public function appendButton($opportunityId) {
        $content = "<div class='view-toolbar-container clearfix'> </div>";
        $content1 = '<div class="view-toolbar-container clearfix"><nav class="clearfix"><div class="default-button" id="CreateMenuActionElement--yt1"><a href="#" onclick="javascript:addProductInOpportunity(\'' . $opportunityId . '\');" class="button-action"><i class="icon-create"></i><span class="button-label">Create</span></a></div>	</nav></div>';
        return $content;
    }

    public function addAgreementProductsCalculation(Costbook $costbook, $quantity, $frequency, $optId) {
        $tQty = 0;
        $opportunityProduct = new OpportunityProduct();
        $opportunityProduct->Quantity = (float) $quantity;
        $opportunityProduct->Frequency = (float) $frequency;
        $opportunityProduct->name = 'Costbook - ' . $costbook->id;

        //$opportunityProduct->Product = $costbook;
        $tQty = (float) $quantity;
        if ($opportunityProduct->Frequency > 0) {
            $tQty *= $opportunityProduct->Frequency;
        }

        //Labor Product calculation
        if ($costbook->costofgoodssold == 'Labor') {
            $currencies = Currency::getAll();
            $opportunityProduct->Total_MHR = round($costbook->costperunit * $tQty);
            if ($costbook->departmentreference != null) {
                $deptReference = DepartmentReference::GetById(intval($costbook->departmentreference));
                $burdenCost = new CurrencyValue();
                $burdenCost->value = round(($deptReference->burdonCost * $tQty), 2);
                $burdenCost->currency = $currencies[0];
                $opportunityProduct->Burden_Cost = $burdenCost;
                $laborCost = new CurrencyValue();
                $laborCost->value = round(($deptReference->laborCost * $tQty), 2);
                $laborCost->currency = $currencies[0];
                $opportunityProduct->Labor_Cost = $laborCost;
            }
        }
        $opportunityProduct->Category_GPM = 40;
        $totalDirectCost = new CurrencyValue();
        $totalDirectCost->value = round(($opportunityProduct->Labor_Cost->value + $opportunityProduct->Burden_Cost->value), 2);
        $totalDirectCost->currency = $currencies[0];
        $opportunityProduct->Total_Direct_Cost = $totalDirectCost;
        $finalCost = new CurrencyValue();
        $finalCost->value = round(($opportunityProduct->Total_Direct_Cost->value / (1 - ($opportunityProduct->Category_GPM / 100))), 2);
        $finalCost->currency = $currencies[0];
        $opportunityProduct->Final_Cost = $finalCost;
        $opportunityProduct->opportunity = $opportunity = Opportunity::GetById(intval($optId));
        $opportunityProduct->save();
    }

    public function makeAgreementTrackingView($agreementId) {
        
        $categories = Category::getAll();
        $TotalDirectCost = 0;
        $content = '<div>';
        $content .= AgreementTrackingAddView::renderScripts();
        $agreementProducts = AgreementProduct::getAllByAgmntId(intval($agreementId));
        $agreement = Agreement::getById(intval($agreementId));
        $opportunityId = $agreement->opportunity->id;
        $countOfSelectedRow = 0;
        $pageOffset = 1;
        $productName = NULL;
        if (isset($agreement)) {   
            $content .= '<div class="wrapper">
      <h1><span class="truncated-title" threedots="Create AgreementTracking"><span class="ellipsis-content">Create AgreementTracking</span></span></h1>
      <div class="wide double-column form">
         <form method="post" action="/app/index.php/agreementTracking/default/create?agreementId=3003" id="edit-form" onsubmit="js:return $(this).attachLoadingOnSubmit(&quot;edit-form&quot;)">
            <div style="display:none"><input type="hidden" name="YII_CSRF_TOKEN" value="9b75d8e35f7f7044e3e7ebe06b387659577b83db"></div>
            <div class="attributesContainer">
               <div class="left-column full-width">
                  <div class="panel">
                     <div class="panelTitle">Agreement Information</div>
                     <table class="form-fields double-column">
                        <colgroup>
                           <col class="col-0">
                           <col class="col-1">
                           <col class="col-2">
                        </colgroup>
                        <tbody>
                           <tr>
                              <th><label for="AgreementTracking_name_id">Agreement Name</label></th>
                              <td colspan="1">
                                 <input type="hidden" id="AgreementTracking_name_id" name="AgreementTracking[name][id]" value="'. $agreement->id.'">
                                 <div class="has-model-select" style ="width:50%"><input type="text" name="AgreementTracking_name_name" value="'. $agreement->name.'" id="AgreementTracking_name_name" disabled ></div>
                                 <ul class="ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all" id="ui-id-3" tabindex="0" style="z-index: 11; display: none;"></ul>
                              </td>
                           </tr>
                           <tr>
                              <th><label for="AgreementTracking_name">Tracking Name</label></th>
                              <td colspan="1">                                 
                                 <div class="has-model-select" style ="width:50%"><input type="text" name="Agreement_Tracking_name" value="" id="Agreement_Tracking_name" ></div>
                             </td>
                           </tr>
                           <tr>
                              <th><label for="AgreementTracking_tracking_date">Tracking Date<span class="required">*</span></label></th>
                              <td colspan="1">
                                 <div class="has-date-select" style ="width:50%">
                                    <input type="text" name="AgreementTracking[tracking_date]" id="AgreementTracking_tracking_date">
                                 </div>
                                 <div class="errorMessage"style="display:none;" id="AgreementTracking_Date">Tracking Date cannot be blank.</div>
                              </td>
                            </tr>
                            <tr>
                              <th><label for="AgreementTracking_total_non_agreement_selected_products">Add-on Sales </label></th>
                              <td colspan="1"><input type="hidden" name="AgreementTracking[total_non_agreement_selected_products]" value="0" id="ytAgreementTracking_total_non_agreement_selected_products"><label class="hasCheckBox"><input type="checkbox" value="1" name="AgreementTracking[total_non_agreement_selected_products]" id="AgreementTracking_total_non_agreement_selected_products"></label></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <div class="float-bar">
               <div class="view-toolbar-container clearfix dock">
                  <div class="form-toolbar"><a href="/app/index.php/agreements/default/details?id='.$agreementId.'" class="cancel-button" id="CancelLinkActionElement--23-yt2"><span class="z-label">Go Back</span></a><a href="#" return false;" class="attachLoading z-button" name="save" id="saveAgreementTracking" onclick="javascript:addAgreementTracking(\''.$agreementId.'\', this);"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Save</span></a></div>
               </div>
            </div>
         </form>
         <div id="modalContainer-edit-form"></div>
      </div>
   </div> <div class="view-toolbar-container clearfix"> </div>';

            if (count($agreementProducts) > 0) {
                $content .= '<div id="selected_products" class="table_border_width" style="padding: 0px;">
                                    
                                        <div class="align_left" style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold;">
                                             Select Agreement Products <span id="showresults" style="color:green; font-weight:none;"></span>
                                        </div>';
                $content .='<table class="items selected_products_table">
                                                <tr style="color:black; padding:0.5%;">
                                                    <th>Product Code</th>
                                                    <th>Product Name</th>
                                                    <th>Assembly Code</th>
                                                    <th>Unit of Measure</th>
                                                    <th>Consumed</th>
                                                    <th>Total Units</th>
                                                    <th>Remaining Units</th>
                                                    <th colspan="8">Completed</th>
                                    </tr> <input value="" name = "list-view-selectedIds" id = "list-view-selectedIds" type = "hidden">';
                
                foreach ($agreementProducts as $row) {
                    $agreementPdctMap[$row->Category][] = $row;
                }
                
                $CategoryKeyCount = 0;
                $totalMhr = 0;
                $totalDirectCost = 0;
                $totalFinalPrice = 0.0;
                $actualGPM = 0;
                $totalRevenue = 0.0;
                foreach ($agreementPdctMap as $CategoryKey => $agreementArray) {
                    $content .='<tr>
                                    <th colspan="10" class="align_left" style="background-color:gray; color:white;">' . $CategoryKey . ' </th>
                                        <th colspan="1" style="background-color:gray; color:white;"> <input type="checkbox" name="CompletedCategory" id="AgreementTrackingCompleted_' . $CategoryKeyCount . '" value="'. $CategoryKey.'" style=""> </th>
                                    <input type="hidden" name="CategoryName" id="CategoryKey_' . $CategoryKeyCount . '" value="' . $CategoryKey . '">
                                        
                                </tr>';
                    foreach ($agreementArray as $agreementKey => $agreementpdt) {
                        $agreementProduct = 1;
                        $consumedUnits = round($agreementpdt->Consumed_Units, 4);
                        if(isset($agreementpdt->Frequency)){
                            $totalavailableUnits = round($agreementpdt->Quantity,4) * round($agreementpdt->Frequency,4);
                            //$remainingUnits = bcsub($totalavailableUnits, $consumedUnits, 4);
                            $remainingUnits = $totalavailableUnits-$consumedUnits;
                        }else{
                            $totalavailableUnits = round($agreementpdt->Quantity,4);
                            //$remainingUnits = bcsub($totalavailableUnits, $consumedUnits, 4);
                            $remainingUnits = $totalavailableUnits-$consumedUnits;
                        }
                        $content .='<tr>
                                        <td>' . $agreementpdt->costbook->productcode . '</td>
                                        <input value=' . $agreementpdt->costbook->productcode . ' name="productCode" id="productCode_' . $countOfSelectedRow . '" type="hidden">    
                                        <input value=' . $agreementpdt->id . ' name="agreement_product_id" id="agreement_product_id_' . $countOfSelectedRow . '" type="hidden">
                                        <input value=' . $agreementpdt->id . ' name="agreement_product_id" id="agreement_category_product_' . $CategoryKeyCount . '" type="hidden">
                                        <td>' . $agreementpdt->name . '</td>
                                        <td>' . $agreementpdt->Assembly_Product_Code . '</td>
                                        <td>' . $agreementpdt->costbook->unitofmeasure . '</td>
                                        <td> <input type="text" id="consumed_unit_' . $countOfSelectedRow . '" value=0></td>
                                        <td>' . $totalavailableUnits.'</td>    
                                        <td>' . round($remainingUnits,4) . '</td>
                                        <input value='.$agreementProduct.' name="is_agreement_product" id="is_agreement_product_' . $countOfSelectedRow . '" type="hidden">
                                        <td colspan="8">  </td>
                                    </tr>';
                        $countOfSelectedRow++;
                    }
                    
                    $CategoryKeyCount++;
                }
                if ($totalFinalPrice > 0) {
                    $actualGPM = (($totalFinalPrice - $totalDirectCost) / $totalFinalPrice) * 100;
                }
                if ($totalMhr > 0) {
                    $totalRevenue = $totalFinalPrice / $totalMhr;
                }

                $content .='<input value="' . $countOfSelectedRow . '" name="Selected_Products_Ids" id="Selected_Products_Ids" type="hidden">';
                $content .=' </table></div></td></tr></table></div>
                            ';
            }
            $content .='<div class="table_border_width" id="add_product_search" style="padding: 0px; display:none;">
                                         <div class="panel">
                                            <div class="align_left" style="color:black; background-color:#E0D1D1; color:black; padding:0.5%; font-weight:bold;">Search</div>
                                            <table class="form-fields items">
                                                <colgroup><col class="col-0"><col class="col-1"></colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th width="20%">
                                                            <label for="agreement_AddProductcategory_value">Select Category</label>
                                                        </th>
                                                        <td colspan="1">
                                                            <div class="hasDropDown">
                                                                <span class="select-arrow"></span>
                                                                    <select id="agreement_AddProductcategory_value" name="Costbook[assemblycategory][value]">
                                                                        <option value="All">All</option>';
            foreach ($categories as $values) {
                $content .= '<option value="' . $values->name . '">' . $values->name . '</option>';
            }
            $content .= '</select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <label for="costofgoodssoldassembly">Select COGS</label>
                                                        </th>
                                                        <td colspan="1">
                                                            <div class="hasDropDown">
                                                                <span class="select-arrow"></span>
                                                                    <select id="agreement_AddProductcostofgoodssold_value" name="Costbook[costofgoodssoldassembly][value][assemblycategory][value]">
                                                                        <option selected="selected" value="All">All</option>
                                                                        <option value="Labor">Labor</option>
                                                                        <option value="Equipment">Equipment</option>
                                                                        <option value="Material">Material</option>
                                                                        <option value="Subcontractor">Subcontractor</option>
                                                                        <option value="Other">Other</option>            
                                                                    </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <label for="costofgoodssoldassembly">Search By Product Name</label>
                                                        </th>
                                                        <td colspan="1"><input type="text" style="" value="" name="productname" id="agreement_productname_value" onkeyup="javascript:searchNonAgreementProducts(\''.$agreementId.'\',\''.$pageOffset.'\',\''.$productName.'\', this);"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="costofgoodssoldassembly"></label>
                                                        </td>
                                                        <td colspan="1">
                                                        <div style="margin-left:32%;">    
                                                            <a id="search" onclick="javascript:searchNonAgreementProducts(\''.$agreementId.'\',\''.$pageOffset.'\',\''.$productName.'\', this);" class="attachLoading cancel-button" name="Search" href="#">
                                                                <span class="z-spinner"></span>
                                                                <span class="z-icon"></span>
                                                                <span class="z-label">Search</span>
                                                            </a>
                                                        </div>    
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                         </div>
                                         <div class="items-wrapper" id="addProductWrapper">
                                               <div id="agmntTrackingSearchProducts" style="display : none;">
                                                    <div id="search_title" style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div>
                                                    <table class="items selected_products_table">
                                                        <thead>
                                                            <tr>
                                                                <th id="list-view-rowSelector" class="checkbox-column">  <label class="hasCheckBox">  <input type="checkbox" id="list-view-rowSelector_all" name="list-view-rowSelector_all" value="1"> </label>  </th>
                                                                <th style="width:15%;" id="list-view_c1"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'code\', \'asc\');">  Product Code </a> </th>
                                                                <th style="width:25%;" id="list-view_c2"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'name\', \'asc\');">  Product Name  </a> </th>
                                                                <th style="width:15%;" id="list-view_c3"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'unit\', \'asc\');"> Unit of Measure </a> </th>
                                                                <th style="width:10%;" id="list-view_c4"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'cost\', \'asc\');"> Unit Direct Cost </a> </th>
                                                                <th style="width:15%;" id="list-view_c5"> Quantity </th>
                                                                <th style="width:20%;" id="list-view_c7"> Category </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="7" style = "padding: 0px;">
                                                                    <div id="agmntTracking_scroll_result">
                                                                        <table>
                                                                        </table>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>   
                                            </div>
                                         
                                         
                                     </div>';
            //$content .= AgreementTrackingUtils::appendDynamicBtns($agreementId);
            $content .='<div class="items-wrapper" id="addProductWrapper">
                       <input type="hidden" id="selectedProductCnt" value="' . $countOfSelectedRow . '" />
                       <div id="searchNonAgreementProducts"><a id="" href="searchResult"></a> </div>';
        }

        $count = 0;
        $content1 = '';

        return $content;
    }
    
    
    public function makeAgreementTrackingEditView($agreementTrackingId){
        $countOfSelectedRow = 0;
        $pageOffset = 1;
        $productName = NULL;
        $categories = Category::getAll();
        $agreementTracking = AgreementTracking::getById(intval($agreementTrackingId));
        $agreementTrackingProducts = AgreementTrackingProducts::getAgreementTrackingProductByTrackingId($agreementTrackingId);
        
        $agreementId = $agreementTracking->agreement->id;
        
        $content = '<div class="wrapper">
            <h1><span class="truncated-title" threedots=""><span class="ellipsis-content">'.$agreementTracking->name.'</span></span></h1>
            <div class="wide form">
               <form method="post" action="/app/index.php/agreementTracking/default/edit?id=1" id="edit-form" onsubmit="js:return $(this).attachLoadingOnSubmit(&quot;edit-form&quot;)">
                  <div style="display:none"><input type="hidden" name="YII_CSRF_TOKEN" value="9fe522528379372527f8422723f4beb32d1a3354"></div>
                  <div class="attributesContainer">
                     <div class="left-column full-width">
                        <div class="panel">
                           <div class="panelTitle">Agreement Tracking Detail</div>
                           <table class="form-fields">
                              <colgroup>
                                 <col class="col-0">
                                 <col class="col-1">
                              </colgroup>
                              <tbody>                                 
                                 <tr>
                                    <th><label for="AgreementTracking_name">Tracking Name</label></th>
                                    <td colspan="1">                                 
                                        <div class="has-model-select" style ="width:50%">';
                if($agreementTracking->name == 'AT'.$agreementTracking->id)
                    $content .= '<input type="text" name="Agreement_Tracking_name" value="" id="Agreement_Tracking_name" >';
                else
                    $content .= '<input type="text" name="Agreement_Tracking_name" value="'.$agreementTracking->name.'" id="Agreement_Tracking_name" >';

                                        $content .= '</div>
                                    </td>
                                 </tr>
                                 <tr>
                                    <th><label for="AgreementTracking_tracking_date">Tracking Date</label></th>
                                    <td colspan="1">
                                        <div class="has-date-select">
                                           <input type="text" name="AgreementTracking[tracking_date]" value="'.$agreementTracking->tracking_date.'" id="AgreementTracking_tracking_date">
                                        </div>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="float-bar">
                     <div class="view-toolbar-container clearfix dock">
                        <div class="form-toolbar"><a href="/app/index.php/agreementTracking/default/details?id='.$agreementTrackingId.'" class="cancel-button" id="CancelLinkActionElement-1-yt2"><span class="z-label">Cancel</span></a><a href="#" class="attachLoading z-button" name="save" id="updateAgreementTracking" onclick="javascript:updateAgreementTracking(\''.$agreementTrackingId.'\', this);"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Save</span></a></div>
                     </div>
                  </div>
               </form>
               <div id="modalContainer-edit-form"></div>
            </div>
         </div> <div class="view-toolbar-container clearfix"> </div>';
        
            if (count($agreementTrackingProducts) > 0) {
                $content .= '<div id="selected_products" class="table_border_width" style="padding: 0px;">
                                    <input value=' . $agreementTrackingId . ' name="agreement_tracking_id" id="agreement_tracking_id" type="hidden">    
                                        <div class="align_left" style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold;">
                                             Tracked Products <span id="showresults" style="color:green; font-weight:none;"></span>
                                        </div>';
                $content .='<table class="items selected_products_table">
                                                <tr style="color:black; padding:0.5%;">
                                                    <th>Product Code</th>
                                                    <th>Product Name</th>
                                                    <th>Quantity Consumed</th>
                                                    <th>Agreement Product</th>
                                                </tr>';
           
                foreach ($agreementTrackingProducts as $row) {
                    $agreementpdt = $row->agreementProduct;
                    $agreementTrackingProduct[$agreementpdt->Category][] = $row;
                }
                
                $CategoryKeyCount = 0;
                $totalMhr = 0;
                $totalDirectCost = 0;
                $totalFinalPrice = 0.0;
                $actualGPM = 0;
                $totalRevenue = 0.0;
                foreach ($agreementTrackingProduct as $CategoryKey => $agreementArray) {
                    
                    $content .='<tr>
                                    <th colspan="10" class="align_left" style="background-color:gray; color:white;">' . $CategoryKey . ' </th>
                                    <input type="hidden" name="CategoryKey" id="CategoryKey_' . $CategoryKeyCount . '" value="' . $CategoryKey . '">
                                        
                                </tr>';
                    foreach ($agreementArray as $agreementKey => $agreementpdt) {
                        $agreementrackingpdt = $agreementpdt->agreementProduct;
                        $agreementProduct = 1;
                        $consumedUnits = 0;
                        $isNonAgreement = 'Yes';
                        if($agreementpdt->is_agreement_product == 0)
                            $isNonAgreement = 'No';
                        $content .='<tr>
                                        <td>' . $agreementrackingpdt->costbook->productcode . '</td>
                                        
                                        <input value=' . $agreementrackingpdt->id . ' name="agreement_product_id" id="agreement_product_id_' . $countOfSelectedRow . '" type="hidden">
                                        <td>' . $agreementrackingpdt->name . '</td>
                                        <td> <input type="text" id="consumed_unit_' . $countOfSelectedRow . '" value=' . $agreementpdt->consumed_unit . '></td>
                                        <input value='.$agreementProduct.' name="is_agreement_product" id="is_agreement_product_' . $countOfSelectedRow . '" type="hidden">
                                         <input value='.$agreementpdt->id.' name="agreement_tracking_product_id" id="agreement_tracking_product_id' . $countOfSelectedRow . '" type="hidden">   
                                        <td> '.$isNonAgreement.'</td>
                                    </tr>';
                        $countOfSelectedRow++;
                    }
                    
                    $CategoryKeyCount++;
                }
                if ($totalFinalPrice > 0) {
                    $actualGPM = (($totalFinalPrice - $totalDirectCost) / $totalFinalPrice) * 100;
                }
                if ($totalMhr > 0) {
                    $totalRevenue = $totalFinalPrice / $totalMhr;
                }

                $content .='<input value="' . $countOfSelectedRow . '" name="Selected_Products_Ids" id="Selected_Products_Ids" type="hidden">';
                $content .=' </table></div></td></tr></table>';
            }
            $content .='<div class="table_border_width" id="add_product_search" style="padding: 0px; display:block;">
                                         <div class="panel">
                                            <div class="align_left" style="color:black; background-color:#E0D1D1; color:black; padding:0.5%; font-weight:bold;">Search</div>
                                            <table class="form-fields items">
                                                <colgroup><col class="col-0"><col class="col-1"></colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th width="20%">
                                                            <label for="agreement_AddProductcategory_value">Select Category</label>
                                                        </th>
                                                        <td colspan="1">
                                                            <div class="hasDropDown">
                                                                <span class="select-arrow"></span>
                                                                    <select id="agreement_AddProductcategory_value" name="Costbook[assemblycategory][value]">
                                                                        <option value="All">All</option>';
            foreach ($categories as $values) {
                $content .= '<option value="' . $values->name . '">' . $values->name . '</option>';
            }
            $content .= '</select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <label for="costofgoodssoldassembly">Select COGS</label>
                                                        </th>
                                                        <td colspan="1">
                                                            <div class="hasDropDown">
                                                                <span class="select-arrow"></span>
                                                                    <select id="agreement_AddProductcostofgoodssold_value" name="Costbook[costofgoodssoldassembly][value][assemblycategory][value]">
                                                                        <option selected="selected" value="All">All</option>
                                                                        <option value="Labor">Labor</option>
                                                                        <option value="Equipment">Equipment</option>
                                                                        <option value="Material">Material</option>
                                                                        <option value="Subcontractor">Subcontractor</option>
                                                                        <option value="Other">Other</option>            
                                                                    </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            <label for="costofgoodssoldassembly">Search By Product Name</label>
                                                        </th>
                                                        <td colspan="1"><input type="text" style="" value="" name="productname" id="agreement_productname_value" onkeyup="javascript:searchNonAgreementProducts(\''.$agreementId.'\',\''.$pageOffset.'\',\''.$productName.'\', this);"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="costofgoodssoldassembly"></label>
                                                        </td>
                                                        <td colspan="1">
                                                        <div style="margin-left:32%;">    
                                                            <a id="search" onclick="javascript:searchNonAgreementProducts(\''.$agreementId.'\',\''.$pageOffset.'\',\''.$productName.'\', this);" class="attachLoading cancel-button" name="Search" href="#">
                                                                <span class="z-spinner"></span>
                                                                <span class="z-icon"></span>
                                                                <span class="z-label">Search</span>
                                                            </a>
                                                        </div>    
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                         </div>
                                        <div class="items-wrapper" id="addProductWrapper">
                                               <div id="agmntTrackingSearchProducts" style="margin-bottom:2%; display: none;">
                                                    <div id="search_title" style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div>
                                                    <table class="items selected_products_table">
                                                        <thead>
                                                            <tr>
                                                                <th id="list-view-rowSelector" class="checkbox-column">  <label class="hasCheckBox">  <input type="checkbox" id="list-view-rowSelector_all" name="list-view-rowSelector_all" value="1"> </label>  </th>
                                                                <th style="width:15%;" id="list-view_c1"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'code\', \'asc\');">  Product Code </a> </th>
                                                                <th style="width:25%;" id="list-view_c2"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'name\', \'asc\');">  Product Name  </a> </th>
                                                                <th style="width:15%;" id="list-view_c3"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'unit\', \'asc\');"> Unit of Measure </a> </th>
                                                                <th style="width:10%;" id="list-view_c4"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreementId.', 1, \'cost\', \'asc\');"> Unit Direct Cost </a> </th>
                                                                <th style="width:15%;" id="list-view_c5"> Quantity </th>
                                                                <th style="width:20%;" id="list-view_c7"> Category </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="7" style = "padding: 0px;">
                                                                    <div id="agmntTracking_scroll_result">
                                                                        <table>
                                                                        </table>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>   
                                            </div>
                                     </div>';
            $content .='<div class="items-wrapper" id="addProductWrapper">
                       <input type="hidden" id="selectedProductCnt" value="' . $countOfSelectedRow . '" />
                       <div id="searchNonAgreementProducts"><a id="" href="searchResult"></a> </div> </div>';

        return  $content;
    }
    
    public function renderViewForAgreementTrackingProduct($agreementTrackingId) {
        
        $countOfSelectedRow = 0;
        $categories = Category::getAll();
        $agreementTracking = AgreementTracking::getById(intval($agreementTrackingId));
        $agreementTrackingProducts = AgreementTrackingProducts::getAgreementTrackingProductByTrackingId($agreementTrackingId);
        
        $agreementId = $agreementTracking->agreement->id;
        
        if(count($agreementTrackingProducts) > 0) {
       
            $tableCreation = '<div class="view-toolbar-container clearfix"><div class="panelTitle">Agreement Tracking Products</div><div class="panel" style = "padding: 2%;">';
            $tableCreation .= '<div style = "border : solid #dfdfdf 1px;"><table class="items" cellpadding="2" cellspacing="0"><colgroup span="5"></colgroup>';

            $tableCreation .= '<thead style="font-weight: bold; color : #999; vertical-align: inherit;">
                                    <th style="font-weight: bold;">Product Code</th>
                                    <th style="font-weight: bold;">Product Name</th>
                                    <th style="font-weight: bold; text-align: center;">Category</th>
                                    <th style="font-weight: bold; text-align: center;">Quantity Consumed</th>';

            $tableCreation .= '<th style="font-weight: bold;">Unit of Measure</th>
                               </thead><tbody>';
            foreach($agreementTrackingProducts as $row) {
                $agreementrackingpdt = $row->agreementProduct;
               $tableCreation .= '<tr><td style="width: 10%;  padding-top: 2px; text-align: left;">'.$agreementrackingpdt->costbook->productcode.'</td>
                                       <td style="width: 25%;  padding-top: 2px; text-align: left;">'.$agreementrackingpdt->name.'</td>
                                       <td style="width: 25%;  padding-top: 2px; text-align: center;">'.$agreementrackingpdt->Category.'</td>
                                       <td style="width: 15%;  padding-top: 2px; text-align: center;">'.$row->consumed_unit.'</td>';

               $tableCreation  .= '<td style="width: 10%;  padding-top: 2px; text-align: center;">'.$agreementrackingpdt->costbook->unitofmeasure.'</td>
                                    </tr>';
            }
            $tableCreation .= '</tbody></table></div></div></div>';


            // Ends Here
            $content = $tableCreation;
            //echo $content; exit;
            return $content;
        } else {
            return $content;
        }
            
    }

    public static function getCurrencyType() {
        return '$';
    }
    
    public function appendDynamicBtns($agreementId){
        $content = '<div id = "uppendBtn" style= "text-align: center; margin-bottom: 2%;">
                        <div id = "afterSearch" class="afterSearchCostBookDatas" style="display:none;">
                            <a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$agreementId.'\', this);" class="attachLoading cancel-button">
                            <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Go Back</span>
                            </a>
                            <a href="#" id="saveAndMore" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$agreementId.'\', this);" name="saveAddMore" class="attachLoading">                                            
                            <span class="z-label">
                                Save Add More
                            </span>
                            </a>
                            <a class="attachLoading " id="Save" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$agreementId.'\', this);" href="#">
                            <span class="z-label">
                                Save
                            </span>
                            </a>
                        </div>
                    </div>';
        return $content;
    }
    
    public static function resetAgmtProductTracking($agmtId, $reset_number=1) {
        
        // To update the consumed units in Agmt Product Table     
        $AgmtProduct =  AgreementProduct::getAgreementProdByAgreementId($agmtId);        
        foreach ($AgmtProduct as $AgmtProducts)
        {                
            if(isset($AgmtProducts) && (!empty($AgmtProducts->Consumed_Units)))
            {
                $agmt_prod                  = AgreementProduct::GetById(intval($AgmtProducts->id));
                $agmt_prod->Consumed_Units  = 0;
                $agmt_prod->is_completed    = NULL;
                $agmt_prod->save();
            }
        }
        
        // To update in the Tracking
        $agmntTracking = AgreementTracking::getAgreementTrackingByAgreementIdForReset($agmtId);
        foreach ($agmntTracking as $trackRow)
        {
            $agreementTrackingObj = AgreementTracking::getById($trackRow->id);
            $agreementTrackingObj->reset_number  = $reset_number;
            try
            {
                $agreementTrackingObj->save();
                
                $agmntTrackingProduct = AgreementTrackingProducts::getAgreementTrackingProductByTrackingIdForReset($trackRow->id);
                
                foreach ($agmntTrackingProduct as $trackProductRow)
                {
                    $updateAgmtTrackingProduct                  = AgreementTrackingProducts::getById($trackProductRow->id);
                    $updateAgmtTrackingProduct->reset_number    = $reset_number;
                    $updateAgmtTrackingProduct->save();
                }
            } 
            catch (Exception $ex) 
            {
                echo $ex->getMessage();
            }
        }
    }
}
?>