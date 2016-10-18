/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

jQuery(function(){
    jQuery('#AgreementTracking_tracking_date').datepicker({'maxDate': 0, 'showOn': 'both', 'buttonText': '<span><!--Date--><\/span>', 'showButtonPanel': true, 'buttonImageOnly': false, 'dateFormat': 'm/d/yy', 'currentText': '<span class=\"z-label\">Now<\/span>', 'closeText': '<span class=\"z-label\">Done<\/span>'});
});



jQuery('input[name="AgreementTracking[total_non_agreement_selected_products]"]').on('click', function(){
    var IsChecked = $('input[name="AgreementTracking[total_non_agreement_selected_products]"]:checked').length > 0;
    if(IsChecked == 1){
        jQuery('#add_product_search').show();
        jQuery('#selected_products').hide();
    }
    else{
        jQuery('#add_product_search').hide();
        jQuery('#searchNonAgreementProducts').hide();
        jQuery('#selected_products').show();
    }
});

function addAgreementTracking(agreementId){
    addAgreementProductJsonObj = [];
    addAgreementProductItems = {};
    
    var isCompletedIds = new Array ();
    $('input[name="CompletedCategory"]').each(function () {
        var sThisVal = (this.checked ? "1" : "0");
        if(sThisVal != 0){
            isCompletedIds.push($(this).val());
        }
    });
    var IsChecked = $('input[name="AgreementTracking[total_non_agreement_selected_products]"]:checked').length > 0;
    if(IsChecked == 1){
        addNonAgreementProducts(agreementId);
        return false;
    }
    
    var url = '/app/index.php/agreementTracking/default/GetAddTracking';
    var trackingName = jQuery('#Agreement_Tracking_name').val();
    var trackingdate = jQuery('#AgreementTracking_tracking_date').val();
    if(trackingdate == ''){
        jQuery('#AgreementTracking_Date').show();
        return false;
    }
    var totalAgreementProducts = jQuery('#Selected_Products_Ids').val();
    var totalNonAgreementProducts = 0;
    var isCompleted = 0;
    var consumednumeric;
    for(var i=0; i < totalAgreementProducts; i++) {
        
        if(parseFloat($('#consumed_unit_'+i).val()) >= 0){
            addAgreementProductItems ["agreement_product_id"] = $('#agreement_product_id_'+i).val();
            consumednumeric = parseFloat($('#consumed_unit_'+i).val());
            addAgreementProductItems ["consumed_unit"] = consumednumeric.toFixed(4); 
            addAgreementProductItems ["product_code"] = $('#productCode_'+i).val();
            addAgreementProductJsonObj.push(addAgreementProductItems);
            addAgreementProductItems = {};
        }else{
            addProductNotification("Consumed value should be greater than zero");
            setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
            return false;
        }
    }  
    if(addAgreementProductJsonObj == ''){
        addProductNotification("Please enter the consumed quantity for atleast one product...!");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }
    
    var data = {
        agreementId: agreementId,
        trackingName: $.trim(trackingName),
        trackingdate: trackingdate, 
        totalAgreementProducts: totalAgreementProducts, 
        totalNonAgreementProducts:totalNonAgreementProducts, 
        products:addAgreementProductJsonObj,
        isCompletedIds:isCompletedIds,
    };
    var dataParams = {
        trackingInformation:JSON.stringify(data),
    }    
    
/*   $('#saveAgreementTracking').addClass('disabled-link');*/
    addProductNotification("Saving...!"); //Showing notification on top of the page
    $.ajax({
        url : url,
        type : 'GET',
        data: dataParams,
        dataType: 'json',
        success : function(data)
        {
            var url = '/app/index.php/agreements/default/details?id='+data;
            window.location.href = url;
            addProductNotification("Agreement Tracking saved sucessfully");
            setTimeout("$('#showresults').fadeIn('slow').hide();", 3000);
        },
        error : function()  {
            console.log("Request failed..!!");
        }
    });
}

