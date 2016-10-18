/**
 * Copy the product template data for creation of product
 */
function copyProductTemplateDataForProduct(templateId, url)
{
    url = url + "?id=" + templateId;
    $.ajax(
        {
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(data)
                     {
                         $("#ProductCategoriesForm_ProductCategory_ids").tokenInput("clear");
                         $(data.categoryOutput).each(function(index)
                         {
                            $("#ProductCategoriesForm_ProductCategory_ids").tokenInput("add", {id: this.id, name: this.name});
                         });
                         $('#Product_type_value').val(data.productType);
                         $('#Product_priceFrequency_value').val(data.productPriceFrequency);
                         $('#Product_sellPrice_currency_id').val(data.productSellPriceCurrency);
                         $('#Product_sellPrice_value').val(data.productSellPriceValue);
                         $('#Product_name').val(data.productName);
                         if($("#Product_description").length > 0)
                         {
                            $('#Product_description').val(data.productDescription);
                         }
                     }
        }
    );
}

/**
 * Adds the product row to the product portlet on details view
 */
function addProductRowToPortletGridView(productTemplateId, url, relationAttributeName, relationModelId, uniquePortletPageId, errorInProcess)
{
    url = url + "&id=" + productTemplateId + "&relationModelId=" + relationModelId + "&relationAttributeName=" + relationAttributeName+"&redirect=true";
    $.ajax(
        {
            type: 'GET',
            url: url,
            beforeSend: function(xhr)
                       {
                           $('#modalContainer').html('');
                           $(this).makeLargeLoadingSpinner(true, '#modalContainer');
                       },
            success: function(dataOrHtml, textStatus, xmlReq)
                     {
                         $(this).processAjaxSuccessUpdateHtmlOrShowDataOnFailure(dataOrHtml, uniquePortletPageId);
                     },
            complete:function(XMLHttpRequest, textStatus)
                     {
                       $('#modalContainer').dialog('close');
                     },
            error:function(xhr, textStatus, errorThrown)
                  {
                      alert(errorInProcess);
                  }
        }
    );
}

jQuery('#list-view-rowSelector_all').live('click', function() {
    var checked = this.checked;
    if (this.checked){
        jQuery(this).parent().addClass('c_on');
    }
    else
    {
        jQuery(this).parent().removeClass('c_on');
    }

    jQuery("input[name='list-view-rowSelector\[\]']").each(function()
    {
        this.checked = checked;
        updateListViewSelectedIds('list-view', $(this).val(), checked);

        //custom checkbox style
        if (this.checked){
            jQuery(this).parent().addClass('c_on');
        }
        else
        {
            jQuery(this).parent().removeClass('c_on');
        }
    });
});

jQuery("input[name='list-view-rowSelector\[\]']").live('click', function() {
    jQuery('#list-view-rowSelector_all').attr( 'checked', jQuery("input[name='list-view-rowSelector\[\]']").length == jQuery("input[name='list-view-rowSelector\[\]']:checked").length);
    updateListViewSelectedIds('list-view', $(this).val(), $(this).attr('checked'));
    if ( jQuery('#list-view-rowSelector_all').attr( 'checked') === 'checked' ){
        jQuery('#list-view-rowSelector_all').parent().addClass('c_on');
    }
    else
    {
        jQuery('#list-view-rowSelector_all').parent().removeClass('c_on');
    }
    if ( this.checked )
    {
        jQuery(this).parent().addClass('c_on');
    }
    else
    {
        jQuery(this).parent().removeClass('c_on');
    }
});

function updateListViewSelectedIds(gridViewId, selectedId, selectedValue) {
    var array = new Array ();
    var processed = false;
    jQuery.each($('#' + gridViewId + "-selectedIds").val().split(','), function(i, value)
        {
            if(selectedId == value)
            {
                if(selectedValue)
                {
                    array.push(value);
                }
                processed = true;
            }
            else
            {
                if(value != '')
                {
                    array.push(value);
                }
            }
         }
     );
    if(!processed && selectedValue)
    {
        array.push(selectedId);
    }
    $('#' + gridViewId + "-selectedIds").val(array.toString());
}

/**
 * Script For Make DropDown Category In Opportunities Add Product Page
 * @author Thamodaran
 */
