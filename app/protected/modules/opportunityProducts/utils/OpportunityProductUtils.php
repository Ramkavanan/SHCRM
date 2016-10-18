<?php

    /**
     * Class utilized by opportunity product selection
     * 
     * @author Ramachandran.K 
     */
    class OpportunityProductUtils   {
        
        const LABOR = 'Labor';
        const EQUIPMENT = 'Equipment';
        const MATERIAL = 'Material';
        const SUBCONTRACT = 'Subcontractor';
        const ASSEMBLY = 'Assembly';
        const OTHER = 'Other';
        const PROJECTFINAL = 'Project Final';
        const RECURRINGFINAL = 'Recurring Final';

        public function addProductFindDuplicate($category, $addProductoptId, $productCode){
              $opptproducts = OpportunityProduct::getOpptPdctBypdctCode($category,$addProductoptId, $productCode);  
              if($opptproducts != null){
                  return TRUE;
              }else {
                  return FALSE;
              }
        }

        public function makeDropDownByCategory($data, $uniqId, $opportunityId, $prdctCode) {
            $countOfDatas=0;
            $datas = explode(',',$data);
            $element = '<select id="Category_'.$uniqId.'" style="width:75%;">';
            for($i=0;$i < count($datas);$i++) {
                // Manual Query for remove Selected category from category dropdown in Add Product Page
                $optProduct = OpportunityProduct::getOpptPdctBypdctCode(ltrim($datas[$i]),$opportunityId, $prdctCode);
                if( $optProduct == null) {
                    $element .= '<option value="'.ltrim($datas[$i]).'">'.ltrim($datas[$i]).'</option>'; 
                    $countOfDatas = count($datas);
                 } 
             }
            $element .= '</select>';
            if($countOfDatas != null){
                return $element;
            }
            else{
                return null;
            }
        }
        
        public static function getCurrencyType() {
            return '$';
        }

        public function makeCostBookProductSelection($datas, $opportunityId) {
            $defaultThemeName       = 'default';
            $defaultThemeBaseUrl    = Yii::app()->themeManager->baseUrl . '/' . $defaultThemeName;
            $categories = Category::getAll();
            $TotalDirectCost=0;
            $pageOffset = 1;
            $content = '<div id="FlashMessageView"></div><div>';
            $opportunityProducts = OpportunityProduct::getAllByOpptId(intval($opportunityId));
            $opportunity = Opportunity::getById(intval($opportunityId));
                $countOfSelectedRow = 0;
                $content .= '<div style="margin-left:42%;"><a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" class="attachLoading cancel-button">
                                <span class="z-label appendButton">
                                    Go Back
                                </span>
                                </a>
                                <a class="attachLoading " id="Save" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" href="#">
                                <span class="z-label">
                                    Save
                                </span>
                                </a>
                            </div>';
                if($opportunity->recordType->value =='Recurring Final') {
                        
                        $content .= '<div class="cgrid-view type-opportunityProducts" id="list-view">
                                <div class="summary">
                                    5 result(s)
                                </div>
                                <div id="add_Product_list_table" class="table_border_width">
                                    <table id="add_Product_List_table_Value" class="add_Product_List_table_Value">
                                        <tr>
                                            <td><label id="totalMhr"> Total Mhr : 0 </label></td>
                                            <td><label id="totalDirectCost"> Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                            <td><label id="budget"> Budget '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                        </tr>
                                        <tr>
                                            <td><label id="Revenue_MHR"> Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': 0.0 </label></td>
                                            <td><label id="Aggregate_GPM"> Aggregate GPM %: 0 </label></td>
                                            <td><label id="Suggested_Price"> Suggested Price '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp</td>';
                                                if($opportunity->aggregateGPM > 0)
                                                {
                                                    $final_gpm_value = $opportunity->aggregateGPM;
                                                    
                                                }
                                                else
                                                {
                                                    $final_gpm_value = 40;
                                                }
                                                $content .='<td><label id="final_GPM"> Final GPM %: <input type="text" name="finalGpm" id="final_gpm" style="width:12%" value='.$final_gpm_value.' disabled> </label></td>
                                            <td><label id="final_amount"> Final Amount '.OpportunityProductUtils::getCurrencyType().': <input type="text" name="finalAmount" style="width:22%" id="final_amt" value="0"> </label></td>
                                        </tr>
                                    </table>
                                </div>';
                        $content .='<input value="0" name="Selected_Products_Ids" id="Selected_Products_Ids" type="hidden">
                                    <input value="0" name="Selected_Products_flag" id="Selected_Products_flag" type="hidden">
                                    <input value="0" name="modified_final_amt" id="modified_final_amt" type="hidden">';
                               
                                if(count($opportunityProducts) > 0){
                                    $content .= '<div id="selected_products" class="table_border_width" style="padding: 0%;">
                                        <div class="align_left" style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold;">
                                             Selected Products <span id="showresults"></span>
                                        </div>
                                            <div style="margin:0.5% 0% 0.5% 45%">
                                                <a href="#" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" class="cancel-button" id="update">
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
                                                    <th>Quantity</th>
                                                    <th>Frequency</th>
                                                    <th>MH</th>
                                                    <th>L+OH</th>
                                                    <th>M</th>
                                                    <th>E</th>
                                                    <th>S</th>
                                                    <th>O</th>
                                                    <th>Total Direct Cost</th>
                                                </tr>';
                                    
                                    foreach($opportunityProducts as $row) {
                                       $opportunityPdctMap[$row->Category][] = $row;
                                    }
                                    $CategoryKeyCount = 0;
                                    $totalMhr = 0;
                                    $totalDirectCost = 0;
                                    $totalFinalPrice = 0.0;
				                    $actualGPM = 0;
                                    $totalRevenue = 0.0;
                                    foreach($opportunityPdctMap as $CategoryKey => $opportunityArray){
                                        $content .='<tr>
                                              <th colspan="12" class="align_left" style="background-color:gray;color:white;">'.$CategoryKey.'</th>
                                              <input type="hidden" name="CategoryKey" id="CategoryKey_'.$CategoryKeyCount.'" value="'.$CategoryKey.'">
                                            </tr>';
                                        foreach ($opportunityArray as $opportunityKey => $opportunitypdt){
                                            $totalMhr += $opportunitypdt->Total_MHR;
                                            $totalDirectCost += $opportunitypdt->Total_Direct_Cost->value;
                                            $totalFinalPrice += $opportunitypdt->Final_Cost->value;
                                            $content .='<tr>
                                                <td>'.$opportunitypdt->costbook->productcode.'</td>
                                                <input value='.$opportunitypdt->id.' name="list_View_Add_Product_SelectedIds"id="list_View_Producted_SelectedIds_'.$countOfSelectedRow.'" type="hidden">
                                                <td>'.$opportunitypdt->name.'</td>
                                                <td>'.$opportunitypdt->costbook->unitofmeasure.'</td>
                                                <td><input type="text" name="updateFrequency&Quantity" id="updateQuantity_'.$countOfSelectedRow.'" value='.$opportunitypdt->Quantity.'></td>
                                                <td><input name="updateFrequency&Quantity" type="text" id="updateFrequency_'.$countOfSelectedRow.'" value='.$opportunitypdt->Frequency.'></td>
                                                <td>'.$opportunitypdt->Total_MHR.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().(($opportunitypdt->Labor_Cost->value)+($opportunitypdt->Burden_Cost->value)).'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Materials_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Equipment_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Sub_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Other_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Total_Direct_Cost->value.'</td>
                                            </tr>';
                                            $countOfSelectedRow++;
                                          }
                                          $CategoryKeyCount++;
                                    }
                                    if($totalFinalPrice > 0) {
						$actualGPM = 	(($totalFinalPrice - $totalDirectCost)/$totalFinalPrice)*100;		
					}
                                    if($totalMhr > 0) {
                                        $totalRevenue = $totalFinalPrice/$totalMhr;				
                                    }
                                    Yii::app()->clientScript->registerScript('calculationForAddProductScreenRecurring',
                                        '$(Selected_Products_Ids).val("'.$countOfSelectedRow.'")
                                         $("#totalMhr").text("Total Mhr : '.$totalMhr.'");
                                         $("#totalDirectCost").text("Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': '.$totalDirectCost.'");   
                                         $("#budget").text("Budget '.OpportunityProductUtils::getCurrencyType().' : '.$opportunity->budget->value.'");
                                         $("#Suggested_Price").text("Suggested Price '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f', $totalFinalPrice).'");
					 $("#Aggregate_GPM").text("Aggregate GPM %: '.sprintf('%.2f', $actualGPM).'");
                                         //$("#Roundedvalue").text(" Rounded value").css({"color":"red"});    
					 $("#Revenue_MHR").text("Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f',$totalRevenue).'");    
                                        // For the final amount
                                         if('.$totalDirectCost.' >0){
                                            var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                            $("#final_amt").val('.$opportunity->finalAmount->value.');
                                         } 
                                         $("#final_gpm").keyup(function() {
                                             if('.$totalDirectCost.' >0){
                                                 var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                                 $("#final_amt").val(tot_amt.toFixed(2));
                                                 $("#modified_final_amt").val(0);
                                             }else {
                                                 $("#final_amt").val(0);
                                                 $("#modified_final_amt").val(0);
                                             }
                                         });

					$("#final_amt").keyup(function() 
					{
                                            if('.$totalDirectCost.' >0)
                                            {
                                                if($("#final_amt").val() > '.$totalDirectCost.')
                                                {
                                                    var tot_gpm = (($("#final_amt").val()*100)-('.$totalDirectCost.'*100))/$("#final_amt").val();
                                                    $("#final_gpm").val(tot_gpm.toFixed(2));
                                                    $("#modified_final_amt").val($("#final_amt").val());
                                                }
                                            }                                          
                                        });
                                     ');
                          $content .=' </table></div></td></tr></table></div>';
                          
                            }
                        $content .='<div class="table_border_width" id="add_product_search" style="padding: 0px;">
                                         <div class="panel">
                                            <div class="align_left" style="color:black; background-color:#E0D1D1; color:black; padding:0.5%; font-weight:bold;">Search</div>
                                            <table class="form-fields">
                                                <colgroup><col class="col-0"><col class="col-1"></colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th width="20%">
                                                            <label for="oppt_AddProductcategory_value">Select Category</label>
                                                        </th>
                                                        <td colspan="1" >
                                                            <div class="hasDropDown">
                                                                <span class="select-arrow"></span>
                                                                    <select id="oppt_AddProductcategory_value" name="Costbook[assemblycategory][value]">
                                                                        <option value="All">All</option>';
                                                                          foreach($categories as $values) {
                                                                          $content .= '<option value="'.$values->name.'">'.$values->name.'</option>';
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
                                                            <div class="hasDropDown">
                                                                <span class="select-arrow"></span>
                                                                    <select id="oppt_AddProductcostofgoodssold_value" name="Costbook[costofgoodssoldassembly][value][assemblycategory][value]">
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
                                                        <td colspan="1"><input type="text" id="opp_productname_value" onkeyup="javascript:searchProducts(\''.$opportunityId.'\',\''.$pageOffset.'\', this);" name="productname" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="costofgoodssoldassembly"></label>
                                                        </td>
                                                        <td colspan="1">
                                                        <div style="margin-left:32%;">                                                            
                                                            <a id="search" onClick="javascript:searchProducts(\''.$opportunityId.'\',\''.$pageOffset.'\', this);" class="attachLoading z-button cancel-button" name="Search" href="#">
                                                                <span id="z-spinner_i2i_search" style="display:none;"><img src="'.$defaultThemeBaseUrl.'/images/zbutton-spinner.gif"></span>
                                                                <span class="z-label">Search</span>
                                                            </a>
                                                        </div>    
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                         </div>
                                     </div>
                                </div>';
                       $content .= OpportunityProductUtils::appendDynamicBtns($opportunityId);                                                                    
                       $content .='<div class="items-wrapper" id="addProductWrapper" style ="margin-top : 2%;">
                       <input type="hidden" id="selectedProductCnt" value="'.$countOfSelectedRow.'" />
                       <div id="searchProducts" style="margin-bottom:2%; display : none;">
                            <div id="search_title" style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div>
                            <table class="items selected_products_table">
                                <thead>
                                    <tr>
                                        <th id="list-view-rowSelector" class="checkbox-column">  <label class="hasCheckBox">  <input type="checkbox" id="list-view-rowSelector_all" name="list-view-rowSelector_all" value="1">   </label>  </th>
                                        <th style="width:10%;" id="list-view_c1"> <a  class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'code\', \'asc\');">  Product Code </a>  </th>
                                        <th style="width:25%; padding: 0px;" id="list-view_c2">  <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'name\', \'asc\');">  Product Name  </a>  </th>
                                        <th style="width:15%; padding: 0px;" id="list-view_c3"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'unit\', \'asc\');"> Unit of Measure </a> </th>
                                        <th style="width:10%; padding: 0px;" id="list-view_c4"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'cost\', \'asc\');"> Unit Direct Cost </a> </th>
                                        <th style="width:10%; padding: 0px;" id="list-view_c5"> Quantity </th>
                                        <th style="width:10%; padding: 0px;" id="list-view_c5"> Frequency </th>
                                        <th style="width:20%; padding: 0px;" id="list-view_c7"> Category </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8" style = "padding: 0px;">
                                            <div id="opt_scroll_result">
                                                <table>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>   
                        </div>';
                }else {
                    $content .= '<div class="cgrid-view type-opportunityProducts" id="list-view">
                                <div class="summary">
                                    5 result(s)
                                </div>
                                <div id="add_product_outer">
                                    <div id="add_Product_list_table" class="table_border_width">
                                        <table id="add_Product_List_table_Value" class="add_Product_List_table_Value">
                                            <tr>
                                                <td><label id="totalMhr"> Total Mhr : 0 </label></td>
                                                <td><label id="totalDirectCost"> Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                                <td><label id="budget"> Budget '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                            </tr>
                                            <tr>
                                                <td><label id="Revenue_MHR"> Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': 0.0 </label></td>
                                                <td><label id="Aggregate_GPM"> Aggregate GPM %: 0 </label></td>
                                                <td><label id="Suggested_Price"> Suggested Price '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp</td>';
                                                if($opportunity->aggregateGPM > 0)
                                                {
                                                    $final_gpm_value = $opportunity->aggregateGPM;
                                                    
                                                }
                                                else
                                                {
                                                    $final_gpm_value = 40;
                                                }
                                                $content .='<td><label id="final_GPM"> Final GPM %: <input type="text" name="finalGpm" id="final_gpm" style="width:12%"  value='.$final_gpm_value.' disabled> </label></td>
                                                <td><label id="final_amount"> Final Amount '.OpportunityProductUtils::getCurrencyType().': <input type="text" name="finalAmount" style="width:22%" id="final_amt" value='.$opportunity->finalAmount->value.'> </label></td>
                                            </tr>
                                        </table>
                                    </div>';
                    $content .='<input value="0" name="Selected_Products_Ids" id="Selected_Products_Ids" type="hidden">
                                <input value="0" name="Selected_Products_flag" id="Selected_Products_flag" type="hidden">
                                <input value="0" name="modified_final_amt" id="modified_final_amt" type="hidden">';
                                
                                if(count($opportunityProducts) > 0){
                                    $content .= '<div id="selected_products" class="table_border_width" style="padding: 0px;">
                                    
                                        <div class="align_left" style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold;">
                                             Selected Products <span id="showresults" style="color:green; font-weight:none;"></span>
                                        </div>
                                            <div style="margin:0.5% 0% 0.5% 45%">
                                                <a href="#" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);"class="cancel-button"id="update">
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
                                                    <th>Quantity</th>
                                                    <th>MH</th>
                                                    <th>L+OH</th>
                                                    <th>M</th>
                                                    <th>E</th>
                                                    <th>S</th>
                                                    <th>O</th>
                                                    <th>Total Direct Cost</th>
                                                    <th>Total Final Price</th>
                                                </tr>';
                                    
                                    foreach($opportunityProducts as $row) {
                                       $opportunityPdctMap[$row->Category][] = $row;
                                    }
                                    $CategoryKeyCount = 0;
                                    $totalMhr = 0;
                                    $totalDirectCost = 0;
                                    $totalDirectCost1 = 0;
				                    $totalFinalPrice = 0.0;
				                    $actualGPM = 0;
                                    $totalRevenue = 0.0;
                                    
                                    
                                    foreach($opportunityPdctMap as $CategoryKey1 => $opportunityArray1){                                        
                                        foreach ($opportunityArray1 as $opportunityKey1 => $opportunitypdt1){
                                            $totalDirectCost1 += $opportunitypdt1->Total_Direct_Cost->value;					    
                                          }
                                    $CategoryKeyCount++;
                                    }
                                    
                                    
                                    foreach($opportunityPdctMap as $CategoryKey => $opportunityArray){
                                        $content .='<tr>
                                              <th colspan="12" class="align_left" style="background-color:gray; color:white;">'.$CategoryKey.'</th>
                                              <input type="hidden" name="CategoryKey" id="CategoryKey_'.$CategoryKeyCount.'" value="'.$CategoryKey.'">
                                            </tr>';
                                        foreach ($opportunityArray as $opportunityKey => $opportunitypdt){
                                            $totalMhr += $opportunitypdt->Total_MHR;
                                            $totalDirectCost += $opportunitypdt->Total_Direct_Cost->value;
					    $totalFinalPrice += $opportunitypdt->Final_Cost->value;
                                            //print_r($opportunitypdt);exit();
                                            $content .='<tr>
                                                <td>'.$opportunitypdt->costbook->productcode.'</td>
                                                <input value='.$opportunitypdt->costbook->productcode.' name="productCode" id="productCode_'.$countOfSelectedRow.'" type="hidden">    
                                                <input value='.$opportunitypdt->id.' name="list_View_Add_Product_SelectedIds"id="list_View_Producted_SelectedIds_'.$countOfSelectedRow.'" type="hidden">
                                                <td>'.$opportunitypdt->name.'</td>
                                                <td>'.$opportunitypdt->costbook->unitofmeasure.'</td>
                                                <td><input type="text" id="updateQuantity_'.$countOfSelectedRow.'" value='.$opportunitypdt->Quantity.'></td>
                                                <td>'.$opportunitypdt->Total_MHR.'</td>    
                                                <td>'.OpportunityProductUtils::getCurrencyType().(($opportunitypdt->Labor_Cost->value)+($opportunitypdt->Burden_Cost->value)).'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Materials_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Equipment_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Sub_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Other_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Total_Direct_Cost->value.'</td>
                                                <td class="prototal">'.OpportunityProductUtils::getCurrencyType() .round($opportunitypdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)),2) .'</td>
                                            </tr>';
                                            $countOfSelectedRow++;
                                          }
                                    $CategoryKeyCount++;
                                    }
					if($totalFinalPrice > 0) {
						$actualGPM = 	(($totalFinalPrice - $totalDirectCost)/$totalFinalPrice)*100;		
					}
					if($totalMhr > 0) {
						$totalRevenue = $totalFinalPrice/$totalMhr;				
					}
                                    
                                     Yii::app()->clientScript->registerScript('calculationForAddProductScreen',
                                        '$(Selected_Products_Ids).val("'.$countOfSelectedRow.'");
                                         $("#totalMhr").text("Total Mhr : '.$totalMhr.'");
                                         $("#totalDirectCost").text("Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': '.$totalDirectCost.'");   
                                         $("#budget").text("Budget '.OpportunityProductUtils::getCurrencyType().': '.$opportunity->budget->value.'");   
					 $("#Suggested_Price").text("Suggested Price '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f', $totalFinalPrice).'");
					 $("#Aggregate_GPM").text("Aggregate GPM %: '.sprintf('%.2f', $actualGPM).'");
                                         //$("#Roundedvalue").text(" Rounded value").css({"color":"red"});
					 $("#Revenue_MHR").text("Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f',$totalRevenue).'");
      
                                         // For the final amount
                                         if('.$totalDirectCost.' > 0){
                                             if('.$opportunity->finalAmount->value.' > 0)
                                                 var tot_amt = '.$opportunity->finalAmount->value.';
                                             else
                                                var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                            $("#final_amt").val(tot_amt.toFixed(2));
                                         } 
                                         $("#final_gpm").keyup(function() {
                                             if('.$totalDirectCost.' >0){
                                                 var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                                 $("#final_amt").val(tot_amt.toFixed(2));
                                                 $("#modified_final_amt").val(0);
                                             } else {
                                                 $("#final_amt").val(0);
                                                 $("#modified_final_amt").val(0);
                                             }
                                         });
					$("#final_amt").keyup(function() 
					{
                                            if('.$totalDirectCost.' >0)
                                            {
                                                if($("#final_amt").val() > '.$totalDirectCost.')
                                                {
                                                    var tot_gpm = (($("#final_amt").val()*100)-('.$totalDirectCost.'*100))/$("#final_amt").val();
                                                    $("#final_gpm").val(tot_gpm.toFixed(2));
                                                    $("#modified_final_amt").val($("#final_amt").val());
                                                }
                                            }                                          
                                        });
                                         // Ends here
                                     ');

                                    
//                          $content .='<input value="'.$countOfSelectedRow.'" name="Selected_Products_Ids" id="Selected_Products_Ids" type="hidden">';
                          $content .=' </table></div></td></tr></table></div>
                            </div>';
                    }
                       $content .='<div class="table_border_width" id="add_product_search" style="padding: 0px;">
                                         <div class="panel">
                                            <div class="align_left" style="color:black; background-color:#E0D1D1; color:black; padding:0.5%; font-weight:bold;">Search</div>
                                            <table class="form-fields items">
                                                <colgroup><col class="col-0"><col class="col-1"></colgroup>
                                                <tbody>
                                                    <tr>
                                                        <th width="20%">
                                                            <label for="oppt_AddProductcategory_value">Select Category</label>
                                                        </th>
                                                        <td colspan="1">
                                                            <div class="hasDropDown">
                                                                <span class="select-arrow"></span>
                                                                    <select id="oppt_AddProductcategory_value" name="Costbook[assemblycategory][value]">
                                                                        <option value="All">All</option>';
                                                                      foreach($categories as $values) {
                                                                      $content .= '<option value="'.$values->name.'">'.$values->name.'</option>';
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
                                                                    <select id="oppt_AddProductcostofgoodssold_value" name="Costbook[costofgoodssoldassembly][value][assemblycategory][value]">
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
                                                        <td colspan="1"><input type="text" id="opp_productname_value" onkeyup="javascript:searchProducts(\''.$opportunityId.'\',\''.$pageOffset.'\', this);" name="productname" /></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="costofgoodssoldassembly"></label>
                                                        </td>
                                                        <td colspan="1">
                                                        <div style="margin-left:32%;">
                                                            <a id="search" onclick="javascript:searchProducts(\''.$opportunityId.'\',\''.$pageOffset.'\', this);" class="attachLoading cancel-button" name="Search" href="#">
                                                                <span id="z-spinner_i2i_search" style="display:none;"><img src="'.$defaultThemeBaseUrl.'/images/zbutton-spinner.gif" ></span>
                                                                <span class="z-label">  Search</span>
                                                            </a>
                                                        </div>    
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                         </div>
                                     </div>';
//                       $content .= OpportunityProductUtils::appendButton($opportunityId);
                       $content .= OpportunityProductUtils::appendDynamicBtns($opportunityId);
                       $content .='<div class="items-wrapper" id="addProductWrapper" style ="margin-top : 2%;">
                       <input type="hidden" id="selectedProductCnt" value="'.$countOfSelectedRow.'" />
                       <div id="searchProducts" style="margin-bottom:2%; display : none;">
                            <div id="search_title" style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div>
                            <table class="items selected_products_table">
                                <thead>
                                    <tr>
                                        <th id="list-view-rowSelector" class="checkbox-column">  <label class="hasCheckBox">  <input type="checkbox" id="list-view-rowSelector_all" name="list-view-rowSelector_all" value="1">   </label>  </th>
                                        <th style="width:15%;" id="list-view_c1"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'code\', \'asc\');">  Product Code  </a></th>
                                        <th style="width:25%;" id="list-view_c2"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'name\', \'asc\');">  Product Name  </a> </th>
                                        <th style="width:15%;" id="list-view_c3"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'unit\', \'asc\');"> Unit of Measure </a> </th>
                                        <th style="width:10%;" id="list-view_c4"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$opportunityId.', 1, \'cost\', \'asc\');"> Unit Direct Cost </a> </th>
                                        <th style="width:15%;" id="list-view_c5"> Quantity </th>
                                        <th style="width:20%;" id="list-view_c7"> Category </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" style = "padding: 0px;">
                                            <div id="opt_scroll_result">
                                                <table>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>   
                       
                        </div>';
                    //$content .= OpportunityProductUtils::appendDynamicBtns($opportunityId); 
               }
               
                $count = 0;
                $content1='';
          
               if($opportunity->recordType->value =='Recurring Final') {
                   foreach($datas as $data) {
                       $dispCategory = OpportunityProductUtils::makeDropDownByCategory($data -> category, $count, $opportunityId, $data -> productcode);
                        $count++;   
                    }
                } else {
                    foreach($datas as $data) {
                                    $dispCategory = OpportunityProductUtils::makeDropDownByCategory($data -> category, $count, $opportunityId, $data -> productcode);
                        $count++;   
                    }
                }
        $content .= '</tbody>
                         </table>
                            <input value="" name="list-view-selectedIds" id="list-view-selectedIds" type="hidden">
                         </div>
                         
                     <input value="'.$opportunity->recordType->value.'" name="recordType" id="recordType_Ids" type="hidden">';
        $content .= '<div id = "uppendBtn" style= "text-align: center;">
                        <div id = "afterSearchCostBookDatas" style="display : none;">
                            <a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" class="attachLoading cancel-button">
                                <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Go Back</span>
                            </a>
                            <a href="#" id="saveAndMore" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" name="saveAddMore" class="attachLoading">                                            
                                <span class="z-label">
                                    Save Add More
                                </span>
                            </a>
                            <a class="attachLoading " id="Save" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" href="#">
                                <span class="z-label">
                                    Save
                                </span>
                            </a>
                        </div>
                    </div></div></div>';
        
        return $content;
    }

    public function appendDynamicBtns($opportunityId){
        $content = '<div id = "uppendBtn" style= "text-align: center;">
                        <div id = "afterSearch" class="afterSearchCostBookDatas" style="display:none;">
                            <a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" class="attachLoading cancel-button">
                            <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Go Back</span>
                            </a>
                            <a href="#" id="saveAndMore" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" name="saveAddMore" class="attachLoading">                                            
                            <span class="z-label">
                                Save Add More
                            </span>
                            </a>
                            <a class="attachLoading " id="Save" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" href="#">
                            <span class="z-label">
                                Save
                            </span>
                            </a>
                        </div>
                    </div>';
        return $content;
    }
    
    protected function AssemblyProductCalculation($costbook){
            $vAssemblyDetails = '';
            $vCategories = '';
            $assemblyDetail = trim($costbook->assemblydetail);
            if (empty($assemblyDetail)){
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
               if(isset($dataProductCode[0])) {
                    if($dataProductCode[0]->costofgoodssold->value == OpportunityProductUtils::LABOR) {
                        $laborTotal += ($dataProductCode[0]->departmentreference->laborCost) * $productDetails[2];
                        $burdenTotal += ($dataProductCode[0]->departmentreference->burdonCost) * $productDetails[2];
                        $mHTotal +=  $productDetails[2];
                    } else { $laborCost = 0; }
                    if($dataProductCode[0]->costofgoodssold->value == OpportunityProductUtils::EQUIPMENT) {
                        $equipmentCost = $dataProductCode[0]->costperunit;
                        $equipmentTotal +=  $equipmentCost * $productDetails[2];
                    } else { $equipmentCost = 0; }
                    if($dataProductCode[0]->costofgoodssold->value == OpportunityProductUtils::MATERIAL) {
                        $materialCost = $dataProductCode[0]->costperunit;
                        $materialTotal +=  $materialCost * $productDetails[2];
                    } else { $materialCost = 0; }
                    if($dataProductCode[0]->costofgoodssold->value == OpportunityProductUtils::SUBCONTRACT) {
                        $subcontractorCost = $dataProductCode[0]->costperunit;
                        $subcontractorTotal +=  $subcontractorCost * $productDetails[2];
                    } else { $subcontractorCost = 0; }
                    if($dataProductCode[0]->costofgoodssold->value == OpportunityProductUtils::OTHER) {
                        $otherCost = $dataProductCode[0]->costperunit;
                        $othersTotal += $otherCost * $productDetails[2];
                    } else { $otherCost = 0; }
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

    public function addOpportunityProductsCalculation(Costbook $costbook,$quantity,$frequency,$optId,$category) {
        try{
            $tQty = 0;
            $opportunityProduct = new OpportunityProduct();
            $opportunityProduct->Quantity = (float) $quantity;
            
            //Frequency is set if the project record type is recurring otherwise not set 
            if($frequency != '') {  
                $opportunityProduct->Frequency = intval($frequency);
            }
            
            $opportunityProduct->Category = $category;
            $opportunityProduct->Product_Code = $costbook->productcode;
            $opportunityProduct->name = $costbook->productname; 
            $currencies = Currency::getAll();

            $opportunityProduct->costbook = $costbook;
            $tQty = (float)$quantity;
            if($opportunityProduct->Frequency > 0) {
                $tQty *= $opportunityProduct->Frequency;
            }

            //Labor Product calculation
            if($costbook->costofgoodssold->value == 'Labor') {
//                $opportunityProduct->Total_MHR = round($costbook->costperunit * $tQty);
                $opportunityProduct->Total_MHR = round($tQty, 2);
                if(floatval($costbook->departmentreference->laborCost) > 0 && floatval($costbook->departmentreference->burdonCost) > 0) {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = round((floatval($costbook->departmentreference->burdonCost) * $tQty),2);
                    $burdenCost->currency          = $currencies[0];
                    $opportunityProduct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = round((floatval($costbook->departmentreference->laborCost) * $tQty),2);
                    $laborCost->currency          = $currencies[0];
                    $opportunityProduct->Labor_Cost = $laborCost;
                } else if(floatval($costbook->departmentreference->laborCost) > 0 &&  floatval($costbook->departmentreference->burdonCost) <= 0) {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = 0.0;
                    $burdenCost->currency          = $currencies[0];
                    $opportunityProduct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = round((floatval($costbook->departmentreference->laborCost) * $tQty),2);
                    $laborCost->currency          = $currencies[0];
                    $opportunityProduct->Labor_Cost = $laborCost;
                } else if(floatval($costbook->departmentreference->burdonCost) > 0 && floatval($costbook->departmentreference->laborCost) <= 0) {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = round((floatval($costbook->departmentreference->burdonCost) * $tQty),2);
                    $burdenCost->currency          = $currencies[0];
                    $opportunityProduct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = 0.0;
                    $laborCost->currency          = $currencies[0];
                    $opportunityProduct->Labor_Cost = $laborCost;

                } else {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = 0.0;
                    $burdenCost->currency          = $currencies[0];
                    $opportunityProduct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = 0.0;
                    $laborCost->currency          = $currencies[0];
                    $opportunityProduct->Labor_Cost = $laborCost;
                }
                //$opportunityProduct->Total_MHR = $tQty;
            } else {
                $burdenCost                    = new CurrencyValue();
                $burdenCost->value             = 0.0;
                $burdenCost->currency          = $currencies[0];
                $opportunityProduct->Burden_Cost = $burdenCost;
                $laborCost                    = new CurrencyValue();
                $laborCost->value             = 0.0;
                $laborCost->currency          = $currencies[0];
                $opportunityProduct->Labor_Cost = $laborCost;
            }

            if($costbook->costofgoodssold->value == 'Material') {
               // $opportunityProduct->Total_MHR = round($tQty);
                $materialCost                    = new CurrencyValue();
                $materialCost->value             = round((floatval($costbook->unitdirectcost)*$tQty),2);
                $materialCost->currency          = $currencies[0];
                $opportunityProduct->Materials_Cost = $materialCost;
            } else {
                $materialCost                    = new CurrencyValue();
                $materialCost->value             = 0.0;
                $materialCost->currency          = $currencies[0];
                $opportunityProduct->Materials_Cost = $materialCost;
            }

            if($costbook->costofgoodssold->value == 'Equipment') {
                //$opportunityProduct->Total_MHR = round($tQty);
                $eqmtCost                    = new CurrencyValue();
                $eqmtCost->value             = round((floatval($costbook->unitdirectcost)*$tQty),2);
                $eqmtCost->currency          = $currencies[0];
                $opportunityProduct->Equipment_Cost = $eqmtCost;
            } else {
                $eqmtCost                    = new CurrencyValue();
                $eqmtCost->value             = 0.0;
                $eqmtCost->currency          = $currencies[0];
                $opportunityProduct->Equipment_Cost = $eqmtCost;            
            }

            if($costbook->costofgoodssold->value == 'Subcontractor') {
                //$opportunityProduct->Total_MHR = round($tQty);

                $subcontCost                    = new CurrencyValue();
                $subcontCost->value             = round((floatval($costbook->unitdirectcost)*$tQty),2);
                $subcontCost->currency          = $currencies[0];
                $opportunityProduct->Sub_Cost = $subcontCost;
            } else {
                $subcontCost                    = new CurrencyValue();
                $subcontCost->value             = 0.0;
                $subcontCost->currency          = $currencies[0];
                $opportunityProduct->Sub_Cost = $subcontCost;
            }
            
            if($costbook->costofgoodssold->value == 'Assembly') {
                $opportunityProductResult  = OpportunityProductUtils::AssemblyProductCalculation($costbook);
                $opportunityProduct->Total_MHR = round(floatval($opportunityProductResult['Total_MHR'])*$tQty, 2);
                $burdenCost                    = new CurrencyValue();
                $burdenCost->value             = round((floatval($opportunityProductResult['Total_Burden_Cost'])*$tQty), 2);
                $burdenCost->currency          = $currencies[0];
                $opportunityProduct->Burden_Cost = $burdenCost;
                $laborCost                    = new CurrencyValue();
                $laborCost->value             = round((floatval($opportunityProductResult['Total_Labour_Cost'])*$tQty), 2);
                $laborCost->currency          = $currencies[0];
                $opportunityProduct->Labor_Cost = $laborCost;
                $materialCost                    = new CurrencyValue();
                $materialCost->value             = round((floatval($opportunityProductResult['Materials_Cost'])*$tQty), 2);
                $materialCost->currency          = $currencies[0];
                $opportunityProduct->Materials_Cost = $materialCost;
                $subcontCost                    = new CurrencyValue();
                $subcontCost->value             = round((floatval($opportunityProductResult['Subcontractor_Cost'])*$tQty), 2);
                $subcontCost->currency          = $currencies[0];
                $opportunityProduct->Sub_Cost = $subcontCost;
                $eqmtCost                    = new CurrencyValue();
                $eqmtCost->value             = round((floatval($opportunityProductResult['Equipment_Cost'])*$tQty), 2);
                $eqmtCost->currency          = $currencies[0];
                $opportunityProduct->Equipment_Cost = $eqmtCost;
            }

            if($costbook->costofgoodssold->value == 'Other') {
               // $opportunityProduct->Total_MHR = round($tQty);
                $otherCost                    = new CurrencyValue();
                $otherCost->value             = round((floatval($costbook->unitdirectcost)*$tQty), 2);
                $otherCost->currency          = $currencies[0];
                $opportunityProduct->Other_Cost = $otherCost;
            } else {
                $otherCost                    = new CurrencyValue();
                $otherCost->value             = 0.0;
                $otherCost->currency          = $currencies[0];
                $opportunityProduct->Other_Cost = $otherCost;
            }
            $values = Category::getCategoryByName($category);
            if(is_array($values) && count($values) > 0) {   
                $opportunityProduct->Category_GPM = $values[0]->targetgpm;
            } else {
                $opportunityProduct->Category_GPM = 40;
            }
            $totalDirectCost = new CurrencyValue();
            $totalDirectCost->value             = round(($opportunityProduct->Labor_Cost->value+$opportunityProduct->Burden_Cost->value + $opportunityProduct->Materials_Cost->value + $opportunityProduct->Equipment_Cost->value + $opportunityProduct->Sub_Cost->value + $opportunityProduct->Other_Cost->value), 2);
            $totalDirectCost->currency          = $currencies[0];
            $opportunityProduct->Total_Direct_Cost = $totalDirectCost;
            $finalCost = new CurrencyValue();
            $finalCost->value             =  round(($opportunityProduct->Total_Direct_Cost->value/(1-($opportunityProduct->Category_GPM/100))), 2);
            $finalCost->currency          =  $currencies[0];
            $opportunityProduct->Final_Cost = $finalCost;
            $opportunityProduct->opportunity = $opportunity = Opportunity::GetById(intval($optId));          
            
            if(!$opportunityProduct->save()) {
                throw new Exception();
            }
            else return TRUE;
        } catch( Exception $ex) {
            //echo 'Exception occured'.$ex;       die;
             return FALSE;
        }
    }
        
        public function updateOpportunityProduct($opptPdct, $quantity, $frequency) {
            try{
                $currencies                       = Currency::getAll();
                $tQty = (float)$quantity;
                $opptPdct->Quantity = $tQty;
            if((float)$frequency > 0) { 
                        $opptPdct->Frequency = intval($frequency);
                        $tQty *= $opptPdct->Frequency;
            }
           if($opptPdct->costbook->costofgoodssold->value == 'Labor') {
  //              $opptPdct->Total_MHR = round($opptPdct->costbook->costperunit * $tQty);
               $opptPdct->Total_MHR = round($tQty, 2);
               if(floatval($opptPdct->costbook->departmentreference->laborCost) > 0 && floatval($opptPdct->costbook->departmentreference->burdonCost) > 0) {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = round((floatval($opptPdct->costbook->departmentreference->burdonCost) * $tQty),2);
                    $burdenCost->currency          = $currencies[0];
                    $opptPdct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = round((floatval($opptPdct->costbook->departmentreference->laborCost) * $tQty),2);
                    $laborCost->currency          = $currencies[0];
                    $opptPdct->Labor_Cost = $laborCost;
                } 
                else if(floatval($opptPdct->costbook->departmentreference->laborCost) > 0 &&  floatval($opptPdct->costbook->departmentreference->burdonCost) <= 0) {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = 0.0;
                    $burdenCost->currency          = $currencies[0];
                    $opptPdct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = round((floatval($opptPdct->costbook->departmentreference->laborCost) * $tQty),2);
                    $laborCost->currency          = $currencies[0];
                    $opptPdct->Labor_Cost = $laborCost;
                }
                else if(floatval($opptPdct->costbook->departmentreference->burdonCost) > 0 && floatval($opptPdct->costbook->departmentreference->laborCost) <= 0) {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = round((floatval($opptPdct->costbook->departmentreference->burdonCost) * $tQty),2);
                    $burdenCost->currency          = $currencies[0];
                    $opptPdct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = 0.0;
                    $laborCost->currency          = $currencies[0];
                    $opptPdct->Labor_Cost = $laborCost;
                }
                else {
                    $burdenCost                    = new CurrencyValue();
                    $burdenCost->value             = 0.0;
                    $burdenCost->currency          = $currencies[0];
                    $opptPdct->Burden_Cost = $burdenCost;
                    $laborCost                    = new CurrencyValue();
                    $laborCost->value             = 0.0;
                    $laborCost->currency          = $currencies[0];
                    $opptPdct->Labor_Cost = $laborCost;
                }
           }
           else {
                $burdenCost                    = new CurrencyValue();
                $burdenCost->value             = 0.0;
                $burdenCost->currency          = $currencies[0];
                $opptPdct->Burden_Cost = $burdenCost;
                $laborCost                    = new CurrencyValue();
                $laborCost->value             = 0.0;
                $laborCost->currency          = $currencies[0];
                $opptPdct->Labor_Cost = $laborCost;
           }
        if($opptPdct->costbook->costofgoodssold->value == 'Material') {
          //  $opptPdct->Total_MHR = round($tQty);
            $materialCost                    = new CurrencyValue();
            $materialCost->value             = round((floatval($opptPdct->costbook->unitdirectcost)*$tQty),2);
            $materialCost->currency          = $currencies[0];
            $opptPdct->Materials_Cost = $materialCost;
        } else {
            $materialCost                    = new CurrencyValue();
            $materialCost->value             = 0.0;
            $materialCost->currency          = $currencies[0];
            $opptPdct->Materials_Cost = $materialCost;
        }

        if($opptPdct->costbook->costofgoodssold->value == 'Equipment') {
          //  $opptPdct->Total_MHR = round($tQty);
            $eqmtCost                    = new CurrencyValue();
            $eqmtCost->value             = round((floatval($opptPdct->costbook->unitdirectcost)*$tQty),2);
            $eqmtCost->currency          = $currencies[0];
            $opptPdct->Equipment_Cost = $eqmtCost;
        } else {
            $eqmtCost                    = new CurrencyValue();
            $eqmtCost->value             = 0.0;
            $eqmtCost->currency          = $currencies[0];
            $opptPdct->Equipment_Cost = $eqmtCost;          
        }
        if($opptPdct->costbook->costofgoodssold->value == 'Subcontractor') {
           // $opptPdct->Total_MHR = round($tQty);
            $subcontCost                    = new CurrencyValue();
            $subcontCost->value             = round((floatval($opptPdct->costbook->unitdirectcost)*$tQty),2);
            $subcontCost->currency          = $currencies[0];
            $opptPdct->Sub_Cost = $subcontCost;
        } else {
            $subcontCost                    = new CurrencyValue();
            $subcontCost->value             = 0.0;
            $subcontCost->currency          = $currencies[0];
            $opptPdct->Sub_Cost = $subcontCost;
        }
         if($opptPdct->costbook->costofgoodssold->value == 'Other') {
          //  $opptPdct->Total_MHR = round($tQty);
            $otherCost                    = new CurrencyValue();
            $otherCost->value             = round((floatval($opptPdct->costbook->unitdirectcost)*$tQty),2);
            $otherCost->currency          = $currencies[0];
            $opptPdct->Other_Cost = $otherCost;
        } else {
            $otherCost                    = new CurrencyValue();
            $otherCost->value             = 0.0;
            $otherCost->currency          = $currencies[0];
            $opptPdct->Other_Cost = $otherCost;
        }
        
        if($opptPdct->costbook->costofgoodssold->value == 'Assembly') {
                $opportunityProductResult  = OpportunityProductUtils::AssemblyProductCalculation($opptPdct->costbook); 
                $opptPdct->Total_MHR = round(floatval($opportunityProductResult['Total_MHR'])*$tQty, 2);
                $burdenCost                    = new CurrencyValue();
                $burdenCost->value             = round((floatval($opportunityProductResult['Total_Burden_Cost'])*$tQty),2);
                $burdenCost->currency          = $currencies[0];
                $opptPdct->Burden_Cost = $burdenCost;
                $laborCost                    = new CurrencyValue();
                $laborCost->value             = round((floatval($opportunityProductResult['Total_Labour_Cost'])*$tQty),2);
                $laborCost->currency          = $currencies[0];
                $opptPdct->Labor_Cost = $laborCost;
                $materialCost                    = new CurrencyValue();
                $materialCost->value             = round((floatval($opportunityProductResult['Materials_Cost'])*$tQty),2);
                $materialCost->currency          = $currencies[0];
                $opptPdct->Materials_Cost = $materialCost;
                $subcontCost                    = new CurrencyValue();
                $subcontCost->value             = round((floatval($opportunityProductResult['Subcontractor_Cost'])*$tQty),2);
                $subcontCost->currency          = $currencies[0];
                $opptPdct->Sub_Cost = $subcontCost;
                $eqmtCost                    = new CurrencyValue();
                $eqmtCost->value             = round((floatval($opportunityProductResult['Equipment_Cost'])*$tQty),2);
                $eqmtCost->currency          = $currencies[0];
                $opptPdct->Equipment_Cost = $eqmtCost;
        }
        
            $totalDirectCost = new CurrencyValue();
            $totalDirectCost->value             = round(($opptPdct->Labor_Cost->value+$opptPdct->Burden_Cost->value + $opptPdct->Materials_Cost->value + $opptPdct->Equipment_Cost->value + $opptPdct->Sub_Cost->value + $opptPdct->Other_Cost->value),2);
            $totalDirectCost->currency          = $currencies[0];
            $opptPdct->Total_Direct_Cost = $totalDirectCost;
            $finalCost = new CurrencyValue();
            $finalCost->value             =  round(($opptPdct->Total_Direct_Cost->value/(1-($opptPdct->Category_GPM/100))),2);
            $finalCost->currency          =  $currencies[0];
            $opptPdct->Final_Cost = $finalCost;
        if(!$opptPdct->save()) {
                throw new Exception();
        }
        return true;
            } catch(Exception $ex) {
                return false;
            }
            
        }

        public function makeOpportunityProductSelection($datas, $opportunityId) {
            $content = '';
            $opptProducts = OpportunityProduct::getAllByOpptId(intval($opportunityId));
            $opportunity = Opportunity::getById($opportunityId);
            $count = count($opptProducts);
            $totalDirectCost = 0;
            $totalDirectCost1=0;
            $totalMH = 0;
            $suggestedPrice = 0;
            $opptPdctMap;
            if(count($opptProducts) > 0) {
                foreach($opptProducts as $row) {
                    $opptPdctMap[$row->Category][] = $row;
                }
                
                if($opportunity->aggregateGPM > 0)
                {
                    $final_gpm_value = $opportunity->aggregateGPM;

                }
                else
                {
                    $final_gpm_value = 40;
                }

            $tableCreation = '<div style="margin-left:42%;">
                                  <a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" class="attachLoading cancel-button">
                                      <span class="z-label appendButton">
                                          Go Back
                                      </span>
                                  </a>
                              </div>
                              <div style="margin-right: 10px; text-align: right; font">                             
                                <a href="/app/index.php/opportunities/default/EstimationPrintView?id='.$opportunityId.'" onclick="javascript:window.open($(this).attr(\'href\'), \'estimationSummary\', \'width=850,height=650,scrollbars=yes\'); return false;">Print view</a>
                              </div>';
            $tableCreation .= '<div class="view-toolbar-container clearfix opportunity_productlist_div">
                                    <div style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold; font-size: 13px;">
                                        Estimate Summary
                                    </div>
                                   ';
            $tableCreation .= '<table  border="1" width="100%" class="items estimatesummary">
                             <colgroup span="5"></colgroup>';
                             
            $tableCreation .= '<thead style="font-weight: bold; background-color:#E6E6E6;padding: 5px;">
                                   <tr>
                                       <th colspan="14" class="oppproductsh"><div class="opportunity_productlist_header" >Opportunity Products  </div><div class="noofproducts">Number Of Products : '.$count.'</div></th>
                                   </tr>                                         
                                        <th class="productcodeh">Product Code</th>
                                        <th class="pronameh">Product Name</th>
                                        <th class="unitofmeasureh">Unit of Measure</th>
                                        <th class="qtyh">Quantity</th>
                                        <th class="frequencyh">Frequency</th>
                                        <th class="totalmhrh">MH</th>
                                        <th class="otherrowsh">L</th>
                                        <th class="otherrowsh">OH</th>
                                        <th class="otherrowsh">M</th>
                                        <th class="otherrowsh">E</th>
                                        <th class="otherrowsh">S</th>
                                        <th class="otherrowsh">O</th>
                                        <th class="prototalh">Total Direct Cost</th>
                                        <th class="prototalh">Total Final Price</th>
                                   </thead><tbody>';
            foreach ($opptPdctMap as $key1 => $optpdctArray1)  {                
                 foreach ($optpdctArray1 as $optKey1 => $optpdt1){
                    $totalDirectCost1 += $optpdt1->Total_Direct_Cost->value;                                   
                  }
             }
             
             foreach ($opptPdctMap as $key => $optpdctArray)  {
                 //Total Final Price = cost price/(1-aggregate gpm for final value)
                $tableCreation .= '<th  class="colspandetailsh" colspan="14">'.$key.'</th>';
                 foreach ($optpdctArray as $optKey => $optpdt){                     
                    $totalDirectCost += $optpdt->Total_Direct_Cost->value;
                    $suggestedPrice += $optpdt->Final_Cost->value;                    
                    $totalMH += $optpdt->Total_MHR;
                    $tableCreation .= '<tr>
                            <td class="productcode">'.$optpdt->costbook->productcode.'</td>
                            <td class="proname">'.$optpdt->name.'</td>
                            <td class="unitofmeasure">'.$optpdt->costbook->unitofmeasure.'</td>
                            <td class="qty">'.$optpdt->Quantity.'</td>
                            <td class="frequency">'.$optpdt->Frequency.'</td>
                            <td class="totalmhr">'.$optpdt->Total_MHR.'</td>
                            <td class="otherrows">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Labor_Cost->value, 2).'</td>
                            <td class="otherrows">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Burden_Cost->value, 2).'</td>
                            <td class="otherrows">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Materials_Cost->value, 2).'</td>
                            <td class="otherrows">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Equipment_Cost->value, 2).'</td>
                            <td class="otherrows">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Sub_Cost->value, 2).'</td>
                            <td class="otherrows">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Other_Cost->value, 2).'</td>
                            <td class="prototal">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Total_Direct_Cost->value, 2).'</td>                            
                            <td class="prototal">'.OpportunityProductUtils::getCurrencyType() .round($optpdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)),2) .'</td>
                        </tr>';
                  }
             }
             
             if($totalMH > 0){
                 $revMHR = number_format(($suggestedPrice/$totalMH),2);
                 $finalAmount = number_format(($opportunity->finalAmount->value/$totalMH),2);
             }else{
                 $revMHR = 0.0;
                 $finalAmount = 0.0;
             }
             
                $tableCreation .= '</tbody></table>';
                if($suggestedPrice > 0 && $opportunity->finalAmount->value > 0){
                           $tableCreation .= '<table style="margin-left: 20%; margin-top:2%;" border="0"
                                cellpadding="2" width="60%" text-align="right">
                        <tr>
                            <td rowspan="2" style="text-align:center; font-weight: bold;color:black;">Direct Cost</td>
                            <td style="text-align:right; font-weight: bold;color:black;">Total</td>
                            <td style="text-align:right;"></td>
                            <td style="text-align:right; font-weight: bold;color:black;">Suggested</td>
                            <td style="text-align:right; font-weight: bold;color:black;">Final</td>
                        </tr>
                        <tr>
                            <td style="text-align:right; color:black;">'.OpportunityProductUtils::getCurrencyType() .number_format($totalDirectCost,2).'</td>
                            <td style="text-align:right; font-weight: bold;color:black;">Price</td>
                            <td style="text-align:right; color:green;">'.OpportunityProductUtils::getCurrencyType() .number_format($suggestedPrice,2).'</td>
                            <td style="text-align:right; color:green;">'.OpportunityProductUtils::getCurrencyType() .number_format($opportunity->finalAmount->value,2).'</td>
                        </tr>
                        <tr>
                            <td style="text-align:center; font-weight: bold;color:black;">MH</td>
                            <td style="text-align:right; color:black;">'.$totalMH.'</td>
                            <td style="text-align:right; font-weight: bold;color:black;">Rev/MH</td>
                            <td style="text-align:right; color:black;">'.OpportunityProductUtils::getCurrencyType() . $revMHR.'</td>
                            <td style="text-align:right;">'.OpportunityProductUtils::getCurrencyType() .round($finalAmount, 2).'</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:right;"></td>
                            <td style="text-align:right; font-weight: bold;color:black;">Aggregate GPM%</td>
                            <td style="text-align:right; color:black;">'.number_format(((($suggestedPrice - $totalDirectCost)/$suggestedPrice)*100),2).' </td>
                            <td style="text-align:right; color:black;">'.number_format(((($opportunity->finalAmount->value -$totalDirectCost )/$opportunity->finalAmount->value)*100),2).'</td>
                        </tr>
                    </table>';
                }    
 		$tableCreation .= '</div><br/><br/><div style="background-color:#E0D1D1; color:black;padding:0.5%; margin-bottom:1%; font-weight:bold; font-size: 13px;">Charts</div>';
                $tableCreation .= OpportunityProductUtils::estimatorSummaryPiechartView($opportunityId);
                $tableCreation .= '<hr>';
        	  $tableCreation .= '</div>';
                $content .= $tableCreation;
            }
            $content .= '</tbody></table>
                <input value="" name="list-view-selectedIds" id="list-view-selectedIds" type="hidden">
                
                </div>';
            return $content;
	}

        protected function estimatorSummaryPiechartView($id) { 
            $l = new OpportunityProductsEstimatorSummaryChartView('OpportunityProductsEstimatorSummaryChartView',null,null);
          //  return $l->renderContent();
            return $l->setOpptId($id);
        }
        
        public function cloneOpportunityProducts($model){
            $opportunityProducts = array();
            $opportunityProducts = OpportunityProduct::getAllByOpptId($model->Opportunity->id);   
            foreach ($opportunityProducts as $opportunityProduct) {
                $clonedOpportunity = new OpportunityProduct();
                ZurmoCopyModelUtil::copy($opportunityProduct, $clonedOpportunity);
                $clonedOpportunity->opportunity = $model;
                $clonedOpportunity->save();
            }
        }
        
        public function makeOpptChangePriceView($datas, $opportunityId) {
            $defaultThemeName       = 'default';
            $defaultThemeBaseUrl    = Yii::app()->themeManager->baseUrl . '/' . $defaultThemeName;
            $categories = Category::getAll();
            $TotalDirectCost=0;
            $pageOffset = 1;
            $content = '<div id="FlashMessageView"></div><div>';
            $opportunityProducts = OpportunityProduct::getAllByOpptId(intval($opportunityId));
            $opportunity = Opportunity::getById(intval($opportunityId));
            
            echo $opportunity->finalAmount->value.'===='.$opportunity->oldFinalAmount;
                $countOfSelectedRow = 0;
                $content .= '<div style="margin-left:42%;"><a href="#" id="GoBack" onclick="javascript:addAndUpdateProductsInOpportunity(\''.$opportunityId.'\', this);" class="attachLoading cancel-button">
                                <span class="z-label appendButton">
                                    Go Back
                                </span>
                                </a>
                                <a class="attachLoading " id="Save" onclick="javascript:updatePriceInOpportunity(\''.$opportunityId.'\', this);" href="#">
                                <span class="z-label">
                                    Save
                                </span>
                                </a>
                            </div>';
                if($opportunity->recordType->value =='Recurring Final' || 1==1) {
                        
                        $content .= '<div class="cgrid-view type-opportunityProducts" id="list-view">
                                <div class="summary">
                                    5 result(s)
                                </div>
                                <div id="add_Product_list_table" class="table_border_width">
                                    <table id="add_Product_List_table_Value" class="items">
                                        <tr>
                                            <td><label id="totalMhr"> Total Mhr : 0 </label></td>
                                            <td><label id="totalDirectCost"> Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                            <td><label id="budget"> Budget '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                        </tr>
                                        <tr>
                                            <td><label id="Revenue_MHR"> Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': 0.0 </label></td>
                                            <td><label id="Aggregate_GPM"> Aggregate GPM %: 0 </label></td>
                                            <td><label id="Suggested_Price"> Suggested Price '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp</td>';
                                                if($opportunity->aggregateGPM > 0)
                                                {
                                                    $final_gpm_value = $opportunity->aggregateGPM;
                                                    
                                                }
                                                else
                                                {
                                                    $final_gpm_value = 40;
                                                }
                                                $content .='<td><label id="final_GPM"> Final GPM %: <input type="text" name="finalGpm" id="final_gpm" style="width:12%" value='.$final_gpm_value.' disabled> </label></td>
                                            <td><label id="final_amount"> Final Amount '.OpportunityProductUtils::getCurrencyType().': <input type="text" name="finalAmount" style="width:22%" id="final_amt" value="0"> </label></td>
                                        </tr>
                                    </table>
                                </div>';
                        $content .='<input value="0" name="modified_final_amt" id="modified_final_amt" type="hidden">';
                               
                                if(count($opportunityProducts) > 0){
                                    $content .= '<div id="selected_products" class="table_border_width" style="padding: 0%;">
                                        <div class="align_left" style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold;">
                                             Selected Products <span id="showresults"></span>
                                        </div>';
                                    $content .='<table class="items selected_products_table">
                                                <tr style="color:black; padding:0.5%;">
                                                    <th>Product Code</th>
                                                    <th>Product Name</th>
                                                    <th>Unit of Measure</th>
                                                    <th>Quantity</th>
                                                    <th>Frequency</th>
                                                    <th>MH</th>
                                                    <th>L+OH</th>
                                                    <th>M</th>
                                                    <th>E</th>
                                                    <th>S</th>
                                                    <th>O</th>
                                                    <th>Total Direct Cost</th>
                                                </tr>';
                                    
                                    foreach($opportunityProducts as $row) {
                                       $opportunityPdctMap[$row->Category][] = $row;
                                    }
                                    $CategoryKeyCount = 0;
                                    $totalMhr = 0;
                                    $totalDirectCost = 0;
                                    $totalFinalPrice = 0.0;
				                    $actualGPM = 0;
                                    $totalRevenue = 0.0;
                                    foreach($opportunityPdctMap as $CategoryKey => $opportunityArray){
                                        $content .='<tr>
                                              <th colspan="12" class="align_left" style="background-color:gray;color:white;">'.$CategoryKey.'</th>
                                              </tr>';
                                        foreach ($opportunityArray as $opportunityKey => $opportunitypdt){
                                            $totalMhr += $opportunitypdt->Total_MHR;
                                            $totalDirectCost += $opportunitypdt->Total_Direct_Cost->value;
                                            $totalFinalPrice += $opportunitypdt->Final_Cost->value;
                                            $content .='<tr>
                                                <td>'.$opportunitypdt->costbook->productcode.'</td>
                                                <input value='.$opportunitypdt->id.' name="list_View_Add_Product_SelectedIds"id="list_View_Producted_SelectedIds_'.$countOfSelectedRow.'" type="hidden">
                                                <td>'.$opportunitypdt->name.'</td>
                                                <td>'.$opportunitypdt->costbook->unitofmeasure.'</td>
                                                <td>'.$opportunitypdt->Quantity.'</td>
                                                <td>'.$opportunitypdt->Frequency.'</td>
                                                <td>'.$opportunitypdt->Total_MHR.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().(($opportunitypdt->Labor_Cost->value)+($opportunitypdt->Burden_Cost->value)).'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Materials_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Equipment_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Sub_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Other_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Total_Direct_Cost->value.'</td>
                                            </tr>';
                                            $countOfSelectedRow++;
                                          }
                                          $CategoryKeyCount++;
                                    }
                                    if($totalFinalPrice > 0) {
						$actualGPM = 	(($totalFinalPrice - $totalDirectCost)/$totalFinalPrice)*100;		
					}
                                    if($totalMhr > 0) {
                                        $totalRevenue = $totalFinalPrice/$totalMhr;				
                                    }
                                    Yii::app()->clientScript->registerScript('calculationForAddProductScreenRecurring',
                                        '//$(Selected_Products_Ids).val("'.$countOfSelectedRow.'")
                                         $("#totalMhr").text("Total Mhr : '.$totalMhr.'");
                                         $("#totalDirectCost").text("Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': '.$totalDirectCost.'");   
                                         $("#budget").text("Budget '.OpportunityProductUtils::getCurrencyType().' : '.$opportunity->budget->value.'");
                                         $("#Suggested_Price").text("Suggested Price '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f', $totalFinalPrice).'");
					 $("#Aggregate_GPM").text("Aggregate GPM %: '.sprintf('%.2f', $actualGPM).'");
                                         //$("#Roundedvalue").text(" Rounded value").css({"color":"red"});    
					 $("#Revenue_MHR").text("Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f',$totalRevenue).'");    
                                        // For the final amount
                                         if('.$totalDirectCost.' >0){
                                            var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                            $("#final_amt").val('.$opportunity->finalAmount->value.');
                                         } 
                                         $("#final_gpm").keyup(function() {
                                             if('.$totalDirectCost.' >0){
                                                 var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                                 $("#final_amt").val(tot_amt.toFixed(2));
                                                 $("#modified_final_amt").val(0);
                                             }else {
                                                 $("#final_amt").val(0);
                                                 $("#modified_final_amt").val(0);
                                             }
                                         });

					$("#final_amt").keyup(function() 
					{
                                            if('.$totalDirectCost.' >0)
                                            {
                                                if($("#final_amt").val() > '.$totalDirectCost.')
                                                {
                                                    var tot_gpm = (($("#final_amt").val()*100)-('.$totalDirectCost.'*100))/$("#final_amt").val();
                                                    $("#final_gpm").val(tot_gpm.toFixed(2));
                                                    $("#modified_final_amt").val($("#final_amt").val());
                                                }
                                            }                                          
                                        });
                                     ');
                          $content .=' </table></div></td></tr></table></div>';
                          
                            }  
               }else {
                    $content .= '<div class="cgrid-view type-opportunityProducts" id="list-view">                                
                                <div id="add_product_outer">
                                    <div id="add_Product_list_table" class="table_border_width">
                                        <table id="add_Product_List_table_Value" class="items">
                                            <tr>
                                                <td><label id="totalMhr"> Total Mhr : 0 </label></td>
                                                <td><label id="totalDirectCost"> Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                                <td><label id="budget"> Budget '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                            </tr>
                                            <tr>
                                                <td><label id="Revenue_MHR"> Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': 0.0 </label></td>
                                                <td><label id="Aggregate_GPM"> Aggregate GPM %: 0 </label></td>
                                                <td><label id="Suggested_Price"> Suggested Price '.OpportunityProductUtils::getCurrencyType().': 0 </label></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp</td>';
                                                if($opportunity->aggregateGPM > 0)
                                                {
                                                    $final_gpm_value = $opportunity->aggregateGPM;                                                    
                                                }
                                                else
                                                {
                                                    $final_gpm_value = 40;
                                                }
                                                $content .='<td><label id="final_GPM"> Final GPM %: <input type="text" name="finalGpm" id="final_gpm" style="width:12%"  value='.$final_gpm_value.' disabled> </label></td>
                                                <td><label id="final_amount"> Final Amount '.OpportunityProductUtils::getCurrencyType().': <input type="text" name="finalAmount" style="width:22%" id="final_amt" value='.$opportunity->finalAmount->value.'> </label></td>
                                            </tr>
                                        </table>
                                    </div>';
                    $content .='<input value="0" name="modified_final_amt" id="modified_final_amt" type="hidden">';
                                
                                if(count($opportunityProducts) > 0){
                                    $content .= '<div id="selected_products" class="table_border_width" style="padding: 0px;">
                                    
                                        <div class="align_left" style="background-color:#E0D1D1; color:black;padding:0.5%; font-weight:bold;">
                                             Selected Products <span id="showresults" style="color:green; font-weight:none;"></span>
                                        </div>
                                            ';
                                    $content .='<table class="items selected_products_table">
                                                <tr style="color:black; padding:0.5%;">
                                                    <th>Product Code</th>
                                                    <th>Product Name</th>
                                                    <th>Unit of Measure</th>
                                                    <th>Quantity</th>
                                                    <th>MH</th>
                                                    <th>L+OH</th>
                                                    <th>M</th>
                                                    <th>E</th>
                                                    <th>S</th>
                                                    <th>O</th>
                                                    <th>Total Direct Cost</th>
                                                    <th>Total Final Price</th>
                                                </tr>';
                                    
                                    foreach($opportunityProducts as $row) {
                                       $opportunityPdctMap[$row->Category][] = $row;
                                    }
                                    $CategoryKeyCount = 0;
                                    $totalMhr = 0;
                                    $totalDirectCost = 0;
                                    $totalDirectCost1 = 0;
				                    $totalFinalPrice = 0.0;
				                    $actualGPM = 0;
                                    $totalRevenue = 0.0;
                                    
                                    
                                    foreach($opportunityPdctMap as $CategoryKey1 => $opportunityArray1){                                        
                                        foreach ($opportunityArray1 as $opportunityKey1 => $opportunitypdt1){
                                            $totalDirectCost1 += $opportunitypdt1->Total_Direct_Cost->value;					    
                                          }
                                    $CategoryKeyCount++;
                                    }
                                    
                                    
                                    foreach($opportunityPdctMap as $CategoryKey => $opportunityArray){
                                        $content .='<tr>
                                              <th colspan="12" class="align_left" style="background-color:gray; color:white;">'.$CategoryKey.'</th>
                                              </tr>';
                                        foreach ($opportunityArray as $opportunityKey => $opportunitypdt){
                                            $totalMhr += $opportunitypdt->Total_MHR;
                                            $totalDirectCost += $opportunitypdt->Total_Direct_Cost->value;
					    $totalFinalPrice += $opportunitypdt->Final_Cost->value;
                                            $content .='<tr>
                                                <td>'.$opportunitypdt->costbook->productcode.'</td>
                                                <input value='.$opportunitypdt->costbook->productcode.' name="productCode" id="productCode_'.$countOfSelectedRow.'" type="hidden">    
                                                <input value='.$opportunitypdt->id.' name="list_View_Add_Product_SelectedIds"id="list_View_Producted_SelectedIds_'.$countOfSelectedRow.'" type="hidden">
                                                <td>'.$opportunitypdt->name.'</td>
                                                <td>'.$opportunitypdt->costbook->unitofmeasure.'</td>
                                                <td>'.$opportunitypdt->Quantity.'</td>
                                                <td>'.$opportunitypdt->Total_MHR.'</td>    
                                                <td>'.OpportunityProductUtils::getCurrencyType().(($opportunitypdt->Labor_Cost->value)+($opportunitypdt->Burden_Cost->value)).'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Materials_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Equipment_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Sub_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Other_Cost.'</td>
                                                <td>'.OpportunityProductUtils::getCurrencyType().$opportunitypdt->Total_Direct_Cost->value.'</td>
                                                <td class="prototal">'.OpportunityProductUtils::getCurrencyType() .round($opportunitypdt->Total_Direct_Cost->value / (1- ((((($opportunity->finalAmount->value -$totalDirectCost1 )/$opportunity->finalAmount->value)*100)) /100)),2) .'</td>
                                            </tr>';
                                            $countOfSelectedRow++;
                                          }
                                    $CategoryKeyCount++;
                                    }
					if($totalFinalPrice > 0) {
						$actualGPM = 	(($totalFinalPrice - $totalDirectCost)/$totalFinalPrice)*100;		
					}
					if($totalMhr > 0) {
						$totalRevenue = $totalFinalPrice/$totalMhr;				
					}
                                    
                                     Yii::app()->clientScript->registerScript('calculationForAddProductScreen',
                                        '//$(Selected_Products_Ids).val("'.$countOfSelectedRow.'");
                                         $("#totalMhr").text("Total Mhr : '.$totalMhr.'");
                                         $("#totalDirectCost").text("Total Direct Cost '.OpportunityProductUtils::getCurrencyType().': '.$totalDirectCost.'");   
                                         $("#budget").text("Budget '.OpportunityProductUtils::getCurrencyType().': '.$opportunity->budget->value.'");   
					 $("#Suggested_Price").text("Suggested Price '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f', $totalFinalPrice).'");
					 $("#Aggregate_GPM").text("Aggregate GPM %: '.sprintf('%.2f', $actualGPM).'");
                                         //$("#Roundedvalue").text(" Rounded value").css({"color":"red"});
					 $("#Revenue_MHR").text("Revenue / Mhr '.OpportunityProductUtils::getCurrencyType().': '.sprintf('%.2f',$totalRevenue).'");
      
                                         // For the final amount
                                         if('.$totalDirectCost.' > 0){
                                             if('.$opportunity->finalAmount->value.' > 0)
                                                 var tot_amt = '.$opportunity->finalAmount->value.';
                                             else
                                                var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                            $("#final_amt").val(tot_amt.toFixed(2));
                                         } 
                                         $("#final_gpm").keyup(function() {
                                             if('.$totalDirectCost.' >0){
                                                 var tot_amt = '.$totalDirectCost.'/(1-$("#final_gpm").val()/100);
                                                 $("#final_amt").val(tot_amt.toFixed(2));
                                                 $("#modified_final_amt").val(0);
                                             } else {
                                                 $("#final_amt").val(0);
                                                 $("#modified_final_amt").val(0);
                                             }
                                         });
					$("#final_amt").keyup(function() 
					{
                                            if('.$totalDirectCost.' >0)
                                            {
                                                if($("#final_amt").val() > '.$totalDirectCost.')
                                                {
                                                    var tot_gpm = (($("#final_amt").val()*100)-('.$totalDirectCost.'*100))/$("#final_amt").val();
                                                    $("#final_gpm").val(tot_gpm.toFixed(2));
                                                    $("#modified_final_amt").val($("#final_amt").val());
                                                }
                                            }                                          
                                        });
                                         // Ends here
                                     ');
                            $content .=' </table></div></td></tr></table></div>
                            </div>';
                    }                    
               }
        $content .= '</tbody></table></div>';
        return $content;
    }
}
?>