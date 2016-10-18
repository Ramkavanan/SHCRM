<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RoutesSearchForm extends OwnedSearchForm
    {
        protected static function getRedBeanModelClassName()
        {
            return 'Route';
        }

        public function __construct(Route $model)
        {
            parent::__construct($model);
        }
    }
?>
