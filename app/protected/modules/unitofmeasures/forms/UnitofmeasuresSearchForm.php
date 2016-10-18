<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class UnitofmeasuresSearchForm extends OwnedSearchForm
    {
        protected static function getRedBeanModelClassName()
        {
            return 'Unitofmeasure';
        }

        public function __construct(Unitofmeasure $model)
        {
            parent::__construct($model);
        }
    }
?>
