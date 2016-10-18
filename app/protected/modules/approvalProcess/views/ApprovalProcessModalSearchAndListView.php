<?php

    class ApprovalProcessModalSearchAndListView extends ModalSearchAndListView
    {
        public static function getListViewClassName()
        {
            return 'ApprovalProcessModalListView';
        }

        public static function getSearchViewClassName()
        {
            return 'ApprovalProcessModalSearchView';
        }
    }
?>
