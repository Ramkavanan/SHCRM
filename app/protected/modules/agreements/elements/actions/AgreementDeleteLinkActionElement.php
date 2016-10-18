<?php
    /**
     * Action create for agreement delete link. This fired when delete the agreement.
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementDeleteLinkActionElement extends DeleteLinkActionElement
    {
        protected function resolveConfirmAlertInHtmlOptions($htmlOptions)
        {
            $htmlOptions['confirm'] = Zurmo::t('Core', 'Are you sure you want to delete this {modelLabel}?',
                                      array('{modelLabel}' => AgreementsModule::getModuleLabelByTypeAndLanguage('SingularLowerCase')));
            return $htmlOptions;
        }
    }
?>
