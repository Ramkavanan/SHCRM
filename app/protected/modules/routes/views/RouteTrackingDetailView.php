<?php
  
    /**
     * View for showing the print view.
     */
    class RouteTrackingDetailView extends View    {
	protected $Id;
        
	public function __construct($data, $routeTrackingId) {
	    $this->Id = $routeTrackingId;
	}
        
	public function renderContent()     {
            
            
	  $content = RoutesUtils::routeTrackingDetail($this->Id);
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
