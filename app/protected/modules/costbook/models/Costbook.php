<?php   

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Costbook
 *
 * @author ideas2it
 */
class Costbook extends Item {
    public static function getByName($name)
    {
        return self::getByNameOrEquivalent('productname', $name);	
    }

    public static function getModuleClassName()
    {
        return 'CostbookModule';
    }

    public function __toString()
    {
        try
        {
            if (trim($this->productname) == '')
            {
                return Zurmo::t('Core', '(Unnamed)');
            }
            return $this->productname;
        }
        catch (AccessDeniedSecurityException $e)
        {
            return '';
        }
    }

    public static function getByProductCode($productCode) {
        return  self::makeModels(ZurmoRedBean::find('costbook', "productcode = :productcode ", array(':productcode' => $productCode)));
    }
    
    public static function getByProductCodeInQuery($productCode) {
        $query = 'SELECT productcode, description FROM costbook WHERE productcode IN (' . $productCode . ')';
        return ZurmoRedBean::getAll($query);
    }

    public static function getByMaxProductCodeInQuery() {
        $query = 'SELECT ifnull(max(productcodevalue)+1,1) as maxproductcode FROM costbook';
        return ZurmoRedBean::getAll($query);
    }

    public static function getByCodeAndId($productCode, $id) {
        return  self::makeModels(ZurmoRedBean::find('costbook', "productcode = :productcode AND id != :id", array(':productcode' => $productCode, ':id' => $id)));
    }

    public static function getAllAssemblyDetails() {
        return  ZurmoRedBean::getAll("SELECT assemblydetail FROM costbook WHERE costofgoodssold_customfield_id IN (SELECT id FROM customfield WHERE VALUE = 'Assembly') AND assemblydetail != ''");
    }

    public static function getAssemblySearch($category, $costOfGoods, $productId, $productName, $sortFor, $sortOrder) {
        $data = Costbook::getById($productId);
        $vCondition = '';
        $vAssemblyDetails = '';
        $assembly_count = 0;

        if($productId != '0') {
            if( $data->assemblydetail != '' && $data->assemblydetailsearch == '(None)' ) {
                $vAssemblyDetails = explode(';', $data->assemblydetail);
            } else if( $data->assemblydetail == '' && $data->assemblydetailsearch != '(None)' ) {
                $vAssemblyDetails = explode(', ', $data->assemblydetailsearch);
            } else if($data->assemblydetail != '' && $data->assemblydetailsearch != '(None)' ) {
                $assembly_details = explode(';', $data->assemblydetail);
                $vAssemblySearchDetails = explode(', ', $data->assemblydetailsearch);
                $vAssemblyDetails = array_merge($assembly_details, $vAssemblySearchDetails);
            }
            $assembly_count = count($vAssemblyDetails);
        }
        if( $vAssemblyDetails != '' ) {
            $productCodes = '';
            for($i=0; $i< $assembly_count; $i++) {
                $str = explode('|', $vAssemblyDetails[$i]);
                if($i == ($assembly_count-1))
                    $productCodes .= '"'.$str[1].'"';
                else
                    $productCodes .= '"'.$str[1].'",';
            }
            $vCondition = 'AND productcode NOT IN('.$productCodes.')';
        }
        
        // For the product name search
        $ProductNameSearchCondition = '';
        if($productName != '') {
            $trimmedProdName = trim($productName);
            $ProductNameSearchCondition =  " AND productname Like '%$trimmedProdName%'";
        }
        
        // For the product sort    
        $productSortCondition =  " A.productname ".$sortOrder;
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


        $query = 'SELECT A.*, D.value AS UnitOfMeasure FROM 
        costbook A 
        LEFT JOIN customfieldvalue AS B ON B.multiplevaluescustomfield_id=A.category_multiplevaluescustomfield_id
        LEFT JOIN customfield AS C ON C.id =A.costofgoodssold_customfield_id
        LEFT JOIN customfield AS D ON A.unitofmeasure_customfield_id =D.id
        WHERE 
        IF("ALL"="'.$category.'",TRUE,B.VALUE="'.$category.'")
        AND IF("ALL"="'.$costOfGoods.'",C.VALUE != "Assembly",C.VALUE="'.$costOfGoods.'")  AND A.id != '.$productId.' 
        '.$vCondition.$ProductNameSearchCondition.'
        GROUP BY A.ID order by'.$productSortCondition;
        return  ZurmoRedBean::getAll($query);
    }

