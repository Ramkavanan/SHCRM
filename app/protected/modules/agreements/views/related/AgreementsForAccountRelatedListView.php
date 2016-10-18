<?php

    class AgreementsForAccountRelatedListView extends AgreementsRelatedListView
    {
        protected function getRelationAttributeName()
        {
            return 'account';
        }

        public static function getDisplayDescription()
        {
            return Zurmo::t('AgreementsModule', 'AgreementsModulePluralLabel For AccountsModuleSingularLabel',
                        LabelUtil::getTranslationParamsForAllModules());
        }

        public static function getAllowedOnPortletViewClassNames()
        {
            return array('AccountDetailsAndRelationsView');
        }
    }
?>
