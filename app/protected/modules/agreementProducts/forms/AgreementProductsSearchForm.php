<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AgreementProductsSearchForm extends OwnedSearchForm
    {
        protected static function getRedBeanModelClassName()
        {
            return 'AgreementProduct';
        }

        public function __construct(AgreementProduct $model)
        {
            parent::__construct($model);
        }
    }
?>
