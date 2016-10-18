<?php
    class RouteCategory extends Item
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
                    'category' => array(RedBeanModel::HAS_ONE,   'Category'),
                    'route' => array(RedBeanModel::HAS_ONE,   'Route'),
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
        
        public static function getCatByRouteId($id)
        {
           return self::makeModels(ZurmoRedBean::find('routecategory', "route_id =:route_id ", array(":route_id" => $id)));
        }
       
    }
?>
