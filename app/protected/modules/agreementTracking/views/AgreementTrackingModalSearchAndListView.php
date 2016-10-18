<?php

class AgreementTrackingModalSearchAndListView extends ModalSearchAndListView {

    public static function getListViewClassName() {
        return 'AgreementTrackingModalListView';
    }

    public static function getSearchViewClassName() {
        return 'AgreementTrackingModalSearchView';
    }

}

?>
