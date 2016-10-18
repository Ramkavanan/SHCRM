<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AgreementTrackingProducts extends Item {
    
    public static function getModuleClassName() {
        return 'AgreementTrackingProductsModule';
    }

    public static function canSaveMetadata() {
        return true;
    }
    
    public static function getDefaultMetadata() {
        $metadata = parent::getDefaultMetadata();
        $metadata[__CLASS__] = array(
            'members' => array(
                //'agreement_product_id',
                'agreement_tracking_id',
                'consumed_unit',
                'remaining_unit',
                'is_completed',
                'is_agreement_product',
                'reset_number',
            ),
            'rules' => array(
                //array('agreement_product_id', 'type', 'type' => 'integer'),
                array('agreement_tracking_id', 'type', 'type' => 'integer'),
                array('consumed_unit', 'type', 'type' => 'float'),
                array('consumed_unit', 'length', 'max' => 18),
                array('consumed_unit', 'numerical', 'precision' => 4),
                array('remaining_unit', 'type', 'type' => 'float'),
                array('remaining_unit', 'length', 'max' => 18),
                array('remaining_unit', 'numerical', 'precision' => 4),
                array('is_completed', 'type', 'type' => 'integer'),
                array('is_agreement_product', 'type', 'type' => 'integer'),
                array('reset_number', 'type', 'type' => 'integer'),
            ),
            'relations' => array(
                'agreementProduct'             => array(RedBeanModel::HAS_ONE,   'AgreementProduct'),
                'agreement'             => array(RedBeanModel::HAS_ONE,   'Agreement'),
            ),
            'elements' => array(
                //'agreement_product_id' => 'Integer',
                'agreement_tracking_id' => 'Integer',
                'consumed_unit' => 'Decimal',
                'remaining_unit' => 'Decimal',
                'is_completed' => 'Integer',
                'is_agreement_product' => 'Integer',
            ),
            'defaultSortAttribute' => 'name'
        );
        return $metadata;
    }
    
    public static function getAgreementTrackingProductByTrackingId($TrackingId) {
        return self::makeModels(ZurmoRedBean::find('agreementtrackingproducts', "agreement_tracking_id =:agreement_tracking_id", array(':agreement_tracking_id' => $TrackingId)));
    }
    
    public static function getAgreementTrackingProductByAgmtTrackingId($agreementTrackingId) {
        return self::makeModels(ZurmoRedBean::find('agreementtrackingproducts', "id =:agreementtracking_id", array(':agreementtracking_id' => $agreementTrackingId)));
    }
    
    public static function getUpdateCompletedProduct($productsIds){
        $query = 'UPDATE agreementtrackingproducts SET is_completed = 1 WHERE agreementproduct_id IN ('.$productsIds.')';
        return  ZurmoRedBean::getAll($query);
    }
    
    public static function getAgmtTrackingProductsByAgmtTrackingId($agreementTrackingId) {
        return self::makeModels(ZurmoRedBean::find('agreementtrackingproducts', "agreement_tracking_id =:agreement_tracking_id", array(':agreement_tracking_id' => $agreementTrackingId)));
    }
    
    // For the tracking reset
    public static function getAgreementTrackingProductByTrackingIdForReset($TrackingId) {
        return self::makeModels(ZurmoRedBean::find('agreementtrackingproducts', "agreement_tracking_id =:agreement_tracking_id AND reset_number is null", array(':agreement_tracking_id' => $TrackingId)));
    }
}
