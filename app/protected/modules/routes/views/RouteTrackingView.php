<?php
  
    /**
     * View for showing the print view.
     */
    class RouteTrackingView extends View    {
        protected $data;
	protected $Id;
        
	public function __construct($data, $id) {
            $this->data = $data;
	    $this->Id = $id;
	}
        
	public function renderContent()     {
            
            
	  $content = RoutesUtils::routeTracking($this->data,$this->Id);
	  $content .= $this->renderScripts();
            return $content;
        }
        
        protected function renderScripts()     {
            Yii::app()->clientScript->registerScriptFile(
            Yii::app()->getAssetManager()->publish(
                Yii::getPathOfAlias('application.modules.routes.elements.assets')) . '/RouteTemplateUtils.js');
       
        }
    }
?>
