<?php
  
    /**
     * Render html to create route step2 view
     * @author Ramachandran.K<ramakavanan@gmail.com>
     */
    class RouteStep2View extends View    {
        private $routeId;
        private $isEdit;
        private $newClonedRouteId;

        /**
         * @param type $routeId value assigend route ID
         * @param type $edit value assigend based on Route Edit mode or Route Clone mode. If Edit mode, $edit value assigend as 'edit'. If CLONE, value assigend as 'clone' and If CREATE, value assigend as 'create'.
         * @param type $newClonedRouteId value assigend based on Route Edit mode or Route Clone mode. If Edit mode and Create mode, $edit value assigend as 0. If CLONE mode, value assigend as 'cloned route Id'.
         */
	public function __construct($routeId, $type, $ClonedRouteId) {
                $this->routeId = $routeId;
                $this->isEdit = $type;
                $this->newClonedRouteId = $ClonedRouteId;
	}
  
        public function renderContent()
        {     
            //$route = Route::getById($this->routeId);
            $content = $this->generateHTMLView();
            $content .= $this->renderScripts();
            return $content;
        }
        
        public function generateHTMLView(){
            return $this->generateHTMLStartView() . $this->generatePanelWrapperHTML() .
                    $this->generateFooterContentHTML() . $this->generateHTMLEndView();
            
        }        
        
        public function generateHTMLStartView() {
            $content = '<div class="SecuredEditAndDetailsView EditAndDetailsView DetailsView ModelView ConfigurableMetadataView MetadataView" id="RouteStep2EditAnDDetailView"><div class="wrapper"><h1><span class="truncated-title" threedots="Create Agreement"><span class="ellipsis-content">Create Route - (Step 2 of 4)</span></span></h1></div><div class="wide">';
            return $content;
        }
        
        public function generatePanelWrapperHTML() {
            $editvalue = 0;
            $selectedAgreementName = array();
            $selectedAgreement = array();
            $cat_arr = array();
            $agmt_ids_arr = array();
            $selectedAgreementList = '';
            $selectedAgreementListName = '';
            if($this->isEdit == 'edit' || $this->isEdit == 'clone'){
                $editvalue = 1;
                
                if($this->isEdit == 'clone')
                {
                    $agreementSelected = RouteAgreement::getAgmtByRouteId($this->newClonedRouteId);
                    if(count($agreementSelected))
                    {
                        $agreementSelected = $agreementSelected;
                    }
                    else
                    {
                        $agreementSelected = RouteAgreement::getAgmtByRouteId($this->routeId);
                    }
                }
                else if($this->isEdit == 'edit')
                    $agreementSelected = RouteAgreement::getAgmtByRouteId($this->routeId);
                
                if(isset($_SESSION['agreementList'])){
                    $selectedAgreement = $_SESSION['agreementList'];
                    $selectedAgreementName = $_SESSION['agreementNameList']; 
                    $selectedAgreementList = implode(',', $selectedAgreement); 
                    $selectedAgreementListName = implode(',', $selectedAgreementName); 
                }else{
                    foreach ($agreementSelected as $value) {
                        $selectedAgreement[] = $value->agreement->id;
                        $selectedAgreementName[$value->agreement->id] = $value->agreement->name;
                    }
                    $selectedAgreementList = implode(',', $selectedAgreement);      
                    $selectedAgreementListName = implode(',', $selectedAgreementName);   
                }
                if($this->isEdit == 'clone'){
                    $cats_arr = RouteCategory::getCatByRouteId($this->newClonedRouteId);   //For Cloned Route only
                }else{
                    $cats_arr = RouteCategory::getCatByRouteId($this->routeId);
                }
                
                foreach($cats_arr as $cat_arr)
                {
                    $arr_cat[] = $cat_arr->category->name;
                }
                $agmt_arr = AgreementProduct::getAgmtIdByCategory($arr_cat);
                foreach($agmt_arr as $agmt)
                {
                   $agmt_ids_arr[] = $agmt['agreement_id'];
                }
                
                if(count($agmt_ids_arr))
                    $agmt_ids_arr = $agmt_ids_arr;
                else
                    $agmt_ids_arr = array('0');
                
                $agmt = Agreement::getAllRecurringActiveAgmt(1, 15, '', $agmt_ids_arr);
            }else if($this->isEdit == 'create'){
                if(isset($_SESSION['agreementList'])){
                    $selectedAgreement = $_SESSION['agreementList'];
                    $selectedAgreementName = $_SESSION['agreementNameList']; 
                    $selectedAgreementList = implode(',', $selectedAgreement); 
                    $selectedAgreementListName = implode(',', $selectedAgreementName); 
                }
                $cats_arr = RouteCategory::getCatByRouteId($this->routeId);
                foreach($cats_arr as $cat_arr)
                {
                    $arr_cat[] = $cat_arr->category->name;
                }
                $agmt_arr = AgreementProduct::getAgmtIdByCategory($arr_cat);
                foreach($agmt_arr as $agmt)
                {
                   $agmt_ids_arr[] = $agmt['agreement_id'];
                }                
                if(count($agmt_ids_arr))
                    $agmt_ids_arr = $agmt_ids_arr;
                else
                    $agmt_ids_arr = array('0');
                
                $agmt = Agreement::getAllRecurringActiveAgmt(1, 15, '', $agmt_ids_arr);
            }
            $allActiveAgmCount = Agreement::getAllRecurringActiveAgmtCount($agmt_ids_arr);
            
            $pageOffset = 1;
            
            $content = '<div class="attributesContainer">
                            <div class="full-width">
                                <div class="panel">
                                    <input type="hidden" id="edit-route" name="edit-route" value="'. $editvalue.'">
                                    <div class="panelTitle">Selected Agreements</div>
                                    <table class="items" id="agreementNameList"> 
                                        <tbody><tr>';
                                    $number = 1;
                                    $key_value = 0 ;
                                    foreach ($selectedAgreementName  as $agreementId => $value) { 
                                            $content .='<td id="selected_agreement_'.$selectedAgreement[$key_value].'">'.$number.'. '.$value.'<input type="hidden" name="AgreementName[]" value="'.$value.'" id="AgreementName_'.$selectedAgreement[$key_value].'"></td>';
                                            if($number%3 ==0)
                                            {
                                                $content .='</tr><tr>';
                                            }$number++;$key_value++;
                                        
                                    }
                                    $content .='</td></tr></tbody> </table>
                                </div>
                                <div class="panel">
                                    <div class="panelTitle">Route Location Informations</div>
                                    <table class="items double-column">
                                        <colgroup>
                                            <col class="col-0">
                                            <col class="col-1">
                                            <col class="col-2">
                                        </colgroup>
                                        <tbody class="">
                                            <tr>
                                                <th>
                                                    <label for="costofgoodssoldassembly">Search Agreements</label>
                                                </th>
                                                <td colspan="2"><input type="text" style="" value="" name="agreement_name_value" id="agreement_name_value"></td>
                                                <td colspan="1">
                                                    <div style="">    
                                                        <a id="search" onclick="javascript:searchActiveAgreement(1);" class="attachLoading cancel-button" name="Search" href="#">
                                                            <span class="z-spinner"></span>
                                                            <span class="z-icon"></span>
                                                            <span class="z-label">Search</span>
                                                        </a>
                                                    </div>    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" id="list-view-selected-agreementIds" name="list-view-selected_agreementIds" value="'.$selectedAgreementList.'">
                                    <input type="hidden" id="list-view-selected_agreementName" name="list-view-selected_agreementName" value="'.$selectedAgreementListName.'">
                                    <input type="hidden" id="routeId" name="routeId" value="'.$this->routeId.'">
                                    <div id="result_div">
                                    <table class="items selected_products_table">
                                        <thead>
                                            <tr>
                                                <th class="checkbox-column">Select</th>
                                                <th id="list-view_c2">Id</th>
                                                <th id="list-view_c3">Agreement Name</th>
                                                <th id="list-view_c4">Account Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                            if(count($agmt))
                                            {
                                                foreach ($agmt as $agreementKey => $agreement) {
                                                $content .='<tr>
                                                    <td colspan="1">
                                                        <input type="hidden" name="AgreementTracking[total_non_agreement_selected_products]" value="0" id="ytAgreementTracking_total_non_agreement_selected_products">
                                                        <input type="hidden" name="AgreementName[]" value="'.$agreement->name.'" id="AgreementName_'.$agreement->id.'">';
                                                        if (in_array($agreement->id, $selectedAgreement) && isset($selectedAgreement)) {
                                                            $content .='<label class="hasCheckBox c_on"><input type="checkbox" value="' . $agreement->id . '" name="list-view-rowSelector[]" id="selected_agreement_'.$agreement->id.'" checked = "checked"></label>';
                                                        }else{
                                                            $content .='<label class="hasCheckBox"><input type="checkbox" value="' . $agreement->id . '" name="list-view-rowSelector[]" id="selected_agreement_'.$agreement->id.'">';
                                                        }
                                                    $content .='</td>
                                                    <td>' . $agreement->id . '</td>
                                                    <td>' . $agreement->name . '</td>
                                                    <td>' . $agreement->account->name . '</td>
                                                </tr>';  
                                                }
                                            }
                                            else
                                            {
                                                $content .='<tr><td colspan="7" class="empty"><span class="empty"><span class="icon-empty"></span>No results found</span></td></tr>';
                                            }
                                $content .='</tbody>   
                                    </table>
                                    <div>';
                                if(count($allActiveAgmCount) > 15){
                                $content .='<div class="pager vertical">
                                        <ul class="endless-list-pager" id="yw3">
                                          <li class="next">
                                            <a href="#" class="vertical-forward-pager" id="list-view-endless-page" onclick="javascript:searchActiveAgreement('.$pageOffset.');"><span>next</span>
                                            </a>
                                          </li>
                                        </ul>
                                    </div>';
                                }
                               $content .='</div>
                            </div>
                       </div>';
            return $content;
        }
        
        public function generateFooterContentHTML() {
            $content = '<div class="float-bar"><div class="view-toolbar-container clearfix dock"><div class="form-toolbar">';
            if((isset ($this->routeId)) && ($this->isEdit == 'edit' || $this->isEdit == 'create')){
                $content .='<a href="/app/index.php/routes/default/edit?id='.$this->routeId.'" class="attachLoading z-button cancel-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Back</span></a>';
            }
            else{
                $content .='<a href="/app/index.php/routes/default/copy?id='.$this->newClonedRouteId.'&type=edit_'.$this->routeId.'" class="attachLoading z-button cancel-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Back</span></a>';
            }
            $content .='<a href="/app/index.php/routes/default" class="cancel-button" id="CancelLinkActionElement--33-yt3"><span class="z-label">Cancel</span></a>
                        <a href="#" onclick="javascript:createRouteStep2('.$this->newClonedRouteId.');" class="attachLoading z-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Next</span></a></div></div></div>';
            return $content;
        }
        
        public function generateHTMLEndView() {
            $content = '</div></div>';
            return $content;
        }
        
        public function renderScripts() {
            Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                                Yii::getPathOfAlias('application.modules.routes.elements.assets')) . '/RouteTemplateUtils.js', CClientScript::POS_END);
           
            Yii::app()->clientScript->registerScript('HideNotification',
                '$("#FlashMessageView").hide();
            ');
        }      
    }
?>

