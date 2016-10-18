$(document).ready(function() { 
    $('#btn-remove').click(function(){
        var countOptions = $('#selectedAgmt option').size();        
        if(parseInt(countOptions) == 1)
        {
            alert('Not allowed to remove all Agreement');
            return;
        }
        $('#selectedAgmt option:selected').each( function() {
            $(this).remove();
        });
    });
    $('#btn-up').bind('click', function() {
        $('#selectedAgmt option:selected').each( function() {
            var newPos = $('#selectedAgmt option').index(this) - 1;
            if (newPos > -1) {
                $('#selectedAgmt option').eq(newPos).before("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });
    $('#btn-down').bind('click', function() {
        var countOptions = $('#selectedAgmt option').size();
        $('#selectedAgmt option:selected').each( function() {
            var newPos = $('#selectedAgmt option').index(this) + 1;
            if (newPos < countOptions) {
                $('#selectedAgmt option').eq(newPos).after("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });
    
/* 
 * Modified on Sep 7 2016
 * Modified by Murugan
 * Command: Route step-4 edit mode to check select all checkbox checked or not condition checking function is calling here
 * */
    selectAllCheckboxStatus();

/* 
 * Modified on Sep 7 2016
 * Modified by Murugan
 * Command: Route step-4 edit mode to check deselect any checkbox then deselect the checkbox event calling here
 * */
    $('.product_checkbox').click(function(){
        selectAllCheckboxStatuschange();
    });

});

jQuery("input[name='list_category_list\[\]']").live('click', function() {
    updateSelectedListIds('list-view', $(this).val(), $(this).attr('checked'), '-selected-categoryIds');
});

jQuery("input[name='list-view-rowSelector\[\]']").live('click', function() {
   updateSelectedListIds('list-view', $(this).val(), $(this).attr('checked'), '-selected-agreementIds');
    var isChecked = false;
    if($(this).attr('checked') === 'checked'){
        isChecked = true;
    }else{
        isChecked = false;
    }
    updateSelectedListName($(this).val(), isChecked, $(this).attr('id'));
});

function updateSelectedListName(selectedId, isChecked, selectedRowId){
    var arrayName = new Array ();
    var html_table = '<table id="agreementNameList" class="items"><tbody><tr>';
    var selected = $('#list-view-selected-agreementIds').val().length;
    if(selected == 0 && isChecked == false){
        html_table = '<table id="agreementNameList" class="items"> <tbody><tr> <td> Please Select Agreements</td></tr></tbody> </table>';
    }else{
        var j=1;
        jQuery.each($('#list-view-selected-agreementIds').val().split(','), function(i, value)
            {
                var agreementName = jQuery('#AgreementName_'+value).val();            
                html_table += '<td>'+ j+'. '+ agreementName + "<input id='AgreementName_"+value+"' type='hidden' value='"+agreementName+"' name='AgreementName[]'></td>";
                arrayName.push(agreementName);
                if(j%3 ==0)
                {
                    html_table +='</tr><tr>';
                }                                    
                j++;
            }
        );
        $('#list-view-selected_agreementName').val(arrayName.toString()); 
        html_table += '</td></tr></tbody></table>';
    }
    $('#agreementNameList').html(html_table);
}

function updateSelectedListIds(gridViewId, selectedId, selectedValue, categoryTypeID) {
    var array = new Array ();
    var processed = false;
    jQuery.each($('#' + gridViewId + categoryTypeID).val().split(','), function(i, value)
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
    $('#' + gridViewId + categoryTypeID).val(array.toString());
}
    
function createRouteStep1(){
    var queryParams = '';
    var routeName = jQuery('#Route_routename').val();
    var crewName = jQuery('#Route_crewname').val();
    var totalSelectedCategories = jQuery('#list-view-selected-categoryIds').val();
    var isEditRoute = jQuery('#edit-route').val().split("_");
    var isCloneBack = jQuery('#is_clone_back').val();
    if(routeName == ''){
        jQuery('#route_name').show();
        return false;
    }
    if(crewName == ''){
        jQuery('#crew_name').show();
        return false;
    }
    if(totalSelectedCategories == ''){
        addProductNotification("Please select atleast one category...!");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }
    if(isEditRoute[0] != '' && isEditRoute[1] == 'edit'){
        var isEdit = isEditRoute;
        //queryParams = '&edit=true';
    }else if(isEditRoute[0] != '' && isEditRoute[1] == 'clone'){
        var isEdit = isEditRoute;    
        //queryParams = '&clone=true';
    }else{
       var isEdit = isEditRoute;
       //queryParams = '&create=true';
    }
    var data = {
        routeName: $.trim(routeName),
        crewName: $.trim(crewName),
        totalSelectedCategories: totalSelectedCategories,
        isEdit: isEdit,
        isCloneBack: isCloneBack
    };
    var dataParams = {
        routeInformation:JSON.stringify(data),
    }
    var url = '/app/index.php/routes/default/GetAddNewRouteAndCategories';
    addProductNotification("Saving...!"); 
    $.ajax({
        url : url,
        type : 'GET',
        data: dataParams,
        dataType: 'json',
        success : function(data)
        {
        
            var cloneId = parseInt(data.newClonedRouteId);

            if(cloneId > 0 && data.type == 'clone'){
                var redirectUrl = '/app/index.php/routes/default/createStep2?id='+data.oldRouteId+'&ClonedRouteId='+cloneId+'&type=clone';
            }else if(cloneId == 0 && data.type == 'edit'){
                var redirectUrl = '/app/index.php/routes/default/createStep2?id='+data.oldRouteId+'&ClonedRouteId='+cloneId+'&type=edit';
            }else{
                var redirectUrl = '/app/index.php/routes/default/createStep2?id='+data.oldRouteId+'&ClonedRouteId='+cloneId+'&type=create';
            }
            window.location.href = redirectUrl;
        },
        error : function()  {
            console.log("Request failed..!!");
        }
    });
}

function createRouteStep2(newClonedRouteId){
    var queryParams = '';
    var totalSelectedAgreement = jQuery('#list-view-selected-agreementIds').val();
    var totalSelectedAgreementName = jQuery('#list-view-selected_agreementName').val();
    var routeId = jQuery('#routeId').val();
    var isEditRoute = jQuery('#edit-route').val();
    if(totalSelectedAgreement == ''){
        addProductNotification("Please select atleast one agreement...!");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }
    var res = totalSelectedAgreement.split(",");
    var agreementCount = res.length;
    if( agreementCount > 15){
        addProductNotification("Only 15 Agreement can be added to Route...!");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }
    if(isEditRoute > 0){
        queryParams = '&edit=true';
    }
    var data = {
        routeId: routeId, 
        totalSelectedAgreement: totalSelectedAgreement,
        newClonedRouteId: newClonedRouteId,
        totalSelectedAgreementName: totalSelectedAgreementName
    };
    var dataParams = {
        routeAgreementInformation:JSON.stringify(data),
    }
    console.log(dataParams);
    var url = '/app/index.php/routes/default/GetAddNewRouteAgreement';
    addProductNotification("Saving...!"); 
    $.ajax({
        url : url,
        type : 'GET',
        data: dataParams,
        dataType: 'json',
        success : function(data)
        {
            var cloneId = parseInt(data.newClonedRouteId);

            if(cloneId > 0){
                var redirectUrl = '/app/index.php/routes/default/createStep3?id='+data.oldRouteId+'&ClonedRouteId='+cloneId+'&type=clone';
            }else{
                var redirectUrl = '/app/index.php/routes/default/createStep3?id='+data.oldRouteId+'&ClonedRouteId='+cloneId+'&type=edit';
            }
            window.location.href = redirectUrl;
        },
        error : function()  {
            console.log("Request failed..!!");
        }
    });
}

function addProductNotification(msg){
    var content = '<div id="FlashMessageBar" class="notify-wrapper">  <div class="jnotify-item-wrapper">  <div class="ui-corner-all jnotify-item ui-state-highlight">  <div class="jnotify-item-close">  <span class="ui-icon ui-icon-circle-close"></span>  </div>  <span class="ui-icon ui-icon-info"></span>  <span>'+msg+'.</span>  </div></div></div>';
    $('#FlashMessageView').show();
    $('#FlashMessageView').html(content);
}

//For Date picker
jQuery(function(){
    jQuery('#RouteTracking_date_of_service').datepicker({'maxDate': 0, 'showOn': 'both', 'buttonText': '<span><!--Date--><\/span>', 'showButtonPanel': true, 'buttonImageOnly': false, 'dateFormat': 'm/d/yy', 'currentText': '<span class=\"z-label\">Now<\/span>', 'closeText': '<span class=\"z-label\">Done<\/span>'});
});

//For Time picker
jQuery(function(){
    jQuery('.time').timepicker({ timeFormat: 'h:mm tt', 'showButtonPanel': true, 'buttonImageOnly': false, 'currentText': '<span class=\"z-label\">Now<\/span>', 'closeText': '<span class=\"z-label\">Done<\/span>'});
});

function searchActiveAgreement(pageOffset) { 
    var url = '/app/index.php/routes/default/GetActiveAgreement';    
    var routeId  = $('#routeId').val();
    var agmtName= $('#agreement_name_value').val();
    $('#agreement_name_value').val($.trim(agmtName));
    var data = {pageOffset:pageOffset, agmtName:$.trim(agmtName), routeId:routeId};
   
    $.ajax({
        url : url,
        type : 'GET',
        data: data,
        dataType: 'json',
        success : function(data)
        { 
            var rowsPerPage = 10; 
            var appendText = '';
            var total_rows = Object.keys(data).length;
            
            appendText += '<table class="items selected_products_table"> <thead> <tr>  <th class="checkbox-column">Select</th> <th id="list-view_c2">Id</th> <th id="list-view_c3">Agreement Name</th> <th id="list-view_c4">Account Name</th> </tr> </thead><tbody></tbody>'; 

            if(data != undefined && data != null && total_rows > 0){

            if(total_rows)
                var counter=0;

            $.each( data, function( productKey, productValue ) {
                var isContains = $('#list-view-selected-agreementIds').val().indexOf(productValue.id) > -1;
           
                if(isContains == true)
                {
                    is_checked = "checked='checked'";
                    add_check_class = ' c_on';
                }
                else
                {
                    is_checked = '';
                    add_check_class = '';
                }

                    appendText += "<tr id='"+productKey+"'> <td class='checkbox-column'>   <label class='hasCheckBox"+add_check_class+"'>  <input type='hidden' name='AgreementName[]' value='"+productValue.name+"' id='AgreementName_"+productValue.id+"'><input id='list-view-rowSelector_"+counter+"' type='checkbox' name='list-view-rowSelector[]' value='"+productValue.id+"' "+is_checked+" />    </label>  </td>   <td>"+productValue.id+"</td>  <td>"+productValue.name+" </td>  <td>"+productValue.account.name+"</td> </tr>";
                    counter++;
                });
                }else {
                    appendText += '<tr><td colspan="7" class="empty"><span class="empty"><span class="icon-empty"></span>No results found</span></td></tr>';
                }
            
            var lmitedOffset = pageOffset * rowsPerPage;
            if(total_rows < lmitedOffset){
                appendText += '</table> </div> </div></div>';
            }else{                
                pageOffset++;
                appendText += '</table> </div> <div class="pager vertical"><ul class="endless-list-pager" id="yw3"><li class="next"><a href="#" class="vertical-forward-pager" id="list-view-endless-page" onClick="javascript:searchActiveAgreement('+pageOffset+');"><span>next</span></a></li></ul></div></div></div>';
            }
            $('#result_div').html(appendText);
        },
        error : function()  {
            alert("No searched Products");
        }
    });     
}

 function addRouteAgreement(route_id,btn_property){    
    var newClonedRouteId = btn_property.id;
    var queryParams = '';
    var agmt_ids = [];
    var isEditRoute = jQuery('#edit-route').val();
    $('#selectedAgmt option').each(function() {
        agmt_ids.push($(this).val());
    });

    if(route_id == ''){
        addProductNotification("Route Id Required");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }

    if(agmt_ids.length == 0){
        addProductNotification("Please select atleast one Agreement");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }
    
    if(isEditRoute > 0){
        var isEdit = isEditRoute;
        queryParams = '&edit=true';
    }
        
    var data = {
        routeId: route_id, 
        agmt_ids: agmt_ids,
        isEdit: isEdit,
        newClonedRouteId: newClonedRouteId
    };

    var dataParams = {
        routeInformation:JSON.stringify(data),
    }

    var url = '/app/index.php/routes/default/AddNewRouteAgreements';
    addProductNotification("Saving...!"); 
    $.ajax({
        url : url,
        type : 'GET',
        data: dataParams,
        dataType: 'json',
        success : function(data)
        {
            var cloneId = parseInt(data.newClonedRouteId);

            if(cloneId > 0){
                var redirecturl = '/app/index.php/routes/default/createStep4?id='+data.oldRouteId+'&ClonedRouteId='+data.newClonedRouteId+'&type=clone';
            }else{
                var redirecturl = '/app/index.php/routes/default/createStep4?id='+data.oldRouteId+'&ClonedRouteId='+data.newClonedRouteId+'&type=edit';
            }
            window.location.href = redirecturl;
        },
        error : function()  {
            console.log("Request failed..!!");
        }
    });
}

function createRouteAgreementProducts(route_id, newClonedRouteId){
    var queryParams = '';
    var isEditRoute = jQuery('#edit-route').val();
    var selected_agmt_prods = new Array ();
    $('input[name="route_prod"]').each(function () {
        var isCheckedProd = (this.checked ? "1" : "0");
        if(isCheckedProd != 0){
            selected_agmt_prods.push($(this).val());
        }
    });
    
    if(route_id == ''){
        addProductNotification("Route Id Required");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }

    if(selected_agmt_prods.length == 0){
        addProductNotification("Please select atleast one Agreement Product");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }
    
    if(isEditRoute != ''){
        var isEdit = isEditRoute;
        queryParams = '&edit=true';
    }
        
    var data = {
        routeId: route_id, 
        selected_agmt_prods: selected_agmt_prods,
        isEdit: isEdit,
        newClonedRouteId: newClonedRouteId
    };

    var dataParams = {
        routeInformation:JSON.stringify(data),
    }

    var url = '/app/index.php/routes/default/AddRouteAgreementProducts';
    addProductNotification("Saving...!"); 
    $.ajax({
        url : url,
        type : 'GET',
        data: dataParams,
        dataType: 'json',
        success : function(data)
        {
            if(data.newClonedRouteId == 0){
                var redirectUrl = '/app/index.php/routes/default/details?id='+data.oldRouteId;
            }else{
                var redirectUrl = '/app/index.php/routes/default/details?id='+newClonedRouteId;
            }
            window.location.href = redirectUrl;
        },
        error : function()  {
            console.log("Request failed..!!");
        }
    });
}

//Function for save route tracking and route tracking products
function saveRouteTrackingProducts(route_id, btn_property){
    if(btn_property.id != 'Cancel'){
        addRouteAgreementProductArray = new Array;
        addRouteTrackingArray = {};
        addRouteAgreementProductDatas = {};
        addRouteTrackingDatas = {};
        var ProdCount = $('#countOfAgmntProd_id').val();
        var url = 'Null';
        var consumedIsEmpty = false; 
        if($(RouteTracking_date_of_service).val() == '' ){
            jQuery('#RouteTracking_date_of_service_Error').show();
            return false;
        }else{
            addRouteTrackingDatas ["date_of_service"] = $(RouteTracking_date_of_service).val(); 
        }

        addRouteTrackingDatas ["route_id"] = route_id;

        if($(RouteTracking_service_start_time).val() == '' ){
            jQuery('#RouteTracking_service_start_time_Error').show();
            return false;
        }else{
            addRouteTrackingDatas ["service_start_time"] = $(RouteTracking_service_start_time).val();
        }

        if($(RouteTracking_service_end_time).val() == '' ){
            jQuery('#RouteTracking_service_end_time_Error').show();
            return false;
        }else{
            addRouteTrackingDatas ["service_end_time"] = $(RouteTracking_service_end_time).val();
        }   
        
        if($(RouteTracking_name).val() == '' ){
            jQuery('#RouteTracking_name_Error').show();
            return false;
        }else{
            addRouteTrackingDatas ["service_name"] = $(RouteTracking_name).val();
        } 

        addRouteTrackingArray["addRouteTrackingDetails"] = addRouteTrackingDatas;

        $('input[name="route_prod_consumed"]').each(function (index) {
            var agreement_id = $(this).attr('id').split("-");
            var consumedUnits = parseFloat($(this).val());
            if(consumedUnits > 0){
                addRouteAgreementProductDatas ["consumed_unit"] = consumedUnits.toFixed(4);
                consumedIsEmpty = true;     
                addRouteAgreementProductDatas ["agreement_id"] = agreement_id[1];
                addRouteAgreementProductDatas ["agreement_product_id"] = agreement_id[2];
                addRouteAgreementProductArray[index] = addRouteAgreementProductDatas;
                addRouteAgreementProductDatas = {};
                url = '/app/index.php/routes/default/AddRouteTrackingProducts';
            }
        });
        if(!consumedIsEmpty){
            addProductNotification("Consumed value should be greater than zero");
            setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
            return false;
        }
        addRouteTrackingArray["addRouteTrackingProdDetails"] = addRouteAgreementProductArray; 

        var data = {
            addRouteTracking: addRouteTrackingArray
        };

        var dataParams = {
            routeTrackingInfo:JSON.stringify(data),
        }
        addProductNotification("Saving...!");
        $('#Save').addClass('disabled-link');
        $.ajax({
            url : url,
            type : 'GET',
            data: dataParams,
            dataType: 'json',
            success : function(data)
            {
                var redirect_url = '/app/index.php/routes/default/details?id='+route_id;
                addProductNotification("Saved successfully...!");
                window.location.href = redirect_url;
            },
            error : function()  {
                console.log("Request failed..!!");
            }
        });
    }else{
        var redirect_url = '/app/index.php/routes/default/details?id='+route_id;
        window.location.href = redirect_url;
    }
}  

/* 
 * Modified on Sep 7 2016
 * Modified by Murugan
 * Command: Route step-4 create/edit mode to select or deselect the select all checkbox then belowed listed checkbox to select and deselect operation is performing here
 * */
function toggle(source) {
  checkboxes = document.getElementsByName('route_prod');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}

/* 
 * Modified on Sep 7 2016
 * Modified by Murugan
 * Command: Route step-4 edit mode to check select all checkbox checked or not condition checking here
 * */
function selectAllCheckboxStatus(){
    if($('#RouteStep4View').find('input.product_checkbox').length==$("#RouteStep4View").find('input:checked').length){
        $("#RouteStep4View").find('input.selectall').prop('checked',true);
    }
}

/* 
 * Modified on Sep 7 2016
 * Modified by Murugan
 * Command: Route step-4 edit mode to check deselect any checkbox then deselect the checkbox event 
 * */
function selectAllCheckboxStatuschange(){
    checkbox_flag = true;
    checkboxes = document.getElementsByName('route_prod');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        console.log(checkboxes[i].checked);
      if(!checkboxes[i].checked){
          checkbox_flag = false;
      }
    }
    if(checkbox_flag == true){
        $("#RouteStep4View").find('input.selectall').prop('checked',true);
    }else{
        $("#RouteStep4View").find('input.selectall').prop('checked',false);
    }
}
    