function makeDropDownCategory(data, count){
    element = '<div class="hasDropDown"><span class="select-arrow"></span><select id="Category_'+count+'" style="width:100%;">';
        for(i=0; i < data.length; i++){
            element += '<option value="'+data[i]+'">'+data[i]+'</option>';
        }
    element += '</select></div>';
    return element;
}
	
/**
 * Script For add costbook as product to Opportunities Add Product Page
 * @author Thamodaran   
 */
function addOpportunityProduct(opportunityId, ids){
    addProductJsonObj = [];
    addProductItem = {};
    var arr = ids.split(',');
    var breakOut = false;
    $("#selectedProductCnt").val(arr.length);
    jQuery.each(jQuery("input[name='list-view-rowSelector[]']"), function(i, value){  
        var txtVal = parseFloat($("#quantity_"+value.id.split('_')[1]).val());
        if(txtVal > 0){
           if(this.checked == false){
               alert("Please enable check box.");
               breakOut = true;
               return false;
           }
        }
    });
    if(breakOut) {
        breakOut = false;
        return false;
    }
    
    jQuery.each(jQuery("input[name='list-view-rowSelector\[\]']:checked"), function(i, value) {
        if($("#frequency_"+value.id.split('_')[1]) != null && $("#frequency_"+value.id.split('_')[1]).val() != undefined){
            addProductItem ["costBookId"] = value.value;
            var quantity = $("#quantity_"+value.id.split('_')[1]).val();
            var frequency = $("#frequency_"+value.id.split('_')[1]).val();
            addProductItem ["costBookId"] = value.value;
            var quantity = parseFloat($("#quantity_"+value.id.split('_')[1]).val());
            if(quantity > 0){
                addProductItem ["add_Quantity"] = quantity.toFixed(4);                
            }else{
                alert("Quantity should be greater than 0");
                $("#quantity_"+value.id.split('_')[1]).focus();
                return false;
            }
            var frequency = parseFloat($("#frequency_"+value.id.split('_')[1]).val());
            if(frequency > 0){
                addProductItem ["add_Frequency"] = frequency;
            }else{
                alert("Frequency should be greater than 0");
                $("#frequency_"+value.id.split('_')[1]).focus();
                return false;
            }
            addProductItem ["add_Category"] = $("#Category_"+value.id.split('_')[1]).val();
            addProductJsonObj.push(addProductItem);
            addProductItem = {};
        }else {
            addProductItem ["costBookId"] = value.value;
            var quantity = parseFloat($("#quantity_"+value.id.split('_')[1]).val());
            if(quantity > 0){
                addProductItem ["add_Quantity"] = quantity.toFixed(4);                
            }else{
                alert("Quantity should be greater than 0");
                return false;
            }
            addProductItem ["add_Frequency"] = '';
            addProductItem ["add_Category"] = $("#Category_"+value.id.split('_')[1]).val();
            addProductJsonObj.push(addProductItem);
            addProductItem = {};
        }
    });
    return addProductJsonObj;
}


/**
 * Script for update quantity and frequency value in Opportunities Add Product Page
 * @author Thamodaran
 */
function updateOpportunityProduct(opportunityId){
    UpdateProductJsonObj = [];
    updateProductItems = {};
    var cnt = $("#Selected_Products_Ids").val();
    for(var i=0; i < cnt; i++) {
        if($("#updateFrequency_"+i).val() != null && $("#updateFrequency_"+i).val() != undefined){
            updateProductItems ["product_ids"] = $('#list_View_Producted_SelectedIds_'+i).val();
            updateProductItems ["Quantity"] = parseFloat($('#updateQuantity_'+i).val()).toFixed(4);
            updateProductItems ["Frequency"] = $('#updateFrequency_'+i).val();
            UpdateProductJsonObj.push(updateProductItems);
            updateProductItems = {};
        }else {
            updateProductItems ["product_ids"] = $('#list_View_Producted_SelectedIds_'+i).val();
            updateProductItems ["Quantity"] = parseFloat($('#updateQuantity_'+i).val()).toFixed(4);
            updateProductItems ["Frequency"] = '';
            UpdateProductJsonObj.push(updateProductItems);
            updateProductItems = {};
        }
    }
    return UpdateProductJsonObj;
}

