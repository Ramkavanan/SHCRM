<?php

    class AgreementProductsModalSearchAndListView extends ModalSearchAndListView
    {
        public static function getListViewClassName()
        {
            return 'AgreementProductsModalListView';
        }

        public static function getSearchViewClassName()
        {
            return 'AgreementProductsModalSearchView';
        }
    }
?>
