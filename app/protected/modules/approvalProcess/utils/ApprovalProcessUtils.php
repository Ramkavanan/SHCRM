<?php

    /**
     *  Class used to interact with controller 
     * 
     * @author Ramachandran.K 
     * 
     * List of Common Functions For Opportunity and Agreement
     *    generateApprovalHistoryTable();
     *    generateApprovalHistory();
     *    generateApprovalHistoryTableEnd();
     *    changeRecalledStatus();
     *    generateMail();
     * 
     */
	class ApprovalProcessUtils   {
		
		public static function generateHTMLForApprovalInOppt($optId) {
                    $approvalHistories = ApprovalProcess::getAllAppProcess($optId);
                    $opt = Opportunity::getById($optId);
                    $opportunityProduct = OpportunityProduct::getAllByOpptId($optId);
                    $content = '<hr style="color:#eee;">
                                <div class="view-toolbar-container clearfix">
                                    <div class="panel">
                                            <div class="panelTitle">
                                                    Approval History 
                                            </div>
                                <div id="approvalHistoryListMsgId" style="margin:10px 0px 10px 350px; color:green; font-weight:bold;"></div>';
                    $content .= '<hr>';
                    //Showing submit aprroval button 
                    if(count($approvalHistories)==0 || $approvalHistories[0]->Status == ApprovalProcess::APPROVED || $approvalHistories[0]->Status == ApprovalProcess::REJECTED || $approvalHistories[0]->Status == ApprovalProcess::RECALLED ){
                        $content .= '<div class="form-toolbar clearfix" >
                                        <a id="subApprovalProces" name="Submit for Approval" class="attachLoading z-button" href="#approvalHistoryListId"  onClick="javascript:searchProducts(\''.$optId.'\',1,1);">
                                            <span class="z-spinner"></span>
                                            <span class="z-icon"></span>';
                        if ($opt->stage->value == Constant::FINALPROPOSAL) {        
                            $content .='<span class="z-label">Submit for Manager Approval</span>';
                        }else if(($opt->stage->value == Constant::ESTIMATE) && !empty($opportunityProduct)){
                            $content .='<span class="z-label">Submit for Estimator Approval</span>';
                        }
                        $content .=' </a>       
                                    </div><br />';
                    } else {
                        $content .= '<div class="form-toolbar clearfix">
							<a id="subApprovalProces" name="Recall the Approval" class="attachLoading z-button" href="#approvalHistoryListId"  onClick="javascript:searchProducts(\''.$optId.'\',2,5);">
								<span class="z-spinner"></span>
								<span class="z-icon"></span>
								<span class="z-label">Recall the Request</span>
							</a>       
							</div><br />';
                    }
                    $content .= ApprovalProcessUtils::generateApprovalHistoryTable();
                    $content .= ApprovalProcessUtils::generateApprovalHistory($approvalHistories, $isagmntId = FALSE);
                    $content .= ApprovalProcessUtils::generateApprovalHistoryTableEnd();
                    return $content;
						
		}
               
                public static function generateHTMLForApprovalInAgmnt($agmntId) {
                    $approvalHistories = ApprovalProcess::getAllAppProcessForAgmnt($agmntId);
                    $content = '<hr style="color:#eee;">
                                <div class="view-toolbar-container clearfix">
                                    <div class="panel">
                                        <div class="panelTitle">
                                            Approval History 
                                        </div>
                                <div id="approvalHistoryListMsgId" style="margin:10px 0px 10px 350px; color:green; font-weight:bold;"></div>';
                    $content .= '<hr>';
                    //Showing submit aprroval button 
                    if(count($approvalHistories)==0 || $approvalHistories[0]->Status == ApprovalProcess::APPROVED || $approvalHistories[0]->Status == ApprovalProcess::REJECTED || $approvalHistories[0]->Status == ApprovalProcess::RECALLED ){
                        $content .= '<div class="form-toolbar clearfix" id = "SubmitForApproval">
                                        <a id="subApprovalProces" name="Submit for Approval" class="attachLoading z-button" href="#approvalHistoryListId"  onClick="javascript:approvalForAgreement(\''.$agmntId.'\',1,1);">
                                            <span class="z-spinner"></span>
                                            <span class="z-icon"></span>
                                            <span class="z-label">Submit for Approval</span>
                                        </a>       
                                    </div><br />';
                    } else {
                        $content .= '<div class="form-toolbar clearfix">
                                        <a id="subApprovalProces" name="Recall the Approval" class="attachLoading z-button" href="#approvalHistoryListId"  onClick="javascript:approvalForAgreement(\''.$agmntId.'\',2,5);">
                                            <span class="z-spinner"></span>
                                            <span class="z-icon"></span>
                                            <span class="z-label">Recall the Request</span>
                                        </a>       
                                    </div><br />';
                    }
                    $content .= ApprovalProcessUtils::generateApprovalHistoryTable();
                    $content .= ApprovalProcessUtils::generateApprovalHistory($approvalHistories, $isagmntId = TRUE);
                    $content .= ApprovalProcessUtils::generateApprovalHistoryTableEnd();
                    return $content;
						
		}
                
                public static function generateApprovalHistoryTable() {
                    $htmlContent = '<table class="items selected_products_table"><colgroup span="7"></colgroup>';
                                           
                    $htmlContent .= '<thead style="font-weight: bold; background-color:#E6E6E6; color: #999;vertical-align: inherit; padding: 5px;">
                                      <th style="width: 10%; font-weight: bold;">Action</th>
                                      <th style="width: 20%; font-weight: bold;">Date</th>
                                      <th style="width: 10%; font-weight: bold;">Status</th>
                                      <th style="width: 15%; font-weight: bold;">Assigned To</th>
                                      <th style="width: 15%; font-weight: bold;">Actual Approver</th>
                                      <th style="width: 20%; font-weight: bold;">Comments</th>
                                      <th style="width: 15%;font-weight: bold;">Overall Status</th>
                                   </thead><tbody>';
                    
                    return $htmlContent;
                }
                
                public static function generateApprovalHistoryTableEnd() {
                    return '</tbody></table>';
                }
                
                //Generate Approval History For Opportunity and Agreement
                
                public static function generateApprovalHistory($approvalHistories, $isagmntId) {
                    $content = '';
                    if(count($approvalHistories)>0) {
                        foreach($approvalHistories as $approvalHistory) {
                                            
                            if($approvalHistory->action != ''){
                                $content .= '<th colspan="6" style="background-color:gray; color:white; font-weight:bold;">'.$approvalHistory->action.'</th><th style="background-color:gray; color:#fff; font-weight:bold;"><span style="background-color:'.($approvalHistory->overallstatus->value == ApprovalProcess::APPROVED ? "green" : ($approvalHistory->overallstatus->value == ApprovalProcess::PENDING ? "#262770": "#a82d31")).'; padding:2px 5px; width:150px;">'.$approvalHistory->overallstatus->value.'</span></th>';		
                                $content .= '<tr style="padding-top: 2px; text-align: left;">';
                                if((trim(Yii::app()->user->userModel) == trim($approvalHistory->actualapprover) || trim(Yii::app()->user->userModel) == trim($approvalHistory->assignedto)) && $approvalHistory->Status->value == ApprovalProcess::PENDING){
                                    if($isagmntId == TRUE){
                                        $content .= '<td ><a href="/app/index.php/approvalProcess/default/Reassign?id='.$approvalHistory->id.'">Reassign</a>&nbsp;<a href="/app/index.php/approvalProcess/default/approvalProcessing?agmntId='.$approvalHistory->agreement->id.'&apId='.$approvalHistory->id.'">Accept/Reject</a></td>';
                                    }else{
                                        $content .= '<td ><a href="/app/index.php/approvalProcess/default/Reassign?id='.$approvalHistory->id.'">Reassign</a>&nbsp;<a href="/app/index.php/approvalProcess/default/approvalProcessing?optId='.$approvalHistory->opportunity->id.'&apId='.$approvalHistory->id.'">Accept/Reject</a></td>';
                                    }
                                } else {
                                    $content .= '<td></td>';
                                }
                                    $content .='<td >'.DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($approvalHistory->date).'</td>
                                                <td >'.$approvalHistory->Status->value.'</td>
                                                <td >'.$approvalHistory->assignedto.'</td>
                                                <td >'.$approvalHistory->actualapprover.'</td>
                                                <td >'.$approvalHistory->comments.'</td>
                                                <td ></td>
                                         </tr>';
                            } else {
                                $content .= '<tr style="padding-top: 2px; text-align: left;">
                                                <td ></td>
                                                <td >'.DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($approvalHistory->date).'</td>
                                                <td >'.$approvalHistory->Status->value.'</td>
                                                <td >'.$approvalHistory->assignedto.'</td>
                                                <td >'.$approvalHistory->actualapprover.'</td>
                                                <td >'.$approvalHistory->comments.'</td>
                                                <td ></td>    
                                             </tr>';
                             }
                        }
                    }
                    return $content;
                }
                
                //Generate E-Mail For Opportunity and Agreement
                public static function generateMail($fromAddress, $recipients, $content)
                {
                    $configurationForm = EmailSmtpConfigurationFormAdapter::makeFormFromGlobalConfiguration();
                    
                    if ($configurationForm->host != null)
                    {
                        $emailAccount       = new EmailAccount();
                        $emailAccount->outboundHost     = $configurationForm->host;
                        $emailAccount->outboundPort     = $configurationForm->port;
                        $emailAccount->outboundUsername = $configurationForm->username;
                        $emailAccount->outboundPassword = ZurmoPasswordSecurityUtil::encrypt($configurationForm->password);
                        $emailAccount->outboundSecurity = $configurationForm->security;
                        $isUser = false;
                        
                        $emailMessage = EmailMessageHelper::customProcessAndCreateEmailMessage($fromAddress, $recipients, $content);
                        $mailer       = new ZurmoSwiftMailer($emailMessage, $emailAccount);
                      
                        $emailMessage = $mailer->sendTestEmail($isUser);
                       
                        $messageContent  = EmailHelper::prepareMessageContent($emailMessage);
                    }
                    else
                    {
                        $messageContent = Zurmo::t('EmailMessagesModule', 'A test email address must be entered before you can send a test email.') . "\n";
                    }
                    Yii::app()->getClientScript()->setToAjaxMode();
                    $messageView = new TestConnectionView($messageContent);                    
                }
                
                //Change Recall Status For Opportunity and Agreement
                public static function changeRecalledStatus($Id,$isAgreement) {
                    if($isAgreement){
                        $approvalHistories = ApprovalProcess::getAllAppProcessForAgmnt($Id);
                    }else{
                        $approvalHistories = ApprovalProcess::getAllAppProcess($Id);
                    }
                    $approvalHistories[0]->Status->value = ApprovalProcess::RECALLED;
                    $approvalHistories[0]->overallstatus->value = ApprovalProcess::RECALLED;
                    if($approvalHistories[0]->save() && $isAgreement ==TRUE) {
                        $agmnt = Agreement::getById($Id);
                        $agmnt->Status->value = Constant::DRAFTAGREEMENT;
                        $agmnt->save();
                    }  else {
                        // Do log here
                    }
                    
                }
	}
?>