/**
 * Script for ckeck new costbook datas quantity and frequency in Add Product Page
 * @author Thamodaran
 */
function checkAddProductIfUndefinedOrNot(addNewOptProducts){
    if((addNewOptProducts == '' || addNewOptProducts == undefined) && addNewOptProducts != null){
        return null;
    }else{
        return $("#list-view-selectedIds").val();
    }
}

/**
 * Script for Add and Update in Opportunities Add Product Page
 * @author Thamodaran
 */
function addAndUpdateProductsInOpportunity(opportunityId, btnProperty) {
   if(btnProperty.id != "GoBack"){
        var ids=$("#list-view-selectedIds").val();
        var updateOptProducts = $("#Selected_Products_Ids").val();
        var addNewOptProducts = ids;
        var addProductValues = checkAddProductIfUndefinedOrNot(addNewOptProducts);
        var finalGpmValue   = $("#final_gpm").val();
        var finalAmount = $("#final_amt").val();
		final_gpm_value = '';
                final_amount = '';
		if(parseFloat(finalGpmValue) > 0 && parseFloat(finalGpmValue) < 100)
		{		
                    final_gpm_value = '&finalGpm='+parseFloat(finalGpmValue);           
                    if($("#modified_final_amt").val() > 0)
                    {
                        final_amount = '&finalAmnt='+parseFloat($("#modified_final_amt").val());
                    }
		}
		else
		{
			alert('Please enter valid GPM');
			return false;
		}
        switch (true){
            case updateOptProducts != 0 && addProductValues != null && btnProperty.id === 'Save':
                var updateProduct = updateOpportunityProduct(opportunityId);
                var addProduct = addOpportunityProduct(opportunityId, ids);
                if(updateProduct != false && addProduct != false){
                    addProductNotification("Saving...!");                       //Showing notification on top of the page
                    var btnPropertyVal = 'Save&Update';
                    url = '/app/index.php/opportunityProducts/default/AddAndUpdateOpportunityProducts?ids='+ids+'&addJsonObj='+JSON.stringify(addProduct)+'&updateJsonObj='+JSON.stringify(updateProduct)+'&optId='+opportunityId+'&btnProperty='+btnProperty.id+final_gpm_value+final_amount;
                    var addAndUpdateCostBookProductAjaxRes = AddAndUpdateCostBookProductAjaxCall(url, btnPropertyVal);
                }
                break;
            case updateOptProducts == 0 && addProductValues != null && btnProperty.id === 'Save':
                var addProduct = addOpportunityProduct(opportunityId, ids);
                if(addProduct != false){
                    addProductNotification("Saving...!");                       //Showing notification on top of the page
                    url = '/app/index.php/opportunityProducts/default/AddOpportunityProducts?ids='+ids+'&addJsonObj='+JSON.stringify(addProduct)+'&optId='+opportunityId+'&btnProperty='+btnProperty.id+final_gpm_value+final_amount;
                    var addAndUpdateCostBookProductAjaxRes = AddAndUpdateCostBookProductAjaxCall(url, btnProperty);
                }
                break;
            case updateOptProducts != 0 && addProductValues == null && btnProperty.id === 'Save':
                var updateProduct = updateOpportunityProduct(opportunityId);
                if(updateProduct != false){
                    addProductNotification("Saving...!");                       //Showing notification on top of the page
                    url = '/app/index.php/opportunityProducts/default/UpdateOpportunityProducts?ids='+ids+'&updateJsonObj='+JSON.stringify(updateProduct)+'&optId='+opportunityId+'&btnProperty='+btnProperty.id+final_gpm_value+final_amount;
                    var addAndUpdateCostBookProductAjaxRes = AddAndUpdateCostBookProductAjaxCall(url, btnProperty);
                }
                break;
            case updateOptProducts == 0 && addProductValues == null && btnProperty.id === 'saveAndMore':
                alert("Please search and choose atleast one product");
                return false;
                break;
            case updateOptProducts != 0 && addProductValues != null && btnProperty.id === 'saveAndMore':
                var addProduct = addOpportunityProduct(opportunityId, ids);
                if(addProduct != false){
                    addProductNotification("Saving...!");                       //Showing notification on top of the page
                    url = '/app/index.php/opportunityProducts/default/AddOpportunityProducts?ids='+ids+'&addJsonObj='+JSON.stringify(addProduct)+'&optId='+opportunityId+'&btnProperty='+btnProperty.id+final_gpm_value+final_amount;
                    var addAndUpdateCostBookProductAjaxRes = AddAndUpdateCostBookProductAjaxCall(url, btnProperty);
                }
                break;
            case updateOptProducts != 0 && addProductValues == null && btnProperty.id === 'saveAndMore':
                var addProduct = addOpportunityProduct(opportunityId, ids);
                break;
            case updateOptProducts == 0 && addProductValues != null && btnProperty.id === 'saveAndMore':
                var addProduct = addOpportunityProduct(opportunityId, ids);
                if(addProduct != false){
                    addProductNotification("Saving...!");                       //Showing notification on top of the page
                    url = '/app/index.php/opportunityProducts/default/AddOpportunityProducts?ids='+ids+'&addJsonObj='+JSON.stringify(addProduct)+'&optId='+opportunityId+'&btnProperty='+btnProperty.id+final_gpm_value+final_amount;
                    var addAndUpdateCostBookProductAjaxRes = AddAndUpdateCostBookProductAjaxCall(url, btnProperty);
                }
                break;
            case updateOptProducts != 0 && addProductValues != null && btnProperty.id === 'update':
                var updateProduct = updateOpportunityProduct(opportunityId);
                if(updateProduct != false){
                    addProductNotification("Updating...!");                     //Showing notification on top of the page
                    url = '/app/index.php/opportunityProducts/default/UpdateOpportunityProducts?ids='+ids+'&updateJsonObj='+JSON.stringify(updateProduct)+'&optId='+opportunityId+'&btnProperty='+btnProperty.id+final_gpm_value+final_amount;
                    var addAndUpdateCostBookProductAjaxRes = AddAndUpdateCostBookProductAjaxCall(url, btnProperty);
                }
                break;
            case updateOptProducts != 0 && addProductValues == null && btnProperty.id === 'update':
                var updateProduct = updateOpportunityProduct(opportunityId);
                if(updateProduct != false){
                    addProductNotification("Updating...!");                     //Showing notification on top of the page
                    url = '/app/index.php/opportunityProducts/default/UpdateOpportunityProducts?ids='+ids+'&updateJsonObj='+JSON.stringify(updateProduct)+'&optId='+opportunityId+'&btnProperty='+btnProperty.id+final_gpm_value+final_amount;
                    var addAndUpdateCostBookProductAjaxRes = AddAndUpdateCostBookProductAjaxCall(url, btnProperty);
                }
                break;
            default : alert("Please search and choose atleast one product");
                return false;
        }
   }else{
            var url = '/app/index.php/opportunities/default/details?id='+opportunityId;
            window.location.href = url;
    }
}

