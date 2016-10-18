<?php
  
    /**
     * View for showing in the user interface when the opportunity add costbooks to opportunity products.
     */
    class TestView extends View    {
        protected $data;
	protected $opportunityId;
        
	public function __construct($data, $optId) {
            $this->data = $data;
	    $this->opportunityId = $optId;
	}
        
	public function renderContent()     {
	  $content = OpportunityProductUtils::makeCostBookProductSelection($this->data,$this->opportunityId);
         // $content = 'Test';
	  $content .= $this->renderScripts();
            return $content;
        }
       
	protected function renderScripts()     {
		Yii::app()->clientScript->registerScriptFile(
                Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('application.modules.opportunityProducts.elements.assets')) . '/OpportunityProductTemplateUtils.js');
           //parent::renderScripts();
          // Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
               //     Yii::getPathOfAlias('application.modules.opportunityProducts.elements.assets')) . '/OpportunityProductTemplateUtils.js',
              //  CClientScript::POS_END);
        }
         
    }
?>
