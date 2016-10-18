$(document).ready(function(){
    $("label[for=Opportunity_account_id]").append("<span id=\"account_required\" class='required'>*</span>");
    $("#Opportunity_finalAmount_value").prop("readonly", true);
    $("#Opportunity_finalAmount_value").css({"background-color":"#efefef","width":"132%"}); 
    $("#OpportunityRecurringEditAndDetailsView .has-date-select").css("z-index","10");
    $("#OpportunityDetailsAndRelationsView_1667").css("display","none");    //For Thamodaran user in Dve (Temp Fix).
    $("#OpportunityDetailsAndRelationsView_1718").css("display","none");    //For Arvind Handu user in Dve (Temp Fix).
    $('.OpportunityProductsForOpportunityRelatedListView').css('display','none'); //To hide the opportunity product panel - Modified by Sundar P - 12-Sep-2016
    if($("#Opportunity_stage_value").val() == "Estimate"){
        $("label[for=Opportunity_estimator_id]").append("<span id=\"estimator_required\" class='required'>*</span>");
        $("label[for=Opportunity_gm_id]").append("<span id=\"gm_required\" class='required'>*</span>");
        $("label[for=Opportunity_estimatorApproval]").append("<span id=\"estimator_approval_required\" class='required'>*</span>");
    }

    $("#Opportunity_stage_value").on("change", function() {
        if($("#Opportunity_stage_value").val() == "Estimate"){
            $("label[for=Opportunity_estimator_id]").append("<span id=\"estimator_required\" class='required'>*</span>");
            $("label[for=Opportunity_gm_id]").append("<span id=\"gm_required\" class='required'>*</span>");
            $("label[for=Opportunity_estimatorApproval]").append("<span id=\"estimator_approval_required\" class='required'>*</span>");
        }else{
            $("#estimator_required").remove();
            $("#gm_required").remove();
            $("#estimator_approval_required").remove();
        }
    });    


});