/**
* Display notification on top of the page
* @author Thamodaran
*/
function addProductNotification(msg){
    var content = '<div id="FlashMessageBar" class="notify-wrapper">  <div class="jnotify-item-wrapper">  <div class="ui-corner-all jnotify-item ui-state-highlight">  <div class="jnotify-item-close">  <span class="ui-icon ui-icon-circle-close"></span>  </div>  <span class="ui-icon ui-icon-info"></span>  <span>'+msg+'.</span>  </div></div></div>';
    $('#FlashMessageView').html(content);
}

/**
 * Script for Add and Update CostBook Product AjaxCall Opportunities Add Product Page
 * @author Thamodaran
 */
function AddAndUpdateCostBookProductAjaxCall(url, btnProperty){
    $.ajax({
        url : url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            switch (true){
                case btnProperty.id == 'Save' || btnProperty == 'Save&Update':
                    var url = '/app/index.php/opportunities/default/details?id='+data;
                    window.location.href = url;
                    addProductNotification("Products saved sucessfully");       //Showing notification on top of the page
                    $("#showresults").text("Products saved sucessfully");
                    setTimeout("$('#showresults').fadeIn('slow').hide();", 3000);
                    break;
                case btnProperty.id == 'update':
                    var url = '/app/index.php/opportunityProducts/default/AddProductsInOpportunity?optId='+data;
                    window.location.href = url;
                    //$("#showresults").text("Products updated sucessfully");
                    addProductNotification("Products updated sucessfully");     //Showing notification on top of the page
                    setTimeout("$('#showresults').fadeIn('slow').hide();", 3000);
                    break;
                case btnProperty.id == 'saveAndMore':
                    var url = '/app/index.php/opportunityProducts/default/AddProductsInOpportunity?optId='+data;
                    window.location.href = url;
                    //$("#showresults").text("Products saved sucessfully");
                    addProductNotification("Products saved sucessfully");       //Showing notification on top of the page
                    setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
                    break;
                default : $("#showresults").text("Products");    
            }
        },
        error: function(data) {
           console.log('Error in opportunity Product return values..!!');
        }
    });
}

