<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTracking extends Item {

    public function __toString() {
        if (trim($this->name) == '') {
            return Yii::t('Default', '(Unnamed)');
        }
        return $this->name;
    }

    public static function getModuleClassName() {
        return 'AgreementTrackingModule';
    }

    public static function canSaveMetadata() {
        return true;
    }

    public static function getDefaultMetadata() {
        $metadata = parent::getDefaultMetadata();
        $metadata[__CLASS__] = array(
            'members' => array(
                'name',
                'consumed_unit',
                'remaining_unit',
                'total_mhr',
                'total_material_units',
                'total_equipment_units',
                'total_quantity_consumed',
                'total_selected_products',
                'total_non_agreement_products',
                'tracking_date',
                'year_to_date_mhr',
                'year_to_date_material_units',
                'year_to_date_equipment_units',
                'reset_number',
            ),
            'relations' => array(
                'agreement'     => array(static::HAS_ONE,   'Agreement'),
                'routetracking'     => array(static::HAS_ONE,   'RouteTracking'),
                'agreementtrackingproducts'             => array(RedBeanModel::HAS_MANY,   'AgreementTrackingProducts'),
            ),
            
            'rules' => array(
                array('name', 'type', 'type' => 'string'),
                array('name', 'length', 'max' => 100),
                array('consumed_unit', 'type', 'type' => 'float'),
                array('consumed_unit', 'length', 'max' => 18),
                array('consumed_unit', 'numerical', 'precision' => 4),
                array('remaining_unit', 'type', 'type' => 'float'),
                array('remaining_unit', 'length', 'max' => 18),
                array('remaining_unit', 'numerical', 'precision' => 4),
                array('total_mhr', 'type', 'type' => 'float'),
                array('total_mhr', 'length', 'max' => 18),
                array('total_mhr', 'numerical', 'precision' => 4),
                array('total_material_units', 'type', 'type' => 'float'),
                array('total_material_units', 'length', 'max' => 18),
                array('total_material_units', 'numerical', 'precision' => 4),
                array('total_equipment_units', 'type', 'type' => 'float'),
                array('total_equipment_units', 'length', 'max' => 18),
                array('total_equipment_units', 'numerical', 'precision' => 4),
                array('total_quantity_consumed', 'type', 'type' => 'float'),
                array('total_quantity_consumed', 'length', 'max' => 18),
                array('total_quantity_consumed', 'numerical', 'precision' => 4),
                array('total_selected_products', 'type', 'type' => 'integer'),
                array('total_non_agreement_products', 'type', 'type' => 'integer'),
                array('tracking_date', 'type', 'type' => 'date'),
                array('year_to_date_mhr', 'length', 'max' => 18),
                array('year_to_date_mhr', 'numerical', 'precision' => 4),
                array('year_to_date_mhr', 'type', 'type' => 'float'),
                array('year_to_date_material_units', 'length', 'max' => 18),
                array('year_to_date_material_units', 'numerical', 'precision' => 4),
                array('year_to_date_material_units', 'type', 'type' => 'float'),
                array('year_to_date_equipment_units', 'length', 'max' => 18),
                array('year_to_date_equipment_units', 'numerical', 'precision' => 4),
                array('year_to_date_equipment_units', 'type', 'type' => 'float'),
                array('reset_number', 'type', 'type' => 'integer'),
            ),
            'elements' => array(
                'name' => 'Text',
                'consumed_unit' => 'Decimal',
                'remaining_unit' => 'Decimal',
                'total_mhr' => 'Decimal',
                'total_material_units' => 'Decimal',
                'total_equipment_units' => 'Decimal',
                'total_quantity_consumed' => 'Decimal',
                'total_selected_products' => 'Integer',
                'total_non_agreement_products' => 'Integer',
                'tracking_date' => 'Date',
                'year_to_date_mhr' => 'Decimal',
                'year_to_date_material_units' => 'Decimal',
                'year_to_date_equipment_units' => 'Decimal',
            ),
            'defaultSortAttribute' => 'name',
        );
        return $metadata;
    }

    public static function isTypeDeletable() {
        return true;
    }

    protected static function translatedAttributeLabels($language) {
        $params = LabelUtil::getTranslationParamsForAllModules();
        return array_merge(parent::translatedAttributeLabels($language), array(
            'name' => Zurmo::t('AgreementTrackingModule', 'Name', $params, null, $language),
            'agreement'   => Zurmo::t('AgreementsModule',  'AgreementsModuleSingularLabel', $params, null, $language),
            'consumed_unit' => Zurmo::t('AgreementTrackingModule', 'Consumed Unit', $params, null, $language),
            'remaining_unit' => Zurmo::t('AgreementTrackingModule', 'Remaining Unit', $params, null, $language),
            'total_mhr' => Zurmo::t('AgreementTrackingModule', 'Total MHR', $params, null, $language),
            'total_material_units' => Zurmo::t('AgreementTrackingModule', 'Total Material Units', $params, null, $language),
            'total_equipment_units' => Zurmo::t('AgreementTrackingModule', 'Total Equipment Units', $params, null, $language),
            'total_quantity_consumed' => Zurmo::t('AgreementTrackingModule', 'Total Quantity Consumed', $params, null, $language),
            'total_selected_products' => Zurmo::t('AgreementTrackingModule', 'Total Selected Products', $params, null, $language),
            'total_non_agreement_products' => Zurmo::t('AgreementTrackingModule', 'Total Add-on Sales', $params, null, $language),
            'tracking_date' => Zurmo::t('AgreementTrackingModule', 'Tracking Date', $params, null, $language),
            'year_to_date_mhr' => Zurmo::t('AgreementTrackingModule', 'Year To Date MHR', $params, null, $language),
            'year_to_date_material_units' => Zurmo::t('AgreementTrackingModule', 'Year To Date Material Units', $params, null, $language),
            'year_to_date_equipment_units' => Zurmo::t('AgreementTrackingModule', 'Year To Date Equipment Units', $params, null, $language),
                )
        );
    }

    public static function getAgreementTrackingById($id) {
        return self::makeModels(ZurmoRedBean::find('agreementtracking', "id =:id", array(':id' => $id)));
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

    public static function getAgmtTrackingByRouteTrackingId($route_track_id) {
        return self::makeModels(ZurmoRedBean::find('agreementtracking', "routetracking_id =:routetracking_id", array(':routetracking_id' => $route_track_id)));
    }
    
    public static function getAgreementTrackingByAgreementId($agreement_id) {
         return self::makeModels(ZurmoRedBean::find('agreementtracking', "agreement_id =:agreement_id", array(':agreement_id' => $agreement_id)));
    }
    
    // For the Agmt Tracking Reset 
    public static function getAgreementTrackingByAgreementIdForReset($agreement_id) {
         return self::makeModels(ZurmoRedBean::find('agreementtracking', "agreement_id =:agreement_id AND reset_number is null", array(':agreement_id' => $agreement_id)));
    }
}
?>