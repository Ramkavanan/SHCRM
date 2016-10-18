<?php

/**
 * Description of AgreementTrackingAddView
 *
 * @author 
 */
class AgreementTrackingEditView extends View {

    private $agreementTrackingId;

    public function __construct($agreementTrackingId) {
        $this->agreementTrackingId = $agreementTrackingId;
    }

    public function renderContent() {
        $content = AgreementTrackingUtils::makeAgreementTrackingEditView($this->agreementTrackingId);
        $content.= $this->renderScripts();
        return $content;
    }

    public function renderScripts() {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                        Yii::getPathOfAlias('application.modules.agreementTracking.elements.assets')) . '/AgreementTrackingTemplateUtils.js', CClientScript::POS_END);
    }
}
?>
