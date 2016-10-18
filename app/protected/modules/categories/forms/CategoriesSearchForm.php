<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CategoriesSearchForm extends OwnedSearchForm
    {
        protected static function getRedBeanModelClassName()
        {
            return 'Category';
        }

        public function __construct(Category $model)
        {
            parent::__construct($model);
        }
    }
?>
