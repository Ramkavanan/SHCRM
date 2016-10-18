<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingDefaultDataMaker extends DefaultDataMaker {

    public function make() {
        $values = array(
            'Type 1',
            'Type 2',
        );
        static::makeCustomFieldDataByValuesAndDefault('AgreementTrackingType', $values);

        $values = array(
            'Value 1',
            'Value 2',
        );
        static::makeCustomFieldDataByValuesAndDefault('AgreementTrackingPickList', $values);

        $values = array(
            'Value 3',
            'Value 4',
        );
        static::makeCustomFieldDataByValuesAndDefault('AgreementTrackingRadioPickList', $values);
    }

}

?>
