<?php

/**
 * Description of AgreementTrackingAddView
 *
 * @author 
 */

class JobSchedulingUtils {
    
    public function makeJobScheduleStep1View($agreementId) {
        $content = '<div>';
        $content .= JobSchedulingCreateView::renderScripts();
        $agreement = Agreement::getById(intval($agreementId));
        $content .= '<div class="wrapper">
      <h1><span class="truncated-title" threedots="Create JobScheduling"><span class="ellipsis-content">Create Job Scheduling</span></span></h1>
      <div class="wide double-column form">
         <form method="post" action="/app/index.php/agreementTracking/default/create?agreementId=3003" id="edit-form" onsubmit="js:return $(this).attachLoadingOnSubmit(&quot;edit-form&quot;)">
            <div style="display:none"><input type="hidden" name="YII_CSRF_TOKEN" value="9b75d8e35f7f7044e3e7ebe06b387659577b83db"></div>
            <div class="attributesContainer">
               <div class="left-column full-width">
                  <div class="panel">
                     <div class="panelTitle"></div>
                     <table class="form-fields double-column">
                        <colgroup>
                           <col class="col-0">
                           <col class="col-1">
                           <col class="col-2">
                        </colgroup>
                        <tbody>
                           <tr>
                              <th><label for="JobScheduling_agreement_id">Agreement Name<span class="required">*</span></label></th>
                              <td colspan="1">
                                 <input type="hidden" id="JobScheduling_agreement_name" name="JobScheduling_agreement_name" value="'. $agreement->id.'">
                                 <div class="has-model-select" style ="width:50%"><input type="text" name="Agreement_name" value="'. $agreement->name.'" id="Agreement_name" disabled ></div>
                                 <ul class="ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all" id="ui-id-3" tabindex="0" style="z-index: 11; display: none;"></ul>
                                 <div class="errorMessage" style="display:none;" id="JobScheduling_agreementName_validate">Crew Name cannot be blank.</div>
                              </td>
                           </tr>
                           <tr>
                              <th><label for="JobScheduling_name">Job Name<span class="required">*</span></label></th>
                              <td colspan="1">                                 
                                 <div class="has-model-select" style ="width:50%"><input type="text" name="JobScheduling[name]" value="" id="JobScheduling_name" ></div>
                                 <div class="errorMessage" style="display:none;" id="JobScheduling_name_validate">Job Name cannot be blank.</div>
                             </td>
                           </tr>
                           <tr>
                              <th><label for="JobScheduling_crewName">Crew Name<span class="required">*</span></label></th>
                              <td colspan="1">
                                 <div class="has-date-select" style ="width:50%">
                                    <input type="text" name="JobScheduling[crewName]" id="JobScheduling_crewName">
                                 </div>
                                 <div class="errorMessage" style="display:none;" id="JobScheduling_crewName_validate">Crew Name cannot be blank.</div>
                              </td>
                            </tr>
                           
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <div class="float-bar">
               <div class="view-toolbar-container clearfix dock">
                  <div class="form-toolbar"><a href="/app/index.php/agreements/default/details?id='.$agreementId.'" class="cancel-button" id="CancelLinkActionElement--23-yt2"><span class="z-label">Go Back</span></a><a href="#" return false;" class="attachLoading" name="save" id="saveAgreementTracking" onclick="javascript:createJobStep1(\''.$agreementId.'\', this);"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Next</span></a></div>
               </div>
            </div>
         </form>
         <div id="modalContainer-edit-form"></div>
      </div>
   </div> <div class="view-toolbar-container clearfix"> </div>';
        return $content;
    }
    
    public static function getDateByWeek($scheduleDate)
    {
        $j=1;
        for($k=0; $k<=11; $k++) {
            $forOdNextMonth= date('m-Y', strtotime("+$k month", strtotime($scheduleDate)));
            $dateArr = explode('-', $forOdNextMonth);
            $year = $dateArr[1];
            $month = $dateArr[0];
            
            $start_day_of_week = 1; // Monday
            // Total number of days in the given month.
            $num_of_days = date("t", mktime(0,0,0,$month,1,$year));
            // Count the number of times it hits $start_day_of_week.
            $num_of_weeks = 0;
            for($i=1; $i<=$num_of_days; $i++)
            {
                $day_of_week = date('w', mktime(0,0,0,$month,$i,$year));
                if($day_of_week==$start_day_of_week)
                {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $dayArr[$j]  = $year.'-'.$month.'-'.$day;
                    $j++;
                }
            } 
        }        
        return $dayArr;
    }

    //For getting the job schedule records which has no week days 
    public static function getJobScheduleNotUpdated(){            
      $query = "SELECT 'edit' isEdit,agreement_id agmtId,jobscheduling_id jobId,
                  group_concat(category_id), group_concat(week_no)
                  ,group_concat(CONCAT(category_id,'_week_',week_no,'=',value,'&') ORDER BY aj.id SEPARATOR ' ') scheduleData
                  FROM jobscheduling js
                  INNER JOIN agreementjobs aj ON aj.jobscheduling_id = js.id 
                  WHERE aj.week_day IS NULL AND YEAR(js.scheduledate) = YEAR(curdate())
                  GROUP BY js.id ORDER BY js.id DESC LIMIT 0,1";
      $result = ZurmoRedBean::getAll($query);
      return $result;
    }
}