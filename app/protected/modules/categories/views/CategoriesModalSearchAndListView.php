<?php

    class CategoriesModalSearchAndListView extends ModalSearchAndListView
    {
        public static function getListViewClassName()
        {
            return 'CategoriesModalListView';
        }

        public static function getSearchViewClassName()
        {
            return 'CategoriesModalSearchView';
        }
    }
?>