function searchNonAgreementProducts(opportunityId, pageOffset,prdname,btnProperty) {
    var productName= $('#agreement_productname_value').val();
    $('#agreement_productname_value').val($.trim(productName));
    if(productName.length == 0 || productName.length >= 3 || btnProperty.id === 'search'){
        $("#searchProducts").focus();
        $("#afterSearch").css('display','block');
        $("#afterSearchCostBookDatas").css('display','block');
        if(opportunityId != null){
            var url = '/app/index.php/agreementTracking/default/GetNonAgreementProducts';
            var category= $('#agreement_AddProductcategory_value').val();
            var costOfGoods= $('#agreement_AddProductcostofgoodssold_value').val();
            var addProductoptId= opportunityId;
            var data = {category: category, costOfGoods: costOfGoods, addProductoptId: addProductoptId, pageOffset:pageOffset, productName:productName};

            $.ajax({
                url : url,
                type : 'GET',
                data: data,
                dataType: 'json',
                success : function(data)
                {
                    var total_rows = data.length;
                    var rowsPerPage = 10; 
                    var appendText = '';
                    if($("#recordType_Ids").val() != 'Recurring Final'){
                        $("#agmntTrackingSearchProducts").css("display","block");
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
                        appendText +="<table class = 'items selected_products_table'>";
                        var counter=0;
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
                            appendText += "<tr id='"+productKey+"'>  <td class='checkbox-width' style = 'padding-left: 5px;'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td style='width:15%;'>"+productValue[0].productcode+" </td>  <td style='width:25%;'>"+productValue[0].productname+"</td>   <td style='width:15%;'>"+productValue[0].UnitOfMeasure+"</td>  <td style='width:10%;'>"+directCost+"</td> <td style='width:15%;'><input type='text' class = 'quantity' id='quantity_"+counter+"' value='1.0'/></td> <td style='width:20%;'> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                        });
                        appendText +="</table>";
                    } else {
                        appendText += '<span class="empty"><span class="icon-empty"></span>No results found</span>';
                    }

                    } else{
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
                        appendText +="<table class = 'items selected_products_table'>";
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
                            appendText += "<tr id='"+productKey+"'>  <td class='checkbox-column'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td>"+productValue[0].productcode+" </td>  <td>"+productValue[0].productname+"</td>   <td>"+productValue[0].UnitOfMeasure+"</td>  <td>"+directCost+"</td> <td><input type='text' id='quantity_"+counter+"' value='1.0'/> <td><input type='text' id='frequency_"+counter+"' value='1'/></td> </td> <td> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                        });
                        appendText +="</table>";
                        }else {
                            appendText += '<span class="empty"><span class="icon-empty"></span>No results found</span>';
                        }
                    }
                    var lmitedOffset = pageOffset * rowsPerPage;
                    $('#agmntTracking_scroll_result').html(appendText);
                },
                error : function()  {
                    alert("No searched Products");
                }
            });
        }
    }    
}

