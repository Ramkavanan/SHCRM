<?php
  
    /**
     * View for showing in the user interface when the opportunity add costbooks to opportunity products.
     */
    class AddAgreementProductView extends View    {
        protected $data;
	protected $agmntId;
        
	public function __construct($data, $agmntId) {
            $this->data = $data;
	    $this->agmntId = $agmntId;
	}
        
	public function renderContent()     {
	  $content = AgreementProductUtils::makeCostBookProductSelection($this->data,$this->agmntId);
         // $content = 'Test';
	  $content.= $this->renderScripts();
            return $content;
        }
       
	protected function renderScripts()     {
		Yii::app()->clientScript->registerScriptFile(
                Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('application.modules.agreementProducts.elements.assets')) . '/AgreementProductTemplateUtils.js');
           //parent::renderScripts();
          // Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
               //     Yii::getPathOfAlias('application.modules.opportunityProducts.elements.assets')) . '/OpportunityProductTemplateUtils.js',
              //  CClientScript::POS_END);
        }
         
    }
?>
