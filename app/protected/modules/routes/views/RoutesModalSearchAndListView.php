<?php

    class RoutesModalSearchAndListView extends ModalSearchAndListView
    {
        public static function getListViewClassName()
        {
            return 'RoutesModalListView';
        }

        public static function getSearchViewClassName()
        {
            return 'RoutesModalSearchView';
        }
    }
?>
