<?php

class AgreementTrackingDeleteLinkActionElement extends DeleteLinkActionElement {

    protected function resolveConfirmAlertInHtmlOptions($htmlOptions) {
        $htmlOptions['confirm'] = Zurmo::t('Core', 'Are you sure you want to delete this {modelLabel}?',array('{modelLabel}' => AgreementTrackingModule::getModuleLabelByTypeAndLanguage('SingularLowerCase')));
        return $htmlOptions;
    }

}

?>