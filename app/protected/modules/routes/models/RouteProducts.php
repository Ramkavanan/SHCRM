<?php
    class RouteProducts extends Item
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

        public static function getRouteProductsByRouteId($id) {
            return self::makeModels(ZurmoRedBean::find('routeproducts', "route_id =:id", array(':id' => $id)));
        }
        
        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    ),
                'relations' => array(
                      'agreement'     => array(RedBeanModel::HAS_ONE,   'Agreement'),
                      'route' => array(RedBeanModel::HAS_ONE,   'Route'),
                      'agreementproduct' => array(RedBeanModel::HAS_ONE,   'AgreementProduct'),
                ),
                
                'rules' => array(
                    array('agreement_id', 'type', 'type' => 'integer'),
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
        
        public static function getRouteProdByAgmtIdRouteId($route_id) {
            return self::makeModels(ZurmoRedBean::find('routeproducts', "route_id =:route_id", array(':route_id' => $route_id)));
        }
       
    }
?>
