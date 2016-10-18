/**
 * Copy the product template data for creation of product
 */

var cnt = 0;
function addProductsInAssembly(productcode, inputid)
{
    if( $("#currAssemblyCnt").val() != undefined) {
        currCount = $('#currAssemblyCnt').val();
    } else if( $("#currAssemblyCnt").val() == undefined) {
        var currCount = $('input[name*="detailRatio"]').length;
    }
    var cnt = $('input[name*="detailRatio"]').length;
    var uom = 'uom';
    var appendText = '';

 if( parseFloat($("#ratio"+inputid).val()) > 0 ) {
    $("#row_"+inputid).hide();
    var url = '/app/index.php/costbook/default/getDataByProductCode?productcode='+productcode;
    appendText += '';
    $.ajax(
    {
        type: 'GET',
        url: url,
        dataType: 'json',
        success: function(data) {
            var results = data.split('$##$');
            appendText += '<tr><td width="15%"><label id="detailRow_'+currCount+'">'+results[1]+'<input type="hidden" id="hidProductCode'+currCount+'" value="'+results[1]+'" /></label></td><td width=30%>'+results[0]+'</td><td width="15%"><input type="text" id="detail_ratio'+currCount+'" name="detailRatio" value="" maxlength="4" style="width:120px;" /></td><td width="20%">'+results[2]+'</td><td width="20%">per '+$('#hidUOM').val()+'</td><input type="hidden" id="currAssemblyCnt" name="currAssemblyCnt" value="" /></tr>';
            $('#detail_products').append(appendText);
            $("#detail_ratio"+currCount).val($("#ratio"+inputid).val());
            $("#currAssemblyCnt").val(parseInt(currCount)+parseInt(1));
            currCount++;
        }
    });
 } else {
        alert('Ratio must be greater than 0');
        $("#ratio"+inputid).focus();
        return false;
 }
}

function saveAssemblyStep2() {
    var check = 0;
    if( $("input[name=detailRatio]").val() == undefined ) {
        alert('Please add atleast one product');
        return false;
    } else {
        var cnt = $('input[name*="detailRatio"]').length;
	var product_code = [];
	var ratio = [];
	var str_pc = '';
	var str_ratio = '';
	for (var i=0; i < cnt; i++) {
            product_code.push($('#hidProductCode'+i).val());
            if(parseFloat($('#detail_ratio'+i).val()) > 0 ){
                check = 1;
                str_pc += 'GICRM|'+$('#hidProductCode'+i).val()+'|'+parseFloat($('#detail_ratio'+i).val()).toFixed(4)+';'; 
            }
	}
        if(check == 0){
             alert("Product must have ratio greater than 0.");
             return false;
        }
	str_pc = str_pc.substring(0, str_pc.length - 1);
	var url = '/app/index.php/costbook/default/saveAssemblyStep2?ids='+str_pc+'&model_id='+$("#hidModelId").val();
	$.ajax(
        {
            type: 'GET',
            url: url,
            success: function(data) { 
                if(data == 1) {
                    window.location.href = '/app/index.php/costbook/default/AssemblyStep3?id='+$("#hidModelId").val();
                } else {
                    //alert('Error in Adding')    
                    return false;
                }    
            },
            failure: function() {
                console.log('fail');
                return false;
            }
        }
        );
    }
}

