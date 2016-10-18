<?php
    class AgreementsModalSearchAndListView extends ModalSearchAndListView
    {
        public static function getListViewClassName()
        {
            return 'AgreementsModalListView';
        }

        public static function getSearchViewClassName()
        {
            return 'AgreementsModalSearchView';
        }
    }
?>
