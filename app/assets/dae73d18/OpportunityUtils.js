
function createNewOpportunity() {
	if($("#Opportunity_RecordType_value").val() != null && $("#Opportunity_RecordType_value").val() != '') {
		//alert("Selected Ids"+$("#list-view-selectedIds").val());
		var recordType = $("#Opportunity_RecordType_value").val();
		var url = '/app/index.php/opportunities/default/projectType';
                var modelId = $("#OppModel_Id").val();
                var moduleId = $("#OppModule_Id").val();
                var appendurl = '';
                if(modelId){
                   appendurl = '?relationModelId='+modelId;
                }                
                if(moduleId){
                    appendurl += '&relationModuleId='+moduleId;
                }
		if(recordType != null && recordType == 'Recurring Final' ) {
			url = '/app/index.php/opportunities/default/recurringType';
		}
		window.location.href = url+appendurl;
	} else {
		alert("Please select atleast one Record type");	
	}
}

