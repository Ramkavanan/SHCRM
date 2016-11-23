<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2015 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2015. All rights reserved".
     ********************************************************************************/

    /**
     * Activities Modules such as Meetings, Notes, and tasks
     * should extend this class to provide generic actions that are uniform across these models.
     */
    abstract class ActivityModelsDefaultController extends ActivitiesModuleController
    {
        public function filters()
        {
            $modelClassName   = $this->getModule()->getPrimaryModelName();
            $viewClassName    = $modelClassName . 'EditAndDetailsView';
            return array_merge(parent::filters(),
                array(
                    array(
                        ZurmoBaseController::REQUIRED_ATTRIBUTES_FILTER_PATH . ' + create, createFromRelation, edit',
                        'moduleClassName' => get_class($this->getModule()),
                        'viewClassName'   => $viewClassName,
                   ),
               )
            );
        }

        protected function getPageViewClassName()
        {
            return $this->getModule()->getPluralCamelCasedName() . 'PageView';
        }

        /**
         * @param $relationAttributeName
         * @param $relationModelId
         * @param $relationModuleId
         * @param $redirectUrl
         */
        public function actionCreateFromRelation($relationAttributeName, $relationModelId, $relationModuleId, $redirectUrl)
        {
            $modelClassName   = $this->getModule()->getPrimaryModelName();
            $activity         = $this->resolveNewModelByRelationInformation( new $modelClassName(),
                                                                                $relationAttributeName,
                                                                                (int)$relationModelId,
                                                                                $relationModuleId);
            $this->actionCreateByModel($activity, $redirectUrl);
        }

        /**
         * @param Activity $activity
         * @param $redirectUrl
         */
        protected function actionCreateByModel(Activity $activity, $redirectUrl)
        {
            $redirectUrl = 'from_create';
            $titleBarAndEditView = $this->makeEditAndDetailsView(
                                            $this->attemptToSaveModelFromPost($activity, $redirectUrl), 'Edit');
            $pageViewClassName = $this->getPageViewClassName();
            $view = new $pageViewClassName(ZurmoDefaultViewUtil::
                                             makeStandardViewForCurrentUser($this, $titleBarAndEditView));
            echo $view->render();
        }

        /**
         * @param $id
         * @param null $redirectUrl
         */
        public function actionDetails($id, $redirectUrl = null)
        {
            $modelClassName    = $this->getModule()->getPrimaryModelName();
            $activity = static::getModelAndCatchNotFoundAndDisplayError($modelClassName, intval($id));
            AuditEvent::logAuditEvent('ZurmoModule', ZurmoModule::AUDIT_EVENT_ITEM_VIEWED, array(strval($activity), get_class($this->getModule())), $activity);
            ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($activity);
            $pageViewClassName = $this->getPageViewClassName();
            $view              = new $pageViewClassName(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this,
                                             $this->makeEditAndDetailsView($activity, 'Details')));
            echo $view->render();
        }

        /**
         * @param $id
         * @param null $redirectUrl
         */
        public function actionEdit($id, $redirectUrl = null)
        {
            $modelClassName    = $this->getModule()->getPrimaryModelName();
            $activity          = $modelClassName::getById(intval($id));
            ControllerSecurityUtil::resolveAccessCanCurrentUserWriteModel($activity);
            $this->processEdit($activity, $redirectUrl);
        }

        /**
         * @param $id
         * @param null $redirectUrl
         */
        public function actionCopy($id, $redirectUrl = null)
        {
            $modelClassName   = $this->getModule()->getPrimaryModelName();
            $copyToActivity   = new $modelClassName();
            $postVariableName = get_class($copyToActivity);
            if (!isset($_POST[$postVariableName]))
            {
                $activity = $modelClassName::getById((int)$id);
                ControllerSecurityUtil::resolveAccessCanCurrentUserReadModel($activity);
                ActivityCopyModelUtil::copy($activity, $copyToActivity);
            }
            $this->processEdit($copyToActivity, $redirectUrl);
        }

        /**
         * @param Activity $activity
         * @param null $redirectUrl
         */
        protected function processEdit(Activity $activity, $redirectUrl = null)
        {
            $redirectUrl = 'from_edit';
            $pageViewClassName = $this->getPageViewClassName();
            $view              = new $pageViewClassName(ZurmoDefaultViewUtil::
                                         makeStandardViewForCurrentUser($this,
                                             $this->makeEditAndDetailsView(
                                                $this->attemptToSaveModelFromPost($activity, $redirectUrl), 'Edit')));
            echo $view->render();
        }

        /**
         * @param $id
         * @param null $redirectUrl
         */
        public function actionDelete($id, $redirectUrl = null)
        {
            if ($redirectUrl == null)
            {
                $redirectUrl = array('/home/default');
            }
            $modelClassName    = $this->getModule()->getPrimaryModelName();            
            $activity          = $modelClassName::getById(intval($id));
            
            //For sending the mail to the attendees
            if($modelClassName == 'Meeting')
            {
                $accountAssigner = User::getById(Yii::app()->user->id);
                
                if ($accountAssigner->outlookEmail != null){
                   $accessToken = outlookCalendar::getAccessToken($accountAssigner->refreshtoken);
                }
                
                $fromAddress = array(
                   'name' => 'VERTWARE',
                   'address' => Constant::DEFAULT_FROM_EMAIL
                );               
                
                $recipients = array();

                if(!empty($accountAssigner->outlookEmail)){
                    $recipients[] = $accountAssigner->outlookEmail;
                }else if(!empty ($accountAssigner->primaryEmail->emailAddress)){
                    $recipients[] = $accountAssigner->primaryEmail->emailAddress;
                }
                
                foreach ($activity->userAttendees as $user) {
                    if(!empty($user->outlookEmail)){
                        $recipients[] = $user->outlookEmail;                    
                    }
                    else if(!empty($user->primaryEmail->emailAddress)){
                        $recipients[] = $user->primaryEmail->emailAddress;
                    }              
                }
                
                // For the Contacts
                if(isset($activity->activityItems) && count($activity->activityItems) > 0)
                {
                    $activityRecipients = MeetingsUtil::getMeetingContactEmails($activity->activityItems, $recipients);
                    $recipients         = $activityRecipients['recipients'];
                }
                
                $mailContent = array(
                    'subject' => '[VERTWARE] MEETING CANCELLATION',
                    'content' => 'Hi there,
                            <p>  The meeting has been cancelled</p>
                            <p><span style="margin-right:17px"><b>Meeting </b></span>:<span> ' . $activity->name . '</span></p>
                            <p style="height:4px;line-height:0.5px"><span style="margin-right:5px">Start Time </span>:<span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($activity->startDateTime) . '</span></p>
                            <p style="height:4px;line-height:0.5px"><span style="margin-right:14px">End Time</span>: <span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($activity->endDateTime) . '</span></p>
                            <p style="height:4px;line-height:0.5px"><span style="margin-right:17px">Location </span>: <span> ' . $activity->location . '</span></p> 
                            <p style="height:8px"><span style="margin-right:9px"><b>Description</b></span><span>: <ul> '.$activity->description.'</ul></span></p>
                            <p style="height:10px"><span style="margin-right:5px">Organized by</span>:<span> ' . $accountAssigner->getFullName() . '</span></p>
                            <hr><p style="height:10px"> Thanks.</p><p style="line-height:0.5px"><b>ShinnedHawks</b></p>'  
                );
                
                // To delete the event in calendar            
                if($accountAssigner->isOutlookSynced > 0 ){
                    $outlookSyncId = outlookCalendar::deleteEventCalendar($accessToken, $accountAssigner->outlookEmail, $activity->outlooksyncid);
                }
                if(count($recipients) > 0){
                    ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                }
                // Ends Here
            }
            
            ControllerSecurityUtil::resolveAccessCanCurrentUserDeleteModel($activity);
            $activity->delete();
            $this->redirect($redirectUrl);
        }
        
        /**
         * Check if form is posted. If form is posted attempt to save. If save is complete, confirm the current
         * user can still read the model.  If not, then redirect the user to the index action for the module.
         */
        protected function attemptToSaveModelFromPost($model, $redirectUrlParams = null, $redirect = true, $returnOnValidate = false)
        {
            assert('$redirectUrlParams == null || is_array($redirectUrlParams) || is_string($redirectUrlParams)');
            $savedSuccessfully   = false;
            $modelToStringValue = null;
            $postVariableName   = get_class($model);
            if($this->getModule()->getPrimaryModelName() == 'Meeting'){
                $oldOwnerId = $model->owner->id;
                $oldUserAttendees  = array();
                foreach ($model->userAttendees as $userAttendee) {
                    $oldUserAttendees[] = $userAttendee;
                }
                
//                $oldContactAttendees  = array();
//                foreach ($model->activityItems as $item) {
//                    $oldContactAttendees[] = $item;
//                }
            }
            if (isset($_POST[$postVariableName]))
            {
                $postData = $_POST[$postVariableName];
                $controllerUtil   = static::getZurmoControllerUtil();
                $model            = $controllerUtil->saveModelFromPost($postData, $model, $savedSuccessfully,
                                                                       $modelToStringValue, $returnOnValidate);
            }
            if ($savedSuccessfully && $redirect)
            {
                if(is_string($redirectUrlParams) && $this->getModule()->getPrimaryModelName() == 'Meeting')
                {
                    $linkUrl = Yii::app()->getBaseUrl(true).'/index.php/meetings/default/details?id='.$model->id;
                    if($model->owner->id != $oldOwnerId)
                    {
                        MeetingsUtil::sendOwnerChangeMailNotification($model, $redirectUrlParams, $linkUrl);
                    }
                    if ($redirectUrlParams == 'from_create') {
                        if(isset($model->userAttendees) && count($model->userAttendees) > 0){
                            foreach ($model->userAttendees as $user) {
                                $recipientsList[] = $user;
                            }
                            if(count($recipientsList) > 0){
                                MeetingsUtil::sendMeetingInviteNotification($model, $recipientsList, $linkUrl, 'from_create');
                            }
                        }
                        else // To send the mail for alone user
                        {
                            $recipientsList = array();
                            MeetingsUtil::sendMeetingInviteNotification($model, $recipientsList, $linkUrl, 'from_create');
                        }
                    } else if ($redirectUrlParams == 'from_edit'){
                        if(!empty($model->userAttendees) && count($model->userAttendees) > 0){
                            //$newRecipientsList = array();
                            //$newRecipientsList = MeetingsUtil::getNewRecipients($oldUserAttendees, $model->userAttendees);
                            $removedRecipientsList = array();
                            $removedRecipientsList = MeetingsUtil::getRemovedRecipients($oldUserAttendees, $model->userAttendees);                            
                            
                            if(count($removedRecipientsList) > 0){
                                MeetingsUtil::sendMeetingRemoveNotification($model, $removedRecipientsList);
                                //$meeting = Meeting::getById(intval($model->id));
                                //MeetingsUtil::SendMeetingInviteNotification($model, $recipientsList, $linkUrl, 'from_edit', $meeting->outlooksyncid);
                            }
                            
                            $oldRecipientsList = array();
//                            foreach ($model->userAttendees as $user) {
//                                $oldRecipientsList[] = $user;
//                            }
                            $meeting = Meeting::getById(intval($model->id));                            
                            MeetingsUtil::SendMeetingInviteNotification($meeting, $oldRecipientsList, $linkUrl, 'from_edit', $meeting->outlooksyncid);
                        }
                    }
                    $redirectUrlParams = '';
                }
                $this->actionAfterSuccessfulModelSave($model, $modelToStringValue, $redirectUrlParams);
            }
            return $model;
        }
    }
?>