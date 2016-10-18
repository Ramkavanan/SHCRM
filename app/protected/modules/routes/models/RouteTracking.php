<?php
    class RouteTracking extends Item
    {
        public function __toString() {
            if (trim($this->name) == '') {
                return Yii::t('Default', '(Unnamed)');
            }
            return $this->name;
        }
        public static function getModuleClassName()
        {
            return 'RoutesModule';
        }

        public static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language), array(
                'name'    => Zurmo::t('RoutesModule', 'Name',  array(), null, $language),
                'date_of_service'    => Zurmo::t('RoutesModule', 'Date Of Service',  array(), null, $language),
                'service_start_time'    => Zurmo::t('RoutesModule', 'Service Start Time',  array(), null, $language),
                'service_end_time'    => Zurmo::t('RoutesModule', 'service End Time',  array(), null, $language),

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
                    'name',
                    'date_of_service',
                    'service_start_time',
                    'service_end_time',
                ),
                'relations' => array(
                      'route' => array(static::HAS_ONE,   'Route'),
                      'routetrackingproducts_id' => array(static::HAS_MANY,   'RouteTrackingProducts'),
                ),
                
                'rules' => array(
                    array('date_of_service',          'required'),
                    array('date_of_service',          'type',      'type' => 'string'),
                    array('name',          'type',      'type' => 'string'),
                    array('service_start_time',          'type',      'type' => 'string'),
                    array('service_end_time',          'type',      'type' => 'string'),
                    
                ),
                'elements' => array(
                    'name'  => 'Text',
                    'date_of_service'  => 'Text',
                    'service_start_time'  => 'Text',
                    'service_end_time'    => 'Text',
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
        
        public static function getTrackingByRouteId($id)
        {
           return self::makeModels(ZurmoRedBean::find('routetracking', "route_id =:route_id Order By id Desc", array(":route_id" => $id)));
        }       
    }
?>
