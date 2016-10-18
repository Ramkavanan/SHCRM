<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JobSchedulingDefaultDataMaker extends DefaultDataMaker {

    public function make() {
        $values = array(
                Zurmo::t('CustomField', 'New'),
                Zurmo::t('CustomField', 'Deactivated'),
            );
        static::makeCustomFieldDataByValuesAndDefault('JobStatusValue', $values);
    }
}
?>
