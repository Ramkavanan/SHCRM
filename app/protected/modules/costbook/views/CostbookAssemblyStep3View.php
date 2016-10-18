<?php

    class CostbookAssemblyStep3View extends View    {
        protected $data;
        
	public function __construct($data) {
            $this->data = $data;
	}
        
	public function renderContent() {
            $categories = Category::getAll();
            $data = Costbook::getById($_GET['id']);
            $vAssemblyDetails = '';
            $vCategories = '';
            $vAssemblyDetails = explode(';', $data->assemblydetail);
            $vCategories =  explode(',', $data->category);

            $mHTotal = 0;
            $laborTotal = 0;
            $equipmentTotal = 0;
            $materailTotal = 0;
            $subcontractorTotal = 0;
            $othersTotal = 0;

            for($i=0; $i< count($vAssemblyDetails); $i++) {
               $str = explode('|', $vAssemblyDetails[$i]);
               $dataProductCode = Costbook::getByProductCode($str[1]);
               if($dataProductCode[0]->costofgoodssold->value == 'Labor') {
                   $laborCost = ($dataProductCode[0]->departmentreference->laborCost+$dataProductCode[0]->departmentreference->burdonCost);
                   $laborTotal +=  $laborCost * $str[2];
                   $mHTotal +=  $str[2];
               } else { $laborCost = 0; }
               if($dataProductCode[0]->costofgoodssold->value == 'Equipment') {
                   $equipmentCost = $dataProductCode[0]->costperunit;
                   $equipmentTotal +=  $equipmentCost * $str[2];
               } else { $equipmentCost = 0; }
               if($dataProductCode[0]->costofgoodssold->value == 'Material') {
                   $materailCost = $dataProductCode[0]->costperunit;
                   $materailTotal +=  $materailCost * $str[2];
               } else { $materailCost = 0; }
               if($dataProductCode[0]->costofgoodssold->value == 'Subcontractor') {
                   $subcontractorCost = $dataProductCode[0]->costperunit;
                   $subcontractorTotal +=  $subcontractorCost * $str[2];
               } else { $subcontractorCost = 0; }
               if($dataProductCode[0]->costofgoodssold->value == 'Other') {
                   $otherCost = $dataProductCode[0]->costperunit;
                   $othersTotal += $otherCost * $str[2];
               } else { $otherCost = 0; }
            }
            $assemblyTotalDirectCost = $laborTotal+$materailTotal+$equipmentTotal+$subcontractorTotal+$othersTotal;
            $url = Yii::app()->createUrl("costbook/default/getDataAssemblySearch");

            $content = '';
            $content .= '<div class="SecuredEditAndDetailsView EditAndDetailsView DetailsView ModelView ConfigurableMetadataView MetadataView" id="CostbookEditAndDetailsView">
                            <div class="wrapper">
                                <h1>
                                    <span class="truncated-title" threedots="Create Costbook">
                                        <span class="ellipsis-content">Step3 of 3 - Assembly Summary</span>
                                    </span>
                                </h1>
                                <div class="wide form">
                                <form method="post" action="/app/index.php/costbook/default/create?clearCache=1&amp;resolveCustomData=1" id="edit-form" onsubmit="js:return $(this).attachLoadingOnSubmit(&quot;edit-form&quot;)">
                                    <input type="hidden" id="hidModelId" name="hidModelId" value="'.$_GET['id'].'" />
                                    <div class="attributesContainer">
                                        <div class="panel border_top_In_Assembly_Detail_Level">
                                            <div class="costBookAssemblyStep2Header">
                                                      Assembly Product (Master)        
                                            </div>    
                                            <table class="items">
                                                <tr>
                                                    <th >Product Code</th>
                                                    <th >Product Name</th>
                                                    <th >Unit of Measure</th>
                                                    <th >MH</th>
                                                    <th >L+OH</th>
                                                    <th >M</th>
                                                    <th >E</th>
                                                    <th >S</th>
                                                    <th >O</th>
                                                    <th >Total Direct Cost</th>
                                                </tr>
                                                <tr>
                                                    <td >'.$data->productcode.'</td>
                                                    <td >'.$data->productname.'</td>
                                                    <td >'.$data->unitofmeasure.'</td>
                                                    <td >'.$mHTotal.'</td>
                                                    <td >$'.round($laborTotal,2).'</td>
                                                    <td >$'.round($materailTotal,2).'</td>
                                                    <td >$'.round($equipmentTotal,2).'</td>
                                                    <td >$'.round($subcontractorTotal,2).'</td>
                                                    <td >$'.round($othersTotal,2).'</td>
                                                    <td>$'.round($assemblyTotalDirectCost,2).'</td>                     
                                                </tr>
                                            </table>
                                        </div>    
                                        <div class="panel border_top_In_Assembly_Detail_Level">
                                            <div class="costBookAssemblyStep2Header">    
                                                Assembly Product (Detail)        
                                            </div>    
                                            <table class="items">
                                                <tr>
                                                    <th >Product Code</th>
                                                    <th >Product Name</th>
                                                    <th >Ratio</th>
                                                    <th >Base Unit of Measure</th>
                                                    <th >Unit of Messure</th>
                                                    <th >MH</th>
                                                    <th >L+OH</th>
                                                    <th >M</th>
                                                    <th >E</th>
                                                    <th >S</th>
                                                    <th >O</th>
                                                </tr>';
                           for($i=0; $i< count($vAssemblyDetails); $i++) {
                               $str = explode('|', $vAssemblyDetails[$i]);
                               $productCode = $str[1];
                               $ratio = $str[2];
                               $dataProductCode = Costbook::getByProductCode($str[1]);
                               if($dataProductCode[0]->costofgoodssold->value == 'Labor') {
                                   $laborCost = ($dataProductCode[0]->departmentreference->laborCost+$dataProductCode[0]->departmentreference->burdonCost) * $str[2];
                                   $MH = $str[2];
                                   $laborTotal +=  $laborCost;
                               } else { $laborCost = 0; $MH = 0; }
                               if($dataProductCode[0]->costofgoodssold->value == 'Equipment') {
                                   $equipmentCost = $dataProductCode[0]->costperunit * $str[2];
                                   $equipmentTotal +=  $equipmentCost;
                               } else { $equipmentCost = 0; }
                               if($dataProductCode[0]->costofgoodssold->value == 'Material') {
                                   $materailCost = $dataProductCode[0]->costperunit * $str[2];
                                   $materailTotal +=  $materailCost;
                               } else { $materailCost = 0; }
                               if($dataProductCode[0]->costofgoodssold->value == 'Subcontractor') {
                                   $subcontractorCost = $dataProductCode[0]->costperunit * $str[2];
                                   $subcontractorTotal +=  $subcontractorCost;
                               } else { $subcontractorCost = 0; }
                               if($dataProductCode[0]->costofgoodssold->value == 'Other') {
                                   $otherCost = $dataProductCode[0]->costperunit * $str[2];
                                   $othersTotal += $otherCost;
                               } else { $otherCost = 0; }
                               
                            //To check Assembly when Assembly products Added inside the Assembly  
                               if($dataProductCode[0]->costofgoodssold->value == 'Assembly') {
                                   $ratio = '';
                               }
                                               $content .= '<tr>
                                                                <td >'.$productCode.'</td>
                                                                <td >'.$dataProductCode[0]->productname.'</td>
                                                                <td >'.$ratio.'</td>
                                                                <td >'.$dataProductCode[0]->unitofmeasure.'</td>
                                                                <td >'.$dataProductCode[0]->unitofmeasure.'</td>
                                                                <td >'.$MH.'</td>
                                                                <td >$'.round($laborCost,2).'</td>
                                                                <td >$'.round($materailCost,2).'</td>
                                                                <td >$'.round($equipmentCost,2).'</td>
                                                                <td >$'.round($subcontractorCost,2).'</td>
                                                                <td >$'.round($otherCost,2).'</td>                   
                                                            </tr>';
                           }
                                            $content .= '</table>
                                                    </div>
                                           <div class="panel border_top_In_Assembly_Detail_Level">
                                                <div class="costBookAssemblyStep2Header">    
                                                    Assembly Information (Master)        
                                                </div>            
                                            <div class="panel border_top_In_Assembly_Detail_Level" style="width:90%; margin: 3% 0% 1% 5%;">
                                                <div class="costBookAssemblyStep2Header">
                                                    Categories
                                                </div>
                                                <div>
                                                <table class="items">
                                                    <tr>
                                                        <th>Category</th>
                                                    </tr>';
                                   for($k=0; $k < count($vCategories); $k++ ) {
                                                            $content .= '<tr>
                                                                            <td>'.$vCategories[$k].'</td>
                                                                        </tr>';
                                    }
                                                        $content .= '</table>
                                                                </div>
                                                                </div>
                                                                <div>
                                                                    <table width="40%" class="items">
                                                                        <tr>
                                                                            <td style="width:50%; align="center">Description</td><td>'.$data->description.'</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width:50%; align="center"">Scope of Work</td><td>'.$data->scopeofwork.'</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width:50%; align="center"">Proposal Text</td><td>'.$data->proposaltext.'</td>
                                                                        </tr>
                                                                    </table>
                                                                 </div>       
                                                    </div>
                                        <div class="float-bar">
                                            <div class="view-toolbar-container clearfix dock">
                                                <div class="form-toolbar">
                                                    <a href="#" class="cancel-button" id="GobackLinkActionElement2" onclick="window.location.href = \'/app/index.php/costbook/default/assemblyStep2?id='.$_GET['id'].'\';"><span class="z-label">Go Back</span></a>
                                                <!--    <a href="#" class="cancel-button" name="Cancel" id="CancelLinkActionElement2" ><span class="z-label">Cancel</span></a> -->
                                                    <a href="#" class="attachLoading z-button" name="save" id="saveyt3" onClick="location.href = \'/app/index.php/costbook/default/details?id='.$_GET['id'].'&from_finish=1\';"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Finish</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div id="modalContainer-edit-form"></div>
                                </div>
                            </div>
                        </div>';
            $content .= $this->renderScripts();
            $this->registerCopyAssemblySearchDataScript();
            return $content;
        }

        protected function registerCopyAssemblySearchDataScript() {
            $cancelurl      = Yii::app()->createUrl('costbook/default/cancelAssemblyStep2');
            // Begin Not Coding Standard
            Yii::app()->clientScript->registerScript('copyAssemblySearchDataScript', "
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
