<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookDetailsViewUtil
 *
 * @author ideas2it
 */
class CostbookDetailsViewUtil extends OwnedSecurableItemDetailsViewUtil
    {
        protected static function getElements($model)
        {
            $elements = parent::getElements($model);
            $elements[] = array('className'  => 'LatestActivityDateTimeElement',
                'parameters' => array($model, 'latestActivityDateTime'),
            );
            return $elements;
        }
}
