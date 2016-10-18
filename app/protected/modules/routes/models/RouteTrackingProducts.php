<?php
    class RouteTrackingProducts extends Item
    {
        public static function getModuleClassName()
        {
            return 'RoutesModule';
        }

        public static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language), array(

                ));
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
                        'consumed_unit'
                    ),
                'relations' => array(
                      'agreementproduct' => array(static::HAS_ONE,   'AgreementProduct'),
                      'routetracking' => array(static::HAS_ONE,   'RouteTracking'),
                      'agreement' => array(static::HAS_ONE,   'Agreement'),
                ),
                
                'rules' => array(
                    array('consumed_unit', 'type', 'type' => 'float'),
                    array('consumed_unit', 'length', 'max' => 18),
                    array('consumed_unit', 'numerical', 'precision' => 4),
                ),
                'elements' => array(
                ),
                'customFields' => array(

                 ),
                'noAudit' => array(
                    
                ),
            );
            return $metadata;
        }

        public static function isTypeDeletable()
        {
            return true;
        }
       
        public static function getTrackingProdByRouteTrackingId($id){
            return self::makeModels(ZurmoRedBean::find('routetrackingproducts', "routetracking_id =:routetracking_id ", array(":routetracking_id" => $id)));
        }
    }
?>
