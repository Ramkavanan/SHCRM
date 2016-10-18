<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookPageView
 *
 * @author ideas2it
 */
class CostbookPageView extends ZurmoDefaultPageView
    {
        protected function getSubtitle()
        {
            return 'Cost Catalog';
		//	return Zurmo::t('CostbooksModule', 'CostbooksModulePluralLabel', LabelUtil::getTranslationParamsForAllModules());
        }
    }   
