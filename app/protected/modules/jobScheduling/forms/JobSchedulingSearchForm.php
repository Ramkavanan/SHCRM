<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JobSchedulingSearchForm extends OwnedSearchForm
    {
        public $sortDescending = true;

        protected static function getRedBeanModelClassName()
        {
            return 'JobScheduling';
        }

        public function __construct(JobScheduling $model)
        {
            parent::__construct($model);
        }
    }
?>
