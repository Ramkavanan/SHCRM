$(document).ready(function()
{
    $("#Agreement_Deactivation_Date").datepicker({"minDate": 0});
    $("#Agreement_Current_Annual_Amount_value").prop("readonly", true);
    $("#Agreement_Project_Agreement_Amount_value").css("background-color","#efefef");
    $("#Agreement_Current_Annual_Amount_value").css("background-color","#efefef"); 
    $("#AgreementRecurringEditAndDetailsView .has-date-select").css("z-index","10");
    $("#AgreementRecurringEditAndDetailsView .has-model-select").css("z-index","10");
    $("#AgreementProjectEditAndDetailsView .has-date-select").css("z-index","10");
    $("#AgreementProjectEditAndDetailsView .has-model-select").css("z-index","10");
    $("#AgreementDetailsAndRelationsView_1787").css("display","none");  //For Thamodaran user in Dve (Temp Fix).
    $("#AgreementDetailsAndRelationsView_1705").css("display","none");  //For Arvind Handu user in Dve (Temp Fix).
     // For the req
    $('.AgreementProductsForAgreementRelatedListView').css('display','none'); //To hide the agreement product panel - Modified by Sundar P - 12-Sep-2016
    $("label[for=Agreement_Agreement_Expiration]").append("<span class='required'>*</span>");
});

function removeSpinner()
{
    $("#saveyt2").removeClass("loading");
}

function removeNotify()
{
    $("#FlashMessageView").fadeIn("slow").hide();
}  

function addNotification(msg){
    var content = '<div id="FlashMessageBar" class="notify-wrapper">  <div class="jnotify-item-wrapper">  <div class="ui-corner-all jnotify-item ui-state-highlight">  <div class="jnotify-item-close">  <span class="ui-icon ui-icon-circle-close"></span>  </div>  <span class="ui-icon ui-icon-info"></span>  <span>'+msg+'</span>  </div></div></div>';
    $("#FlashMessageView").show();
    $("#FlashMessageView").html(content);
}

function createNewAgreement() {
	if($("#Agreement_RecordType_value").val() != null && $("#Agreement_RecordType_value").val() != '') {
		//alert("Selected Ids"+$("#list-view-selectedIds").val());
		var recordType = $("#Agreement_RecordType_value").val();
		var url = '/app/index.php/agreements/default/projectType';
		if(recordType != null && recordType == 'Recurring Agreement' ) {
			url = '/app/index.php/agreements/default/recurringType';
		}
		window.location.href = url;
	} else {
		alert("Please select atleast one product");	
	}
}

