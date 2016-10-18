<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class OpportunityProduct extends Item {
    public function __toString()
        {
            if (trim($this->name) == '')
            {
                return Yii::t('Default', '(Unnamed)');
            }
            return $this->name;
        }
        
        public static function getOpptPdctBypdctCode($category, $optId, $prdctCode) {
            return ZurmoRedBean::getAll('SELECT costbook_id from opportunityproduct WHERE category = :category AND opportunity_id = :optId AND product_code =:prdctCode ',
                        array(':category' => $category, ':optId' => $optId, ':prdctCode'=>$prdctCode));
            //return self::makeModels(ZurmoRedBean::find('opportunity_product', "product_code = :prdctCode", array(':prdctCode' => $prdctCode)));
        }
        
        public static function getAll($name) {
            return  self::makeModels(ZurmoRedBean::find('opportunity_product', "name = :name ", array(':name' => $name)));
        }
	
       public static function getAllByOpptId($id) {
		return self::makeModels(ZurmoRedBean::find('opportunityproduct', "opportunity_id =:id", array(':id' => $id)));
	}
        
        public static function getByName($name)   {
            assert('is_string($name) && $name != ""');
            return self::makeModels(ZurmoRedBean::find('opportunity_product', "name = :name ", array(':name' => $name)));
        }
        
        public static function getAddProductSearchData($category, $costOfGoods, $productName, $pageOffset, $pageSize, $sortFor, $sortOrder) {
            
            // For the product name search
            $productNameSearchCondition = '';
            if($productName != '') {
                $trimmedProdName = trim($productName);
                $productNameSearchCondition =  "AND productname Like '%$trimmedProdName%'";
            }
            
            // For the product sort            
            if($sortFor == 'name') {                
                $productSortCondition =  " A.productname ".$sortOrder;
            }
            else if($sortFor == 'code') {                
                $productSortCondition =  " A.productcode ".$sortOrder;
            }
            else if($sortFor == 'unit') {                
                $productSortCondition =  " D.value ".$sortOrder;
            }
            elseif($sortFor == 'cost') {                
                $productSortCondition =  " A.unitdirectcost ".$sortOrder;
            }            
            
           $query = 'SELECT A.*, D.value AS UnitOfMeasure,Dept.laborcost As deptLaborCost,Dept.burdoncost As deptBurdonCost,C.value As CostOfGoodsSold, B.value AS Category FROM 
            costbook A
            LEFT JOIN customfieldvalue AS B ON B.multiplevaluescustomfield_id=A.category_multiplevaluescustomfield_id
            LEFT JOIN customfield AS C ON C.id =A.costofgoodssold_customfield_id
            LEFT JOIN customfield AS D ON A.unitofmeasure_customfield_id =D.id
            LEFT JOIN departmentreference AS Dept ON A.departmentreference_id =Dept.id
            WHERE 
            IF("ALL"="'.$category.'",TRUE,B.VALUE="'.$category.'")
            AND IF("ALL"="'.$costOfGoods.'",TRUE,C.VALUE="'.$costOfGoods.'") '.$productNameSearchCondition.' order by'.$productSortCondition;          
            return  ZurmoRedBean::getAll($query);
        }
        
        public static function getModuleClassName()
        {
            return 'OpportunityProductsModule';
        }

        public static function canSaveMetadata()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'name',
                    'Category',
                    'Category_GPM',
                    'Frequency',
                    'Old_Auto_Number_Name',
                    'Old_Opportunity_ID',
                    'Old_Opportunity_Products_ID',
                    'Old_product_Id',
                    'Opportunity_Key',
                    'Product_Code',
                    'Product_Cost_of_Goods_Sold',
                    'Quantity',
                    'ServiceDate',
                    'Discount',
                    'Description',
                    'Total_MHR',
                ),
               'relations' => array(
                   
                        'Burden_Cost'   => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Burden_Cost'),                   
                        'Equipment_Cost'   => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Equipment_Cost'),
                        'Final_Cost'   => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Final_Cost'),                  
                        'Labor_Cost'   => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Labor_Cost'), 
                        'Materials_Cost'   => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Materials_Cost'), 
                        'opportunity'	      => array(static::HAS_ONE,   'Opportunity'),
                        'Other_Cost'   => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Other_Cost'),
                        'costbook'      => array(static::HAS_ONE,   'Costbook'),
                        'Sub_Cost'   => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Sub_Cost'),                   
                        'Total_Direct_Cost'      => array(RedBeanModel::HAS_ONE,   'CurrencyValue', RedBeanModel::OWNED, 
						RedBeanModel::LINK_TYPE_SPECIFIC, 'Total_Direct_Cost'),
		),
                'rules' => array(
                   array('costbook',           'required'),
                   array('opportunity',           'required'),
                   array('name',           'type',           'type'  => 'string'),
                   array('name',           'length',         'max'   => 100),
                   array('Category',           'type',           'type'  => 'string'),
                   array('Category_GPM',   'length',         'max'   => 12),
                   array('Category_GPM',   'numerical',      'precision' => 4),
                   array('Category_GPM',   'type',           'type'   => 'float'),
                   array('Frequency',   'length',         'max'   => 12),
                   array('Frequency',   'numerical',      'precision' => 4),
                   array('Frequency',   'type',           'type'   => 'float'),
                   array('Old_Auto_Number_Name',           'type',           'type'  => 'string'),
                   array('Old_Opportunity_ID',           'type',           'type'  => 'string'),
                   array('Old_Opportunity_Products_ID',           'type',           'type'  => 'string'),
                   array('Old_product_Id',           'type',           'type'  => 'string'),
                   array('Opportunity_Key',           'type',           'type'  => 'string'),
                   array('Product_Code',    'type',           'type'  => 'string'),
                   array('Product_Cost_of_Goods_Sold',    'type',           'type'  => 'string'),
                   array('Quantity',   'length',         'max'   => 18),
                   array('Quantity',   'numerical',      'precision' => 4),
                   array('Quantity',   'type',           'type'   => 'float'),
		   array('Total_MHR',   'length',         'max'   => 18),
                   array('Total_MHR',   'numerical',      'precision' => 4),
                   array('Total_MHR',   'type',           'type'   => 'float'),
		   array('ServiceDate',   'type',           'type'   => 'datetime'),
		   array('Discount',   'length',         'max'   => 18),
                   array('Discount',   'numerical',      'precision' => 2),
                   array('Discount',   'type',           'type'   => 'float'),
		   array('Description',    'type',           'type'  => 'string'),
                ),
                'elements' => array(
                    'name'   => 'Text',
                    'Category'   => 'Text',
                    'Burden_Cost' => 'CurrencyValue',
                    'Category_GPM'   => 'Decimal',
                    'Equipment_Cost' => 'CurrencyValue',
                    'Final_Cost' => 'CurrencyValue',
                    'Frequency'   => 'Decimal',
                    'Labor_Cost'    => 'CurrencyValue',
                    'Materials_Cost'    => 'CurrencyValue',
                    'Old_Auto_Number_Name'  => 'Text',
                    'Old_Opportunity_ID'  => 'Text',
                    'Old_Opportunity_Products_ID'  => 'Text',
                    'Old_product_Id'  => 'Text',
		    'opportunity'		=> 'Opportunity',
                    'Opportunity_Key'  => 'Text',
                    'Other_Cost' => 'CurrencyValue',
                    'costbook'   => 'Costbook',	
                    'Product_Code'   => 'Text',
                    'Product_Cost_of_Goods_Sold'   => 'Text',
                    'Quantity'       => 'Decimal',
                    'Sub_Cost' => 'CurrencyValue',                    
                    'Total_Direct_Cost' => 'CurrencyValue',                    
                    'Total_MHR'       => 'Decimal',
                    'ServiceDate' => 'DateTime',
                    'Discount'       => 'Decimal',
		    'Description'		=> 'TextArea',
                ),
                'defaultSortAttribute' => 'name',
                'noAudit' => array(
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }

        protected static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language),
                array(
                    'name'   => Zurmo::t('OpportunityProductsModule', 'Product Name',  $params, null, $language),
                    'Burden_Cost' => Zurmo::t('OpportunityProductsModule', 'Burden Cost',  $params, null, $language), 
                    'Category'   => Zurmo::t('OpportunityProductsModule', 'Category',  $params, null, $language),
                    'Category_GPM'   => Zurmo::t('OpportunityProductsModule', 'Category GPM',  $params, null, $language),
                    'Equipment_Cost' => Zurmo::t('OpportunityProductsModule', 'Equipment Cost',  $params, null, $language), 
                    'Final_Cost' => Zurmo::t('OpportunityProductsModule', 'Final Cost',  $params, null, $language), 
                    'Frequency'   => Zurmo::t('OpportunityProductsModule', 'Frequency',  $params, null, $language),
                    'Labor_Cost' => Zurmo::t('OpportunityProductsModule', 'Labor Cost',  $params, null, $language), 
                    'Materials_Cost' => Zurmo::t('OpportunityProductsModule', 'Materials Cost',  $params, null, $language), 
                    'Old_Auto_Number_Name'   => Zurmo::t('OpportunityProductsModule', 'Old Auto Number Name',  $params, null, $language),
                    'Old_Opportunity_ID'   => Zurmo::t('OpportunityProductsModule', 'Old Auto Number Name',  $params, null, $language),
                    'Old_Opportunity_Products_ID'   => Zurmo::t('OpportunityProductsModule', 'Old Opportunity Products ID',  $params, null, $language),
                    'Old_product_Id'   => Zurmo::t('OpportunityProductsModule', 'Old Product ID',  $params, null, $language),
		    'opportunity'		=> Zurmo::t('OpportunityProductsModule', 'Opportunity',  $params, null, $language),
                    'Opportunity_Key'   => Zurmo::t('OpportunityProductsModule', 'Opportunity Key',  $params, null, $language),
                    'Other_Cost'		=> Zurmo::t('OpportunityProductsModule', 'Other Cost',  $params, null, $language),
                    'costbook'   => Zurmo::t('OpportunityProductsModule', 'Product',  $params, null, $language),	  
                    'Product_Code'   => Zurmo::t('OpportunityProductsModule', 'Product Code',  $params, null, $language),
                    'Product_Cost_of_Goods_Sold'   => Zurmo::t('OpportunityProductsModule', 'Product Cost of Goods Sold',  $params, null, $language),
                    'Quantity'       => Zurmo::t('OpportunityProductsModule', 'Quantity',  $params, null, $language),
                    'Sub_Cost' => Zurmo::t('OpportunityProductsModule', 'Sub Cost',  $params, null, $language),
                    'Total_Direct_Cost' => Zurmo::t('OpportunityProductsModule', 'Total Direct Cost',  $params, null, $language),
                    'Total_MHR'       => Zurmo::t('OpportunityProductsModule', 'Total MHR',  $params, null, $language),
                    'ServiceDate'       => Zurmo::t('OpportunityProductsModule', 'Service Date',  $params, null, $language),
                    'Discount'       => Zurmo::t('OpportunityProductsModule', 'Discount',  $params, null, $language),
                    'Description'       => Zurmo::t('OpportunityProductsModule', 'Description',  $params, null, $language),
                )
            );
        }
}
?>
