$(document).ready(function(){
    $("label[for=User_jobTitle]").append("<span class='required'>*</span>");
    $("label[for=User_officePhone]").append("<span class='required'>*</span>");
    $("label[for=User_manager_id]").append("<span class='required'>*</span>");
    $("label[for=User_role_id]").append("<span class='required'>*</span>");
    $("label[for=User_mobilePhone]").append("<span class='required'>*</span>");
    $("label[for=User_department]").append("<span class='required'>*</span>");
    $("label[for=User_language_value]").append("<span class='required'>*</span>");
    $("label[for=User_timeZone_value]").append("<span class='required'>*</span>");
    $("label[for=User_currency_id]").append("<span class='required'>*</span>");
    $("label:contains('Address')").append("<span class='required'>*</span>");
    $("label[for=User_locale_value]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_jobTitle]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_officePhone]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_manager_id]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_role_id]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_mobilePhone]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_department]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_language_value]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_timeZone_value]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_currency_id]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_locale_value]").append("<span class='required'>*</span>");
    $("label[for=UserPasswordForm_primaryEmail_emailAddress]").append("<span class='required'>*</span>");

/** 
 * For mobile and phone number validations in user create page.  
 */
    var fieldIds = ["#UserPasswordForm_officePhone", "#UserPasswordForm_mobilePhone"];
    PhoneNumAndMobNunberValidation(fieldIds);
});

function PhoneNumAndMobNunberValidation(ids){
    $.each(ids, function( index, value ) {
        $(value).attr("placeholder", "(___) ___-____");
        $(value).attr("maxlength","10");
        $(value).keypress(function(e){
            this.value = this.value.replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,"($1) $2-$3");
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }            
        });
    });
}
