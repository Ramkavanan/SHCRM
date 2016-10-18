/**
 * Created By : Murugan M
 * Created On : Sep 15 2016
 * Description : Outlook to sync the meeting should need the 'End Time'. So we added 'End Time' as a mandatory field
 * @param {type} param
 */
$(document).ready(function(){
    $("label[for=Meeting_endDateTime]").append("<span id=\"endDateTime_required\" class='required'> *</span>");
});



