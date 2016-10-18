<?php
    /**
     * Class for showing a message and create link when there are no agreements visible to the logged in user when
     * going to the agreement list view.
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsZeroModelsYetView extends ZeroModelsYetView
    {
        protected function getCreateLinkDisplayLabel()
        {
            return Zurmo::t('AgreementsModule', 'Create AgreementsModuleSingularLabel', LabelUtil::getTranslationParamsForAllModules());
        }

        protected function getMessageContent()
        {
            return Zurmo::t('AgreementsModule', '<h2>"In the middle of difficulty lies Agreement."</h2> ' .
                                     '<i>- Albert Einstein</i></i><div class="large-icon"></div><p>In the middle of a well-implemented ' .
                                     'GI-CRM system lies Agreements, or expressions of potential revenue. ' .
                                     'Nothing difficult about creating an Agreement, so go ahead and create the ' .
                                     'first one!</p>');
        }
    }
?>
