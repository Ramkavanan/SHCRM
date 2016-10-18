<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostBook
 *
 * @author ideas2it
 */
class CostbookModule extends SecurableModule{
    //put your code here
    //Security
    const RIGHT_CREATE_COSTBOOK = 'Create Costbook';
    const RIGHT_DELETE_COSTBOOK = 'Delete Costbook';
    const RIGHT_ACCESS_COSTBOOK = 'Access Costbook Tab';
    
    public static function getTranslatedRightsLabels()
    {
        $params                              = LabelUtil::getTranslationParamsForAllModules();
        $labels                              = array();
        $labels[self::RIGHT_CREATE_COSTBOOK] = Zurmo::t('CostbookModule', 'Create CostbookModulePluralLabel',     $params);
        $labels[self::RIGHT_DELETE_COSTBOOK] = Zurmo::t('CostbookModule', 'Delete CostbookModulePluralLabel',     $params);
        $labels[self::RIGHT_ACCESS_COSTBOOK] = Zurmo::t('CostbookModule', 'Access CostbookModulePluralLabel Tab', $params);
        return $labels;
    }
    public function getDependencies()
    {
        return array(
                'configuration',
                'zurmo',
            );
    }
    public function getRootModelNames()
    {
        return array('Costbook');
    }
    public static function getDefaultMetadata()
    {       
        $metadata = array();
        $CostCatalogUserIdArr = array();
        $isUsersInCostCatalogGroup = FALSE;
        $CostCatalogGroup = Group::getByName(Constant::CATALOGMANAGER);
        foreach($CostCatalogGroup->users as $CostCatalogGroupUserId)
        {
            $CostCatalogUserIdArr[] = $CostCatalogGroupUserId->id;
        }
        if(in_array(Yii::app()->user->userModel->id, $CostCatalogUserIdArr)){
            $isUsersInCostCatalogGroup = TRUE;
        }
        if(empty(Yii::app()->user->userModel->isRootUser) && $isUsersInCostCatalogGroup == FALSE){
            $metadata['global'] = array(    //Meta data for normal user
                'globalSearchAttributeNames' => array()
            );  
        }else{
            $metadata['global'] = array(    //Meta data for super user And Cost Catalog(Group).
                'tabMenuItems' => array(
                    array(
                        'label'  => "Cost Catalog",
                        'url'    => array('/costbook/default'),
                        'right'  => self::RIGHT_ACCESS_COSTBOOK,
                        'mobile' => true,
                    ),
                ),
                'designerMenuItems' => array(
                    'showFieldsLink' => true,
                    'showGeneralLink' => true,
                    'showLayoutsLink' => true,
                    'showMenusLink' => true,
                ),
                'shortcutsCreateMenuItems' => array(
                    array(
                        'label'  => "Cost Catalog",
                        'url'    => array('/costbook/default/create'),
                        'right'  => self::RIGHT_CREATE_COSTBOOK,
                        'mobile' => true,
                    ),
                ),
                'globalSearchAttributeNames' => array(
                    'productname'
                )
            );
        }
        return $metadata;
    }

    public static function isTypeDeletable(){
        return true;
    }

    public static function getPrimaryModelName()
    {
        return 'Costbook';
    }

    public static function getSingularCamelCasedName()
    {
        return 'Costbook';
    }

    protected static function getSingularModuleLabel()
    {
        return 'Cost Catalog';
    }

    protected static function getPluralModuleLabel()
    {
        return 'Cost Catalog';
    }

    public static function getAccessRight()
    {
        return self::RIGHT_ACCESS_COSTBOOK;
    }

    public static function getCreateRight()
    {
        return self::RIGHT_CREATE_COSTBOOK;
    }

    public static function getDeleteRight()
    {
        return self::RIGHT_DELETE_COSTBOOK;
    }

    public static function getDefaultDataMakerClassName()
    {
        return 'CostbooksDefaultDataMaker';
    }

    public static function getGlobalSearchFormClassName()
    {
        return 'CostbookSearchForm';
    }

	// To disable to labor cost and burdon cost in view page
    public static function isAutomaticLaborCostDisabled()
    {
        $metadata = static::getMetadata();
        if (isset($metadata['global']['automaticDisable']))
        {
            return (bool) $metadata['global']['automaticDisable'];
        }
        return false;
    }
}