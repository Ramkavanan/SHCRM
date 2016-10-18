<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementProduct extends Item {

    public function __toString() {
        if (trim($this->name) == '') {
            return Yii::t('Default', '(Unnamed)');
        }
        return $this->name;
    }

    public static function getModuleClassName() {
        return 'AgreementProductsModule';
    }

    public static function canSaveMetadata() {
        return true;
    }
    
    public static function getProductIdByCategory($category, $agreementId){
        return ZurmoRedBean::getAll('SELECT id from agreementproduct WHERE category = :category AND agreement_id = :agmntId ', array(':category' => $category, ':agmntId' => $agreementId));
    }

    public static function getagmntProductByproductCode($category, $agreementId, $prdctCode) {
        return ZurmoRedBean::getAll('SELECT costbook_id from agreementproduct WHERE category = :category AND agreement_id = :agmntId AND product_code =:prdctCode ', array(':category' => $category, ':agmntId' => $agreementId, ':prdctCode' => $prdctCode));
    }

    public static function getAllAgmntProducts($id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "agreement_id =:id AND is_non_agreement_product = 0 AND assembly_product_code IS NULL", array(':id' => $id)));
    }
    
    public static function getAllByAgmntId($id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "agreement_id =:id AND is_completed IS NULL AND is_non_agreement_product = 0  AND product_code NOT LIKE '%A-%'", array(':id' => $id)));
    }
    
    public static function getAllByAgmntIdForExisting($id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "agreement_id =:id AND is_non_agreement_product = 0 AND assembly_product_code IS NULL", array(':id' => $id)));
    }
    
    public static function getAllProdByAgmntId($id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "agreement_id =:id", array(':id' => $id)));
    }
        	            
    public static function getAddProductSearchData($category, $costOfGoods, $productName, $pageOffset, $pageSize, $sortFor, $sortOrder) {

        // For the product name search
        $productNameSearchCondition = '';
        if ($productName != '') {
            $trimmedProdName = trim($productName);
            $productNameSearchCondition = "AND productname Like '%$trimmedProdName%'";
        }

        // For the product sort            
        if ($sortFor == 'name') {
            $productSortCondition = " A.productname " . $sortOrder;
        } else if ($sortFor == 'code') {
            $productSortCondition = " A.productcode " . $sortOrder;
        } else if ($sortFor == 'unit') {
            $productSortCondition = " D.value " . $sortOrder;
        } elseif ($sortFor == 'cost') {
            $productSortCondition = " A.unitdirectcost " . $sortOrder;
        }

        $query = 'SELECT A.*, D.value AS UnitOfMeasure,Dept.laborcost As deptLaborCost,Dept.burdoncost As deptBurdonCost,C.value As CostOfGoodsSold, B.value AS Category FROM 
            costbook A
            LEFT JOIN customfieldvalue AS B ON B.multiplevaluescustomfield_id=A.category_multiplevaluescustomfield_id
            LEFT JOIN customfield AS C ON C.id =A.costofgoodssold_customfield_id
            LEFT JOIN customfield AS D ON A.unitofmeasure_customfield_id =D.id
            LEFT JOIN departmentreference AS Dept ON A.departmentreference_id =Dept.id
            WHERE 
            IF("ALL"="' . $category . '",TRUE,B.VALUE="' . $category . '")
            AND IF("ALL"="' . $costOfGoods . '",TRUE,C.VALUE="' . $costOfGoods . '") ' . $productNameSearchCondition . ' order by' . $productSortCondition;
        return ZurmoRedBean::getAll($query);
    }

    public static function getAllByAssembly() {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "product_code LIKE 'A-%' AND is_assembly_prod_updated IS NULL", array()));
    }
    
    public static function getDefaultMetadata() {
        $metadata = parent::getDefaultMetadata();
        $metadata[__CLASS__] = array(
            'members' => array(
                'name',
                'Assembly_Frequency',
                'Assembly_Product_Code',
                'Assembly_Quantity',
                'Category',
                'Category_GPM',
                'Cloned_Product',
                'Frequency',
                'minFrequency',
                'minQuantity',
                'Old_Id',
                'Old_Id_name',
                'Product_Code',
                'Quantity',
                'Total_MHR',
                'Consumed_Units',
                'Is_Non_Agreement_Product',
                'is_completed',
                'is_assembly_prod_updated',
            ),
            'relations' => array(
                'agreement' => array(static::HAS_ONE, 'Agreement'),
//			'Assembly_Product'    => array(static::HAS_ONE,   'Costbook'),
                'costbook' => array(static::HAS_ONE, 'Costbook'),
                'Burden_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Burden_Cost'),
                'Equipment_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Equipment_Cost'),
                'Suggested_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Suggested_Cost'),
                'Labor_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Labor_Cost'),
                'Materials_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Materials_Cost'),
                'opportunity' => array(static::HAS_ONE, 'Opportunity'),
                'Other_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Other_Cost'),
                'costbook' => array(static::HAS_ONE, 'Costbook'),
                'Sub_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Sub_Cost'),
                'Total_Direct_Cost' => array(RedBeanModel::HAS_ONE, 'CurrencyValue', RedBeanModel::OWNED,
                    RedBeanModel::LINK_TYPE_SPECIFIC, 'Total_Direct_Cost'),
            ),
            'rules' => array(
                array('name', 'required'),
                array('agreement', 'required'),
                array('name', 'type', 'type' => 'string'),
                array('name', 'length', 'max' => 100),
                array('Assembly_Frequency', 'length', 'max' => 12),
                array('Assembly_Frequency', 'numerical', 'precision' => 4),
                array('Assembly_Frequency', 'type', 'type' => 'float'),
                array('Assembly_Product_Code', 'type', 'type' => 'string'),
                array('Assembly_Quantity', 'length', 'max' => 12),
                array('Assembly_Quantity', 'numerical', 'precision' => 4),
                array('Assembly_Quantity', 'type', 'type' => 'float'),
                array('Category', 'type', 'type' => 'string'),
                array('Category_GPM', 'length', 'max' => 12),
                array('Category_GPM', 'numerical', 'precision' => 4),
                array('Category_GPM', 'type', 'type' => 'float'),
                array('Cloned_Product', 'type', 'type' => 'boolean'),
                array('Frequency', 'length', 'max' => 12),
                array('Frequency', 'numerical', 'precision' => 4),
                array('Frequency', 'type', 'type' => 'float'),
                array('minFrequency', 'length', 'max' => 12),
                array('minFrequency', 'numerical', 'precision' => 4),
                array('minFrequency', 'type', 'type' => 'float'),
                array('minQuantity', 'length', 'max' => 12),
                array('minQuantity', 'numerical', 'precision' => 4),
                array('minQuantity', 'type', 'type' => 'float'),
                array('Old_Id', 'type', 'type' => 'string'),
                array('Old_Id_name', 'type', 'type' => 'string'),
                array('Product_Code', 'type', 'type' => 'string'),
                array('Quantity', 'length', 'max' => 18),
                array('Quantity', 'numerical', 'precision' => 4),
                array('Quantity', 'type', 'type' => 'float'),
                array('Total_MHR', 'length', 'max' => 18),
                array('Total_MHR', 'numerical', 'precision' => 4),
                array('Total_MHR', 'type', 'type' => 'float'),
                array('Consumed_Units', 'length', 'max' => 18),
                array('Consumed_Units', 'numerical', 'precision' => 4),
                array('Consumed_Units', 'type', 'type' => 'float'),
                array('Is_Non_Agreement_Product', 'type', 'type' => 'boolean'),
                array('Is_Non_Agreement_Product', 'default', 'value' => 0),
                array('is_completed', 'type', 'type' => 'integer'),
                array('is_assembly_prod_updated', 'type', 'type' => 'integer'),
            ),
            'elements' => array(
                'agreement' => 'Agreement',
                'Assembly_Product' => 'Costbook',
                'costbook' => 'Costbook',
                'name' => 'Text',
                'Assembly_Frequency' => 'Decimal',
                'Assembly_Product_Code' => 'Text',
                'Assembly_Quantity' => 'Decimal',
                'Category' => 'Text',
                'Category_GPM' => 'Decimal',
                'Cloned_Product' => 'CheckBox',
                'Frequency' => 'Decimal',
                'minFrequency' => 'Decimal',
                'minQuantity' => 'Decimal',
                'Old_Id' => 'Text',
                'Old_Id_name' => 'Text',
                'Product_Code' => 'Text',
                'Quantity' => 'Decimal',
                'Total_MHR' => 'Decimal',
                //New Fields Added
                'Burden_Cost' => 'CurrencyValue',
                'Equipment_Cost' => 'CurrencyValue',
                'Suggested_Cost' => 'CurrencyValue',
                'Labor_Cost' => 'CurrencyValue',
                'Materials_Cost' => 'CurrencyValue',
                'Sub_Cost' => 'CurrencyValue',
                'Total_Direct_Cost' => 'CurrencyValue',
                'Consumed_Units' => 'Decimal',
                'Is_Non_Agreement_Product' => 'Integer',
                'is_completed' => 'Integer',
                'Other_Cost' => 'CurrencyValue',
                'is_assembly_prod_updated' => 'Integer',
            ),
            'defaultSortAttribute' => 'name',
            'noAudit' => array(
            ),
        );
        return $metadata;
    }

    public static function isTypeDeletable() {
        return true;
    }

    protected static function translatedAttributeLabels($language) {
        $params = LabelUtil::getTranslationParamsForAllModules();
        return array_merge(parent::translatedAttributeLabels($language), array(
            'agreement' => Zurmo::t('AgreementProductsModule', 'Agreement', $params, null, $language),
            'Assembly_Product' => Zurmo::t('AgreementProductsModule', 'Assembly Product', $params, null, $language),
            'Product' => Zurmo::t('AgreementProductsModule', 'Product', $params, null, $language),
            'name' => Zurmo::t('AgreementProductsModule', 'Agreement Products Name', $params, null, $language),
            'Assembly_Frequency' => Zurmo::t('AgreementProductsModule', 'Assembly Frequency', $params, null, $language),
            'Assembly_Product_Code' => Zurmo::t('AgreementProductsModule', 'Assembly Product Code', $params, null, $language),
            'Assembly_Quantity' => Zurmo::t('AgreementProductsModule', 'Assembly Quantity', $params, null, $language),
            'Category' => Zurmo::t('AgreementProductsModule', 'Category', $params, null, $language),
            'Category_GPM' => Zurmo::t('AgreementProductsModule', 'Category GPM', $params, null, $language),
            'Cloned_Product' => Zurmo::t('AgreementProductsModule', 'Cloned Product', $params, null, $language),
            'Frequency' => Zurmo::t('AgreementProductsModule', 'Frequency', $params, null, $language),
            'minFrequency' => Zurmo::t('AgreementProductsModule', 'MinFrequency', $params, null, $language),
            'minQuantity' => Zurmo::t('AgreementProductsModule', 'MinQuantity', $params, null, $language),
            'Old_Id' => Zurmo::t('AgreementProductsModule', 'Old Id', $params, null, $language),
            'Old_Id_name' => Zurmo::t('AgreementProductsModule', 'Old Id name', $params, null, $language),
            'Product_Code' => Zurmo::t('AgreementProductsModule', 'Product Code', $params, null, $language),
            'Quantity' => Zurmo::t('AgreementProductsModule', 'Quantity', $params, null, $language),
            'Total_MHR' => Zurmo::t('AgreementProductsModule', 'Total MHR', $params, null, $language),
            'costbook' => Zurmo::t('AgreementProductsModule', 'Product', $params, null, $language),
            'Burden_Cost' => Zurmo::t('AgreementProductsModule', 'Burden Cost', $params, null, $language),
            'Equipment_Cost' => Zurmo::t('AgreementProductsModule', 'Equipment Cost', $params, null, $language),
            'Suggested_Cost' => Zurmo::t('AgreementProductsModule', 'Suggested Cost', $params, null, $language),
            'Labor_Cost' => Zurmo::t('AgreementProductsModule', 'Labor Cost', $params, null, $language),
            'Materials_Cost' => Zurmo::t('AgreementProductsModule', 'Materials Cost', $params, null, $language),
            'Consumed_Units' => Zurmo::t('AgreementProductsModule', 'Consumed Units', $params, null, $language),
            'Other_Cost' => Zurmo::t('AgreementProductsModule', 'Other_Cost',  $params, null, $language),
                )
        );
    }

    public static function getNonAgtProdByAgtId($id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "agreement_id =:id, is_non_agreement_product =:is_non_agreement_product", array(':id' => $id, ':is_non_agreement_product' => 1)));
    }
       
    public static function getAgreementProductById($Id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "id =:id", array(':id' => $Id)));
    }
     public static function getAgreementByAgreementId($Id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "agreement_id =:id", array(':id' => $Id)));
    }
    
    public static function getUpdateCompletedProduct($productsIds){
        $query = 'UPDATE agreementproduct SET is_completed = 1 WHERE id IN ('.$productsIds.')';
        return  ZurmoRedBean::getAll($query);
    }
    
    public static function getProductCompletedStatus($agreementId){
        $results = self::makeModels(ZurmoRedBean::find('agreementproduct', "agreement_id =:agreement_id AND is_completed IS NULL", array(':agreement_id' => $agreementId)));
        return count($results);
    }
    
    public static function getAgmtProdByAgmtId($agmtIds) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "product_code not LIKE 'A-%' AND agreement_id IN (".ZurmoRedBean::genSlots( $agmtIds ).")", $agmtIds));
    }
    
    public static function getAgmtProdIdByAgmtIdCostBookId($agmtId, $costbookId, $prod_code, $asmbly_prod_code, $Category) {
        if($asmbly_prod_code)
            $asmbly_prod_code = " AND assembly_product_code = '".$asmbly_prod_code."'";
        else {
            $asmbly_prod_code = 'AND assembly_product_code IS NULL';
        }
        
        return ZurmoRedBean::findOne('agreementproduct', "costbook_id =:costbook_id ".$asmbly_prod_code." AND product_code=:product_code AND agreement_id =:agreement_id AND category =:category ", array(':costbook_id' => $costbookId, ':agreement_id' => $agmtId, ':product_code' => $prod_code, ':category' => $Category));
    }
    
    // Written query for time being.
    public static function getAgmtIdByCategory($cat_arr) {
        $cat_string = implode("','", $cat_arr);
        $cat_string = "'".$cat_string."'";
        $query = 'select agreement_id from agreementproduct WHERE category IN ('.$cat_string.') group by agreement_id';
        return  ZurmoRedBean::getAll($query);
    }
    
    public static function getAgmtProdByAgmtIdInRoute($agmtIds) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "is_non_agreement_product != 1 AND product_code not LIKE 'A-%' AND agreement_id IN (".ZurmoRedBean::genSlots( $agmtIds ).")", $agmtIds));
    }
    
    public static function getAgmtProdCategoryByAgmtId($agmtId) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "total_mhr > 0 AND product_code not LIKE 'A-%' AND agreement_id =:agreement_id", array(':agreement_id' => $agmtId)));
    }
    
    public static function getAllLabourProdByAgmntId($agmtId) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "product_code LIKE 'L-%' AND agreement_id =:agreement_id", array(':agreement_id' => $agmtId)));
    }
    
    public static function getAgreementProdByAgreementId($Id) {
        return self::makeModels(ZurmoRedBean::find('agreementproduct', "is_non_agreement_product != 1 AND agreement_id =:id", array(':id' => $Id)));
    }
}

?>
