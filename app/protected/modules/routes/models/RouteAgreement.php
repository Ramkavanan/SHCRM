<?php
    class RouteAgreement extends Item
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

                    ),
                'relations' => array(
                      'agreement'     => array(static::HAS_ONE,   'Agreement'),
                      'route' => array(static::HAS_ONE,   'Route'),
                ),
                
                'rules' => array(
                    
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
       
        public static function getAgmtByRouteId($id)
        {
           return self::makeModels(ZurmoRedBean::find('routeagreement', "route_id =:route_id ", array(":route_id" => $id)));
        }
    }
?>
