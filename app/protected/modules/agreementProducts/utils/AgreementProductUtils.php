<?php

/**
 * Class utilized by Agreement product selection
 * 
 * @author Thamodaran.K 
 */
class AgreementProductUtils {

    public function makeCostBookProductSelection($costBookDatas, $agmntId) {
        $agreement = Agreement::getById(intval($agmntId));
        $content = AgreementProductUtils::appendLayoutStart($agmntId);
        $content .= AgreementProductUtils::appendButton($agmntId);
        $content .= AgreementProductUtils::appendSummaryDiv($agreement);
        $content .= AgreementProductUtils::existingAgmntProducts($agreement);
        $content .= AgreementProductUtils::appendSearchDiv($agmntId);
        $content .= AgreementProductUtils::appendCostBookResult($agreement);
        return $content;
    }

    public function addProductFindDuplicate($category, $addProductagmntId, $productCode) {
        $agmntproducts = AgreementProduct::getagmntProductByproductCode($category, $addProductagmntId, $productCode);
        if ($agmntproducts != null) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function appendLayoutStart($agmntId) {
        $content = '<div class="SecuredEditAndDetailsView EditAndDetailsView DetailsView ModelView ConfigurableMetadataView MetadataView" id="CostbookEditAndDetailsView">
                            <div class="wrapper">
                                <h1>
                                    <span class="truncated-title" threedots="Create Costbook"><span class="ellipsis-content">Add Products for Agreement</span></span>
                                </h1>
                                <div class="wide form">
                                    <div class="attributesContainer">
                                        <div class="left-column" style="width:100%;">';
        return $content;
    }

    public function appendButton($agmntId) {
        $content = '<div style="margin-left:42%;"><a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInAgreement(\'' . $agmntId . '\', this);" class="attachLoading cancel-button">
                                <span class="z-label appendButton">
                                    Go Back
                                </span>
                                </a>
                                <a class="attachLoading " id="Save" onclick="javascript:addAndUpdateProductsInAgreement(\'' . $agmntId . '\', this);" href="#">
                                <span class="z-label">
                                    Save
                                </span>
                                </a>
                            </div>';
        return $content;
    }

    public function appendSummaryDiv($agreement) {
        $content = '<div class="cgrid-view type-agreementProducts" id="list-view">
                                <div class="summary">
                                    5 result(s)
                                </div>
                                <div id="add_Product_list_table" class="table_border_width">
                                    <table id="add_Product_List_table_Value" class="add_Product_List_table">
                                        <tr>
                                            <td><label id="totalMhr"> Total Mhr : 0 </label></td>
                                            <td><label id="totalDirectCost"> Total Direct Cost ' . Constant::CURRENCY . ': 0 </label></td>
                                            <td><label id="budget"> Budget ' . Constant::CURRENCY . ': 0 </label></td>
                                        </tr>
                                        <tr>
                                            <td><label id="Revenue_MHR"> Revenue / Mhr ' . Constant::CURRENCY . ': 0.0 </label></td>
                                            <td><label id="Aggregate_GPM"> Aggregate GPM %: 0 </label></td>
                                            <td><label id="Suggested_Price"> Suggested Price ' . Constant::CURRENCY . ': 0 </label></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp</td>';
        if ($agreement->Current_GPM > 0) {
            $final_gpm_value = $agreement->Current_GPM;
        } else {
            $final_gpm_value = 40;
        }
        $content .='<td><label id="final_GPM"> Final GPM %: <input type="text" name="finalGpm" id="final_gpm" style="width:20%" value=' . $final_gpm_value . ' disabled> </label></td>';
        if ($agreement->RecordType == Constant::RECURRINGAGREEMENT) {
            $content .='<td><label id="final_amount"> Final Amount ' . Constant::CURRENCY . ': <input type="text" name="finalAmount" style="width:30%" id="final_amt" value="0"> </label></td>';
        } else {
            $content .='<td><label id="final_amount"> Final Amount ' . Constant::CURRENCY . ': <input type="text" name="finalAmount" style="width:30%" id="final_amt" value="0"> </label></td>';
        }
        $content .='  <input value="' . $agreement->RecordType . '" name="recordType" id="recordType_Ids" type="hidden">
                                                <input value="0" name="modified_final_amt" id="modified_final_amt" type="hidden">    
                                        </tr>
                                    </table>
                                </div></div>';
        return $content;
    }

    protected function existingAgmntProducts($agreement) {
        $agreementProducts = AgreementProduct::getAllByAgmntIdForExisting(intval($agreement->id));
        $countOfSelectedRow = 0;
        $content = '<input value="0" name="Selected_Products_Ids" id="Selected_Products_Ids" type="hidden">';
        if (count($agreementProducts) > 0) {
            $content .= '<div id="selected_products" class="table_border_width" style="padding: 0%;">
                    <div class="align_left" style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold;">
                         Selected Products <span id="showresults"></span>
                    </div>
                        <div style="margin:0.5% 0% 0.5% 45%">
                            <a href="#" onclick="javascript:addAndUpdateProductsInAgreement(\'' . $agreement->id . '\', this);" class="cancel-button" id="update">
                                <span class="z-label">
                                    Update Values
                                </span>
                            </a>
                        </div>';
            $content .='<table class="items selected_products_table">
                            <tr style="color:black; padding:0.5%;">
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Unit of Measure</th>
                                <th>Quantity</th>';
            if ($agreement->RecordType == Constant::RECURRINGAGREEMENT) {
                $content .= '<th>Frequency</th>';
            }
            $content .= '<th>MH</th>
                                <th>L+OH</th>
                                <th>M</th>
                                <th>E</th>
                                <th>S</th>
                                <th>O</th>
                                <th>Total Direct Cost</th>
                            </tr>';

            foreach ($agreementProducts as $row) {
                $agreementPdctMap[$row->Category][] = $row;
            }
            $CategoryKeyCount = 0;
            $totalMhr = 0;
            $totalDirectCost = 0;
            $totalSuggestedPrice = 0.0;
            $actualGPM = 0;
            $totalRevenue = 0.0;
            foreach ($agreementPdctMap as $CategoryKey => $agreementArray) {
                $content .='<tr>
                          <th colspan="12" class="align_left" style="background-color:gray;color:white;">' . $CategoryKey . '</th>
                          <input type="hidden" name="CategoryKey" id="CategoryKey_' . $CategoryKeyCount . '" value="' . $CategoryKey . '">
                          <input value="" name="list-view-selectedIds" id="list-view-selectedIds" type="hidden">    
                        </tr>';
                foreach ($agreementArray as $agreementKey => $agreementpdt) {
                    $totalMhr += $agreementpdt->Total_MHR;
                    $totalDirectCost += $agreementpdt->Total_Direct_Cost->value;
                    $totalSuggestedPrice += $agreementpdt->Suggested_Cost->value;
                    $content .='<tr>
                            <td>' . $agreementpdt->costbook->productcode . '</td>
                            <input value=' . $agreementpdt->id . ' name="list_View_Add_Product_SelectedIds"id="list_View_Producted_SelectedIds_' . $countOfSelectedRow . '" type="hidden">
                            <td>' . $agreementpdt->name . '</td>
                            <td>' . $agreementpdt->costbook->unitofmeasure . '</td>
                            <td><input type="text" name="updateFrequency&Quantity" id="updateQuantity_' . $countOfSelectedRow . '" value=' . $agreementpdt->Quantity . '></td>';
                    if ($agreement->RecordType == Constant::RECURRINGAGREEMENT) {
                        $content .=' <td><input name="updateFrequency&Quantity" type="text" id="updateFrequency_' . $countOfSelectedRow . '" value=' . $agreementpdt->Frequency . '></td>';
                    }
                    $content .='<td>' . $agreementpdt->Total_MHR . '</td>
                            <td>' . Constant::CURRENCY . (($agreementpdt->Labor_Cost->value) + ($agreementpdt->Burden_Cost->value)) . '</td>
                            <td>' . Constant::CURRENCY . $agreementpdt->Materials_Cost . '</td>
                            <td>' . Constant::CURRENCY . $agreementpdt->Equipment_Cost . '</td>
                            <td>' . Constant::CURRENCY . $agreementpdt->Sub_Cost . '</td>
                            <td>' . Constant::CURRENCY . $agreementpdt->Other_Cost . '</td>
                            <td>' . Constant::CURRENCY . $agreementpdt->Total_Direct_Cost->value . '</td>
                        </tr>';
                    $countOfSelectedRow++;
                }
                $CategoryKeyCount++;
            }
            if ($totalSuggestedPrice > 0) {
                $actualGPM = (($totalSuggestedPrice - $totalDirectCost) / $totalSuggestedPrice) * 100;
            }
            if ($totalMhr > 0) {
                $totalRevenue = $totalSuggestedPrice / $totalMhr;
            }
            Yii::app()->clientScript->registerScript('calculationForAddProductScreenRecurring', '$(Selected_Products_Ids).val("' . $countOfSelectedRow . '")
                     $("#totalMhr").text("Total Mhr : ' . $totalMhr . '");
                     $("#totalDirectCost").text("Total Direct Cost ' . Constant::CURRENCY . ': ' . $totalDirectCost . '");   
                     $("#budget").text("Budget ' . Constant::CURRENCY . ' : ' . $agreement->budget->value . '");
                     $("#Suggested_Price").text("Suggested Price ' . Constant::CURRENCY . ': ' . sprintf('%.2f', $totalSuggestedPrice) . '");
                     $("#Aggregate_GPM").text("Aggregate GPM %: ' . sprintf('%.2f', $actualGPM) . '");
                     //$("#Roundedvalue").text(" Rounded value").css({"color":"red"});    
                     $("#Revenue_MHR").text("Revenue / Mhr ' . Constant::CURRENCY . ': ' . sprintf('%.2f', $totalRevenue) . '");    
                    // For the final amount
                     if(' . $totalDirectCost . ' >0){
                        var tot_amt = ' . $totalDirectCost . '/(1-$("#final_gpm").val()/100);
                        if(' . $agreement->Current_Annual_Amount->value . ' >0){
                            $("#final_amt").val(' . $agreement->Current_Annual_Amount->value . ');
                        }else{    
                            $("#final_amt").val(' . $agreement->Project_Agreement_Amount->value . ');    
                        }
                     } 
                     $("#final_gpm").keyup(function() {
                         if(' . $totalDirectCost . ' >0){
                             var tot_amt = ' . $totalDirectCost . '/(1-$("#final_gpm").val()/100);
                             $("#final_amt").val(tot_amt.toFixed(2));
                             $("#modified_final_amt").val(0);
                         }else {
                             $("#final_amt").val(0);
                             $("#modified_final_amt").val(0);
                         }
                     });

                    $("#final_amt").keyup(function() 
                    {
                        if(' . $totalDirectCost . ' >0)
                        {
                            if($("#final_amt").val() > ' . $totalDirectCost . ')
                            {
                                var tot_gpm = (($("#final_amt").val()*100)-(' . $totalDirectCost . '*100))/$("#final_amt").val();
                                $("#final_gpm").val(tot_gpm.toFixed(2));
                                $("#modified_final_amt").val($("#final_amt").val());
                            }
                        }                                          
                    });
                 ');
            $content .=' </table></div>';
            return $content;
        }
    }

    protected function appendSearchDiv($agmntId) {
        $categories = Category::getAll();
        $pageOffset = 1;
        $content = '<div class="table_border_width border_top_In_Assembly_Detail_Level" id="add_product_search" style="padding: 0px;">
                         <div class="panel">
                            <div class="align_left" style="color:black; background-color:#E0D1D1; color:black; padding:0.5%; font-weight:bold;">Search</div>
                            <table class="form-fields" style = "table-align : center;">
                                <colgroup><col class="col-0"><col class="col-1"></colgroup>
                                <tbody>
                                    <tr>
                                        <th width="10%">
                                            <label for="agmnt_AddProductcategory_value">Select Category</label>
                                        </th>
                                        <td colspan="1" >
                                            <div class="hasDropDown" style = "width : 60%">
                                                <span class="select-arrow"></span>
                                                    <select id="agmnt_AddProductcategory_value" name="Costbook[assemblycategory][value]">
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
                                        <td colspan="1" style="margin: 0px;">
                                            <div class="hasDropDown"style ="width :60%;">
                                                <span class="select-arrow"></span>
                                                    <select id="agmnt_AddProductcostofgoodssold_value" name="Costbook[costofgoodssoldassembly][value][assemblycategory][value]">
                                                        <option selected="selected" value="All">All</option>
                                                        <option value="Labor">Labor</option>
                                                        <option value="Equipment">Equipment</option>
                                                        <option value="Material">Material</option>
                                                        <option value="Subcontractor">Subcontractor</option>
                                                        <option value="Other">Other</option>
                                                        <option value="Assembly">Assembly</option>
                                                    </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <label for="costofgoodssoldassembly">Search By Product Name</label>
                                        </th>
                                        <td colspan="1"><input type="text" id="agmnt_productname_value" name="productname" value="" style="width : 60%" onkeyup="javascript:searchProducts(\'' . $agmntId . '\',\'' . $pageOffset . '\', this);"/></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="costofgoodssoldassembly"></label>
                                        </td>
                                        <td colspan="1">
                                        <div style="margin-left:20%;">                                                            
                                            <a id="search" onClick="javascript:searchProducts(\'' . $agmntId . '\',\'' . $pageOffset . '\', this);" class="attachLoading z-button cancel-button" name="Search" href="#">
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
                         <div id="result_div"></div>
                     </div>
                </div>';
        $content .= AgreementProductUtils::appendDynamicBtns($agmntId);
        $content .='<div class="items-wrapper" id="addProductWrapper"></div>';
        return $content;
    }

    protected function appendDynamicBtns($agmntId) {
        $content = '<div id = "uppendBtn" style= "text-align: center; margin-bottom: 2%;">
                        <div id = "afterSearch" class="afterSearchCostBookDatas" style="display:none;">
                            <a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInAgreement(\'' . $agmntId . '\', this);" class="attachLoading cancel-button">
                            <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Go Back</span>
                            </a>
                            <a href="#" id="saveAndMore" onclick="javascript:addAndUpdateProductsInAgreement(\'' . $agmntId . '\', this);" name="saveAddMore" class="attachLoading">                                            
                            <span class="z-label">
                                Save Add More
                            </span>
                            </a>
                            <a class="attachLoading " id="Save" onclick="javascript:addAndUpdateProductsInAgreement(\'' . $agmntId . '\', this);" href="#">
                            <span class="z-label">
                                Save
                            </span>
                            </a>
                        </div>
                    </div>';
        return $content;
    }

    protected function appendCostBookResult($agreement) {
//        $content = '<div id="searchProducts" style = "margin : 1.5%;"></div>';
        if($agreement->RecordType == Constant::PROJECTAGREEMENT){
            $content = '<div class="items-wrapper" id="addProductWrapper" style="padding-left : 15px; padding-right : 15px;">
                       <div id="searchProducts" style="margin-bottom:2%; display : none;">
                            <div id="search_title" style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div>
                            <table class="items selected_products_table">
                                <thead>
                                    <tr>
                                        <th id="list-view-rowSelector" class="checkbox-column">  <label class="hasCheckBox">  <input type="checkbox" id="list-view-rowSelector_all" name="list-view-rowSelector_all" value="1">   </label>  </th>
                                        <th style="width:15%;" id="list-view_c1"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'code\', \'asc\');">  Product Code </a> </th>
                                        <th style="width:25%;" id="list-view_c2"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'name\', \'asc\');">  Product Name  </a> </th>
                                        <th style="width:15%;" id="list-view_c3"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'unit\', \'asc\');"> Unit of Measure </a> </th>
                                        <th style="width:10%;" id="list-view_c4"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'cost\', \'asc\');"> Unit Direct Cost </a> </th>
                                        <th style="width:15%;" id="list-view_c5"> Quantity </th>
                                        <th style="width:20%;" id="list-view_c7"> Category </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" style = "padding: 0px;">
                                            <div id="agmnt_scroll_result">
                                                <table>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>   
                       
                       
                        </div>';
        }else{
            $content ='<div class="items-wrapper" id="addProductWrapper" style="padding-left : 15px; padding-right : 15px;">
                       <div id="searchProducts" style="margin-bottom:2%; display : none;">
                            <div id="search_title" style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div>
                            <table class="items selected_products_table">
                                <thead>
                                    <tr>
                                        <th id="list-view-rowSelector" class="checkbox-column">  <label class="hasCheckBox">  <input type="checkbox" id="list-view-rowSelector_all" name="list-view-rowSelector_all" value="1"> </label>  </th>
                                        <th style="width:10%;" id="list-view_c1">  <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'code\', \'asc\');">  Product Code </a> </th>
                                        <th style="width:25%; padding: 0px;" id="list-view_c2">  <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'name\', \'asc\');">  Product Name  </a> </th>
                                        <th style="width:15%; padding: 0px;" id="list-view_c3"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'unit\', \'asc\');"> Unit of Measure </a> </th>
                                        <th style="width:10%; padding: 0px;" id="list-view_c4"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$agreement->id.', 1, \'cost\', \'asc\');"> Unit Direct Cost </a> </th>
                                        <th style="width:10%; padding: 0px;" id="list-view_c5"> Quantity </th>
                                        <th style="width:10%; padding: 0px;" id="list-view_c5"> Frequency </th>
                                        <th style="width:20%; padding: 0px;" id="list-view_c7"> Category </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8" style = "padding: 0px;">
                                            <div id="agmnt_scroll_result">
                                                <table>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>   
                        </div>';
        }
        return $content;
    }

    public function appendAddProductBtn($id) {
        $tableCreation = '<div class="form-toolbar clearfix">
                                   <a id="addProduct" name="Add Products" class="attachLoading z-button" href="/app/index.php/agreementProducts/default/AddProductsInAgreement?agmntId=' . $id . '">
                                       <span class="z-spinner"></span>
                                       <span class="z-icon"></span>
                                       <span class="z-label">
                                           Add Products
                                       </span>
                                   </a>
                               </div>';
        return $tableCreation;
    }

    public function saveAssemblyProductsCalculation($agreementProduct, $productQuantity, $productFrequency = NULL, $costBookData, $category){
        try {
            $tQty = 0;
            $agreementProduct->Quantity =  round($productQuantity, 4);

            //Frequency is set if the project record type is recurring otherwise not set 
            if (!empty($productFrequency)) {
                $agreementProduct->Frequency = intval($productFrequency);
            }
            $currencies = Currency::getAll();
            $tQty = (float) $productQuantity;
            if ($agreementProduct->Frequency > 0) {
                $tQty *= $agreementProduct->Frequency;
            }
            $agreementProduct->Category = $category;
            
            //Labor Product calculation
            if ($costBookData[0]->costofgoodssold->value == Constant::LABOUR) {
                $agreementProduct->Total_MHR = round($tQty, 2);
                if (floatval($costBookData[0]->departmentreference->laborCost) > 0 && floatval($costBookData[0]->departmentreference->burdonCost) > 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = round((floatval($costBookData[0]->departmentreference->burdonCost) * $tQty), 2);
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = round((floatval($costBookData[0]->departmentreference->laborCost) * $tQty), 2);
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                } else if (floatval($costBookData[0]->departmentreference->laborCost) > 0 && floatval($costBookData[0]->departmentreference->burdonCost) <= 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = 0.0;
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = round((floatval($costBookData[0]->departmentreference->laborCost) * $tQty), 2);
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                } else if (floatval($costBookData[0]->departmentreference->burdonCost) > 0 && floatval($costBookData[0]->departmentreference->laborCost) <= 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = round((floatval($costBookData[0]->departmentreference->burdonCost) * $tQty), 2);
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = 0.0;
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                } else {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = 0.0;
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = 0.0;
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                }
            } else {
                $burdenCost = new CurrencyValue();
                $burdenCost->value = 0.0;
                $burdenCost->currency = $currencies[0];
                $agreementProduct->Burden_Cost = $burdenCost;
                $laborCost = new CurrencyValue();
                $laborCost->value = 0.0;
                $laborCost->currency = $currencies[0];
                $agreementProduct->Labor_Cost = $laborCost;
            }

            if ($costBookData[0]->costofgoodssold->value == Constant::MATERIAL) {
                $materialCost = new CurrencyValue();
                $materialCost->value = round((floatval($costBookData[0]->unitdirectcost) * $tQty), 2);
                $materialCost->currency = $currencies[0];
                $agreementProduct->Materials_Cost = $materialCost;
            } else {
                $materialCost = new CurrencyValue();
                $materialCost->value = 0.0;
                $materialCost->currency = $currencies[0];
                $agreementProduct->Materials_Cost = $materialCost;
            }

            if ($costBookData[0]->costofgoodssold->value == Constant::EQUIPMENT) {
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = round((floatval($costBookData[0]->unitdirectcost) * $tQty), 2);
                $eqmtCost->currency = $currencies[0];
                $agreementProduct->Equipment_Cost = $eqmtCost;
            } else {
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = 0.0;
                $eqmtCost->currency = $currencies[0];
                $agreementProduct->Equipment_Cost = $eqmtCost;
            }

            if ($costBookData[0]->costofgoodssold->value == Constant::SUBCONTRACT) {
                $subcontCost = new CurrencyValue();
                $subcontCost->value = round((floatval($costBookData[0]->unitdirectcost) * $tQty), 2);
                $subcontCost->currency = $currencies[0];
                $agreementProduct->Sub_Cost = $subcontCost;
            } else {
                $subcontCost = new CurrencyValue();
                $subcontCost->value = 0.0;
                $subcontCost->currency = $currencies[0];
                $agreementProduct->Sub_Cost = $subcontCost;
            }

            if ($costBookData[0]->costofgoodssold->value == Constant::OTHER) {
                $otherCost = new CurrencyValue();
                $otherCost->value = round((floatval($costBookData[0]->unitdirectcost) * $tQty), 2);
                $otherCost->currency = $currencies[0];
                $agreementProduct->Other_Cost = $otherCost;
            } else {
                $otherCost = new CurrencyValue();
                $otherCost->value = 0.0;
                $otherCost->currency = $currencies[0];
                $agreementProduct->Other_Cost = $otherCost;
            }
            $values = Category::getCategoryByName($category);
            if (is_array($values) && count($values) > 0) {
                $agreementProduct->Category_GPM = $values[0]->targetgpm;
            } else {
                $agreementProduct->Category_GPM = 40;
            }
            $totalDirectCost = new CurrencyValue();
            $totalDirectCost->value = round(($agreementProduct->Labor_Cost->value + $agreementProduct->Burden_Cost->value + $agreementProduct->Materials_Cost->value + $agreementProduct->Equipment_Cost->value + $agreementProduct->Sub_Cost->value + $agreementProduct->Other_Cost->value), 2);
            $totalDirectCost->currency = $currencies[0];
            $agreementProduct->Total_Direct_Cost = $totalDirectCost;
            $finalCost = new CurrencyValue();
            $finalCost->value = round(($agreementProduct->Total_Direct_Cost->value / (1 - ($agreementProduct->Category_GPM / 100))), 2);
            $finalCost->currency = $currencies[0];
            $agreementProduct->Suggested_Cost = $finalCost;
            if (!$agreementProduct->save()) {
                throw new Exception();
            } else{
                return $ret_arr = array(
                    'agt_prod_obj' => $agreementProduct
                );
            }
        } catch (Exception $ex) {
            return FALSE;
        }
        
    }




    public function addAgreementProductsCalculation(Costbook $costbook, $quantity, $frequency, $agmntId, $category) {
        try {
            $tQty = 0;
            $agreementProduct = new AgreementProduct();
            $agreementProduct->Quantity = (float) $quantity;

            //Frequency is set if the project record type is recurring otherwise not set 
            if ($frequency != '') {
                $agreementProduct->Frequency = intval($frequency);
            }

            $agreementProduct->Category = $category;
            $agreementProduct->Product_Code = $costbook->productcode;
            $agreementProduct->name = $costbook->productname;
            $currencies = Currency::getAll();

            $agreementProduct->costbook = $costbook;
            $tQty = (float) $quantity;
            if ($agreementProduct->Frequency > 0) {
                $tQty *= $agreementProduct->Frequency;
            }

            //Labor Product calculation
            if ($costbook->costofgoodssold->value == Constant::LABOUR) {
                $agreementProduct->Total_MHR = round($tQty, 2);
                if (floatval($costbook->departmentreference->laborCost) > 0 && floatval($costbook->departmentreference->burdonCost) > 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = round((floatval($costbook->departmentreference->burdonCost) * $tQty), 2);
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = round((floatval($costbook->departmentreference->laborCost) * $tQty), 2);
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                } else if (floatval($costbook->departmentreference->laborCost) > 0 && floatval($costbook->departmentreference->burdonCost) <= 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = 0.0;
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = round((floatval($costbook->departmentreference->laborCost) * $tQty), 2);
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                } else if (floatval($costbook->departmentreference->burdonCost) > 0 && floatval($costbook->departmentreference->laborCost) <= 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = round((floatval($costbook->departmentreference->burdonCost) * $tQty), 2);
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = 0.0;
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                } else {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = 0.0;
                    $burdenCost->currency = $currencies[0];
                    $agreementProduct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = 0.0;
                    $laborCost->currency = $currencies[0];
                    $agreementProduct->Labor_Cost = $laborCost;
                }
            } else {
                $burdenCost = new CurrencyValue();
                $burdenCost->value = 0.0;
                $burdenCost->currency = $currencies[0];
                $agreementProduct->Burden_Cost = $burdenCost;
                $laborCost = new CurrencyValue();
                $laborCost->value = 0.0;
                $laborCost->currency = $currencies[0];
                $agreementProduct->Labor_Cost = $laborCost;
            }

            if ($costbook->costofgoodssold->value == Constant::MATERIAL) {
                $materialCost = new CurrencyValue();
                $materialCost->value = round((floatval($costbook->unitdirectcost) * $tQty), 2);
                $materialCost->currency = $currencies[0];
                $agreementProduct->Materials_Cost = $materialCost;
            } else {
                $materialCost = new CurrencyValue();
                $materialCost->value = 0.0;
                $materialCost->currency = $currencies[0];
                $agreementProduct->Materials_Cost = $materialCost;
            }

            if ($costbook->costofgoodssold->value == Constant::EQUIPMENT) {
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = round((floatval($costbook->unitdirectcost) * $tQty), 2);
                $eqmtCost->currency = $currencies[0];
                $agreementProduct->Equipment_Cost = $eqmtCost;
            } else {
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = 0.0;
                $eqmtCost->currency = $currencies[0];
                $agreementProduct->Equipment_Cost = $eqmtCost;
            }

            if ($costbook->costofgoodssold->value == Constant::SUBCONTRACT) {
                $subcontCost = new CurrencyValue();
                $subcontCost->value = round((floatval($costbook->unitdirectcost) * $tQty), 2);
                $subcontCost->currency = $currencies[0];
                $agreementProduct->Sub_Cost = $subcontCost;
            } else {
                $subcontCost = new CurrencyValue();
                $subcontCost->value = 0.0;
                $subcontCost->currency = $currencies[0];
                $agreementProduct->Sub_Cost = $subcontCost;
            }

            if ($costbook->costofgoodssold->value == Constant::ASSEMBLY) {
                $agreementProductResult = AgreementProductUtils::AssemblyProductCalculation($costbook);
                $agreementProduct->Total_MHR = round(floatval($agreementProductResult['Total_MHR']) * $tQty, 2);
                $burdenCost = new CurrencyValue();
                $burdenCost->value = round((floatval($agreementProductResult['Total_Burden_Cost']) * $tQty), 2);
                $burdenCost->currency = $currencies[0];
                $agreementProduct->Burden_Cost = $burdenCost;
                $laborCost = new CurrencyValue();
                $laborCost->value = round((floatval($agreementProductResult['Total_Labour_Cost']) * $tQty), 2);
                $laborCost->currency = $currencies[0];
                $agreementProduct->Labor_Cost = $laborCost;
                $materialCost = new CurrencyValue();
                $materialCost->value = round((floatval($agreementProductResult['Materials_Cost']) * $tQty), 2);
                $materialCost->currency = $currencies[0];
                $agreementProduct->Materials_Cost = $materialCost;
                $subcontCost = new CurrencyValue();
                $subcontCost->value = round((floatval($agreementProductResult['Subcontractor_Cost']) * $tQty), 2);
                $subcontCost->currency = $currencies[0];
                $agreementProduct->Sub_Cost = $subcontCost;
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = round((floatval($agreementProductResult['Equipment_Cost']) * $tQty), 2);
                $eqmtCost->currency = $currencies[0];
                $agreementProduct->Equipment_Cost = $eqmtCost;
            }

            if ($costbook->costofgoodssold->value == Constant::OTHER) {
                $otherCost = new CurrencyValue();
                $otherCost->value = round((floatval($costbook->unitdirectcost) * $tQty), 2);
                $otherCost->currency = $currencies[0];
                $agreementProduct->Other_Cost = $otherCost;
            } else {
                $otherCost = new CurrencyValue();
                $otherCost->value = 0.0;
                $otherCost->currency = $currencies[0];
                $agreementProduct->Other_Cost = $otherCost;
            }
            $values = Category::getCategoryByName($category);
            if (is_array($values) && count($values) > 0) {
                $agreementProduct->Category_GPM = $values[0]->targetgpm;
            } else {
                $agreementProduct->Category_GPM = 40;
            }
            $totalDirectCost = new CurrencyValue();
            $totalDirectCost->value = round(($agreementProduct->Labor_Cost->value + $agreementProduct->Burden_Cost->value + $agreementProduct->Materials_Cost->value + $agreementProduct->Equipment_Cost->value + $agreementProduct->Sub_Cost->value + $agreementProduct->Other_Cost->value), 2);
            $totalDirectCost->currency = $currencies[0];
            $agreementProduct->Total_Direct_Cost = $totalDirectCost;
            $finalCost = new CurrencyValue();
            $finalCost->value = round(($agreementProduct->Total_Direct_Cost->value / (1 - ($agreementProduct->Category_GPM / 100))), 2);
            $finalCost->currency = $currencies[0];
            $agreementProduct->Suggested_Cost = $finalCost;
            $agreementProduct->agreement = $agreement = Agreement::GetById(intval($agmntId));

            if (!$agreementProduct->save()) {
                throw new Exception();
            } else{
                AgreementProductUtils::SaveNewAssemblyProducts($costbook, $quantity, $frequency, $agmntId, $category);
                return $ret_arr = array(
                    'agt_prod_obj' => $agreementProduct
                );
            }
        } catch (Exception $ex) {
            return FALSE;
        }
    }
    public function SaveNewAssemblyProducts($costbook, $quantity, $frequency, $agmntId, $category){
        $vAssemblyDetails = '';
        $assemblyDetail = trim($costbook->assemblydetail);
        if (empty($assemblyDetail)) {
            return FALSE;
        }
        $vAssemblyDetails = explode(';', $costbook->assemblydetail);
        foreach ($vAssemblyDetails as $vAssemblyDetail) {
                $agmntProduct = new AgreementProduct();
                $productDetails = explode('|', $vAssemblyDetail);
                $costBookData = Costbook::getByProductCode($productDetails[1]);
                $agmntProduct->Assembly_Product_Code = $costbook->productcode;
                if(strpos($costBookData[0]->productcode, 'A') == FALSE){
                   $agmntProduct->name = $costBookData[0]->productname;
                   $agmntProduct->costbook = $costbook = Costbook::GetById(intval($costBookData[0]->id));
                   $agmntProduct->Product_Code = $costBookData[0]->productcode;
                   $agmntProduct->agreement = $agreement = Agreement::GetById(intval($agmntId));
                    if(empty($frequency)) {
                        $productQuantity = $quantity * $productDetails[2];
                        $productFrequency = NULL;
                    } else {
                        $productQuantity = $quantity * $productDetails[2];
                        $productFrequency = $frequency;
                    }
                    AgreementProductUtils::saveAssemblyProductsCalculation($agmntProduct, (float) $productQuantity, $productFrequency, $costBookData, $category);
                }
            }
    }

    public function updateAgreementProductCalculation($agmntpdct, $quantity, $frequency, $agmnt) {
        try {
            $currencies = Currency::getAll();
            $tQty = (float) $quantity;
            $agmntpdct->Quantity = $tQty;
            if ((float) $frequency > 0) {
                $agmntpdct->Frequency = intval($frequency);
                $tQty *= $agmntpdct->Frequency;
            }
            if ($agmntpdct->costbook->costofgoodssold->value == Constant::LABOUR) {
                $agmntpdct->Total_MHR = round($tQty, 2);
                if (floatval($agmntpdct->costbook->departmentreference->laborCost) > 0 && floatval($agmntpdct->costbook->departmentreference->burdonCost) > 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = round((floatval($agmntpdct->costbook->departmentreference->burdonCost) * $tQty), 2);
                    $burdenCost->currency = $currencies[0];
                    $agmntpdct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = round((floatval($agmntpdct->costbook->departmentreference->laborCost) * $tQty), 2);
                    $laborCost->currency = $currencies[0];
                    $agmntpdct->Labor_Cost = $laborCost;
                } else if (floatval($agmntpdct->costbook->departmentreference->laborCost) > 0 && floatval($agmntpdct->costbook->departmentreference->burdonCost) <= 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = 0.0;
                    $burdenCost->currency = $currencies[0];
                    $agmntpdct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = round((floatval($agmntpdct->costbook->departmentreference->laborCost) * $tQty), 2);
                    $laborCost->currency = $currencies[0];
                    $agmntpdct->Labor_Cost = $laborCost;
                } else if (floatval($agmntpdct->costbook->departmentreference->burdonCost) > 0 && floatval($agmntpdct->costbook->departmentreference->laborCost) <= 0) {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = round((floatval($agmntpdct->costbook->departmentreference->burdonCost) * $tQty), 2);
                    $burdenCost->currency = $currencies[0];
                    $agmntpdct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = 0.0;
                    $laborCost->currency = $currencies[0];
                    $agmntpdct->Labor_Cost = $laborCost;
                } else {
                    $burdenCost = new CurrencyValue();
                    $burdenCost->value = 0.0;
                    $burdenCost->currency = $currencies[0];
                    $agmntpdct->Burden_Cost = $burdenCost;
                    $laborCost = new CurrencyValue();
                    $laborCost->value = 0.0;
                    $laborCost->currency = $currencies[0];
                    $agmntpdct->Labor_Cost = $laborCost;
                }
            } else {
                $burdenCost = new CurrencyValue();
                $burdenCost->value = 0.0;
                $burdenCost->currency = $currencies[0];
                $agmntpdct->Burden_Cost = $burdenCost;
                $laborCost = new CurrencyValue();
                $laborCost->value = 0.0;
                $laborCost->currency = $currencies[0];
                $agmntpdct->Labor_Cost = $laborCost;
            }
            if ($agmntpdct->costbook->costofgoodssold->value == Constant::MATERIAL) {
                $materialCost = new CurrencyValue();
                $materialCost->value = round((floatval($agmntpdct->costbook->unitdirectcost) * $tQty), 2);
                $materialCost->currency = $currencies[0];
                $agmntpdct->Materials_Cost = $materialCost;
            } else {
                $materialCost = new CurrencyValue();
                $materialCost->value = 0.0;
                $materialCost->currency = $currencies[0];
                $agmntpdct->Materials_Cost = $materialCost;
            }

            if ($agmntpdct->costbook->costofgoodssold->value == Constant::EQUIPMENT) {
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = round((floatval($agmntpdct->costbook->unitdirectcost) * $tQty), 2);
                $eqmtCost->currency = $currencies[0];
                $agmntpdct->Equipment_Cost = $eqmtCost;
            } else {
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = 0.0;
                $eqmtCost->currency = $currencies[0];
                $agmntpdct->Equipment_Cost = $eqmtCost;
            }
            if ($agmntpdct->costbook->costofgoodssold->value == Constant::SUBCONTRACT) {
                $subcontCost = new CurrencyValue();
                $subcontCost->value = round((floatval($agmntpdct->costbook->unitdirectcost) * $tQty), 2);
                $subcontCost->currency = $currencies[0];
                $agmntpdct->Sub_Cost = $subcontCost;
            } else {
                $subcontCost = new CurrencyValue();
                $subcontCost->value = 0.0;
                $subcontCost->currency = $currencies[0];
                $agmntpdct->Sub_Cost = $subcontCost;
            }
            if ($agmntpdct->costbook->costofgoodssold->value == Constant::OTHER) {
                $otherCost = new CurrencyValue();
                $otherCost->value = round((floatval($agmntpdct->costbook->unitdirectcost) * $tQty), 2);
                $otherCost->currency = $currencies[0];
                $agmntpdct->Other_Cost = $otherCost;
            } else {
                $otherCost = new CurrencyValue();
                $otherCost->value = 0.0;
                $otherCost->currency = $currencies[0];
                $agmntpdct->Other_Cost = $otherCost;
            }

            if ($agmntpdct->costbook->costofgoodssold->value == Constant::ASSEMBLY) {
                $agreementProductResult = AgreementProductUtils::AssemblyProductCalculation($agmntpdct->costbook);
                $agmntpdct->Total_MHR = round(floatval($agreementProductResult['Total_MHR']) * $tQty, 2);
                $burdenCost = new CurrencyValue();
                $burdenCost->value = round((floatval($agreementProductResult['Total_Burden_Cost']) * $tQty), 2);
                $burdenCost->currency = $currencies[0];
                $agmntpdct->Burden_Cost = $burdenCost;
                $laborCost = new CurrencyValue();
                $laborCost->value = round((floatval($agreementProductResult['Total_Labour_Cost']) * $tQty), 2);
                $laborCost->currency = $currencies[0];
                $agmntpdct->Labor_Cost = $laborCost;
                $materialCost = new CurrencyValue();
                $materialCost->value = round((floatval($agreementProductResult['Materials_Cost']) * $tQty), 2);
                $materialCost->currency = $currencies[0];
                $agmntpdct->Materials_Cost = $materialCost;
                $subcontCost = new CurrencyValue();
                $subcontCost->value = round((floatval($agreementProductResult['Subcontractor_Cost']) * $tQty), 2);
                $subcontCost->currency = $currencies[0];
                $agmntpdct->Sub_Cost = $subcontCost;
                $eqmtCost = new CurrencyValue();
                $eqmtCost->value = round((floatval($agreementProductResult['Equipment_Cost']) * $tQty), 2);
                $eqmtCost->currency = $currencies[0];
                $agmntpdct->Equipment_Cost = $eqmtCost;
                OpportunityUtils::AssemblyProductSave($agmntpdct, $agmnt, $quantity, $frequency);
            }
            $totalDirectCost = new CurrencyValue();
            $totalDirectCost->value = round(($agmntpdct->Labor_Cost->value + $agmntpdct->Burden_Cost->value + $agmntpdct->Materials_Cost->value + $agmntpdct->Equipment_Cost->value + $agmntpdct->Sub_Cost->value + $agmntpdct->Other_Cost->value), 2);
            $totalDirectCost->currency = $currencies[0];
            $agmntpdct->Total_Direct_Cost = $totalDirectCost;
            $finalCost = new CurrencyValue();
            $finalCost->value = round(($agmntpdct->Total_Direct_Cost->value / (1 - ($agmntpdct->Category_GPM / 100))), 2);
            $finalCost->currency = $currencies[0];
            $agmntpdct->Suggested_Cost = $finalCost;
            if (!$agmntpdct->save()) {
                throw new Exception();
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    protected function AssemblyProductCalculation($costbook) {
        $vAssemblyDetails = '';
        $vCategories = '';
        $assemblyDetail = trim($costbook->assemblydetail);
        if (empty($assemblyDetail)) {
            return FALSE;
        }
        $vAssemblyDetails = explode(';', $costbook->assemblydetail);
        $mHTotal = 0;
        $laborTotal = 0;
        $burdenTotal = 0;
        $equipmentTotal = 0;
        $materialTotal = 0;
        $subcontractorTotal = 0;
        $othersTotal = 0;
        $productArray = array();
        foreach ($vAssemblyDetails as $vAssemblyDetail) {
            $productDetails = explode('|', $vAssemblyDetail);
            $dataProductCode = Costbook::getByProductCode($productDetails[1]);
            if (isset($dataProductCode[0])) {
                if ($dataProductCode[0]->costofgoodssold->value == Constant::LABOUR) {
                    $laborTotal += ($dataProductCode[0]->departmentreference->laborCost) * $productDetails[2];
                    $burdenTotal += ($dataProductCode[0]->departmentreference->burdonCost) * $productDetails[2];
                    $mHTotal += $productDetails[2];
                } else {
                    $laborCost = 0;
                }
                if ($dataProductCode[0]->costofgoodssold->value == Constant::EQUIPMENT) {
                    $equipmentCost = $dataProductCode[0]->costperunit;
                    $equipmentTotal += $equipmentCost * $productDetails[2];
                } else {
                    $equipmentCost = 0;
                }
                if ($dataProductCode[0]->costofgoodssold->value == Constant::MATERIAL) {
                    $materialCost = $dataProductCode[0]->costperunit;
                    $materialTotal += $materialCost * $productDetails[2];
                } else {
                    $materialCost = 0;
                }
                if ($dataProductCode[0]->costofgoodssold->value == Constant::SUBCONTRACT) {
                    $subcontractorCost = $dataProductCode[0]->costperunit;
                    $subcontractorTotal += $subcontractorCost * $productDetails[2];
                } else {
                    $subcontractorCost = 0;
                }
                if ($dataProductCode[0]->costofgoodssold->value == Constant::OTHER) {
                    $otherCost = $dataProductCode[0]->costperunit;
                    $othersTotal += $otherCost * $productDetails[2];
                } else {
                    $otherCost = 0;
                }
            } else {
                continue;
            }
        }
        $productArray['Total_Labour_Cost'] = $laborTotal;
        $productArray['Total_Burden_Cost'] = $burdenTotal;
        $productArray['Total_MHR'] = $mHTotal;
        $productArray['Materials_Cost'] = $materialTotal;
        $productArray['Equipment_Cost'] = $equipmentTotal;
        $productArray['Subcontractor_Cost'] = $subcontractorTotal;
        $productArray['Other_Cost'] = $othersTotal;
        return $productArray;
    }

}

?>