function updateAgreementTracking(agreementTrackingId){

    var ids                 = $("#list-view-selectedIds").val();            
    var addNewOptProducts   = ids;
    var addProductValues    = checkAddProductIfUndefinedOrNot(addNewOptProducts);
    var newNonAgmtProduct   = false;

    if(addProductValues != null)
    {
        newNonAgmtProduct = addAgreementProduct(agreementTrackingId, ids);        
    }

    addAgreementProductJsonObj = [];
    addAgreementProductItems = {};
    var url = '/app/index.php/agreementTracking/default/GetUpdateTracking';
    var trackingId = jQuery('#agreement_tracking_id').val();
    var trackingName = jQuery('#Agreement_Tracking_name').val();
    var trackingdate = jQuery('#AgreementTracking_tracking_date').val();
    var totalAgreementProducts = jQuery('#Selected_Products_Ids').val();
    var totalNonAgreementProducts = 0;
    var isCompleted = 0;
    var consumednumeric;
    for(var i=0; i < totalAgreementProducts; i++) {
        
        if(parseFloat($('#consumed_unit_'+i).val()) >= 0){
            addAgreementProductItems ["agreement_product_id"] = $('#agreement_product_id_'+i).val();
            consumednumeric = parseFloat($('#consumed_unit_'+i).val());
            addAgreementProductItems ["consumed_unit"] = consumednumeric.toFixed(4); 
            addAgreementProductItems ["agreement_tracking_product_id"] = $('#agreement_tracking_product_id'+i).val();
            addAgreementProductJsonObj.push(addAgreementProductItems);
            addAgreementProductItems = {};
        }
    }
    
    var data = {
        trackingName: $.trim(trackingName),
        trackingdate: trackingdate, 
        trackingId: trackingId,
        products:addAgreementProductJsonObj,
        newProducts:newNonAgmtProduct
    };
    var dataParams = {
        trackingInformation:JSON.stringify(data),
    }
/*    $('#updateAgreementTracking').addClass('disabled-link');*/
    $.ajax({
        url : url,
        type : 'GET',
        data: dataParams,
        dataType: 'json',
        success : function(data)
        {
            var url = '/app/index.php/agreements/default/details?id='+data;
            window.location.href = url;
            addProductNotification("Agreement Tracking updated sucessfully");
            setTimeout("$('#showresults').fadeIn('slow').hide();", 3000);
        },
        error : function()  {
            console.log("Request failed..!!");
        }
    });
    
}

//For adding the Non agreement products
function addNonAgreementProducts(agreementId) 
{
    var ids                 = $("#list-view-selectedIds").val();            
    var addNewOptProducts   = ids;
    var addProductValues    = checkAddProductIfUndefinedOrNot(addNewOptProducts);
    var trackingName        = jQuery('#Agreement_Tracking_name').val();
    var trackingdate        = jQuery('#AgreementTracking_tracking_date').val();
    if(trackingdate == ''){
        jQuery('#AgreementTracking_Date').show();
        return false;
    }
    if(addProductValues != null)
    {
        var addProduct = addAgreementProduct(agreementId, ids);
        if(addProduct != false)
        {
            addProductNotification("Saving...!"); //Showing notification on top of the page
            url = '/app/index.php/agreementTracking/default/addNonAgreementProducts?ids='+ids+'&addJsonObj='+JSON.stringify(addProduct)+'&agtId='+agreementId+'&track_date='+trackingdate+'&track_name='+trackingName;
            
            $.ajax({
                url : url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = '/app/index.php/agreements/default/details?id='+data;
                    window.location.href = url;
                    addProductNotification("Products saved sucessfully"); //Showing notification on top of the page
                    setTimeout("$('#showresults').fadeIn('slow').hide();", 3000);
                },
                error: function(data) {
                   console.log("Request failed..!!");
                }
            });
        }
        else
        {
            alert("Please search and choose atleast one product");
            return false;
        }
   }
   else
    {
        addProductNotification("Consumed value should be greater than zero");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3500);
        return false;
    }
}

function checkAddProductIfUndefinedOrNot(addNewOptProducts){
    if((addNewOptProducts == '' || addNewOptProducts == undefined) && addNewOptProducts != null){
        return null;
    }else{
        return $("#list-view-selectedIds").val();
    }
}

function addProductNotification(msg){
    var content = '<div id="FlashMessageBar" class="notify-wrapper">  <div class="jnotify-item-wrapper">  <div class="ui-corner-all jnotify-item ui-state-highlight">  <div class="jnotify-item-close">  <span class="ui-icon ui-icon-circle-close"></span>  </div>  <span class="ui-icon ui-icon-info"></span>  <span>'+msg+'.</span>  </div></div></div>';
    $('#FlashMessageView').html(content);
}

