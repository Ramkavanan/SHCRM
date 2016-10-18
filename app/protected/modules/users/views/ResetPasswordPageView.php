<?php
  
    /**
     * View for showing the print view.
     */
    class ResetPasswordPageView extends ZurmoPageView    {
        
	public function __construct() {            
	}
        
        protected function getSubtitle()
        {
            return Zurmo::t('ZurmoModule', 'Reset Forgot Password');
        }
        
	public function renderContent()
        {                        
            $base_url       =  Yii::app()->theme->baseUrl;      
            $csrfTokenName  = Yii::app()->request->csrfTokenName;
            $csrfToken      = Yii::app()->request->csrfToken;
            
            $errPresent=0;
            if(isset($_REQUEST['err']))
            {
                $errPresent=1;
                if($_REQUEST['err'] == 1)
                {
                    $errMsg = 'Problem in password reset, please try again later.';
                }                
                else
                    $errPresent=0;
            }

            $tableCreation = '';                

            $tableCreation .= '<div class="login-container GridView">';
            if($errPresent == true)
            {
                $tableCreation .= '<div id="FlashMessageView">
                        <div id="FlashMessageBar" class="notify-wrapper">
                            <div class="jnotify-item-wrapper">
                                <div class="ui-corner-all jnotify-item ui-state-highlight">                                    
                                    <span class="ui-icon ui-icon-info"></span>
                                    <span>'.$errMsg.'</span>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
                $tableCreation .= '<div id="LoginView" class="clearfix background-3">
                    <div id="login-box" class="clearfix">
                        <div id="LoginLogo" class="zurmo-logo"></div>
                        <div class="form">
                            <form id="login-form" action="" method="post">
                                <div style="display:none"><input type="hidden" value="'.$csrfToken.'" name="'.$csrfTokenName.'" /></div>
                                <div>
                                    <label for="LoginForm_email">Password</label><input name="UserPasswordForm[newPassword]" id="UserPasswordForm_password" type="password" />
                                    <div class="errorMessage" id="LoginForm_email_em_" style="display:none"></div>
                                </div>    
                                <div>
                                    <label for="LoginForm_email">Confirm Password</label><input name="UserPasswordForm[newPassword_repeat]" id="UserPasswordForm_passwordrepeat" type="password" />
                                    <div class="errorMessage" id="LoginForm_email_em_" style="display:none"></div>
                                </div>  
                                <div><a id="Email" name="Email" class="attachLoading z-button" onclick="validatePassword();" href="javascript:void(0);"><span class="z-label">Submit</span></a></div>
                            </form>
                        </div>
                    </div>                   
                </div>
                <footer id="FooterView">
                    <div class="container"><a href="#" id="credit-link" class="clearfix"><span>Copyright &#169; Vertware Inc., '.date('Y').'. All rights reserved.</span></a></div>
                </footer>
            </div>
            <script type="text/javascript">
               function validatePassword()
               {                    
                    var password = document.getElementById("UserPasswordForm_password").value;
                    var confirmPassword = document.getElementById("UserPasswordForm_passwordrepeat").value;
                    
                    if (password == "")
                    {
                        alert("Password field should not be empty.");
                        return false;                       
                    }
                    
                    if (password.length < 4)
                    {
                        alert("Password should be more than 4 digit.");
                        return false;                       
                    }

                    if (password != confirmPassword) 
                    {
                        alert("Passwords did not match.");
                        return false;                       
                    }
                    else
                    {                                      
                        document.getElementById("login-form").submit();
                    }
               }
               
               document.getElementById("UserPasswordForm_passwordrepeat").onkeydown = function(e){
                    if(e.keyCode == 13){
                      validatePassword();
                    }
                 };
            </script>';
                   
            return $tableCreation;
        }
    }
?>