    protected function beforeSave() {
        if (parent::beforeSave()) {
            $this->productkey = 'GICRM|'.$this->productcode;
            $currencyHelper = Yii::app()->currencyHelper;
            if($this->costofgoodssold->value == 'Labor') {
                if($this->departmentreference->id < 0 ) {
                    $this->addError('departmentreference', Zurmo::t('Core', 'Department Reference cannot be blank'));
                    $currencyHelper->resetErrors();
                    return false;
                } else { 
                    $deptRefId = $this->departmentreference->id;
                    $deptRefs = DepartmentReference::getDeptRefById(intval($deptRefId));
                    $this->laborCost = $deptRefs[0]->laborCost;
                    $this->burdenCost = $deptRefs[0]->burdonCost;
                    $this->costperunit = ($this->laborCost+$this->burdenCost);
                    $this->unitdirectcost = ($this->laborCost+$this->burdenCost);
                }
                if($this->id < 0) {
                    $rest = substr($this->productcode,0,2);
                    if($rest != 'L-') {
                        $this->productcode = 'L-'.$this->productcode;
                    }    
                }
                return true;
            }
            if($this->costofgoodssold->value == 'Equipment' || $this->costofgoodssold->value == 'Material' || $this->costofgoodssold->value == 'Subcontractor' || $this->costofgoodssold->value == 'Other') {
                if($this->unitofmeasure == '(None)' ) {
                    $this->addError('unitofmeasure', Zurmo::t('Core', 'Please select Unit Of Measure'));
                    $currencyHelper->resetErrors();
                    return false;
                }
                if($this->costperunit == '' ) {
                    $this->addError('costperunit', Zurmo::t('Core', 'Cost Rate cannot be blank'));
                    $currencyHelper->resetErrors();
                    return false;
                } else {
                    $cost = floatval($this->costperunit);
                    $this->unitdirectcost = $cost;
                }
                if($this->id < 0) {
                    if($this->costofgoodssold->value == 'Equipment') {
                        $rest = substr($this->productcode,0,2);
                        if($rest != 'E-') {
                            $this->productcode = 'E-'.$this->productcode;
                        }
                    }
                    if($this->costofgoodssold->value == 'Material') {
                        $rest = substr($this->productcode,0,2);
                        if($rest != 'M-') {
                            $this->productcode = 'M-'.$this->productcode;
                        }    
                    }
                    if($this->costofgoodssold->value == 'Subcontractor') {
                        $rest = substr($this->productcode,0,2);
                        if($rest != 'S-') {
                            $this->productcode = 'S-'.$this->productcode;
                        }    
                    }
                    if($this->costofgoodssold->value == 'Other') {
                        $rest = substr($this->productcode,0,2);
                        if($rest != 'O-') {
                            $this->productcode = 'O-'.$this->productcode;
                        }    
                    }
                }   
                return true;
            }
            if($this->costofgoodssold->value == 'Assembly') {
                if(empty($_POST['Costbook']['assemblydetailsearch']['values']) && (preg_match('/edit/', $_SERVER['REQUEST_URI'])) ) {
                   if(empty($this->assemblydetail))
                   {
                    $this->assemblydetailsearch = NULL;
                    $this->assemblydetail = NULL;
                   }
                }
                if($this->unitofmeasure == '(None)' ) {
                    $this->addError('unitofmeasure', Zurmo::t('Core', 'Please select Unit Of Measure'));
                    $currencyHelper->resetErrors();
                    return false;
                }
                if($this->id < 0) {
                    $rest = substr($this->productcode,0,2);
                    if($rest != 'A-') {
                        $this->productcode = 'A-'.$this->productcode;
                    }    
                }
                return true;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Returns the display name for the model class.
     * @return dynamic label name based on module.
     */
    public static function getProductCode($productCode) {
        return  self::makeModels(ZurmoRedBean::find('costbook', "productcode = :productcode ", array(':productcode' => $productCode)));
    }

    protected static function getLabel()
    {
        return 'CostbookModuleSingularLabel';
    }

    /**
     * Returns the display name for plural of the model class.
     * @return dynamic label name based on module.
     */
    protected static function getPluralLabel()
    {
        return 'CostbookModulePluralLabel';
    }

    public static function canSaveMetadata()
    {
        return true;
    }

    //In this section we define medata for the class (for example fields of type integer, float or string)
    public static function getDefaultMetadata()
    {
        $metadata = parent::getDefaultMetadata();
        $metadata[__CLASS__] = array(
            'members' => array(
                'assembly_productcode',
                'productname',
                'costperunit',
                'prevcostperunit',
                'description',
                'active',
                'assemblydetail',
                'assemblydetailold',
                'assemblydetailsearch',
                'assemblydetailsearchtext',
                'failurereplacementrate',
                'finalcost',
                'newassembly',
                'oldassemblyid',
                'oldcostbookid',
                'productcode',
                'productkey',
                'productkeysortorder',
                'proposaltext',
                'scopeofwork',
                'selectedassemblydetails',
                'sortorder',
                'unitdirectcost',
                'laborCost',
                'burdenCost',
                'assemblycreatefinal',
                'productcodevalue'
            ),
            'relations'=>array(
                'departmentreference'   => array(static::HAS_ONE,   'DepartmentReference'),
                'costofgoodssold' => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,RedBeanModel::LINK_TYPE_SPECIFIC, 'costofgoodssold'),
                'costofgoodssoldassembly' => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,RedBeanModel::LINK_TYPE_SPECIFIC, 'costofgoodssoldassembly'),
                'unitofmeasure' => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,RedBeanModel::LINK_TYPE_SPECIFIC, 'unitofmeasure'),
                'category' => array(RedBeanModel::HAS_ONE,   'OwnedMultipleValuesCustomField', RedBeanModel::OWNED,RedBeanModel::LINK_TYPE_SPECIFIC, 'category'),
                'assemblycategory' => array(RedBeanModel::HAS_ONE,   'OwnedCustomField', RedBeanModel::OWNED,RedBeanModel::LINK_TYPE_SPECIFIC, 'assemblycategory'),
                'assemblydetailsearch' => array(RedBeanModel::HAS_ONE,   'OwnedMultipleValuesCustomField', RedBeanModel::OWNED,RedBeanModel::LINK_TYPE_SPECIFIC, 'assemblydetailsearch'),
            ),
            'rules' => array(
               // array('productcode',        'required'),
               // array('productcode',          'length',  'min'  => 1, 'max' => 15),
                array('assembly_productcode', 'type',    'type' => 'string'),
                array('productname',      'type',    'type' => 'string'),
                array('productname',        'required'),
                array('productname',          'length',  'min'  => 1, 'max' => 64),
                array('licensenumber', 'type',    'type' => 'integer'),
                array('costperunit', 'type',    'type' => 'float'),
                array('prevcostperunit', 'type',    'type' => 'float'),
                array('description',      'type',    'type' => 'string'),
                array('active',      'type',    'type' => 'string'),
                array('assemblydetail',      'type',    'type' => 'string'),
                array('assemblydetailold',      'type',    'type' => 'string'),
                array('assemblydetailsearch',      'type',    'type' => 'string'),
                array('assemblydetailsearchtext',      'type',    'type' => 'string'),
                array('category',        'required'),
                array('costofgoodssold',      'type',    'type' => 'string'),
                array('failurereplacementrate',      'type',    'type' => 'string'),
                array('finalcost',      'type',    'type' => 'float'),
                array('newassembly',      'type',    'type' => 'string'),
                array('oldassemblyid',      'type',    'type' => 'string'),
                array('oldcostbookid',      'type',    'type' => 'string'),
                array('productcode',      'type',    'type' => 'string'),
                array('productkey',      'type',    'type' => 'string'),
                array('productkeysortorder',      'type',    'type' => 'string'),
                array('proposaltext',      'type',    'type' => 'string'),
                array('scopeofwork',      'type',    'type' => 'string'),
                array('selectedassemblydetails',      'type',    'type' => 'string'),
                array('sortorder',      'type',    'type' => 'string'),
                array('unitdirectcost',      'type',    'type' => 'float'),
                array('unitofmeasure',      'type',    'type' => 'string'),
                array('laborCost',    'type',    'type' => 'float'),
                array('burdenCost',      'type',    'type' => 'float'),
                array('assemblycreatefinal', 'type', 'type' => 'integer'),
                array('productcodevalue', 'type', 'type' => 'integer'),
            ),
            'elements' => array(
                'departmentreference'     => 'DepartmentReference',
                'assembly_productcode'  => 'Text',
                'productcode'           => 'Text',
                'productname'           => 'Text',	
                'costperunit'           => 'Decimal',
                'prevcostperunit'       => 'Decimal',
                'description'           => 'TextArea',
                'laborCost'             => 'Text',
                'burdenCost'            => 'Text',
                'category'            => 'MultiSelectDropDown',
            ),
            'customFields' => array(
                'costofgoodssold'   => 'CostOfGoodsTypes',
                'unitofmeasure'     => 'UnitOfMeasureTypes',
                'category'     	=> 'CategoryTypes',
                'assemblydetailsearch'     	=> 'AssemblyDetailSearchTypes',
            ),
            'defaultSortAttribute' => 'createdDateTime',                    

            );
        return $metadata;
    }
    public static function isTypeDeletable()
    {
        return true;
    }

