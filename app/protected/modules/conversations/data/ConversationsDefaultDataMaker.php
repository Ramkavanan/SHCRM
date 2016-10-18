<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ConversationsDefaultDataMaker extends DefaultDataMaker {

    public function make() {
        $values = array(
            Zurmo::t('CustomField', 'Initial meeting'),
            Zurmo::t('CustomField', 'Concept verification'),
            Zurmo::t('CustomField', 'Proposal'),
            Zurmo::t('CustomField', 'CARE calls'),
            /* For the Import data
            Zurmo::t('CustomField', 'Agreement'),
            Zurmo::t('CustomField', 'Initial Meeting'),
            Zurmo::t('CustomField', 'Proposal'),
            Zurmo::t('CustomField', 'initial meeting'),
            Zurmo::t('CustomField', 'Annual Employee Review'),
            Zurmo::t('CustomField', 'follow up'),
            Zurmo::t('CustomField', '90 Day Review'),
            Zurmo::t('CustomField', 'Employee Review'),
            Zurmo::t('CustomField', 'follow up on landscaping'),
            Zurmo::t('CustomField', 'Call'),
            Zurmo::t('CustomField', 'Call with Curt'),
            Zurmo::t('CustomField', 'Deactivation'),
            Zurmo::t('CustomField', 'cancel services'),
            Zurmo::t('CustomField', 'CV'),
            Zurmo::t('CustomField', 'Email: follow up'),
            Zurmo::t('CustomField', 'lawn care'),
            Zurmo::t('CustomField', 'lawn services for son'),
            Zurmo::t('CustomField', 'landscaping'),
            Zurmo::t('CustomField', 'Email response'),
            Zurmo::t('CustomField', 'Agreement Assignment'),
            Zurmo::t('CustomField', 'Tom is back from FLA/post'),
            Zurmo::t('CustomField', 'decision - email'),
            Zurmo::t('CustomField', 'lawn care email thread'),
            Zurmo::t('CustomField', 'Lawn treatment'),
            Zurmo::t('CustomField', 'Agreement Update'),
            Zurmo::t('CustomField', 'Agreement Up Date'),
            Zurmo::t('CustomField', 'Start in April instead of May'),
            Zurmo::t('CustomField', 'Approve Agreement'),
            Zurmo::t('CustomField', 'Cancel Services'),
            Zurmo::t('CustomField', 'patio repair/forego SCU'),
            Zurmo::t('CustomField', 'Return Call'),
            Zurmo::t('CustomField', 'Activate Agreement'),
            Zurmo::t('CustomField', 'Deactivate Agreement'),
            Zurmo::t('CustomField', 'Agreement Deactivation'),
            Zurmo::t('CustomField', 'Estimate Notes'),
            Zurmo::t('CustomField', 'Follow up'),
            Zurmo::t('CustomField', 'materials'),
            Zurmo::t('CustomField', '2015 adjustment'),
            Zurmo::t('CustomField', 'emails about service'),
            Zurmo::t('CustomField', 'Install ID/addr post'),
            Zurmo::t('CustomField', 'Fert treatment'),
            Zurmo::t('CustomField', 'Quote for Reseed and Leveling'),
            Zurmo::t('CustomField', 'cancel serives'),
            Zurmo::t('CustomField', 'assigned to Kasey'),
            Zurmo::t('CustomField', 'Call to schedule initial meeting'),
            Zurmo::t('CustomField', 'gave to Kasey'),
            Zurmo::t('CustomField', 'Proposal Approval'),
            Zurmo::t('CustomField', 'mulch'),
            Zurmo::t('CustomField', 'services'),
            Zurmo::t('CustomField', 'Call to restart lawn tx'),
            Zurmo::t('CustomField', 'Deactivate Account'),
            Zurmo::t('CustomField', 'Estimating'),
            Zurmo::t('CustomField', 'my lawn quote'),
            Zurmo::t('CustomField', 'contract'),
            Zurmo::t('CustomField', 'SCU'),
            Zurmo::t('CustomField', 'proposal'),
            Zurmo::t('CustomField', 'Estimate'),
            Zurmo::t('CustomField', 'Updated Proposal Needed'),
            Zurmo::t('CustomField', 'Picture and Estimate'),
            Zurmo::t('CustomField', 'Estimate to move a large tree'),
            Zurmo::t('CustomField', 'Left Message'),
            Zurmo::t('CustomField', 'Email: checking in'),
            Zurmo::t('CustomField', 'Projects'),
            Zurmo::t('CustomField', 'Account Owner'),
            Zurmo::t('CustomField', 'Agreement Activation'),
            Zurmo::t('CustomField', 'PPC'),
            Zurmo::t('CustomField', 'grub curative'),
            Zurmo::t('CustomField', 'follow up on SOIL forms'),
            Zurmo::t('CustomField', 'Proposal/Agreement Approval'),
            Zurmo::t('CustomField', 'Quote for grading and seeding'),
            Zurmo::t('CustomField', 'project questions'),
            Zurmo::t('CustomField', 'projects'),
            Zurmo::t('CustomField', 'patio design'),
            Zurmo::t('CustomField', 'plant replacements'),
            Zurmo::t('CustomField', 'design/estimate needed'),
            Zurmo::t('CustomField', 'follow up on SOIL forms #2'),
            Zurmo::t('CustomField', 'Email: lawn repair'),
            Zurmo::t('CustomField', 'no email or phone provided-asked Kay Penix'),
            Zurmo::t('CustomField', 'Steves response'),
            Zurmo::t('CustomField', 'trimming'),
            Zurmo::t('CustomField', 'Beech Tree'),
            Zurmo::t('CustomField', 'Postpone the proposed work'),
            Zurmo::t('CustomField', 'Answer to CV'),
            Zurmo::t('CustomField', 'spraying'),
            Zurmo::t('CustomField', 'concern about weeds'),
            Zurmo::t('CustomField', 'CV budget'),
            Zurmo::t('CustomField', 'tree spray'),
            Zurmo::t('CustomField', 'deck construction'),
            Zurmo::t('CustomField', 'Follow Up'),
            Zurmo::t('CustomField', 'Other'),
            Zurmo::t('CustomField', 'wall follow up'),
            Zurmo::t('CustomField', 'INCLUDE SEEDING'),
            Zurmo::t('CustomField', 'Agreement Approval'),
            Zurmo::t('CustomField', 'Annuals'),
            Zurmo::t('CustomField', 'follow up on patio'),
            Zurmo::t('CustomField', 'Site Measurement'),
            Zurmo::t('CustomField', 'update price'),
            Zurmo::t('CustomField', 'Pond Treatments'),
            Zurmo::t('CustomField', 'Approve Addendum'),
            Zurmo::t('CustomField', 'question & price for entire lawn'),
            Zurmo::t('CustomField', 'Estimate Completed'),
            Zurmo::t('CustomField', 'add mowing to estimate'),
            Zurmo::t('CustomField', 'Activate the agreement'),
            Zurmo::t('CustomField', 'service concerns'),
            Zurmo::t('CustomField', 'Approve Estimate'),
            Zurmo::t('CustomField', 'Called to Schedule Meeting'),
            Zurmo::t('CustomField', 'Additional Landscape'),
            Zurmo::t('CustomField', 'follow up on Christmas lights'),
            Zurmo::t('CustomField', 'Review Estimate'),
            Zurmo::t('CustomField', 'Quote'),
            Zurmo::t('CustomField', 'Estimate for project'),
            Zurmo::t('CustomField', 'Budget Studies Completed'),
            Zurmo::t('CustomField', 'grubs and fall lawn conversation'),
            Zurmo::t('CustomField', 'Follow up on patio'),
            Zurmo::t('CustomField', 'Email: patio promotion/follow up'),
            Zurmo::t('CustomField', 'Leaf Removal'),
            Zurmo::t('CustomField', 'check'),
            Zurmo::t('CustomField', 'limestone/sand'),
            Zurmo::t('CustomField', 'new trees'),
            Zurmo::t('CustomField', 'Review Update'),
            Zurmo::t('CustomField', 'patio follow up'),
            Zurmo::t('CustomField', 'email discussion'),
            Zurmo::t('CustomField', 'Maple Tree'),
            Zurmo::t('CustomField', 'activation'),
            Zurmo::t('CustomField', 'Agreement Activate'),
            Zurmo::t('CustomField', 'Quote for snow removal & Mowing'),
            Zurmo::t('CustomField', 'Activation'),
            Zurmo::t('CustomField', 'Pricing'),
            Zurmo::t('CustomField', 'Arb pruning'),
            Zurmo::t('CustomField', 'outstanding invoice'),
            Zurmo::t('CustomField', 'revisions'),
            Zurmo::t('CustomField', 'appointment'),
            Zurmo::t('CustomField', 'Fall Clean Up?'),
            Zurmo::t('CustomField', 'Cancel appointment'),
            Zurmo::t('CustomField', 'sent follow up letter'),
            Zurmo::t('CustomField', 'Email: Jim Campbell - Whirlpool Presentation'),
            Zurmo::t('CustomField', 'lawn services'),
            Zurmo::t('CustomField', 'Info Update'),
            Zurmo::t('CustomField', 'Cancel services'),
            Zurmo::t('CustomField', 'estimate needed'),
            Zurmo::t('CustomField', 'Landscape lights'),
            Zurmo::t('CustomField', 'Adjustment'),
            Zurmo::t('CustomField', 'Lead'),
            Zurmo::t('CustomField', 'Call Back'),
            Zurmo::t('CustomField', 'Follow up on the project'),
            Zurmo::t('CustomField', 'yard repair'),
            Zurmo::t('CustomField', 'Return call'),
            Zurmo::t('CustomField', 'flower bed'),
            Zurmo::t('CustomField', 'Renewal Proposal'),
            Zurmo::t('CustomField', 'lawn treatments'),
            Zurmo::t('CustomField', 'mowing'),
            Zurmo::t('CustomField', 'Spring Aeration'),
            Zurmo::t('CustomField', 'MEASUREMENTS'),
            Zurmo::t('CustomField', 'Zoning Inspector'),
            Zurmo::t('CustomField', 'Follow up- Forum 2 Project'),
            Zurmo::t('CustomField', 'Red Mulch'),
            Zurmo::t('CustomField', 'Material'),
            Zurmo::t('CustomField', 'Resolution'),
            Zurmo::t('CustomField', 'Follow Up Care Call on the SCU'),
            Zurmo::t('CustomField', 'early spring treatment'),
            Zurmo::t('CustomField', 'Stop at house'),
            Zurmo::t('CustomField', 'f/u on SCU'),
            Zurmo::t('CustomField', 'pine tree staking & garden cleanup'),
            Zurmo::t('CustomField', 'Dave- Care Call'),
            Zurmo::t('CustomField', 'Drop off info'),
            Zurmo::t('CustomField', 'Estimate completed'),
            Zurmo::t('CustomField', 'Start Date for Project'),
            Zurmo::t('CustomField', 'MEASUREMENT'),
            Zurmo::t('CustomField', 'SOW'),
            Zurmo::t('CustomField', 'MEARSUREMENT'),
            Zurmo::t('CustomField', 'Grass Seed'),
            Zurmo::t('CustomField', 'Follow Up- Sab Nevada Project'),
            Zurmo::t('CustomField', 'follow up on FCU'),
            Zurmo::t('CustomField', 'Lawn Repair'),
            Zurmo::t('CustomField', 'Scope of work'),
            Zurmo::t('CustomField', 'weeds & flagpole ring'),
            Zurmo::t('CustomField', 'Follow up- Winter services'),
            Zurmo::t('CustomField', 'CV the Budget'),
            Zurmo::t('CustomField', 'A/O & FCU'),
            Zurmo::t('CustomField', 'Material Schedule'),
            Zurmo::t('CustomField', 'Quote for irrigation'),
            Zurmo::t('CustomField', 'Note sent with Payment'),
            Zurmo::t('CustomField', 'A/O'),
            Zurmo::t('CustomField', 'Meeting'),
            Zurmo::t('CustomField', 'House Sold'),
            Zurmo::t('CustomField', 'Site Visit'),
            Zurmo::t('CustomField', 'Hose Repair and Site Visit'),
            Zurmo::t('CustomField', 'dethatching'),
            Zurmo::t('CustomField', 'Stump grinding'),
            Zurmo::t('CustomField', 'Individual GPM'),
            Zurmo::t('CustomField', 'Agreement Signing'),
            Zurmo::t('CustomField', 'call back'),
            Zurmo::t('CustomField', 'Care call Meeting'),
            Zurmo::t('CustomField', 'Discuss Proposal'),
            Zurmo::t('CustomField', 'Quote for bed install'),
            Zurmo::t('CustomField', 'Care Call and Renewal'),
            Zurmo::t('CustomField', 'Follow up on the summer service proposal'),
            Zurmo::t('CustomField', 'Discuss the Proposal'),
            Zurmo::t('CustomField', 'Follow up /Care Call'),
            Zurmo::t('CustomField', 'Follow up on the proposl from 2015'),
            Zurmo::t('CustomField', 'Change services'),
            Zurmo::t('CustomField', 'follow up on work'),
            Zurmo::t('CustomField', 'Spring Letter'),
            Zurmo::t('CustomField', 'Spring Follow up'),
            Zurmo::t('CustomField', 'quote for SCU'),
            Zurmo::t('CustomField', 'Follow up on service'),
            Zurmo::t('CustomField', 'uneven backyard'),
            Zurmo::t('CustomField', 'review call notes from 5/2/14'),
            Zurmo::t('CustomField', 'follow up-14min meeting'),
            Zurmo::t('CustomField', 'commercial intro letter 2'),
            Zurmo::t('CustomField', 'Send Letter re: organic lawn care'),
            Zurmo::t('CustomField', 'Follow up to Proposal'),
            Zurmo::t('CustomField', 'Send Letter'),
            Zurmo::t('CustomField', 'follow up to proposal if no word yet'),
            Zurmo::t('CustomField', 'Proposal follow up'),
            Zurmo::t('CustomField', 'L/B'),
            Zurmo::t('CustomField', 'cut wire'),
            Zurmo::t('CustomField', 'kickoff notes'),
            Zurmo::t('CustomField', 'payment'),
            Zurmo::t('CustomField', 'SCU & mowing'),
            Zurmo::t('CustomField', 'discuss & estimate maint services'),
            Zurmo::t('CustomField', 'create opportunity'),
            Zurmo::t('CustomField', 'Mowing'),
            Zurmo::t('CustomField', 'Damage to bush'),
            Zurmo::t('CustomField', 'plants'),
            Zurmo::t('CustomField', 'lawn repair'),
            Zurmo::t('CustomField', 'eft payment'),
            Zurmo::t('CustomField', 'mowing/lawn care'),
            Zurmo::t('CustomField', 'tree trimmer'),
            Zurmo::t('CustomField', 'edge/mulch'),
            Zurmo::t('CustomField', 'pruning'),
            Zurmo::t('CustomField', 'pond'),
            Zurmo::t('CustomField', 'Weeds & dead bush'),
            Zurmo::t('CustomField', 'FYI'),
            Zurmo::t('CustomField', 'weeds in beds'),
            Zurmo::t('CustomField', 'pruning - Fairground St'),
            Zurmo::t('CustomField', 'Potholes'),
            Zurmo::t('CustomField', 'General Clean Up'),
            Zurmo::t('CustomField', 'Estimate for filling/seeding'),
            Zurmo::t('CustomField', 'a large weed'),
            Zurmo::t('CustomField', 'bagging'),
            Zurmo::t('CustomField', 'Assign Estimate'),
            Zurmo::t('CustomField', 'pond fountain'),
            Zurmo::t('CustomField', 'Leaves'),
            Zurmo::t('CustomField', 'Christmas Lighting'),
            Zurmo::t('CustomField', 'call'),
            Zurmo::t('CustomField', 'salt'),
            Zurmo::t('CustomField', 'billing address'),
            Zurmo::t('CustomField', 'Cancel SCU'),
            Zurmo::t('CustomField', 'Hoover Residence Landscape Lighting Proposal'),
            Zurmo::t('CustomField', 'Irrigation/Watering'),
            Zurmo::t('CustomField', 'Final Estimate'),
            Zurmo::t('CustomField', 'drainage/warranty wrap up'),
            Zurmo::t('CustomField', 'Design needed'),
            Zurmo::t('CustomField', 'update design/estimate'),
            Zurmo::t('CustomField', 'clarifications'),
            Zurmo::t('CustomField', 'site survey/design/estimate needed'),
            Zurmo::t('CustomField', 'Recommend new plants'),
            Zurmo::t('CustomField', '3D Image'),
            Zurmo::t('CustomField', 'Updated estimate needed'),
            Zurmo::t('CustomField', 'plan & estimate edits'),
            Zurmo::t('CustomField', 'update estimate'),
            Zurmo::t('CustomField', 're-estimate needed'),
            Zurmo::t('CustomField', 'wall'),
            Zurmo::t('CustomField', 'Design /Estimate Needed'),
            Zurmo::t('CustomField', 'Design and estimate Needed'),
            Zurmo::t('CustomField', 'Design and Estimate Needed'),
            Zurmo::t('CustomField', 'seeding'),
            Zurmo::t('CustomField', 'start date'),
            Zurmo::t('CustomField', 'schedule work'),
            Zurmo::t('CustomField', 'updated design/estimate needed'),
            Zurmo::t('CustomField', 'Re-estimate'),
            Zurmo::t('CustomField', 'final changes & estimate'),
            Zurmo::t('CustomField', 'Estimate Needed'),
            Zurmo::t('CustomField', 'Adjust Estimate'),
            Zurmo::t('CustomField', 'enter MLO'),
            Zurmo::t('CustomField', 'Review Updated Estimate'),
            Zurmo::t('CustomField', 'Review GPMs'),
            Zurmo::t('CustomField', 'Estimate Adjustment'),
            Zurmo::t('CustomField', 'prepare render with measurements'),
            Zurmo::t('CustomField', 'Picture Location'),
            Zurmo::t('CustomField', 'CV the SOW'),
            Zurmo::t('CustomField', 'send to Belgard'),
            Zurmo::t('CustomField', 'account credit'),
            Zurmo::t('CustomField', 'cancel lawn treatments'),
            Zurmo::t('CustomField', 'weeds'),
            Zurmo::t('CustomField', 'Birthday'),
            Zurmo::t('CustomField', 'new seeding & weeds'),
            Zurmo::t('CustomField', 'leaves'),
            Zurmo::t('CustomField', 'Reset PTO'),
            Zurmo::t('CustomField', 'grub control'),
            Zurmo::t('CustomField', 'L/B & project'),
            Zurmo::t('CustomField', 'fert schedule'),
            Zurmo::t('CustomField', 'change treatments & lawn rolling'),
            Zurmo::t('CustomField', 'Training'),
            Zurmo::t('CustomField', 'prepay invoice'),
            Zurmo::t('CustomField', 'aeration'),
            Zurmo::t('CustomField', 'statement'),
            Zurmo::t('CustomField', 'weed spray'),
            Zurmo::t('CustomField', 'treatments'),
            Zurmo::t('CustomField', 'update invoice & resent'),
            Zurmo::t('CustomField', 'service'),
            Zurmo::t('CustomField', 'Quote for lawn treatments'),
            Zurmo::t('CustomField', 'quote for Aeration'),
            Zurmo::t('CustomField', 'collections call'),
            Zurmo::t('CustomField', 'Detail and Pricing'),
            Zurmo::t('CustomField', 'Kick Off'),
            Zurmo::t('CustomField', 'Agreement'),
            Zurmo::t('CustomField', 'Intro Meeting'),
            Zurmo::t('CustomField', 'Send Proposal'),
            Zurmo::t('CustomField', 'Prospecting'),
            Zurmo::t('CustomField', 'quote for edging and mulch'),
            Zurmo::t('CustomField', 'Proposal Meeting'),
            Zurmo::t('CustomField', 'Left message'),
            Zurmo::t('CustomField', 'Sent Text message'),
            Zurmo::t('CustomField', 'Call ball'),
            Zurmo::t('CustomField', 'Send E-mail'),
            Zurmo::t('CustomField', 'additional note'),
            Zurmo::t('CustomField', 'Stop In'),
            Zurmo::t('CustomField', 'Power Washing'),
            Zurmo::t('CustomField', 'Feb Event'),
            Zurmo::t('CustomField', 'Email Intro'),
            Zurmo::t('CustomField', 'Think of Us Email'),
            Zurmo::t('CustomField', '101 Event Invite'),
            Zurmo::t('CustomField', 'Event Email 3'),
            Zurmo::t('CustomField', 'Event Reminder Email'),
            Zurmo::t('CustomField', 'Event Confirmation'),
            Zurmo::t('CustomField', 'Event Final Email'),
            Zurmo::t('CustomField', 'Event F/U'),
            Zurmo::t('CustomField', 'Never To Early'),
            Zurmo::t('CustomField', 'Never Too Early'),
            Zurmo::t('CustomField', 'Feb Event FU'),
            Zurmo::t('CustomField', 'Feb Event Confirm 2'),
            Zurmo::t('CustomField', 'Event Survey'),
            Zurmo::t('CustomField', 'Plan Ahead'),
            Zurmo::t('CustomField', 'Weigh Your Options'),
            Zurmo::t('CustomField', 'Text'),
            Zurmo::t('CustomField', 'mite treatment'),
            Zurmo::t('CustomField', 'dandelions'),
            Zurmo::t('CustomField', 'New Hire'),
            Zurmo::t('CustomField', 'paver patio'),
            Zurmo::t('CustomField', 'Kasey'),
            Zurmo::t('CustomField', 'bagworms'),
            Zurmo::t('CustomField', 'acct on hold'),
            Zurmo::t('CustomField', 'extra spraying'),
            Zurmo::t('CustomField', 'complaint'),
            Zurmo::t('CustomField', 'Add Services - Perimeter Pest'),
            Zurmo::t('CustomField', 'Peremeter PEst'),
            Zurmo::t('CustomField', 'wilted plants after spray'),
            Zurmo::t('CustomField', 'grubs'),
            Zurmo::t('CustomField', 'return call'),
            Zurmo::t('CustomField', 'Weeds'),
            Zurmo::t('CustomField', 'tree staking & garden cleanup'),
            Zurmo::t('CustomField', 'Wall'),
            Zurmo::t('CustomField', 'bagworm treatment'),
            Zurmo::t('CustomField', 'Voice Mail'),
            Zurmo::t('CustomField', 'blue spruce trees'),
            Zurmo::t('CustomField', 'Call - Treatments'),
            Zurmo::t('CustomField', 'eft pymt'),
            Zurmo::t('CustomField', 'bugs'),
            Zurmo::t('CustomField', 'Ants'),
            Zurmo::t('CustomField', 'Criss Cross Aeration'),
            Zurmo::t('CustomField', 'FCA'),
            Zurmo::t('CustomField', 'aeration/overseed'),
            Zurmo::t('CustomField', 'collections'),
            Zurmo::t('CustomField', 'Collections Call'),
            Zurmo::t('CustomField', 'co'),
            Zurmo::t('CustomField', 'Collections call'),
            Zurmo::t('CustomField', 'addition to lawn'),
            Zurmo::t('CustomField', 'Prepay FC Discount'),
            Zurmo::t('CustomField', 'Review + increase'),
            Zurmo::t('CustomField', 'Year End Review'),
            Zurmo::t('CustomField', 'ReHire'),
            Zurmo::t('CustomField', 'payment plan'),
            Zurmo::t('CustomField', 'lawn condition'),
            Zurmo::t('CustomField', 'Broad leaf weeds in lawn'),
            Zurmo::t('CustomField', 'appointmentl'),
            Zurmo::t('CustomField', 'lawn treatment'),
            Zurmo::t('CustomField', 'account'),
            Zurmo::t('CustomField', 'Cancel Snow Services'),
            Zurmo::t('CustomField', 'quote'),
            Zurmo::t('CustomField', 'Refusal to pay invoice'),
            Zurmo::t('CustomField', 'colletions call'),
            Zurmo::t('CustomField', 'New Hire (Rehire)'),
            Zurmo::t('CustomField', 'Might Cancel'),
            Zurmo::t('CustomField', 'prepay PPC'),
            Zurmo::t('CustomField', 'L/B & EFT'),
            Zurmo::t('CustomField', 'address'),
            Zurmo::t('CustomField', 'switch to LB EFT'),
            Zurmo::t('CustomField', 'grass seed'),
            Zurmo::t('CustomField', 'Blue Spruce Trees'),
            Zurmo::t('CustomField', 'cancel services/EFT'),
            Zurmo::t('CustomField', 'quote for patio restoration'),
            Zurmo::t('CustomField', 'Initial Meeting'),
            Zurmo::t('CustomField', '101 Event FU'),
            Zurmo::t('CustomField', '101 Survey FU'),
            Zurmo::t('CustomField', '2nd Opinion Email'),
            Zurmo::t('CustomField', 'A&O Quote'),
            Zurmo::t('CustomField', 'Assign Follow Up'),
            Zurmo::t('CustomField', 'Assign Task'),
            Zurmo::t('CustomField', 'Call To schedule'),
            Zurmo::t('CustomField', 'Call-JL Dowell'),
            Zurmo::t('CustomField', 'Called back in'),
            Zurmo::t('CustomField', 'Channel Partner Connection'),
            Zurmo::t('CustomField', 'Channel Partner Mtg'),
            Zurmo::t('CustomField', 'Coaching'),
            Zurmo::t('CustomField', 'Congrats Email'),
            Zurmo::t('CustomField', 'Congratulation FU'),
            Zurmo::t('CustomField', 'Congratulations'),
            Zurmo::t('CustomField', 'contact for additional work'),
            Zurmo::t('CustomField', 'EBook'),
            Zurmo::t('CustomField', 'estimate'),
            Zurmo::t('CustomField', 'Event Email Invite'),
            Zurmo::t('CustomField', 'Event Invite Email'),
            Zurmo::t('CustomField', 'Event Reminder'),
            Zurmo::t('CustomField', 'F/U - Call'),
            Zurmo::t('CustomField', 'Feb Event F/U'),
            Zurmo::t('CustomField', 'Feb Event Promo'),
            Zurmo::t('CustomField', 'FU'),
            Zurmo::t('CustomField', 'Give JTs info'),
            Zurmo::t('CustomField', 'Lead Followup'),
            Zurmo::t('CustomField', 'N/A'),
            Zurmo::t('CustomField', 'Never Early Too'),
            Zurmo::t('CustomField', 'Never to Early'),
            Zurmo::t('CustomField', 'Please call'),
            Zurmo::t('CustomField', 'Power Washing 2'),
            Zurmo::t('CustomField', 'Power Washing Info'),
            Zurmo::t('CustomField', 'PW Promo Flyer'),
            Zurmo::t('CustomField', 'Snow Intro'),
            Zurmo::t('CustomField', 'Think of Us in 2016'),
            Zurmo::t('CustomField', 'Weigh You Options'),
            Zurmo::t('CustomField', 'Weigh Your Email'),
            Zurmo::t('CustomField', 'Voles'),
            Zurmo::t('CustomField', 'Lawn Care'),
            Zurmo::t('CustomField', 'Lawn'),
            Zurmo::t('CustomField', 'Follow Up'),
            Zurmo::t('CustomField', 'Left Message'),
            Zurmo::t('CustomField', 'Support'),
            Zurmo::t('CustomField', 'Account Management'),
            Zurmo::t('CustomField', 'Call'),
            Zurmo::t('CustomField', 'Send Proposal'),
            Zurmo::t('CustomField', 'Meeting'),
            Zurmo::t('CustomField', 'SPRAY'),
            Zurmo::t('CustomField', 'update Contact info'),
            Zurmo::t('CustomField', 'APPROVAL'),
            Zurmo::t('CustomField', 'Deactivate'),
            Zurmo::t('CustomField', 'Other'),
            Zurmo::t('CustomField', 'Site Visit'),
            Zurmo::t('CustomField', 'Change Value'),
            Zurmo::t('CustomField', 'Concept Verification'),
            Zurmo::t('CustomField', 'Initial Meeting'),
            Zurmo::t('CustomField', 'Proposal'),
            Zurmo::t('CustomField', 'Prospecting'),
            Zurmo::t('CustomField', 'SPCU'),
            Zurmo::t('CustomField', 'Deers'),
            Zurmo::t('CustomField', 'Ainsworth Contract'),
            Zurmo::t('CustomField', 'Ferero, Concept Verification Meeting'),
            Zurmo::t('CustomField', 'Quitting'),
            Zurmo::t('CustomField', 'erie airport'),
            Zurmo::t('CustomField', 'proposal'),
            Zurmo::t('CustomField', 'landscape bed'),
            Zurmo::t('CustomField', 'nw proposal'),
            Zurmo::t('CustomField', 'ret. wall site work'),
            Zurmo::t('CustomField', 'retaining wall'),
            Zurmo::t('CustomField', 'Email: meeting'),
            Zurmo::t('CustomField', 'Decision Maker'),
            Zurmo::t('CustomField', 'introduction call'),
            Zurmo::t('CustomField', 'Client CARE'),
            Zurmo::t('CustomField', 'Big corporation and Trace Lawn and Landscaping'),
            Zurmo::t('CustomField', 'No Message'),
            Zurmo::t('CustomField', 'Visit'), */
        );
        static::makeCustomFieldDataByValuesAndDefault('SubjectTypes', $values);
    }
}

?>
