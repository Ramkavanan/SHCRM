$(document).ready(function(){
    $(".allow_decimal").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
        {
          evt.preventDefault();
        }
    });
});

function createJobStep1(agreementId){
    var agreementId = jQuery('#JobScheduling_agreement_name').val();
    var jobName = jQuery('#JobScheduling_name').val();
    var crewName = jQuery('#JobScheduling_crewName').val();
    if(jobName == ''){
        jQuery('#JobScheduling_name_validate').show();
        return false;
    }
    if(crewName == ''){
        jQuery('#JobScheduling_crewName_validate').show();
        return false;
    }
     
    var data = {
        jobName: $.trim(jobName),
        crewName: $.trim(crewName),
        agreementId: $.trim(agreementId),
    };
    var dataParams = {
        jobInformation:JSON.stringify(data),
    }
    var url = '/app/index.php/jobScheduling/default/GetJobSchedule';
    addProductNotification("Saving...!"); 
    $.ajax({
        url : url,
        type : 'GET',
        data: dataParams,
        dataType: 'json',
        success : function(data)
        {
            var jobId = parseInt(data.jobId);
            var agreementId = parseInt(data.agreementId);
            if(jobId > 0 && agreementId){
                var redirectUrl = '/app/index.php/jobScheduling/default/CreateStep2?id='+jobId+'&agmt_id='+agreementId;
            }
            window.location.href = redirectUrl;
        },
        error : function()  {
            console.log("Request failed..!!");
        }
    });
}

function createJobScheduleStep2(job_id, agmt_id){
    var scheduledIsEmpty;
    var weekDataArr = [];
    var splCharPresent = false;
    $('#weekSchedules input[type=text]').each(function () {        
        var scheduledUnits = parseFloat($.trim($(this).val()));
        if(scheduledUnits > 0){
            stringToSplit = this.name;
            stringToSplit = stringToSplit.substring(0, stringToSplit.indexOf('_week_'));
           
            if (weekDataArr[stringToSplit] == undefined) {
                weekDataArr[stringToSplit] = 0;
            }                     
            weekDataArr[stringToSplit] = parseFloat(weekDataArr[stringToSplit]) + parseFloat(scheduledUnits);            
            scheduledIsEmpty = true;
        }
        
        /* if($(this).val() != '' && isNaN(scheduledUnits))
        {
            if(jQuery.isNumeric(scheduledUnits) == false)
            {
                splCharPresent = true;
                return false;
            }
        } */
    });
    
    /*if(splCharPresent == true){
        addProductNotification("Please provide only numbers for Job Schedule");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }*/    
    
    if(!scheduledIsEmpty){
        addProductNotification("Please provide atleast one Job Schedule value");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 3000);
        return false;
    }
    var catArr=[];
    var valueExceeds;    
    $.each( weekDataArr, function( index, value ){
        if (value != undefined) {
            var cvalue = parseFloat($("#catIds_"+index).val());
            if(cvalue.toFixed(2) != value.toFixed(2))
            {
                valueExceeds = index;
                exceededValue = value.toFixed(2);                
            }
            catArr.push('catIds_'+index);
         }
    });   
    
    
    if(valueExceeds){
        addProductNotification("Allowed manhours for "+$("#catIds_"+valueExceeds).attr("name")+ " is "+$("#catIds_"+valueExceeds).val()+ " but you scheduled "+exceededValue+". Please reschedule");
        setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 4000);
        return false;
    }
    
    var validatePassed=true;
    $('input[id^="catIds_"]').each(function() {
        if(jQuery.inArray(this.id, catArr) !== -1) {
            // already validated
        } else {
            stringToSplit = this.id;
            stringToSplit = stringToSplit.split('catIds_');
            valueExceeds = stringToSplit[1];
            exceededValue = 0;
            addProductNotification("Allowed manhours for "+$("#catIds_"+valueExceeds).attr("name")+ " is "+$("#catIds_"+valueExceeds).val()+ " but you scheduled "+exceededValue+". Please reschedule");
            setTimeout("$('#FlashMessageView').fadeIn('slow').hide();", 4000);
            validatePassed = false;
        }
    });
    
    if(validatePassed == false)
        return false;
    
    var data = {
        jobId: job_id, 
        agmtId: agmt_id,
        YII_CSRF_TOKEN: $("#YII_CSRF_TOKEN").val(),
        scheduleData: $("#jobSchedulingForm").serialize(),
        isEdit: $("#jobRenderType").val(),
    };
    
    var url = '/app/index.php/jobScheduling/default/AddJobScheduling';
    $('#preloader').attr('class','preloader');
    addProductNotification("Saving...!"); 
    $.ajax({
        url : url,
        type : 'POST',
        data: data,
        dataType: 'json',
        success : function(data)
        {            
            var redirectUrl = '/app/index.php/jobScheduling/default/details?id='+data;
            $('#preloader').fadeOut('slow',function(){$(this).remove();});
            window.location.href = redirectUrl;
        }
    });
    setTimeout("$('#preloader').fadeOut('slow',function(){$(this).remove();});",60000);
}

function addProductNotification(msg){
    var content = '<div id="FlashMessageBar" class="notify-wrapper">  <div class="jnotify-item-wrapper">  <div class="ui-corner-all jnotify-item ui-state-highlight">  <div class="">  <span class=""></span>  </div>  <span class="ui-icon ui-icon-info"></span>  <span>'+msg+'.</span>  </div></div></div>';
    $('#FlashMessageView').show();
    $('#FlashMessageView').html(content);
}