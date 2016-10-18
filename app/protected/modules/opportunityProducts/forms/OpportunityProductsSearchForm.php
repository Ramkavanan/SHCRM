<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class OpportunityProductsSearchForm extends OwnedSearchForm
    {
        protected static function getRedBeanModelClassName()
        {
            return 'OpportunityProduct';
        }

        public function __construct(OpportunityProduct $model)
        {
            parent::__construct($model);
        }
    }
?>