/**
 * Script for search products in Opportunities Add Product Page
 * @author Thamodaran
 */
function searchProducts(opportunityId, pageOffset, btnProperty) {
    var productName= $.trim($('#opp_productname_value').val());
    $('#opp_productname_value').val($.trim(productName));
    if(productName.length >= 3 || productName.length === 0 || btnProperty.id === 'search'){
        $( "#searchProducts" ).focus();
        $("#afterSearch").css('display','block');
        $("#afterSearchCostBookDatas").css('display','block');
        if(opportunityId != null){
            var url = '/app/index.php/opportunityProducts/default/GetAddProductSearch';
            var category= $('#oppt_AddProductcategory_value').val();
            var costOfGoods= $('#oppt_AddProductcostofgoodssold_value').val();
            var addProductoptId= opportunityId;
            var data = {category: category, costOfGoods: costOfGoods, addProductoptId: addProductoptId, pageOffset:pageOffset, productName:productName};
            $("#z-spinner_i2i_search").css({"display":"inline-block","margin-top":"3px","position":"absolute","margin-left": "3px"});
            $.ajax({
                url : url,
                type : 'GET',
                data: data,
                dataType: 'json',
                success : function(data)
                {
                    $("#z-spinner_i2i_search").hide();
                    var total_rows = data.length;
                    var rowsPerPage = 500; 
                    var appendText = '';
                    if($("#recordType_Ids").val() != 'Recurring Final'){
                        $("#searchProducts").css("display","block");
                        var items = {};
                        var categoryDatas = [];
                        var categoryData = [];
                        if(data != undefined && data != null && total_rows > 0){                
                            for(var i =0; i < total_rows; i++) {
                                if(!items[[data[i].productcode]]){
                                   items[[data[i].productcode]] = [];
                                }
                                var productcode = data[i].productcode;
                                items[productcode].push(data[i]);
                            }
                            var counter=0;
                            appendText += "<table class = 'items selected_products_table'>";
                            $.each( items, function( productKey, productValue ) {
                               $(this).data('serial', counter++);                   
                                $.each( productValue, function( key, value ){
                                    categoryDatas.push(value.Category);
                                });
                                var categoryRes = makeDropDownCategory(categoryDatas, counter);
                                categoryDatas = [];
                                if(productValue[0].CostOfGoodsSold == 'Labor'){
                                    directCost = parseFloat(productValue[0].deptBurdonCost) + parseFloat(productValue[0].deptLaborCost); 
                                }else {
                                    directCost = productValue[0].unitdirectcost;
                                }
                                appendText += "<tr id='"+productKey+"'>  <td class='checkbox-width' style = 'padding-left: 5px;'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td style='width:15%;'>"+productValue[0].productcode+" </td>  <td style='width:25%;'>"+productValue[0].productname+"</td>   <td style='width:15%;'>"+productValue[0].UnitOfMeasure+"</td>  <td style='width:10%;'>"+directCost+"</td> <td style='width:15%;'><input type='text' class = 'quantity' id='quantity_"+counter+"' value='0'/></td> <td style='width:20%;'> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                            });
                            appendText += "</table>";                        
                        } else {
                            appendText += '<span class="empty"><span class="icon-empty"></span>No results found</span>';
                        }

                    } else{
                        $("#searchProducts").css("display","block");
                        var items = {};
                        var categoryDatas = [];
                        var categoryData = [];
                        if(data != undefined && data != null && total_rows > 0){
                        for(var i =0; i < total_rows; i++) {              
                            if(items[[data[i].productcode]] == undefined){
                               items[[data[i].productcode]] = [];
                            }
                            var productcode = data[i].productcode;
                            items[productcode].push(data[i]);
                        }
                        if(data.length)
                        var counter=0;
                        var directCost = 0; 
                        appendText += "<table class = 'items selected_products_table'>";
                        $.each( items, function( productKey, productValue ) {
                            $(this).data('serial', counter++);
                            $.each( productValue, function( key, value ){
                                categoryDatas.push(value.Category);
                            });
                            var categoryRes = makeDropDownCategory(categoryDatas, counter);
                            categoryDatas = [];
                            if(productValue[0].CostOfGoodsSold == 'Labor'){
                                directCost = productValue[0].deptBurdonCost + productValue[0].deptLaborCost; 
                            }else {
                                directCost = productValue[0].unitdirectcost;
                            }
                            appendText += "<tr id='"+productKey+"'>  <td class='checkbox-width' style = 'padding-left: 5px;'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td style='width:10%;'>"+productValue[0].productcode+" </td>  <td style='width:25%;'>"+productValue[0].productname+"</td>   <td style='width:15%;'>"+productValue[0].UnitOfMeasure+"</td>  <td style='width:10%;'>"+directCost+"</td> <td style='width:10%;'><input type='text' class = 'quantity' id='quantity_"+counter+"' value='0'/></td> <td style='width:10%;'><input type='text' class = 'quantity' id='frequency_"+counter+"' value='1'/></td> <td style='width:20%;'> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                        });
                        appendText += "</table>";
                        }else {
                            appendText += '<span class="empty"><span class="icon-empty"></span>No results found</span>';
                        }
                    }
                    var lmitedOffset = pageOffset * rowsPerPage;
                    $('#opt_scroll_result').html(appendText);
                },
                error : function()  {
                    alert("No searched Products");
                }
            });
        }
    }
}

function sortProducts(opportunityId, pageOffset, sortFor, sortOrder) {
    $( "#searchProducts" ).focus();
    sortProductNotification('Loading...');
    $("#afterSearch").css('display','block');
    $("#afterSearchCostBookDatas").css('display','block');
    if(opportunityId != null){
    var url = '/app/index.php/opportunityProducts/default/GetAddProductSearch';
    var category= $('#oppt_AddProductcategory_value').val();
    var costOfGoods= $('#oppt_AddProductcostofgoodssold_value').val();
    var addProductoptId= opportunityId;
    var productName= $('#opp_productname_value').val();
    $('#opp_productname_value').val($.trim(productName));
    var data = {category: category, costOfGoods: costOfGoods, addProductoptId: addProductoptId, pageOffset:pageOffset, productName:productName, sortFor:sortFor, sortOrder:sortOrder};

    if(sortOrder == 'asc')
    {
	nextSortOrder = 'desc';
    }
    else if(sortOrder == 'desc')
    {
	nextSortOrder = 'asc';
    }

    codeOrderClass = '';
    if(sortFor == 'code')
    {
            codeOrderClass = nextSortOrder;
    }

    nameOrderClass = '';
    if(sortFor == 'name')
    {
            nameOrderClass = nextSortOrder;
    }

    unitOrderClass = '';
    if(sortFor == 'unit')
    {
            unitOrderClass = nextSortOrder;
    }

    costOrderClass = '';
    if(sortFor == 'cost')
    {
            costOrderClass = nextSortOrder;
    }

    $.ajax({
        url : url,
        type : 'GET',
        data: data,
        dataType: 'json',
        success : function(data)
        {
		setTimeout("$('#FlashMessageView').fadeIn('slow');", 1500);
		$('#FlashMessageView').html('');          
		var total_rows = data.length;
            var rowsPerPage = 500; 
            var appendText = '';
            if($("#recordType_Ids").val() != 'Recurring Final'){
                appendText += '<table class="items selected_products_table"><div style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div> <thead><tr>  <th id="list-view-rowSelector" class="checkbox-column" >  <label class="hasCheckBox">  <input value="1" name="list-view-rowSelector_all" id="list-view-rowSelector_all" type="checkbox">   </label>  </th>  <th style="width:15%;" id="list-view_c1">  <a class="sort-link '+codeOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'code\', \''+nextSortOrder+'\');">  Product Code </a> </th>  <th style="width:25%;" id="list-view_c2">  <a class="sort-link '+nameOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'name\', \''+nextSortOrder+'\');">  Product Name  </a>  </th>  <th style="width:15%;" id="list-view_c3"> <a class="sort-link '+unitOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'unit\', \''+nextSortOrder+'\');"> Unit of Measure </a> </th> <th style="width:10%;" id="list-view_c4"> <a class="sort-link '+costOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'cost\', \''+nextSortOrder+'\');"> Unit Direct Cost </a> </th> <th style="width:15%;" id="list-view_c5"> Quantity </th> <th style="width:20%;" id="list-view_c7"> Category </th> </tr> </thead><tbody><tr><td colspan="7" style = "padding: 0px;"><div style="height: 250px; overflow: auto; scrollbar-base-color: #ff8c00; scrollbar-arrow-color: white;"><table class = "items selected_products_table">'; 
                var items = {};
                var categoryDatas = [];
                var categoryData = [];
            if(data != undefined && data != null && total_rows > 0){                
                for(var i =0; i < total_rows; i++) {
                    if(!items[[data[i].productcode]]){
                       items[[data[i].productcode]] = [];
                    }
                    var productcode = data[i].productcode;
                    items[productcode].push(data[i]);
                }
                var counter=0;
                $.each( items, function( productKey, productValue ) {
                   $(this).data('serial', counter++);                   
                    $.each( productValue, function( key, value ){
                        categoryDatas.push(value.Category);
                    });
                    var categoryRes = makeDropDownCategory(categoryDatas, counter);
                    categoryDatas = [];
                    if(productValue[0].CostOfGoodsSold == 'Labor'){
                        directCost = productValue[0].deptBurdonCost + productValue[0].deptLaborCost; 
                    }else {
                        directCost = productValue[0].unitdirectcost;
                    }
                    appendText += "<tr id='"+productKey+"'>  <td class='checkbox-width' style = 'padding-left: 5px;'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td style='width:15%;'>"+productValue[0].productcode+" </td>  <td style='width:25%;'>"+productValue[0].productname+"</td>   <td style='width:15%;'>"+productValue[0].UnitOfMeasure+"</td>  <td style='width:10%;'>"+directCost+"</td> <td style='width:15%;'><input type='text' class = 'quantity' id='quantity_"+counter+"' value='0'/></td> <td style='width:20%;'> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                });
                    appendText += "</table></div></td></tr></tbody></table>";
            } else {
                appendText += '<tr><td colspan="7" class="empty"><span class="empty"><span class="icon-empty"></span>No results found</span></td></tr>';
            }

            } else{
               appendText += '<table class="items selected_products_table"><div style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div> <tbody><tr>  <th class="checkbox-column" id="list-view-rowSelector">  <label class="hasCheckBox">  <input value="1" name="list-view-rowSelector_all" id="list-view-rowSelector_all" type="checkbox">  </label>  </th>  <th style="width:10%;" id="list-view_c1"> <a class="sort-link '+codeOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'code\', \''+nextSortOrder+'\');">  Product Code </a> </th>  <th style="width:25%; padding: 0px;" id="list-view_c2">  <a class="sort-link '+nameOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'name\', \''+nextSortOrder+'\');">  Product Name  </a>  </th>  <th style="width:15%; padding: 0px;" id="list-view_c3"> <a class="sort-link '+unitOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'unit\', \''+nextSortOrder+'\');"> Unit of Measure </a></th> <th style="width:10%; padding: 0px;" id="list-view_c4"> <a class="sort-link '+costOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+opportunityId+', 1, \'cost\', \''+nextSortOrder+'\');"> Unit Direct Cost </a> </th> <th style="width:10%; padding: 0px;" id="list-view_c5"> Quantity </th> <th style="width:10%; padding: 0px;" id="list-view_c5"> Frequency </th> <th style="width:20%; padding: 0px;" id="list-view_c7"> Category </th> </tr> </thead><tbody><tr><td colspan="8" style = "padding: 0px;"><div style="height: 250px; overflow: auto; scrollbar-base-color: #ff8c00; scrollbar-arrow-color: white;"><table class = "items selected_products_table">'; 
                var items = {};
                var categoryDatas = [];
                var categoryData = [];
                if(data != undefined && data != null && total_rows > 0){
                for(var i =0; i < total_rows; i++) {              
                    if(items[[data[i].productcode]] == undefined){
                       items[[data[i].productcode]] = [];
                    }
                    var productcode = data[i].productcode;
                    items[productcode].push(data[i]);
                }
                if(data.length)
                var counter=0;
                $.each( items, function( productKey, productValue ) {
                    $(this).data('serial', counter++);
                    $.each( productValue, function( key, value ){
                        categoryDatas.push(value.Category);
                    });
                    var categoryRes = makeDropDownCategory(categoryDatas, counter);
                    categoryDatas = [];
                    if(productValue[0].CostOfGoodsSold == 'Labor'){
                        directCost = productValue[0].deptBurdonCost + productValue[0].deptLaborCost; 
                    }else {
                        directCost = productValue[0].unitdirectcost;
                    }
                    appendText += "<tr id='"+productKey+"'>  <td class='checkbox-width' style = 'padding-left: 5px;'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td style='width:10%;'>"+productValue[0].productcode+" </td>  <td style='width:25%;'>"+productValue[0].productname+"</td>   <td style='width:15%;'>"+productValue[0].UnitOfMeasure+"</td>  <td style='width:10%;'>"+directCost+"</td> <td style='width:10%;'><input type='text' class = 'quantity' id='quantity_"+counter+"' value='0'/></td> <td style='width:10%;'><input type='text' class = 'quantity' id='frequency_"+counter+"' value='1'/></td> <td style='width:20%;'> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                });
                    appendText += "</table></div></td></tr></tbody></table>";
                }else {
                    appendText += '<tr><td colspan="7" class="empty"><span class="empty"><span class="icon-empty"></span>No results found</span></td></tr>';
                }
            }
            var lmitedOffset = pageOffset * rowsPerPage;
            $('#searchProducts').html(appendText);
        },
        error : function()  {
            alert("No searched Products");
        }
    });
    }
}

function sortProductNotification(msg){
    var content = '  <div class="ui-corner-all jnotify-item ui-state-highlight">  <div class="jnotify-item-close">  <span class="ui-icon ui-icon-circle-close"></span>  </div>  <span class="ui-icon ui-icon-info"></span>  <span>'+msg+'.</span>  </div>';
    $('#FlashMessageView').show();
    $('#FlashMessageView').html(content);
}

function updatePriceInOpportunity(opportunityId) {   
        
    var finalGpmValue   = $("#final_gpm").val();
    var finalAmount = $("#final_amt").val();
    final_gpm_value = '';
    final_amount = '';
    if(parseFloat(finalGpmValue) > 0 && parseFloat(finalGpmValue) < 100)
    {		
        final_gpm_value = '&finalGpm='+parseFloat(finalGpmValue);
        if($("#modified_final_amt").val() > 0)
        {
            final_amount = '&finalAmnt='+parseFloat($("#modified_final_amt").val());
        }
        else
            final_amount = '&finalAmnt='+finalAmount;
    }
    else
    {
        alert('Please enter valid GPM');
        return false;
    }

    addProductNotification("Saving...!"); //Showing notification on top of the page
    var btnPropertyVal = 'Save&Update';
    url = '/app/index.php/opportunityProducts/default/updateOpportunityPrice?optId='+opportunityId+final_gpm_value+final_amount;
    
    $.ajax({
        url : url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {               
            var url = '/app/index.php/opportunities/default/details?id='+data;
            window.location.href = url;
            addProductNotification("Price updated sucessfully");       //Showing notification on top of the page
           // $("#showresults").text("Price updated sucessfully");
            setTimeout("$('#showresults').fadeIn('slow').hide();", 3000);                    
        },
        error: function(data) {
           console.log('Error in opportunity Product return values..!!');
        }
    });
}