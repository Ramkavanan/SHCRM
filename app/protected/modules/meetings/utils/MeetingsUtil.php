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

    class MeetingsUtil
    {
        /**
         * @param Meeting $meeting
         * @param string $link
         * @return string
         */
        public static function renderDaySummaryContent(Meeting $meeting, $link)
        {
            $content = null;
            $title       = '<h3>' . $meeting->name . '<span>' . $link . '</span></h3>';
            $dateContent = DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($meeting->startDateTime);
            $localEndDateTime = DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($meeting->endDateTime);
            if ($localEndDateTime != null)
            {
                $dateContent .= ' - ' . $localEndDateTime;
            }
            $dateContent .= '<br/>';
            $content .= self::renderActivityItemsContentsExcludingContacts($meeting);
            if (count($meeting->activityItems) > 0 || count($meeting->userAttendees) > 0)
            {
                $attendeesContent = null;
                $contactLabels = self::getExistingContactRelationsLabels($meeting->activityItems);
                foreach ($contactLabels as $label)
                {
                    if ($attendeesContent != null)
                    {
                        $attendeesContent .= '<br/>';
                    }
                    $attendeesContent .= $label;
                }
                foreach ($meeting->userAttendees as $user)
                {
                    if ($attendeesContent != null)
                    {
                        $attendeesContent .= '<br/>';
                    }
                    $params             = array('label' => strval($user), 'redirectUrl' => null, 'wrapLabel' => false);
                    $moduleClassName    = $user->getModuleClassName();
                    $moduleId           = $moduleClassName::getDirectoryName();
                    $element            = new DetailsLinkActionElement('default', $moduleId, $user->id, $params);
                    $attendeesContent  .= '<i class="icon-'.strtolower(get_class($user)).'"></i> ' . $element->render();
                }
                if ($attendeesContent != null )
                {
                    $content .= $attendeesContent . '<br/>';
                }
            }
            $content = $title . $dateContent . ZurmoHtml::tag('div', array('class' => 'meeting-details'), $content);
            if ($meeting->location != null)
            {
                $content .=  ZurmoHtml::tag('strong', array(), Zurmo::t('ZurmoModule', 'Location')) . '<br/>';
                $content .= $meeting->location;
                $content .= '<br/>';
            }
            if ($meeting->description != null)
            {
                $content .= ZurmoHtml::tag('strong', array(), Zurmo::t('ZurmoModule', 'Description')) . '<br/>';
                $content .= $meeting->description;
            }
            return ZurmoHtml::tag('div', array('class' => 'meeting-summary'), $content);
        }

        protected static function getExistingContactRelationsLabels($activityItems)
        {
            $existingContacts = array();
            $modelDerivationPathToItem = RuntimeUtil::getModelDerivationPathToItem('Contact');
            foreach ($activityItems as $item)
            {
                try
                {
                    $contact = $item->castDown(array($modelDerivationPathToItem));
                    if (get_class($contact) == 'Contact')
                    {
                        $params             = array('label' => strval($contact), 'redirectUrl' => null, 'wrapLabel' => false);
                        $moduleClassName    = $contact->getModuleClassName();
                        $moduleId           = $moduleClassName::getDirectoryName();
                        $element            = new DetailsLinkActionElement('default', $moduleId, $contact->id, $params);
                        $existingContacts[] = '<i class="icon-'.strtolower(get_class($contact)).'"></i> ' . $element->render();
                    }
                }
                catch (NotFoundException $e)
                {
                    //do nothing
                }
            }
            return $existingContacts;
        }

        protected static function getNonExistingContactRelationsLabels($activityItems)
        {
            $existingContacts = array();
            $modelDerivationPathToItem = RuntimeUtil::getModelDerivationPathToItem('Contact');
            foreach ($activityItems as $item)
            {
                try
                {
                    $contact = $item->castDown(array($modelDerivationPathToItem));
                    if (get_class($contact) == 'Contact')
                    {
                        $params          = array('label' => strval($contact), 'redirectUrl' => null, 'wrapLabel' => false);
                        $moduleClassName = $contact->getModuleClassName();
                        $moduleId        = $moduleClassName::getDirectoryName();
                        $element          = new DetailsLinkActionElement('default', $moduleId, $contact->id, $params);
                        $existingContacts[] = '<i class="icon-'.strtolower(get_class($contact)).'"></i> ' . $element->render();
                    }
                }
                catch (NotFoundException $e)
                {
                    //do nothing
                }
            }
            return $existingContacts;
        }

        protected static function renderActivityItemsContentsExcludingContacts(Meeting $meeting)
        {
            $activityItemsModelClassNamesData = ActivitiesUtil::getActivityItemsModelClassNamesDataExcludingContacts();
            $content = null;
            foreach ($activityItemsModelClassNamesData as $relationModelClassName)
            {
                $activityItemForm = null;
                //ASSUMES ONLY A SINGLE ATTACHED ACTIVITYITEM PER RELATION TYPE.
                foreach ($meeting->activityItems as $item)
                {
                    try
                    {
                        $modelDerivationPathToItem = RuntimeUtil::getModelDerivationPathToItem($relationModelClassName);
                        $castedDownModel           = $item->castDown(array($modelDerivationPathToItem));
                        if ($content != null)
                        {
                            $content .= '<br/> ';
                        }
                        $params          = array('label' => strval($castedDownModel), 'redirectUrl' => null, 'wrapLabel' => false);
                        $moduleClassName = $castedDownModel->getModuleClassName();
                        $moduleId        = $moduleClassName::getDirectoryName();
                        $element          = new DetailsLinkActionElement('default', $moduleId, $castedDownModel->id, $params);
                        //Render icon
                        $content .= '<i class="icon-'.strtolower(get_class($castedDownModel)).'"></i> ';
                        $content .= $element->render();
                        break;
                    }
                    catch (NotFoundException $e)
                    {
                    }
                }
            }
            if ($content != null)
            {
                $content .= '<br/>';
            }
            return $content;
        }

        /**
         * Gets full calendar item data.
         * @return string
         */
        public function getCalendarItemData()
        {
            $name             = $this->name;
            $location         = $this->location;
            $startDateTime    = DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay(
                                    $this->startDateTime,
                                    DateTimeUtil::DATETIME_FORMAT_DATE_WIDTH,
                                    DateTimeUtil::DATETIME_FORMAT_TIME_WIDTH,
                                    true);
            $endDateTime      = DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay(
                                    $this->endDateTime,
                                    DateTimeUtil::DATETIME_FORMAT_DATE_WIDTH,
                                    DateTimeUtil::DATETIME_FORMAT_TIME_WIDTH,
                                    true);
            $language         = Yii::app()->languageHelper->getForCurrentUser();
            $translatedAttributeLabels = self::translatedAttributeLabels($language);
            return array($translatedAttributeLabels['name']            => $name,
                         $translatedAttributeLabels['location']        => $location,
                         $translatedAttributeLabels['startDateTime']   => $startDateTime,
                         $translatedAttributeLabels['endDateTime']     => $endDateTime);
        }
        
         /**
         * Send Owner change email notification.
         * @return string
         */
        public function sendOwnerChangeMailNotification($model, $redirectUrlParams, $linkUrl){
            if ($redirectUrlParams == 'from_create') {
                $subject = '[VERTWARE] A NEW MEETING IS ASSIGNED TO YOU';
            } elseif ($redirectUrlParams == 'from_edit') {
                $subject = '[VERTWARE] MEETING OWNER CHANGE';
            }
            $ownerAccount = User::getById($model->owner->id);
            $recipients = array();
            if (!empty($ownerAccount->primaryEmail->emailAddress)){
                $recipients = array($ownerAccount->primaryEmail->emailAddress);
            }
            $accountAssigner = User::getById(Yii::app()->user->id);
            if (!empty($accountAssigner->primaryEmail->emailAddress)){
               $fromAddress = $accountAssigner->primaryEmail->emailAddress;
            } else {
               $fromAddress = Constant::DEFAULT_FROM_EMAIL;
            } 
            $fromAddress = Constant::DEFAULT_FROM_EMAIL;
            $fromAddress = array(
               'name' => 'VERTWARE',
               'address' => $fromAddress
            );
            $attendeesList = '';
            foreach ($model->userAttendees as $user) {
                if(!empty($user->primaryEmail->emailAddress)){
                    $attendeesList .= '<li>'.$user->getFullName().' ('.$user->primaryEmail->emailAddress.')</li>';
                } else {
                    $attendeesList .= '<li>'.$user->getFullName().'</li>';
                }
            }
            $mailContent = array(
               'subject' => $subject,
               'content' => 'Hi '.$ownerAccount->firstName.',    
                       <p><span style="margin-right:17px"><b>Meeting </b></span>:<span> ' . $model->name . '</span></p>
                       <p style="height:4px;line-height:0.5px"><span style="margin-right:17px">Location </span>: <span> ' . $model->location . '</span></p> 
                       <p style="height:4px;line-height:0.5px"><span style="margin-right:5px">Start Time </span>:<span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($model->startDateTime) . '</span></p>
                       <p style="height:4px;line-height:0.5px"><span style="margin-right:14px">End Time</span>: <span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($model->endDateTime) . '</span></p>
                       <p style="height:8px"><span style="margin-right:9px"><b>Attendees</b></span><span>: <ul> '.$attendeesList.'</ul></span></p>
                       <p style="height:10px"><span style="margin-right:5px">Organized by</span>:<span> ' . $accountAssigner->getFullName() . '</span></p>
                       <p style="height:10px"><span style="margin-right:8px">Please see </span>: <span><a href="' . $linkUrl . '"> ' . $linkUrl . '</a></span></p>
                       <br/><hr><p style="height:10px"> Thanks.</p><p style="line-height:0.5px"><b>ShinnedHawks</b></p>' 
            );
            if(count($recipients) > 0){
                ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
            }    
            
       }
       
        /**
         * Send meeting invite to the new meeting attendees.
         * @return string
         */
        public function sendMeetingInviteNotification($model, $recipientsList, $linkUrl,$requestFrom, $syncid=''){
        
            if($requestFrom == 'from_create')
            {
                $subject = '[VERTWARE] MEETING INVITATION';
                $content = 'You have been invited to a meeting. Please find the meeting details below:';
            }
            else if($requestFrom == 'from_edit')
            {
                $subject = '[VERTWARE] Meeting Details Updated';
                $content = 'Meeting details have been updated.  Please find the meeting details below:';
            }
            $ownerAccount = User::getById($model->owner->id);
            $accountAssigner = User::getById(Yii::app()->user->id);
            if ($accountAssigner->outlookEmail != null){
               $accessToken = outlookCalendar::getAccessToken($accountAssigner->refreshtoken);
               $fromAddress = $accountAssigner->outlookEmail;
            } else if($accountAssigner->primaryEmail->emailAddress){
               $fromAddress = $accountAssigner->primaryEmail->emailAddress;
            } else {
               $fromAddress = Constant::DEFAULT_FROM_EMAIL;
            } 
            $fromAddress = Constant::DEFAULT_FROM_EMAIL;
            $fromAddress = array(
               'name' => 'VERTWARE',
               'address' => $fromAddress
            );
            $attendeesList = '';
            $recipients = array();
            $outLookAttendees = array();
            $outlookAttendeesList = 0;
            /**
             * Modified by : Murugan M
             * Modified date : Sep 15 2016
             * Description : The following 'foreach' loop not needed because we splitted the outlookEmail
             * having attendees and outlookEmail not having attendees and put into their list into recipients array in next 'foreach'
             */
//            foreach ($recipientsList as $user) {
//                if(!empty($user->primaryEmail->emailAddress)){
//                    $recipients[] = $user->primaryEmail->emailAddress;
//                }
//                if(!empty($user->outlookEmail)){
//                    $outlookAttendeesList++;
//                }
//            }
            foreach ($model->userAttendees as $user) {
                
                if(!empty($user->outlookEmail)){
                    
                    /**
                     * Modified by : Murugan M
                     * Modified date : Sep 15 2016
                     * Description : Mail content inside attendees list to added outlook account having attendees
                     */
                    if (!in_array($user->outlookEmail, $outLookAttendees)) {
                        $outLookAttendees[] = $user->outlookEmail;
                        $attendeesList .= '<li>'.$user->getFullName().'  ('.$user->outlookEmail.')</li>';
                    }
                }else if(!empty($user->primaryEmail->emailAddress)){                
                    $attendeesList .= '<li>'.$user->getFullName().'  ('.$user->primaryEmail->emailAddress.')</li>';
                    $recipients[] = $user->primaryEmail->emailAddress;
                } else {
                    $attendeesList .= '<li>'.$user->getFullName().'</li>';
                }                
            }
            // For the Contacts
            if(isset($model->activityItems) && count($model->activityItems) > 0)
            {
                $activityRecipients = self::getMeetingContactEmails($model->activityItems, $recipients);
                $recipients = $activityRecipients['recipients'];
                $attendeesList .= $activityRecipients['attendeesList'];
            }
            /**
             * Modified by : Murugan M
             * Modified date : Sep 15 2016
             * Description : Recipients mail content to body message changes applied. This message is given from Krishna.
             */
            // To add the user 
            
            if(!empty($accountAssigner->outlookEmail)){
                $recipients[] = $accountAssigner->outlookEmail;
            }else if(!empty ($accountAssigner->primaryEmail->emailAddress)){
                $recipients[] = $accountAssigner->primaryEmail->emailAddress;
            }
//             $recipients[] = $accountAssigner->primaryEmail->emailAddress;            
            
            $mailContent = array(
                'subject' => $subject,
                'content' => 'Hi there,
                        <p>'.$content.'</p>
                        <p><span style="margin-right:17px"><b>Meeting </b></span>:<span> ' . $model->name . '</span></p>
                        <p style="height:4px;line-height:0.5px"><span style="margin-right:5px">Start Time </span>:<span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($model->startDateTime) . '</span></p>
                        <p style="height:4px;line-height:0.5px"><span style="margin-right:14px">End Time</span>: <span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($model->endDateTime) . '</span></p>
                        <p style="height:4px;line-height:0.5px"><span style="margin-right:17px">Location </span>: <span> ' . $model->location . '</span></p> 
                        <p style="height:8px"><span style="margin-right:9px"><b>Attendees</b></span><span>: <ul> '.$attendeesList.'</ul></span></p>
                        <p style="height:8px"><span style="margin-right:9px"><b>Description</b></span><span>: <ul> '.$model->description.'</ul></span></p>
                        <p style="height:10px"><span style="margin-right:5px">Organized by</span>:<span> ' . $accountAssigner->getFullName() . '</span></p>
                        <p style="height:10px"><span style="margin-right:8px">Please see </span>: <span><a href="' . $linkUrl . '"> ' . $linkUrl . '</a></span></p>
                        <br/><hr><p style="height:10px"> Thanks.</p><p style="line-height:0.5px"><b>ShinnedHawks</b></p>' 
            );            

//            if(count($recipients) > 0){
                if($accountAssigner->isOutlookSynced > 0 ){
                    /**
                     * Modified by : Murugan M
                     * Modified date : Sep 15 2016
                     * Description : The foolowing commanded 'if' condition is not needed because we are using 'from_edit' condition to create and update the outlook sync
                     */
//                    if($requestFrom == 'from_edit')
//                    {
//                        //outlookCalendar::deleteEventCalendar($accessToken, $fromAddress['address'], $syncid);
//                    }
                    $outlookSyncId = outlookCalendar::addEventToCalendar($accessToken,  $accountAssigner->outlookEmail, $model->name, $model->location, $model->startDateTime, $model->endDateTime, $model->description, $outLookAttendees,$requestFrom, $syncid);
                    if($requestFrom == 'from_create')
                        Meeting::updateOutLookSyncId($outlookSyncId, $model->id);
                    
                    if(count($recipients) > 0){
                        ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                    }
                }else{
                    ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);
                }               
//            }            

       }
       
        /**
         * Gets new meeting attendee user.
         * @return string
         */
       public function getNewRecipients($oldUserAttendees, $userAttendees){
           $recipients = array();
           if(count($userAttendees) > 0){
               foreach ($userAttendees as $newUserAttendee) {
                    $isExistingAttendee = FALSE;
                    if(count($oldUserAttendees) > 0){
                        foreach ($oldUserAttendees as $oldUserAttendee) {
                            if($newUserAttendee->id != $oldUserAttendee->id){
                                continue;
                            } else {
                                $isExistingAttendee = TRUE;
                                break;
                            } 
                        }
                        if(!$isExistingAttendee){
                            $recipients[] = $newUserAttendee;
                        }    
                    } else {
                        $recipients[] = $newUserAttendee;
                    }
               }
           }
           return $recipients;
       }
       
       public function getRemovedRecipients($oldUserAttendees, $userAttendees){
           $removedRecipients = array();
           if(count($oldUserAttendees) > 0){
               foreach ($oldUserAttendees as $removedAttendee) {
                    $isRemovedAttendee = TRUE;
                    if(count($userAttendees) > 0){
                        foreach ($userAttendees as $newAttendee) {
                            if($removedAttendee->id != $newAttendee->id){
                                continue;
                            } else {
                                $isRemovedAttendee = FALSE;
                                break;
                            }
                        }
                        if($isRemovedAttendee){
                            $removedRecipients[$removedAttendee->id] = $removedAttendee;                            
                        }    
                    }
               }
           }
           return $removedRecipients;
       }
       
        public function sendMeetingRemoveNotification($model, $recipientsList)
        {        
            $accountAssigner = User::getById($model->owner->id);
            
            if($accountAssigner->primaryEmail->emailAddress){
               $fromAddress = $accountAssigner->primaryEmail->emailAddress;
            } else {
               $fromAddress = Constant::DEFAULT_FROM_EMAIL;
            } 
            $fromAddress = Constant::DEFAULT_FROM_EMAIL;
            $fromAddress = array(
               'name' => 'VERTWARE',
               'address' => $fromAddress
            );
            
            foreach ($recipientsList as $user) {                
                if(!empty($user->primaryEmail->emailAddress)){
                    $recipients[] = $user->primaryEmail->emailAddress;
                }                
            }
            
            $mailContent = array(
                'subject' => '[VERTWARE] Meeting Removal',
                'content' => 'Hi there,
                        <p>You have been removed from the meeting.</p>
                        <p><span style="margin-right:17px"><b>Meeting </b></span>:<span> ' . $model->name . '</span></p>
                        <p style="height:4px;line-height:0.5px"><span style="margin-right:5px">Start Time </span>:<span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($model->startDateTime) . '</span></p>
                        <p style="height:4px;line-height:0.5px"><span style="margin-right:14px">End Time</span>: <span> ' . DateTimeUtil::convertDbFormattedDateTimeToLocaleFormattedDisplay($model->endDateTime) . '</span></p>
                        <p style="height:4px;line-height:0.5px"><span style="margin-right:17px">Location </span>: <span> ' . $model->location . '</span></p> 
                        <hr><p style="height:10px"> Thanks.</p><p style="line-height:0.5px"><b>ShinnedHawks</b></p>' 
            );
            ApprovalProcessUtils::generateMail($fromAddress, $recipients, $mailContent);                
        }
        
        public static function getMeetingContactEmails($activityItems, $recipients)
        {
            $existingContacts = array();
            $attendeesList='';
            $modelDerivationPathToItem = RuntimeUtil::getModelDerivationPathToItem('Contact');
            foreach ($activityItems as $item)
            {
                try
                {
                    $contact = $item->castDown(array($modelDerivationPathToItem));
                    
                    if(isset($contact->primaryEmail))
                    {
                        if(!empty($contact->primaryEmail->emailAddress))
                        {
                            $recipients[] = $contact->primaryEmail->emailAddress;
                        }
                    }
                    
                    if(isset($contact->firstName)) {
                        $attendeesList .= '<li>'.$contact->firstName.' '.$contact->lastName.'</li>';
                    }
                }
                catch (NotFoundException $e)
                {
                    //echo 'Contact activy issue'.$e;                    
                }
            }
            $existingContacts['recipients'] = $recipients;
            $existingContacts['attendeesList'] = $attendeesList;
            return $existingContacts;
        }       
    }
?>