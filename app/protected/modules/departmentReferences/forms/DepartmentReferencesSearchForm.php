<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class DepartmentReferencesSearchForm extends OwnedSearchForm
    {
        public $sortDescending = true;

        protected static function getRedBeanModelClassName()
        {
            return 'DepartmentReference';
        }

        public function __construct(DepartmentReference $model)
        {
            parent::__construct($model);
        }
    }
?>
