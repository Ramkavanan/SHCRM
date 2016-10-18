<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingSearchForm extends OwnedSearchForm {

    protected static function getRedBeanModelClassName() {
        return 'AgreementTracking';
    }

    public function __construct(AgreementTracking $model) {
        parent::__construct($model);
    }

}

?>
