<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookZeroModelsYetView
 *
 * @author ideas2it
 */
class CostbookZeroModelsYetView extends ZeroModelsYetView{
    protected function getCreateLinkDisplayLabel()
        {
            return Zurmo::t('CostbookModule', 'Create CostbookModuleSingularLabel', LabelUtil::getTranslationParamsForAllModules());
        }

        protected function getMessageContent()
        {
            return Zurmo::t('CostbookModule', '<h2>"Lead, follow, or get out of the way."</h2> ' .
                                     '<i>- Thomas Paine</i></i><div class="large-icon"></div><p>Leads are potential clients that should be followed up ' .
                                     'on.  Be the first to create a Lead or get out of the way so someone else can.</p>');
        }

        /**
         * While the model is still a contact, the image should show a lead.
         * (non-PHPdoc)
         * @see ZeroModelsYetView::getIconName()
         */
        protected function getIconName()
        {
            return 'Costbook';
        }
}
