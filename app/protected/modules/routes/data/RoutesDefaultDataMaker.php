<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RoutesDefaultDataMaker extends DefaultDataMaker {

    public function make() {
        $values = array(
            'Bed Maintenance',
            'Clean Ups',
        );
        static::makeCustomFieldDataByValuesAndDefault('CategoryTypes', $values);
    }
}
?>