    public static function getRollUpRulesType()
    {
        return 'Costbook';
    }        

    public static function hasReadPermissionsOptimization()
    {
        return true;
    }

    public static function hasReadPermissionsSubscriptionOptimization()
    {
        return true;
    }

    public function setNextItemNumber()
    {

    }

    protected static function translatedAttributeLabels($language)
    {
        $params = LabelUtil::getTranslationParamsForAllModules();
        $paramsForAffiliations = $params;
        return array_merge(parent::translatedAttributeLabels($language),
            array(
                'departmentreference'   => Zurmo::t('DepartmentReferencesModule',      'DepartmentReferencesModuleSingularLabel', $params, null, $language),
                'assembly_productcode'  => Zurmo::t('CostbookModule', 'Assembly Product Code', $params, null, $language),
                'productname'           => Zurmo::t('CostbookModule', 'Product Name', $params, null, $language),
                'licensenumber'         => Zurmo::t('CostbookModule', 'License Number',     	     $params, null, $language),
                'costperunit'           => Zurmo::t('CostbookModule', 'Cost Rate',          $params, null, $language),
                'laborCost'             => Zurmo::t('CostbookModule', 'Labor Cost',          $params, null, $language),
                'burdenCost'            => Zurmo::t('CostbookModule', 'Department Overhead',          $params, null, $language),
                'description'           => Zurmo::t('CostbookModule', 'Description',				 $params, null, $language),
                'active'                => Zurmo::t('CostbookModule',   'Active',				     $params, null, $language),
                'assemblydetail'        => Zurmo::t('CostbookModule',   'Assembly Detail',		    $params, null, $language),
                'assemblydetailold'     => Zurmo::t('CostbookModule',   'Assembly Detail Old',		$params, null, $language),
                'assemblydetailsearch'  => Zurmo::t('CostbookModule',   'Assembly Details',	$params, null, $language),
                'assemblydetailsearchtext'=> Zurmo::t('CostbookModule', 'Assembly Detail Search Text',$params, null, $language),
                'category'              => Zurmo::t('CostbookModule',   'Category', $params, null, $language),
                'assemblycategory'      => Zurmo::t('CostbookModule',   'Select Category', $params, null, $language),
                'costofgoodssold'       => Zurmo::t('CostbookModule',   'Cost of Goods Sold', $params, null, $language),
                'costofgoodssoldassembly'=> Zurmo::t('CostbookModule',   'Select COGS', $params, null, $language),
                'departmentreference'   => Zurmo::t('CostbookModule',   'Department Reference', $params, null, $language),
                'failurereplacementrate'=> Zurmo::t('CostbookModule',   'Failure/Replacement Rate', $params, null, $language),
                'finalcost'             => Zurmo::t('CostbookModule',   'Final Cost', $params, null, $language),
                'newassembly'           => Zurmo::t('CostbookModule',   'New Assembly',				 $params, null, $language),
                'oldassemblyid'         => Zurmo::t('CostbookModule',   'Old Assembly Id',				 $params, null, $language),
                'oldcostbookid'         => Zurmo::t('CostbookModule',   'Old Cost Book Id',				 $params, null, $language),
                'productcode'           => Zurmo::t('CostbookModule',   'Product Code',				 $params, null, $language),
                'productkey'            => Zurmo::t('CostbookModule',   'Product Key',				 $params, null, $language),
                'productkeysortorder'   => Zurmo::t('CostbookModule',   'Product Key Sort Order',	 $params, null, $language),			
                'proposaltext'          => Zurmo::t('CostbookModule',   'Proposal Text',				 $params, null, $language),
                'scopeofwork'           => Zurmo::t('CostbookModule',   'Scope of Work',				 $params, null, $language),
                'selectedassemblydetails'=> Zurmo::t('CostbookModule',  'Selected Assembly Details', $params, null, $language),
                'sortorder'             => Zurmo::t('CostbookModule',   'Sort Order',	$params, null, $language),
                'unitdirectcost'        => Zurmo::t('CostbookModule',   'Unit Direct Cost',				 $params, null, $language),
                'unitofmeasure'         => Zurmo::t('CostbookModule',   'Unit of Measure',				 $params, null, $language),

            )
        );
    }

}
