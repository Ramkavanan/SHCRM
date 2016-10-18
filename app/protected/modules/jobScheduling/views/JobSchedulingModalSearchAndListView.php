<?php

    class JobSchedulingModalSearchAndListView extends ModalSearchAndListView
    {
        public static function getListViewClassName()
        {
            return 'JobSchedulingModalListView';
        }

        public static function getSearchViewClassName()
        {
            return 'JobSchedulingModalSearchView';
        }
    }
?>
