<?php
  
    /**
     * View for showing in the user interface when the opportunity add costbooks to opportunity products.
     */
    class AgmntRecordType extends View    {
        protected $data;
        
	public function __construct($data) {
            $this->data = $data;
	}
        
	public function renderContent()     {
          $content = '<div class="SecuredEditAndDetailsView EditAndDetailsView DetailsView ModelView ConfigurableMetadataView MetadataView" id="AgreementEditAndDetailsView"><div class="wrapper"><h1><span class="truncated-title" threedots="Create Agreement"><span class="ellipsis-content">Create Agreement</span></span></h1><div class="wide form"><form method="post" action="/app/index.php/agreements/default/create?clearCache=1&amp;resolveCustomData=1" id="edit-form" onsubmit="js:return $(this).attachLoadingOnSubmit(&quot;edit-form&quot;)">
<div style="display:none"><input type="hidden" name="YII_CSRF_TOKEN" value="433d9fecc4694627b69cd329039a49e2caa834ff"></div><div class="attributesContainer"><div class="left-column"><div class="panel"><div class="panelTitle">Select Agreement Record Type</div><table class="form-fields"><colgroup><col class="col-0"><col class="col-1"></colgroup><tbody><tr><th><label for="Agreement_RecordType_value">Record Type of new record</label></th><td colspan="1"><div class="hasDropDown"><span class="select-arrow"></span><select id="Agreement_RecordType_value" name="Agreement[RecordType][value]">
<option value="Project Agreement">Project Agreement</option>
<option value="Recurring Agreement">Recurring Agreement</option>
</select></div></td></tr></tbody></table></div></div>
<div class="right-column"><div class="right-side-edit-view-panel"><h3>Available Agreement Record Types</h3>
<table style="border:1px solid #e0e3e5; display: table;border-collapse: separate;border-spacing: 15px;" border="0" cellpadding="1" cellspacing="1"><tbody><tr style="padding: 5px;"><th><h4>Record Type Name</h4></th><th><h4>Description</h4></th></tr>
<tr><th class="recordTypeName" scope="row"><b>Project Agreement</b></th><td class="recordTypeDescription">&nbsp;</td></tr>
<tr class="last"><th class="recordTypeName" scope="row"><b>Recurring Agreement</b></th><td class="recordTypeDescription">&nbsp;</td></tr>
</tbody></table>
</div></div></div>
<div class="float-bar"><div class="view-toolbar-container clearfix dock"><div class="form-toolbar"><a href="#" onclick="javascript:createNewAgreement();" class="attachLoading z-button" name="save" id="saveyt2"><span class="z-spinner"></span><span class="z-icon"></span><span class="z-label">Save</span></a><a href="/app/index.php/agreements/default" class="cancel-button" id="CancelLinkActionElement--33-yt3"><span class="z-label">Cancel</span></a></div></div></div></form><div id="modalContainer-edit-form"></div></div></div></div>';
	  $content .= $this->renderScripts();
            return $content;
        }
       
	protected function renderScripts()     {
		Yii::app()->clientScript->registerScriptFile(
                Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('application.modules.agreements.elements.assets')) . '/AgreementUtils.js');
        }
         
    }
?>
