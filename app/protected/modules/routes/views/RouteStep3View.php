<?php

/**
 * Description of AgreementTrackingAddView
 *
 * @author 
 */
class RouteStep3View extends View {

    public $routeId;
    public $newClonedRouteId;

    public function __construct($routeId, $ClonedRouteId, $type) {
        $this->routeId = $routeId;
        $this->newClonedRouteId = $ClonedRouteId;
        $this->isEdit = $type;
    }

    public function renderContent() {
        $content = RoutesUtils::makeRouteStep3View($this->routeId, $this->newClonedRouteId, $this->isEdit);
        $content .= $this->renderScripts();
        return $content;
    }

    public function renderScripts() {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                        Yii::getPathOfAlias('application.modules.routes.elements.assets')) . '/RouteTemplateUtils.js', CClientScript::POS_END);
    }
}
?>