function addAgreementProduct(agreementId, ids){
    addProductJsonObj = [];
    addProductItem = {};
    var arr = ids.split(',');
    $("#selectedProductCnt").val(arr.length);
    jQuery.each(jQuery("input[name='list-view-rowSelector\[\]']:checked"), function(i, value) {
        
        addProductItem ["costBookId"] = value.value;
        var quantity = parseFloat($("#quantity_"+value.id.split('_')[1]).val());
        if(quantity > 0){
            addProductItem ["add_Quantity"] = quantity.toFixed(4);                
        }
        
        addProductItem ["add_Category"] = $("#Category_"+value.id.split('_')[1]).val();
        addProductJsonObj.push(addProductItem);
        addProductItem = {};
        
    });
    return addProductJsonObj;
}

function makeDropDownCategory(data, count){
    element = '<div class="hasDropDown"><span class="select-arrow"></span><select id="Category_'+count+'" style="width:100%;">';
        for(i=0; i < data.length; i++){
            element += '<option value="'+data[i]+'">'+data[i]+'</option>';
        }
    element += '</select>';
    return element;
}

//For adding the Non agreement products to the Tracking
function addNonAgreementProductsForTracking(agreementTrackingId)
{
    var ids                 = $("#list-view-selectedIds").val();            
    var addNewOptProducts   = ids;
    var addProductValues    = checkAddProductIfUndefinedOrNot(addNewOptProducts);

    if(addProductValues != null)
    {
        var addProduct = addAgreementProduct(agreementTrackingId, ids);
        if(addProduct != false)
        {
            addProductNotification("Saving...!"); //Showing notification on top of the page
            url = '/app/index.php/agreementTracking/default/addNonAgreementProductsForTracking?ids='+ids+'&addJsonObj='+JSON.stringify(addProduct)+'&agtTrackId='+agreementTrackingId;
            
            $.ajax({
                url : url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                },
                error: function(data) {
                   console.log('Error in Product values..!!');
                }
            });
        }
        else
        {
            alert("Please search and choose atleast one product");
            return false;
        }
   }
   else
    {
        var url = '/app/index.php/agreements/default/details?id='+agreementId;
        window.location.href = url;
    }
}

