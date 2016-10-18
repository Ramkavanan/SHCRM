<?php
  
    /**
     * Render html to create route step2 view
     * @author Ramachandran.K<ramakavanan@gmail.com>
     */
    class RouteStep4View extends View    {
        private $routeId;
        private $newClonedRouteId;

	public function __construct($routeId, $ClonedRouteId, $type) {
            $this->routeId = $routeId;
            $this->newClonedRouteId = $ClonedRouteId;
            $this->isEdit = $type;	
	}
  
        public function renderContent()
        {    
            $content = $this->generateHTMLView();
            $content .= $this->renderScripts();
            return $content;
        }
        
        public function generateHTMLView(){
            return $this->generateHTMLStartView() . $this->generatePanelWrapperHTML() .
                    $this->generateFooterContentHTML() . $this->generateHTMLEndView();
            
        }        
        
        public function generateHTMLStartView() {
            $content = '<div class="SecuredEditAndDetailsView EditAndDetailsView DetailsView ModelView ConfigurableMetadataView MetadataView" id="RouteStep2EditAnDDetailView"><div class="wrapper"><h1><span class="truncated-title" threedots="Create Agreement"><span class="ellipsis-content">Create Route - (Step 4 of 4)</span></span></h1></div><div class="wide">';
            return $content;
        }
        
        public function generatePanelWrapperHTML() {
            $editvalue = 0;
            if(isset($isEdit)){
                $editvalue = 'edit';
            }
            if($this->isEdit == 'clone'){
                $agmt   = RouteAgreement::getAgmtByRouteId($this->newClonedRouteId);
            }else{
                $agmt   = RouteAgreement::getAgmtByRouteId($this->routeId);                
            }
            $agmt_id_arr = '';
            $countOfSelectedRow = 0;
            $CategoryKeyCount = 0;
            $i =1;            
//            $editvalue = 0;
//            if(isset($isEdit)){
//                $editvalue = 1;
//            }
            
            if($this->isEdit == 'edit' || $this->isEdit == 'clone'){                
                $routeProducts = RouteProducts::getRouteProductsByRouteId($this->routeId);
                foreach ($routeProducts as $value) {
                    $selectedProducts[$value->agreementproduct->id] = $value->agreementproduct->id;
                }
            }
            
            $content = '<div class="attributesContainer">
                            <div>
                                <div class="panel details-table">
                                    <div class="panelTitle">Route Location Informations</div>
                                    <table class="form-fields">                                
                                        <tbody><tr>';
                                foreach ($agmt as $agreementKey => $agreement) {
                                    $content .='<td>' .$i.'. '. $agreement->agreement->name. '</td>';

                                    if($i%3 ==0)
                                    {
                                        $content .='</tr><tr>';
                                    }                                    
                                    $i++;   
                                 }                                         
                                 $content .='</td></tr></tbody>
                                     </table>                                 
                                </div>
                            </div>
                       </div>';
            
            
                                 
                                $agreementcount =0;
                                foreach ($agmt as $agreementKey => $agreement) {
                                    $agmt_id_arrcount[] = $agreement->agreement->id;                                    
                                    $agreementcount++;
                                } 
                                //each agreement we assign take , 10% width
                                $totalcolumnsneed=$agreementcount;
                                $siglewidth=60/$totalcolumnsneed;
                                
                                
                                $siglewidthper=$siglewidth.'%';
                                
            
            $content .='<div class="panel">
                            <input type="hidden" id="edit-route" name="edit-route" value="'. $this->isEdit.'">                                
                             <div class="selectall_div">   
                                Select All  <input type="checkbox" onClick="toggle(this)" class="selectall" />
                            </div>
                            <table class="form-fields items">                                
                                <tbody>
                                    <tr>
                                        <td colspan="19" class="route_value_rows" ><div class="route_header_div"><table class="items route_header_rows">
                                            <tr>
                                            <th class="product_code_col" style="width:10%;">Product Code</th>
                                            <th style="width:10%;">Assembly</th>
                                            <th style="width:10%;">Product</th>
                                            <th style="width:10%;">UOM</th>';
                                            $j =1;
                                            foreach ($agmt as $agreementKey => $agreement) {
                                                $agmt_id_arr[] = $agreement->agreement->id;
                                                //$content .='<th style="line-height: 18px; white-space: normal;" width="100px;">' .  $agreement->agreement->name . '</th>';
                                                $content .='<th style="line-height: 18px; white-space: normal; cursor: pointer;width:'.$siglewidthper.';" title="'.$agreement->agreement->name.'">'.$j.'</th>';
                                                $j++;
                                            } 

                                        $content .='</tr></table></div></td></tr><tr><td colspan="19" class="route_value_rows" ><div class="ScrollStyle"><table class="items route_header_rows">';

                                    $agmtProds   = AgreementProduct::getAgmtProdByAgmtIdInRoute($agmt_id_arr);
                                    $agreementPdctMap = array();
                                    $agreementPdctIds = array();
                                    foreach ($agmtProds as $agmtProd) {
                                        //$agreementPdctIds[$agmtProd->Category][$agmtProd->costbook->id]['present_agmt_ids'][] = $agmtProd->agreement->id;
                                        $agreementPdctIds[$agmtProd->Category][$agmtProd->costbook->productcode.'-'.$agmtProd->Assembly_Product_Code]['present_agmt_ids'][] = $agmtProd->agreement->id;
                                        $agreementPdctMap[$agmtProd->Category][$agmtProd->costbook->productcode.'-'.$agmtProd->Assembly_Product_Code] = $agmtProd;
                                    }
                                    $column_count = 4+count($agmt_id_arr);
                                    foreach ($agreementPdctMap as $CategoryKey => $agreementArray) {
                    $content .='<tr>
                                    <th colspan="'.$column_count.'" class="align_left" style="background-color:gray; color:white;">' . $CategoryKey . ' </th>
                                </tr>';
                    /*$cost_book_id_arr = array();
                    $assembly_code_arr = array();
                    $agmt_prod_code_arr = array();
                    $asmbly_code_exist = 1;
                    $product_code_exist = 1; */
                    foreach ($agreementArray as $agreementKey => $agreementpdt) {                        
                       /* if(in_array($agreementpdt->costbook->id, $cost_book_id_arr))
                        {   
                            if(!empty($agreementpdt->Assembly_Product_Code))
                            {
                                if(in_array($agreementpdt->Assembly_Product_Code, $assembly_code_arr))
                                {
                                   $asmbly_code_exist = 2;
                                   continue;
                                }
                            }                            
                            else if(in_array($agreementpdt->costbook->productcode, $agmt_prod_code_arr))
                            {
                                $product_code_exist = 2;
                                continue;
                            }                                                       
                        }                        
                        $cost_book_id_arr[$agreementpdt->costbook->id] = $agreementpdt->costbook->id;
                        
                        if(!empty($agreementpdt->Assembly_Product_Code))
                            $assembly_code_arr[$agreementpdt->Assembly_Product_Code] = $agreementpdt->Assembly_Product_Code;
                        else
                            $agmt_prod_code_arr[$agreementpdt->costbook->productcode] = $agreementpdt->costbook->productcode;
                        */
                        $agreementProduct = 1;                        
                        $content .='<tr>
                                        <td style="width:10%;">' .$agreementpdt->costbook->productcode . '
                                            <input value=' . $agreementpdt->costbook->id . ' name="productId" id="productId_' . $countOfSelectedRow . '" type="hidden">
                                            <input value=' . $agreementpdt->id . ' name="agreement_product_id" id="agreement_product_id_' . $countOfSelectedRow . '" type="hidden">
                                            <input value=' . $agreementpdt->id . ' name="agreement_product_id" id="agreement_category_product_' . $CategoryKeyCount . '" type="hidden">
                                        </td>
                                        <td style="width:10%;">' . $agreementpdt->Assembly_Product_Code . '</td>
                                        <td style="width:10%;">' . $agreementpdt->name . '</td>
                                        <td style="width:10%;">' . $agreementpdt->costbook->unitofmeasure . '</td>';
                                        foreach ($agmt_id_arr as $agmt_id) {
                                            if(in_array($agmt_id, $agreementPdctIds[$CategoryKey][$agreementpdt->costbook->productcode.'-'.$agreementpdt->Assembly_Product_Code]['present_agmt_ids']))
                                            {
                                                $content .='<td style="width:'.$siglewidthper.';"> ';
                                                
                                                //To get the Agmtprod Id based on the agmt & costbook Id
                                                $getAgmtProdId   = AgreementProduct::getAgmtProdIdByAgmtIdCostBookId($agmt_id, $agreementpdt->costbook->id, $agreementpdt->costbook->productcode, $agreementpdt->Assembly_Product_Code, $agreementpdt->Category);

                                                if(isset($selectedProducts[$getAgmtProdId->id]))
                                                    $checked = 'checked=checked';
                                                else 
                                                   $checked = '';

                                                $content .='<input type="checkbox" class="product_checkbox" name="route_prod" id="route_prod_' . $CategoryKeyCount . '" value="'.$getAgmtProdId->id.'~'.$agmt_id.'~'.$agreementpdt->costbook->id.'" '.$checked.'></td>';
                                            }
                                            else
                                            {
                                                $content .='<td style="width:'.$siglewidthper.';"></td>';
                                            }
                                        }

                                    $content .=' </tr>';
                        $countOfSelectedRow++;
                    }
                                     
                    $CategoryKeyCount++;
                }
                                   
                                $content .= '</td></tr></tbody></table></div>
                                    

                            </tbody>
                            </table>
                     </div>
                     <div id="result_div"></div>';
            return $content;
        }
        
        public function generateFooterContentHTML() {
            $content = '<div class="float-bar"><div class="view-toolbar-container clearfix dock"><div class="form-toolbar">';
            if(!empty ($this->routeId) && empty($this->newClonedRouteId)){
                $content .='<a href="/app/index.php/routes/default/createStep3?id='.$this->routeId.'&ClonedRouteId='.$this->newClonedRouteId.'&type='.$this->isEdit.'" class="cancel-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Back</span></a>';
            }else{
                $content .='<a href="/app/index.php/routes/default/createStep3?id='.$this->routeId.'&ClonedRouteId='.$this->newClonedRouteId.'&type='.$this->isEdit.'" class="cancel-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Back</span></a>';
            }
            $content .='<a href="/app/index.php/routes/default" class="cancel-button" id="CancelLinkActionElement--33-yt3"><span class="z-label">Cancel</span></a>
                        <a href="#" onclick="javascript:createRouteAgreementProducts(\''.$this->routeId.'\','.$this->newClonedRouteId.');" class="attachLoading z-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Finish</span></a></div></div></div>';
            return $content;
        }
        
        public function generateHTMLEndView() {
            $content = '</div></div>';
            return $content;
        }
        
        public function renderScripts() {
            Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                            Yii::getPathOfAlias('application.modules.routes.elements.assets')) . '/RouteTemplateUtils.js', CClientScript::POS_END);
        }      
    }
?>

