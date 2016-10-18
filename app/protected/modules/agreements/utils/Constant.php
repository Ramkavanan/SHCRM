<?php

/**
 * Class utilized For Constants Only
 * 
 * @author Thamodaran.K 
 */
class Constant {

    const CURRENCY = '$';
    const GM = 'GM';
    const OPPORTUNITYMODULEID = 'opportunities';
    const CATALOGMANAGER = 'Catalog Manager';
    
    /**
     * For Job Scheduling
     */
    
    const SCHEDULED = 'Scheduled';
    const UNSCHEDULED = 'Unscheduled';
    /**
     * For Opportunity will be archived
     */
    const ARCHIVE = 'archived';    //Also used in job scheduling.
    
    /**
     * For Opportunity Record Types
     */
    const RECURRING = 'Recurring Final';
    const PROJECT = 'Project Final';

    /**
     * For Opportunity Sales Stages
     */
    const QUALIFICATIONANDEDUCATION = 'Qualification and Education';
    const CONCEPTANDVERIFICATION = 'Concept Verification';
    const WON = 'Won';
    const LOST = 'Lost';
    const ONHOLD = 'On-Hold';
    const KICKOFF = 'Kick-Off';
    const PROPOSAL = 'Proposal';
    const PROSPECTING = 'Prospecting';
    //not to use QUALIFICATIONANDEDUCATION
    const CONSULTING = 'Consulting';

    /**
     * For Opportunity Sales Stages And used in Approval Process
     */
    CONST SOLUTIONPHASE = 'Solution Phase';
    CONST ESTIMATE = 'Estimate';
    CONST FINALPRICING = 'Final Pricing';
    CONST FINALPROPOSAL = 'Final Proposal';
    CONST AGREEMENT = 'Agreement';

    /**
     * For Agreement Record Types
     */
    const RECURRINGAGREEMENT = 'Recurring Agreement';
    const PROJECTAGREEMENT = 'Project Agreement';

    /**
     * For Agreement Status
     */
    const DRAFTAGREEMENT = 'Draft';    //Also used in cloned Agmnt.
    const ACTIVEAGREEMENT = 'Active';    //Also used in job scheduling and cloned Agmnt.
    const PENDINGAGREEMENT = 'Pending';
    const COMPLETEDAGREEMENT = 'Completed';    //Also used in job scheduling and cloned Agmnt.
    const DEACTIVATED = 'Deactivated';    //Also used in job scheduling and cloned Agmnt.
    const CLOSED = 'Closed';    //Also used in job scheduling and cloned Agmnt.

    /**
     * For Agreement Type
     */
    const OPPORTUNITYAGREEMENT = 'Opportunity';
    const CLONEAGREEMENT = 'Clone';

    /**
     * For costofgoodssold in CostBook
     */
    const LABOUR = 'Labor';
    const EQUIPMENT = 'Equipment';
    const MATERIAL = 'Material';
    const SUBCONTRACT = 'Subcontractor';
    const ASSEMBLY = 'Assembly';
    const OTHER = 'Other';

    /**
     * For Approval Process
     */
    CONST APPROVED = 'Approved';
    CONST REJECTED = 'Rejected';
    CONST RECALLED = 'ReCalled';
    CONST PENDING = 'Pending';
    CONST SUBMITTED = 'Submitted';
    CONST REASSIGNED = 'ReAssigned';
    
    //Default From Email Address
    CONST DEFAULT_FROM_EMAIL = 'notification@vertware.net';

    /**
     * For Create Button in Create Meeting 
     */
    const AGREEMENTS = 'agreements';

    /**
     * For Adding Recently viewed count( Default 10 + RECENTLY_VIEWED_ADD_COUNT)
     */
    const RECENTLY_VIEWED_ADD_COUNT = '10';

}

?>