function sortProducts(agreementId, pageOffset, sortFor, sortOrder) {    
    sortProductNotification('Loading...');
    $( "#searchProducts" ).focus();
    $("#afterSearch").css('display','block');
    $("#afterSearchCostBookDatas").css('display','block');
    if(agreementId != null){
	var url = '/app/index.php/agreementTracking/default/GetNonAgreementProducts';
    var category= $('#agreement_AddProductcategory_value').val();
    var costOfGoods= $('#agreement_AddProductcostofgoodssold_value').val();
    var addProductoptId= agreementId;
    var productName= $('#agreement_productname_value').val();
    $('#agreement_productname_value').val($.trim(productName));

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
            if($("#recordType_Ids").val() != 'Recurring Agreement'){
                $("#agmntTrackingSearchProducts").css("display","block");
                appendText += '<table class="items selected_products_table"><div style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div> <thead><tr>  <th id="list-view-rowSelector" class="checkbox-column" >  <label class="hasCheckBox">  <input value="1" name="list-view-rowSelector_all" id="list-view-rowSelector_all" type="checkbox">   </label>  </th>  <th style="width:15%;" id="list-view_c1">  <a class="sort-link '+codeOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'code\', \''+nextSortOrder+'\');">  Product Code </a> </th>  <th style="width:25%;" id="list-view_c2">  <a class="sort-link '+nameOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'name\', \''+nextSortOrder+'\');">  Product Name  </a>  </th>  <th style="width:15%;" id="list-view_c3"> <a class="sort-link '+unitOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'unit\', \''+nextSortOrder+'\');"> Unit of Measure </a> </th> <th style="width:10%;" id="list-view_c4"> <a class="sort-link '+costOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'cost\', \''+nextSortOrder+'\');"> Unit Direct Cost </a> </th> <th style="width:15%;" id="list-view_c5"> Quantity </th> <th style="width:20%;" id="list-view_c7"> Category </th> </tr> </thead><tbody><tr><td colspan="7" style = "padding: 0px;"><div id="agmntTracking_scroll_result"><table class = "items selected_products_table" style="margin: 0px;">'; 
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
                        directCost = parseFloat(productValue[0].deptBurdonCost) + parseFloat(productValue[0].deptLaborCost); 
                    }else {
                        directCost = productValue[0].unitdirectcost;
                    }
                    //appendText += "<tr id='"+productKey+"'>  <td class='checkbox-column'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td>"+productValue[0].productcode+" </td>  <td>"+productValue[0].productname+"</td>   <td>"+productValue[0].UnitOfMeasure+"</td>  <td>"+productValue[0].unitdirectcost+"</td> <td><input type='text' id='quantity_"+counter+"' value='1.0'/></td> <td> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                    appendText += "<tr id='"+productKey+"'>  <td class='checkbox-width' style = 'padding-left: 5px;'>   <label class='hasCheckBox'>  <input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue[0].id+"'/>    </label>  </td>     <td style='width:15%;'>"+productValue[0].productcode+" </td>  <td style='width:25%;'>"+productValue[0].productname+"</td>   <td style='width:15%;'>"+productValue[0].UnitOfMeasure+"</td>  <td style='width:10%;'>"+directCost+"</td> <td style='width:15%;'><input type='text' class = 'quantity' id='quantity_"+counter+"' value='1.0'/></td> <td style='width:20%;'> "+categoryRes+" </td><input value='' name = 'list-view-selectedIds' id = 'list-view-selectedIds' type = 'hidden'></td></tr>";
                });
                    appendText += "</table></div></td></tr></tbody></table>";
            } else {
                appendText += '<tr><td colspan="7" class="empty"><span class="empty"><span class="icon-empty"></span>No results found</span></td></tr>';
            }

            } else{
                $("#agmntTrackingSearchProducts").css("display","block");
                appendText += '<table class="items selected_products_table"><div style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div> <tbody><tr>  <th class="checkbox-column" id="list-view-rowSelector">  <label class="hasCheckBox">  <input value="1" name="list-view-rowSelector_all" id="list-view-rowSelector_all" type="checkbox">  </label>  </th>  <th style="width:10%;" id="list-view_c1"> <a class="sort-link '+codeOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'code\', \''+nextSortOrder+'\');">  Product Code </a> </th>  <th style="width:25%; padding: 0px;" id="list-view_c2">  <a class="sort-link '+nameOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'name\', \''+nextSortOrder+'\');">  Product Name  </a>  </th>  <th style="width:15%; padding: 0px;" id="list-view_c3"> <a class="sort-link '+unitOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'unit\', \''+nextSortOrder+'\');"> Unit of Measure </a></th> <th style="width:10%; padding: 0px;" id="list-view_c4"> <a class="sort-link '+costOrderClass+'" href="javascript:void(0);" onClick="javascript:sortProducts('+agreementId+', 1, \'cost\', \''+nextSortOrder+'\');"> Unit Direct Cost </a> </th> <th style="width:10%; padding: 0px;" id="list-view_c5"> Quantity </th> <th style="width:10%; padding: 0px;" id="list-view_c5"> Frequency </th> <th style="width:20%; padding: 0px;" id="list-view_c7"> Category </th> </tr> </thead><tbody><tr><td colspan="8" style = "padding: 0px;"><div style="height: 250px; overflow: auto; scrollbar-base-color: #ff8c00; scrollbar-arrow-color: white;"><table class = "items selected_products_table" style="margin: 0px;">'; 
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
                        directCost = parseFloat(productValue[0].deptBurdonCost) + parseFloat(productValue[0].deptLaborCost); 
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
            
            $('#agmntTrackingSearchProducts').html(appendText);
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

