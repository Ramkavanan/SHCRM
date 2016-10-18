<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApprovalProcessSearchForm extends OwnedSearchForm
    {
        protected static function getRedBeanModelClassName()
        {
            return 'ApprovalProcess';
        }

        public function __construct(ApprovalProcess $model)
        {
            parent::__construct($model);
        }
    }
?>
