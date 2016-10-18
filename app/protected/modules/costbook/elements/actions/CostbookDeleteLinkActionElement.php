<?php
    class CostbookDeleteLinkActionElement extends DeleteLinkActionElement
    {
        protected function resolveConfirmAlertInHtmlOptions($htmlOptions)
        {
            $htmlOptions['confirm'] = Yii::t('Default',
                                             'Are you sure you want to remove this CostbooksModuleSingularLowerCaseLabel?',
                                             LabelUtil::getTranslationParamsForAllModules());
            return $htmlOptions;
        }
    }
?>
