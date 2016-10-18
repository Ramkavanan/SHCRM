<?php

/**
 * Description of AgreementTrackingAddView
 *
 * @author 
 */
class JobSchedulingCreateView extends View {

    private $agreementId;

    public function __construct($agreementId) {
        $this->agreementId = $agreementId;
    }

    public function renderContent() {
        $content = JobSchedulingUtils::makeJobScheduleStep1View($this->agreementId);
        return $content;
    }

    public function renderScripts() {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                        Yii::getPathOfAlias('application.modules.jobScheduling.elements.assets')) . '/jobSchedulingTemplateUtils.js', CClientScript::POS_END);
    }
}
?>

