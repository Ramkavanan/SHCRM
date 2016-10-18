<?php

    /**
     * Agreement Page view to extends the zurmo page view to display the page in UI
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsPageView extends ZurmoDefaultPageView
    {
        protected function getSubtitle()
        {
            return Zurmo::t('AgreementModule', 'AgreementsModulePluralLabel', LabelUtil::getTranslationParamsForAllModules());
        }
    }
?>
