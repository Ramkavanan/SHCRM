<?php

/**
 * Description of AgreementTrackingAddView
 *
 * @author 
 */
class AgreementTrackingAddView extends View {

    private $agreementId;

    public function __construct($agreementId) {
        $this->agreementId = $agreementId;
    }

    public function renderContent() {
        $content = AgreementTrackingUtils::makeAgreementTrackingView($this->agreementId);
        return $content;
    }

    public function renderScripts() {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                        Yii::getPathOfAlias('application.modules.agreementTracking.elements.assets')) . '/AgreementTrackingTemplateUtils.js', CClientScript::POS_END);
    }
}
?>

