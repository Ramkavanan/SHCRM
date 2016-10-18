<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApprovalProcess extends Item {
    public function __toString()
    {
        if (trim($this->comments) == '')
        {
            return Yii::t('Default', '(Unnamed)');
        }
        return $this->comments;
    }
        
    CONST APPROVED = 'Approved';
    CONST REJECTED = 'Rejected';
    CONST RECALLED = 'ReCalled';
    CONST PENDING = 'Pending';
    CONST SUBMITTED = 'Submitted';
    CONST REASSIGNED = 'ReAssigned';  
    CONST ESTIMATE = 'Estimate';
    CONST FINALPRICING = 'Final Pricing';
    CONST FINALPROPOSAL = 'Final Proposal';
    CONST AGREEMENT = 'Agreement';     

    public static function getModuleClassName()
    {
        return 'ApprovalProcessModule';
    }

            public static function getDeptRefById($id) {
                    return self::makeModels(ZurmoRedBean::find('approvalprocess', "id =:id", array(':id' => $id)));
            }

    public static function canSaveMetadata()
    {
        return true;
    }

            protected function beforeSave() {
                    if (parent::beforeSave()) {

                            /*$dt = date("Y-m-d H:i:s");
                            $this->opportunity_id = 5636;
                            $this->action = 1;				
                            $this->date = $dt;
                            $this->status = 3;
                            $this->assignedto = 4;
                            $this->actualapprover = 5;
                            $this->overallstatus = 6;*/

                            return true;
                    } else {
                            return false;
                    }
            }

    public static function getDefaultMetadata()
    {
        $metadata = parent::getDefaultMetadata();
        $metadata[__CLASS__] = array(
            'members' => array(
                                'action',
                                'date',
                                'comments',
                                'name',
                                //'jobScheduleApproval'
            ),           
            'relations' => array(
                'opportunity'       => array(static::HAS_ONE,   'Opportunity'),
                'agreement'       => array(static::HAS_ONE,   'Agreement'),
                'assignedto'       => array(static::HAS_ONE,   'User',  static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'assignedto'),
                'actualapprover'       => array(static::HAS_ONE,   'User',  static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'actualapprover'),
                'actualgmapprover'       => array(static::HAS_ONE,   'User',  static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'actualgmapprover'),
                'Status' => array(static::HAS_ONE,   'OwnedCustomField', static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'Status'),
                'overallstatus' => array(static::HAS_ONE,   'OwnedCustomField', static::NOT_OWNED, static::LINK_TYPE_SPECIFIC, 'overallstatus'),

                ),
            'rules' => array(					
                array('action', 'type', 'type' => 'string'),
                array('name', 'type', 'type' => 'string'),
                array('comments', 'length', 'max'   => 100),
                array('date', 'type', 'type' => 'datetime'),
                array('comments', 'type', 'type'  => 'string'),
                array('comments', 'length', 'max'   => 200),    
                //array('jobScheduleApproval', 'type',      'type' => 'boolean'),                
            ),
            'elements' => array(
                'comments'   => 'TextArea',   
                'name'   => 'Text',   
                'opportunity' => 'Opportunity',
                'agreement' => 'Agreement',
                'assignedto' => 'User',
                'actualapprover' => 'User',
                'actualgmapprover' => 'User',                
                'Status' => 'DropDown',
                'overallstatus' => 'DropDown',
                //'jobScheduleApproval' => 'CheckBox',
            ),
            'customFields' => array(
                'Status'		      => 'APStatus',
                'overallstatus' 	      => 'OAStatus',
            ),
            'defaultSortAttribute' => 'createdDateTime',
            'noAudit' => array(
            ),
        );
        return $metadata;
    }

    public static function isTypeDeletable()
    {
        return true;
    }

    protected static function translatedAttributeLabels($language)
    {
        $params = LabelUtil::getTranslationParamsForAllModules();
        return array_merge(parent::translatedAttributeLabels($language),
            array(
                'opportunity_id' => Zurmo::t('ApprovalProcessModule', 'Opportunity',  $params, null, $language),
                'agreement_id' => Zurmo::t('ApprovalProcessModule', 'Agreement',  $params, null, $language),
                'action' => Zurmo::t('ApprovalProcessModule', 'Action',  $params, null, $language),
                'date' => Zurmo::t('ApprovalProcessModule', 'Date',  $params, null, $language),
                'Status' => Zurmo::t('ApprovalProcessModule', 'Status',  $params, null, $language),                    
                'assignedto' => Zurmo::t('ApprovalProcessModule', 'Assigned To',  $params, null, $language),
                'actualapprover' => Zurmo::t('ApprovalProcessModule', 'Actual Approver',  $params, null, $language),
                'actualgmapprover' => Zurmo::t('ApprovalProcessModule', 'Actual Approver',  $params, null, $language),
                'comments' => Zurmo::t('ApprovalProcessModule', 'Comments',  $params, null, $language),
                'overallstatus' => Zurmo::t('ApprovalProcessModule', 'Overall Status',  $params, null, $language),
                'name' => Zurmo::t('ApprovalProcessModule', 'Name',  $params, null, $language),
            )
        );
    }

    //For getting the approvals of the opportunity
    public static function getAllAppProcess($id) {
            return self::makeModels(ZurmoRedBean::find('approvalprocess', "opportunity_id =:id ORDER BY id DESC", array(':id' => $id)));
    }
    //For getting the approvals of the Agreement
    public static function getAllAppProcessForAgmnt($id) {
            return self::makeModels(ZurmoRedBean::find('approvalprocess', "agreement_id =:id ORDER BY id DESC", array(':id' => $id)));
    }
}
?>
