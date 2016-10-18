/**
 * Script for initializing the approval request
 */
jQuery(function(){
    jQuery('#Agreement_Renewal_Date').datepicker({'minDate': 0, 'showOn': 'both', 'buttonText': '<span><!--Date--><\/span>', 'showButtonPanel': true, 'buttonImageOnly': false, 'dateFormat': 'm/d/yy', 'currentText': '<span class=\"z-label\">Now<\/span>', 'closeText': '<span class=\"z-label\">Done<\/span>'});
});

//For getting the opportunity id
var oppid = getParameterByName('id'); 
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}


//For the approval process ajax call
function searchProducts(opportunityId, ty, stat) {	//For the type 1 = On page load, 2 = On submitting the request
	
	var flr1 = 1;
	if(stat == 1){
		var flr = confirm("Once you submit this record for approval, you might not be able to edit it or recall it from the approval process depending on your settings. Continue?");
		if (flr == true) {
			flr1 = 1;
		}else{
			flr1 = 2;
		} 
	}
	
	
	if((opportunityId != null) && (flr1 == 1)){			
		var url = '/app/index.php/approvalProcess/default/NewApprovalRequest?optId='+opportunityId+'&typ='+ty+'&stats='+stat;
		$('#subApprovalProces').addClass('disabled-link'); //To prevent multiple submit
		$.ajax({
			url : url,
			type : 'GET',					
			success : function(res)
			{
				//$("#approvalProcess").html(res);	
                                window.location = '/app/index.php/opportunities/default/details?id='+opportunityId;
			},
			error : function (xhr, ajaxOptions, thrownError) {				
			//	$('#approvalHistoryListMsgId').html("Error in loading the content.");
			}
		});			
	}
}

function updateApprovalProcessDetails(isAccept,optId, apId){
    //$('#approvalProcessAccept,#approvalProcessReject').addClass('disabled-link');
    var url = '';
    var isJobSchedule = false;
    if(isAccept != undefined && isAccept != null && isAccept == 'accept'){
        if($("#jobScheduleCheck").attr("checked")){
            isJobSchedule = true;
        }
        var ren_date = $("#Agreement_Renewal_Date").val();
        if(ren_date == ''){
            jQuery('#Agreement_Renewal_Date_req').show();
            return false;
        }
        url = '/app/index.php/approvalProcess/default/acceptOrRejectapprovalprocess?comment='+$("#approvalProcessComment").val()+'&status=accept&optId='+optId+'&apId='+apId+'&job='+isJobSchedule+'&renDate='+ren_date;
    }
    else if(isAccept != undefined && isAccept != null && isAccept == 'reject'){
          url = '/app/index.php/approvalProcess/default/acceptOrRejectapprovalprocess?comment='+$("#approvalProcessComment").val()+'&status=reject&optId='+optId+'&apId='+apId;
    }
    else{
        url='/app/index.php/home/default';
    }
        window.location = url;
}


//For the agreement approval process ajax call
function approvalForAgreement(agreementId, ty, stat) {	//For the type 1 = On page load, 2 = On submitting the request
	var getConfirmation = 1;
	if(stat == 1){
		var isConfirm = confirm("Once you submit this record for approval, you might not be able to edit it or recall it from the approval process depending on your settings. Continue?");
		if (isConfirm == true) {
			getConfirmation = 1;
		}else{
			getConfirmation = 2;
		} 
	}
	if((agreementId != null) && (getConfirmation == 1)){			
		var url = '/app/index.php/approvalProcess/default/NewApprovalRequestForAgreement?agmntId='+agreementId+'&typ='+ty;
		$('#subApprovalProces').addClass('disabled-link'); //To prevent multiple submit
		$.ajax({
			url : url,
			type : 'GET',					
			success : function(res)
			{
				//$("#approvalProcess").html(res);	
                                window.location = '/app/index.php/agreements/default/details?id='+agreementId;
			},
			error : function (xhr, ajaxOptions, thrownError) {				
			//	$('#approvalHistoryListMsgId').html("Error in loading the content.");
			}
		});			
	}
}

  function updateApprovalProcessDetailsForAgmnt(isAccept,agmntId, apId){
      $('#approvalProcessAccept,#approvalProcessReject').addClass('disabled-link');
      var url = '';
      if(isAccept != undefined && isAccept != null && isAccept == 'accept'){
          url = '/app/index.php/approvalProcess/default/acceptOrRejectapprovalprocessForAgmnt?comment='+$("#approvalProcessComment").val()+'&status=accept&agmntId='+agmntId+'&apId='+apId;
      }
      else if(isAccept != undefined && isAccept != null && isAccept == 'reject'){
          url = '/app/index.php/approvalProcess/default/acceptOrRejectapprovalprocessForAgmnt?comment='+$("#approvalProcessComment").val()+'&status=reject&agmntId='+agmntId+'&apId='+apId;
      }
      else{
          url='/app/index.php/home/default';
      }
      window.location = url;
  }




