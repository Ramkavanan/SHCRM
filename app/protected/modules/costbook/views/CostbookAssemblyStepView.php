<?php

    class CostbookAssemblyStepView extends View    {
        protected $data;
        
	public function __construct($data) {
            $this->data = $data;
	}

	public function renderContent() {
            $categories = Category::getAll();
            $data = Costbook::getById($_GET['id']);
            $vAssemblyDetails = '';
            $assembly_count = 0;
            if($data->assemblydetail != '' && $data->assemblydetailsearch == '(None)' ) {
                $vAssemblyDetails = explode(';', $data->assemblydetail);
            }
            else if($data->assemblydetail == '' && $data->assemblydetailsearch != '(None)' ) {
                $vAssemblyDetails = explode(', ', $data->assemblydetailsearch);
            } else if($data->assemblydetail != '' && $data->assemblydetailsearch != '(None)' ) {
                $assembly_details = explode(';', $data->assemblydetail);
                $vAssemblySearchDetails = explode(', ', $data->assemblydetailsearch);
                $vAssemblyDetails = array_unique(array_merge($assembly_details, $vAssemblySearchDetails));
            }
            
           if(is_array($vAssemblyDetails)) {
               $vAssemblyDetails = array_filter($vAssemblyDetails);
           }

            $assembly_count = count($vAssemblyDetails);

            $url = Yii::app()->createUrl("costbook/default/getDataAssemblySearch");

            $content = '<div class="SecuredEditAndDetailsView EditAndDetailsView DetailsView ModelView ConfigurableMetadataView MetadataView" id="CostbookEditAndDetailsView">
                            <div class="wrapper">
                                <h1>
                                    <span class="truncated-title" threedots="Create Costbook"><span class="ellipsis-content">Step 2 of 3 - Assembly Detail</span></span>
                                </h1>
                                <div class="wide form">
                                <form method="post" action="/app/index.php/costbook/default/create?clearCache=1&amp;resolveCustomData=1" id="edit-form" onsubmit="js:return $(this).attachLoadingOnSubmit(&quot;edit-form&quot;)">
                                    <input type="hidden" id="hidModelId" name="hidModelId" value="'.$_GET['id'].'" />
                                    <input type="hidden" name="hidUOM" id="hidUOM" value="'.$data->unitofmeasure.'" />
                                        <div class="attributesContainer">
                                            <div class="left-column" style="width:100%;">
                                                <div class="border_top_In_Assembly_Detail_Level">
                                                    <div class="costBookAssemblyStep2Header" >Detail Products</div>
                                                        <table width=100% class="items" id="detail_products">
                                                            <th width=15%>Product Code</th><th width=30%>Product Name</th><th width=15%>Ratio</th><th width=20%>Base Unit of Measure</th><th width=20%>Unit of Measure</th>';
                                        if($vAssemblyDetails != '' ) {
                                            for($i=0; $i< $assembly_count; $i++) {
                                                $str = explode('|', $vAssemblyDetails[$i]);
                                                $dataProductCode = Costbook::getByProductCode($str[1]);
                                                if(!isset($dataProductCode[0]))
                                                    continue;
                                                $content .= '<tr>
                                                                <td >'.$str[1].'<input type="hidden" id="hidProductCode'.$i.'" value="'.$str[1].'" /></td>
                                                                <td >'.$dataProductCode[0]->productname.'</td>
                                                                <td ><input type="text" id="detail_ratio'.$i.'" name="detailRatio" value="'.$str[2].'" style="width:120px;" /></td>
                                                                <td >'.$dataProductCode[0]->unitofmeasure.'</td>
                                                                <td >per '.$data->unitofmeasure.'<input type="hidden" name="currAssemblyCnt" id="currAssemblyCnt" value="'.$assembly_count.'" /></td>
                                                            </tr>';
                                            }
                                        }
                                            $content .= '</table><br>
                                                    </div>
                                                    <div class="panel border_top_In_Assembly_Detail_Level">
                                                        <div class="costBookAssemblyStep2Header">Search</div>
                                                            <table class="form-fields items" id="costBookAssemblyStep2Headerid">
                                                                <colgroup><col class="col-0"><col class="col-1"></colgroup>
                                                                <tbody>
                                                                    <tr>
                                                                        <th><label for="Costbook_assemblycategory_value">Select Category</label></th>
                                                                        <td colspan="1">
                                                                            <div class="hasDropDown">
                                                                                <span class="select-arrow"></span>
                                                                                    <select id="Costbook_assemblycategory_value" name="Costbook[assemblycategory][value]">
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
                                                                                <select id="Costbook_costofgoodssoldassembly_value" name="Costbook[costofgoodssoldassembly][value][assemblycategory][value]">
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
                                                                        <td colspan="1"><input type="text" id="Costbook_productname_value" name="productname" value="" style="" /></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td colspan="1">
                                                                            <a id="saveyt2" class="attachLoading cancel-button" name="Search" href="#"  style="margin-left:21.7%;">
                                                                                <span class="z-spinner"></span>
                                                                                <span class="z-icon"></span>
                                                                                <span class="z-label">Search</span>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>';
                                            $costBookId = $_GET['id']; 
                                            $content .='<div class="items-wrapper" id="addProductWrapper">
                                                           <div id="costbook_searchProducts" style="margin-bottom:2%; display : none;">
                                                                <div id="search_title" style="background-color:#E0D1D1;  color:black; padding:0.5%; font-weight:bold;"> Choose Products </div>
                                                                <table class="items selected_products_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width:20%; padding-left: 5px;padding-right: 0px;" id="list-view_c1">  <a  class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$costBookId.', \'code\', \'asc\');">  Product Code </a> </th>
                                                                            <th style="width:25%; padding: 0px;" id="list-view_c2">  <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$costBookId.', \'name\', \'asc\');">Product Name</a> </th>
                                                                            <th style="width:15%; padding: 0px;" id="list-view_c3"> Ratio </th>
                                                                            <th style="width:20%; padding: 0px;" id="list-view_c4"> Base Unit of Measure </th>
                                                                            <th style="width:10%; padding: 0px;" id="list-view_c5"> <a class="sort-link" href="javascript:void(0);" onClick="javascript:sortProducts('.$costBookId.', \'unit\', \'asc\');">Unit of Measure</a> </th>
                                                                            <th style="width:10%; padding: 0px;" id="list-view_c5"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="8" style = "padding: 0px;">
                                                                                <div id="costbook_scroll">
                                                                                    <table>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>   
                                                            </div>

                                                        <div id="result_div"></div>
                                                    </div>
                                                    <div class="float-bar">
                                                        <div class="view-toolbar-container clearfix dock">
                                                            <div class="form-toolbar">
                                                                <a href="#" class="cancel-button" id="GobackLinkActionElement2" onclick="window.location.href = \'/app/index.php/costbook/default/editAssembly?id='.$_GET['id'].'\';"><span class="z-label">Go Back</span></a>
                                                                <a href="#" class="cancel-button" name="Cancel" id="CancelLinkActionElement2" ><span class="z-label">Cancel</span></a>
                                                                <a href="#" class="save-button" name="save" id="saveyt3" onClick="javascript:saveAssemblyStep2();"><span class="z-label">Save and View Summary</span></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            <div id="modalContainer-edit-form">
                                            </div>
                                        </div>
                                    </div>
                                </div>';
            $content .= $this->renderScripts();
            $this->registerCopyAssemblySearchDataScript();
            return $content;
        }

        protected function registerCopyAssemblySearchDataScript() {
            $url            = Yii::app()->createUrl('costbook/default/getAssemblySearchData');
            $cancelurl      = Yii::app()->createUrl('costbook/default/cancelAssemblyStep2');
            // Begin Not Coding Standard
            Yii::app()->clientScript->registerScript('copyAssemblySearchDataScript', "
                $('#saveyt2').click(function()
                    {
                        var productName = $.trim($('#Costbook_productname_value').val());
                        addProductId = $('#hidModelId').val();
                        $.ajax(
                        {
                            url : '" . $url . "?category=' + $('#Costbook_assemblycategory_value').val()+'&costOfGoods='+ $('#Costbook_costofgoodssoldassembly_value').val()+'&productId='+ $('#hidModelId').val()+'&productName='+ $('#Costbook_productname_value').val(),
                            type : 'GET',
                            dataType: 'json',
                            success : function(data)
                            {
                                var total_rows = data.length;
                                $('#costbook_searchProducts').css('display','block');
                                var prod_name_search_str = $('#Costbook_productname_value').val();
                                $('#Costbook_productname_value').val($.trim(prod_name_search_str));

                                var appendText ='';
                                appendText += '<table class = \"items selected_products_table\">';
                                if(data != undefined && data != null && total_rows > 0){
                                    for(var i =0; i < data.length;i++) {
                                        if($('#currAssemblyCnt').val() != undefined ) {
                                            var j = (parseInt(i)+parseInt($('#currAssemblyCnt').val()));
                                        } else {
                                            var j = i;
                                        }
                                        appendText += '<tr id=row_'+j+'><td style=\"width:19%; padding-left: 5px;\">'+data[i].productcode+'</td><td style=\"width:25%; padding-left: 11px;\">'+data[i].productname+'</td><td style=\"width:15%\"><input type=\"text\" id=\"ratio'+j+'\" name=\"ratio\" value=\"0.0\" maxlength=\"10\" style=\"width:150px\" /></td><td style=\"width:20%\">'+data[i].UnitOfMeasure+'</td><td style=\"width:10%\">per '+$('#hidUOM').val()+'</td><td style=\"width:10%;\"><a href=\"#\" class=\"button-action\" onclick=\"addProductsInAssembly(\''+data[i].productcode+'\', \''+j+'\')\"><i class=\"icon-create\"></i><span class=\"button-label\">Add</span></a></td></tr>';
                                    }
                                    appendText += '</table></div>';
                                    $('#costbook_scroll').html(appendText);
                                    j++;
                                 }else {
                                    appendText += '<span class=\"empty\"><span class=\"icon-empty\"></span>No results found</span>';
                                    $('#costbook_scroll').html(appendText);
                                }   
                            },
                            error : function()
                            {
                                //todo: error call
                            }
                        }
                        );
                    }
                );                

                $('#Costbook_productname_value').keyup(function()
                    {
                        var productName = $.trim($('#Costbook_productname_value').val());
                        if(productName.length == 0 || productName.length >= 3){
                            addProductId = $('#hidModelId').val();
                            $.ajax(
                            {
                                url : '" . $url . "?category=' + $('#Costbook_assemblycategory_value').val()+'&costOfGoods='+ $('#Costbook_costofgoodssoldassembly_value').val()+'&productId='+ $('#hidModelId').val()+'&productName='+ $('#Costbook_productname_value').val(),
                                type : 'GET',
                                dataType: 'json',
                                success : function(data)
                                {
                                    var total_rows = data.length;
                                    //console.log($('#currAssemblyCnt').val());
                                    $('#costbook_searchProducts').css('display','block');
                                    var prod_name_search_str = $('#Costbook_productname_value').val();
                                    $('#Costbook_productname_value').val($.trim(prod_name_search_str));

                                    var appendText ='';
                                    appendText += '<table class = \"items selected_products_table\">';
                                    if(data != undefined && data != null && total_rows > 0){
                                        for(var i =0; i < data.length;i++) {
                                            if($('#currAssemblyCnt').val() != undefined ) {
                                                var j = (parseInt(i)+parseInt($('#currAssemblyCnt').val()));
                                            } else {
                                                var j = i;
                                            }
                                            appendText += '<tr id=row_'+j+'><td style=\"width:19%;padding-left: 5px;\">'+data[i].productcode+'</td><td style=\"width:25%; padding-left: 11px;\">'+data[i].productname+'</td><td style=\"width:15%\"><input type=\"text\" id=\"ratio'+j+'\" name=\"ratio\" value=\"0.0\" maxlength=\"10\" style=\"width:150px\" /></td><td style=\"width:20%\">'+data[i].UnitOfMeasure+'</td><td style=\"width:10%\">per '+$('#hidUOM').val()+'</td><td style=\"width:10%;\"><a href=\"#\" class=\"button-action\" onclick=\"addProductsInAssembly(\''+data[i].productcode+'\', \''+j+'\')\"><i class=\"icon-create\"></i><span class=\"button-label\">Add</span></a></td></tr>';
                                        }
                                        appendText += '</table></div>';
                                        $('#costbook_scroll').html(appendText);
                                        j++;
                                     }else {
                                        appendText += '<span class=\"empty\"><span class=\"icon-empty\"></span>No results found</span>';
                                        $('#costbook_scroll').html(appendText);
                                    }   
                                },
                                error : function()
                                {
                                    //todo: error call
                                }
                            }
                            );
                        }
                    }
                );
                
                $('#CancelLinkActionElement2').click(function()
                    {
                        if (confirm('Are you sure want to Cancel?')) { 
                            $.ajax(
                            {
                                url : '" . $cancelurl . "?id='+ $('#hidModelId').val(),
                                type : 'GET',
                                dataType: 'json',
                                success : function(data)
                                {
                                    if(data == 1) {
                                        window.location.href = '/app/index.php/costbook/default/';
                                    }
                                },
                                error : function()
                                {
                                    //todo: error call
                                }
                            }
                            );
                        }

                    }
                );


            ");
        // End Not Coding Standard
        }

	protected function renderScripts() {
            Yii::app()->clientScript->registerScriptFile(
            Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('application.modules.costbook.elements.assets')) . '/CostbookAssemblySearchTemplateUtils.js');
        }

    }

    
?>
