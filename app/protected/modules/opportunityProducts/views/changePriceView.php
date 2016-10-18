<?php
  
    /*
    * View for showing in the user interface when the user changes the price of opportunity.
    */    
    class changePriceView extends View    {
		private $data;
        private $opportunityId;

		public function __construct($data, $optId) {
			$this->data = $data;
			$this->opportunityId = $optId;
		}
	  
		public function renderContent()
		{        
			$content = OpportunityProductUtils::makeOpptChangePriceView($this->data,$this->opportunityId);
			return $content;
		}
			
		protected function renderScripts()
		{
			   Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
						Yii::getPathOfAlias('application.modules.opportunityProducts.elements.assets')) . '/OpportunityProductTemplateUtils.js',
					CClientScript::POS_END);
		}
                 
    }
?>
