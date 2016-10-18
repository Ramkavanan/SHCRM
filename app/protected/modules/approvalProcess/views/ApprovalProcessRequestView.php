<?php

    class ApprovalProcessRequestView extends View    {
        private $optId;
        private $agmntId;
        private $apId;

	public function __construct($optId = null, $agmntId = null ,$apId) {
            $this-> optId = $optId;
            $this-> agmntId = $agmntId;
            $this-> apId = $apId;
	}
  
        public function renderContent()
        {                 
            $content = $this->generateHTMLView();
            $content .= $this->renderScripts();
            return $content;
        }
        
        public function generateHTMLView(){
            return  $this->generatePanelWrapperHTML() . $this->generateHTMLEndView();            
        }                  
        
        
        /**
         * Used For Both Agreement and Opportunity Request Process
         * 
         */
        public function generatePanelWrapperHTML() {
            $approvalProcess = ApprovalProcess::getById($this->apId);
            if(!empty($this->optId)){
                $opportunity = Opportunity::getById($this->optId);
            }else{
                $agreement = Agreement::getById($this->agmntId);
            }

            $content = '<div class="wrapper">
                        <h1><span class="truncated-title" threedots="Approve/Reject Approval Request"><span class="ellipsis-content">Approve/Reject Approval Request</span></span></h1>
                            <div class="wide double-column form"> 
                                <form id="edit-form" method="post" action="" onsubmit="">
                                    <div class="left-column full-width">
                                        <div class="panel">
                                            <table class="form-fields double-column">
                                                <colgroup>
                                                <col class="col-0">
                                                <col class="col-1">
                                                <col class="col-2">
                                                <col class="col-3">
                                                </colgroup>
                                                <tbody>';

            if(!empty($this->optId)){
                $opp_redirect_url = Yii::app()->getBaseUrl(true) . '/index.php/opportunities/default/details?id=' . $this->optId;
                $content .='<tr><th><label for="Approval_Opportunity_Name">Opportunity Name</label></th><td colspan="1"><a href="'.$opp_redirect_url.'">'.$opportunity->name.'</a></td></tr>
                            
                            <tr><th><label for="Approval_Opportunity_Owner">Opportunity Owner</label></th><td colspan="1">'.$opportunity->owner.'</td></tr>';                            
                if($opportunity->stage->value == Constant::FINALPROPOSAL){
                    if($opportunity->oldFinalAmount > 0)
                    {
                        $content .='<tr><th><label for="Approval_Opportunity_OwnerFinal_Price">Change Price Request</label></th><td colspan="1">'.$opportunity->finalAmount->value.'</td></tr>
                        <tr><th><label for="Approval_Previous_Final_Price">Previous Final Price</th><td colspan="1">'.$opportunity->oldFinalAmount.'</td></tr>';
                    }
                    if($opportunity->recordType->value == OpportunityUtils::RECURRING){
                        $content .='<th><label for="Agreement_renewal_date">Renewal Date<span class="required">*</span></label></th>
                              <td colspan="1">
                                 <div class="has-date-select" style ="width:50%">
                                    <input type="text" name="Agreement[Renewal Date]" id="Agreement_Renewal_Date">
                                 </div>
                                 <div class="errorMessage"style="display:none;" id="Agreement_Renewal_Date_req">Agreement Renewal Date cannot be blank.</div>
                              </td>';
                    }
//                    $content .='<tr><th>
//                            <label for="Opportunity_estimatorApproval">Schedule a Job</label>
//                            </th>
//                            <td colspan="1">                              
//                                <label class="hasCheckBox">
//                                    <input type="checkbox" name="Opportunity[jobScheduleCheck]" id="jobScheduleCheck">
//                                </label>
//                            </td>
//                            <th></div></td></tr>';
                }
            }else{
                $content .='<tr><th>Agreement Name</th><td colspan="1">'.$agreement->name.'</td></tr>
                            <tr><th>Agreement Owner</th><td colspan="1">'.$agreement->owner.'</td></tr>';
            }
                    
            $content .='<tr><th><label for="Approval_Opportunity_Name">Comments</label></th>
                        <td colspan="2">
                        <textarea id="approvalProcessComment" draggable="false" cols="10" rows="6" name="Approvalprocess[comment]" style="min-height: 120px;  overflow-y: scroll;resize: none;"></textarea>
                        <div class="shadow" style="position: absolute; top: -50000px; left: -50000px; visibility: hidden; width: 890px; font-size: 12px; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; line-height: 16.2px; resize: none;">a</div>
                        </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="float-bar">
               <div class="view-toolbar-container clearfix dock">
                  <div class="form-toolbar">';

            if(!empty($this->optId)){
                $content .='<a id="approvalProcessAccept" name="approve" class="attachLoading z-button" href="javascript:void(0);" onclick="javascript:updateApprovalProcessDetails(\'accept\','.$this->optId.','.$this-> apId.');"> 
                            <span class="z-spinner"></span>
                            <span class="z-icon"></span>
                            <span class="z-label">Approve</span>
                        </a>       
                        <a id="approvalProcessReject" name="reject" class="cancel-button" href="javascript:void(0);" onclick="javascript:updateApprovalProcessDetails(\'reject\','.$this->optId.','.$this-> apId.');">
                                <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Reject</span>
                        </a>
                        <a id="approvalProcessCancel" name="cancel" class="cancel-button" href="javascript:void(0);"  onClick="javascript:updateApprovalProcessDetails();">
                                <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Cancel</span>
                        </a>';
            }else{
               $content .='<a id="approvalProcessAccept" name="approve" class="z-button" href="javascript:void(0);" onclick="javascript:updateApprovalProcessDetailsForAgmnt(\'accept\','.$this->agmntId.','.$this-> apId.');"> 
                                <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Approve</span>
                          </a>       
                          <a id="approvalProcessReject" name="reject" class="cancel-button" href="javascript:void(0);" onclick="javascript:updateApprovalProcessDetailsForAgmnt(\'reject\','.$this->agmntId.','.$this-> apId.');">
                                <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Reject</span>
                          </a>
                          <a id="approvalProcessCancel" name="cancel" class="cancel-button" href="javascript:void(0);"  onClick="javascript:updateApprovalProcessDetailsForAgmnt();">
                                <span class="z-spinner"></span>
                                <span class="z-icon"></span>
                                <span class="z-label">Cancel</span>
                         </a> ';
            }
            $content .='
                    </div>
                </div>
                </div>
            </form>
           </div>
       </div>';
       return $content;           
    }        
        
        public function ProcessRequest(){            
                $content = '<div class="wrapper">
                    <h1><span class="truncated" threedots="Approval History"><span class="ellipsis-content">Approval History</span></span></h1>
                        <div class="wide double-column form">                
                                <div class="left-column full-width">
                                    <div class="panel">
                            <table class="items selected_products_table">
                                               <colgroup span="7"></colgroup>
                                           
                            <thead style="font-weight: bold; background-color:#E6E6E6; color: #999;vertical-align: inherit; padding: 5px;">
                                  <th style="width: 10%; font-weight: bold;">Action</th>
                                  <th style="width: 20%; font-weight: bold;">Date</th>
                                  <th style="width: 10%; font-weight: bold;">Status</th>
                                  <th style="width: 15%; font-weight: bold;">Assigned To</th>
                                  <th style="width: 15%; font-weight: bold;">Actual Approver</th>
                                  <th style="width: 20%; font-weight: bold;">Comments</th>
                                  <th style="width: 15%;font-weight: bold;">Overall Status</th>
                            </thead>
                            <tbody>';
                           // $appApproval = new ApprovalProcess(); 
                            $appApproval = ApprovalProcess::getAllAppProcess($this->optId);                                                    
                           /** $statusArr = array(1=>'Pending',2=>'Approved',3=>'Rejected',4=>'Reassigned',5=>'Recalled');            
                            if (count($appApproval)>0){                                                               
                                foreach($appApproval as $history) { 	                                			  
                                    $content .= '<tr><td><div>'.$history->action.'</div></td><td><div>'.$history->date.'</div></td><td><div>'.$statusArr[$history->status].'</div></td><td><div>'.$history->assignedto.'</div></td><td><div>'.$history->actualapprover.'</div></td><td><div>'.$history->comments.'</div></td><td><div>'.$statusArr[$history->overallstatus].'</div></td></tr>'; 
                                    }
                                }
                            else{*/
                            $content .=  '<th colspan="7" style="background-color:gray; color:white; font-weight:bold;"><span class="empty">No results found</span></th>';
                           // }
                                  
                      $content .=      '</tbody>                              
                            </table>
                        </div></div></div></div>';   
                //$render = ApprovalProcess::getAll();    
               return $content;
        }
        
       
        public function generateHTMLEndView() {
            $content = '<div></div>';
            return $content;
        }
         
        //To load js file present in the current module
        protected function renderScripts()     {
           Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('application.modules.approvalProcess.elements.assets')) . '/ApprovalProcessTemplateUtils.js',
                CClientScript::POS_END);         
        }
                 
    }
?>

