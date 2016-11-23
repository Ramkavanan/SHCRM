<?php
  
    /**
     * View for showing the print view.
     */
    class ForgotPasswordPageView extends ZurmoPageView    {
        
	public function __construct() {            
	}
        
        protected function getSubtitle()
        {
            return Zurmo::t('ZurmoModule', 'Forgot Password');
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
                    $errMsg = 'Please provide a valid Email.';
                }
                else if($_REQUEST['err'] == 2)
                {
                    $errMsg = 'Provided Email is not yet registered with us.';
                }
                else
                    $errPresent=0;
            }

            $tableCreation = '';

            $tableCreation .= '
            <div class="login-container GridView">
            ';
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
                                    <label for="LoginForm_email">Email</label><input name="LoginForm[email]" id="LoginForm_email" type="text" />
                                    <div class="errorMessage" id="LoginForm_email_em_" style="display:none"></div>
                                </div>                                
                                <div><a id="Email" name="Email" class="attachLoading z-button" onclick="validateEmail();" href="javascript:void(0);"><span class="z-label">Send</span></a>&nbsp;&nbsp;&nbsp;  
                                <a id="Cancel-Email" name="Cancel-Email" class="attachLoading cancel-button" href="/app/index.php/zurmo/default/login"><span class="z-label">Cancel</span></a></div>
                            </form>
                        </div>
                    </div>                   
                </div>
                <footer id="FooterView">
                    <div class="container"><a href="#" id="credit-link" class="clearfix"><span>Copyright &#169; ShinnedHawks Inc., '.date('Y').'. All rights reserved.</span></a></div>
                </footer>
                </div>
               
            <script type="text/javascript">
               function validateEmail()
               {
                    email = document.getElementById("LoginForm_email").value;
                    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    if(re.test(email))
                    {
                       document.getElementById("login-form").submit();
                    }
                    else
                    {
                        alert("Please enter a valid email");
                        return false;
                    }
               }
               
               
            </script>
       ';
                   
            return $tableCreation;
        }
    }
?>