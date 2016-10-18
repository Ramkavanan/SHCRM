<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookSearchForm
 *
 * @author ideas2it
 */
class CostbookSearchForm extends OwnedSearchForm{

   public $sortDescending = true;

   protected static function getRedBeanModelClassName()
    {
        return 'Costbook';
    }

    public function __construct(Costbook $model)
    {
       	parent::__construct($model);
    }
}
