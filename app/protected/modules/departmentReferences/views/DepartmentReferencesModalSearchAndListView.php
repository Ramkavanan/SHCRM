<?php

    class DepartmentReferencesModalSearchAndListView extends ModalSearchAndListView
    {
        public static function getListViewClassName()
        {
            return 'DepartmentReferencesModalListView';
        }

        public static function getSearchViewClassName()
        {
            return 'DepartmentReferencesModalSearchView';
        }
    }
?>