function sortProducts(productId, sortFor, sortOrder) {
    sortProductNotification('Loading...');
    $( "#searchProducts" ).focus();
    $("#afterSearch").css('display','block');
    $("#afterSearchCostBookDatas").css('display','block');

    if(productId != null)
    { 	
        var url = '/app/index.php/costbook/default/getAssemblySearchData';
        var category = $('#Costbook_assemblycategory_value').val();
        var costOfGoods = $('#Costbook_costofgoodssoldassembly_value').val();
        var addProductoptId = productId;
        var productName = $('#Costbook_productname_value').val();
        $('#Costbook_productname_value').val($.trim(productName));

        var data = {category: category, costOfGoods: costOfGoods, productId: addProductoptId, productName:productName, sortFor:sortFor, sortOrder:sortOrder};

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

        $.ajax(
        {
            url : url,
            type : 'GET',
            data: data,
            dataType: 'json',
            success : function(data)
            {
                setTimeout("$('#FlashMessageView').fadeIn('slow');", 1500);
		$('#FlashMessageView').html(''); 
                var total_rows = data.length;
                var prod_name_search_str = $('#Costbook_productname_value').val();
                $('#Costbook_productname_value').val($.trim(prod_name_search_str));

                var appendText ='<div><div class=\"costBookAssemblyStep2Header\">Products</div><table width=100% id=\"costBookAssemblyStep2HeaderTable\" class=\"items\"><tr><thead><th id="list-view_c2" style="width:20%; padding-left: 5px;"><a class=\"sort-link\ '+codeOrderClass+'" href=\"javascript:void(0);\" onClick=\"javascript:sortProducts(\''+productId+'\', \'code\', \''+nextSortOrder+'\');\">Product Code</a></th><th id="list-view_c3" style="width:25%; padding: 0px;"><a class=\"sort-link\ '+nameOrderClass+'" href=\"javascript:void(0);\" onClick=\"javascript:sortProducts(\''+productId+'\', \'name\', \''+nextSortOrder+'\');\">Product Name</a></th><th id="list-view_c3" style="width:15%; padding: 0px;">Ratio</th><th id="list-view_c4" style="width:20%; padding: 0px;">Base Unit of Measure</th><th id="list-view_c5" style="width:10%; padding: 0px;"><a class=\"sort-link\ '+unitOrderClass+'" href=\"javascript:void(0);\" onClick=\"javascript:sortProducts(\''+productId+'\', \'unit\', \''+nextSortOrder+'\');\">Unit of Measure</a></th><th id="list-view_c5" style="width:10%; padding: 0px;"></th></tr> </thead><tbody><tr><td colspan="7" style = "padding: 0px;"><div id="costbookAsemStep"><table>';
                if(data != undefined && data != null && total_rows > 0){
                    for(var i =0; i < data.length;i++) {
                        if($('#currAssemblyCnt').val() != undefined ) {
                            var j = (parseInt(i)+parseInt($('#currAssemblyCnt').val()));
                        } else {
                            var j = i;
                        }
                        appendText += '<tr id=row_'+j+'><td style=\"width:20%;padding-left: 5px;\">'+data[i].productcode+'</td><td style=\"width:25%\">'+data[i].productname+'</td><td style=\"width:10%\"><input type=\"text\" id=\"ratio'+j+'\" name=\"ratio\" value=\"0.0\" maxlength=\"10\" style=\"width:150px\" /></td><td style=\"width:20%\">'+data[i].UnitOfMeasure+'</td><td style=\"width:10%\">per '+$('#hidUOM').val()+'</td><td style=\"width:10%;\"><a href=\"#\" class=\"button-action\" onclick=\"addProductsInAssembly(\''+data[i].productcode+'\', \''+j+'\')\"><i class=\"icon-create\"></i><span class=\"button-label\">Add</span></a></td></tr>';
                    }
                    appendText += "</table></div></td></tr></tbody></table></table></div>";
                    $('#costbook_searchProducts').html(appendText);
                    j++;
                 }else {
                    appendText += '<tr><td colspan=\"7\" class=\"empty\"><span class=\"empty\"><span class=\"icon-empty\"></span>No results found</span></td></tr>';
                    $('#costbook_searchProducts').html(appendText);
                }   
            },
            error : function()
            {
                //todo: error call
            }
        });
    }
}

function sortProductNotification(msg){
    var content = '  <div class="ui-corner-all jnotify-item ui-state-highlight">  <div class="jnotify-item-close">  <span class="ui-icon ui-icon-circle-close"></span>  </div>  <span class="ui-icon ui-icon-info"></span>  <span>'+msg+'.</span>  </div>';
    $('#FlashMessageView').show();
    $('#FlashMessageView').html(content);
}