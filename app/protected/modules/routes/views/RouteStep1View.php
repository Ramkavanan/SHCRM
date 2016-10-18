<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RouteStep1View extends View {
    public $routeId;
    public $isClone;
    public $isCloneBack;
    
    public function __construct($routeId, $isClone = NULL, $isCloneBack = NULL) {
        $this->routeId = $routeId;
        $this->isClone = $isClone;
        $this->isCloneBack  = $isCloneBack;
    }
    
    public function renderContent() {
        $content = RoutesUtils::createRouteStep1($this->routeId, $this->isClone, $this->isCloneBack);
        $content .= $this->renderScripts();
        return $content;
    }
    
    protected function renderScripts()     {
        Yii::app()->clientScript->registerScriptFile(
        Yii::app()->getAssetManager()->publish(
        Yii::getPathOfAlias('application.modules.routes.elements.assets')) . '/RouteTemplateUtils.js');

    }
}