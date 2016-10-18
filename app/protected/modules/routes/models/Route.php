<?php 
    class Route extends Item
    {
        CONST STEP1 = 1;
        
        CONST STEP2 = 2;
        
        CONST STEP3 = 3;
        
        CONST STEP4 = 4;
        
        public function __toString()
        {
            try
            {
                if (trim($this->name) == '')
                {
                    return Zurmo::t('Core', '(Unnamed)');
                }
                return $this->name;
            }
            catch (AccessDeniedSecurityException $e)
            {
                return '';
            }
        }

        public static function getModuleClassName()
        {
            return 'RoutesModule';
        }

        public static function translatedAttributeLabels($language)
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return array_merge(parent::translatedAttributeLabels($language), array(
		'name'        => Zurmo::t('RoutesModule',     'Route Name',  array(), null, $language),
                'crewname'    => Zurmo::t('RoutesModule', 'Crew Name',  array(), null, $language),
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
                    'crewname',
                ),
                'relations' => array(
                    'agreement'         => array(static::MANY_MANY,  'Agreement'),
                    'category'         => array(static::MANY_MANY,  'Category'),
                      
                ),
                
                'rules' => array(
                    array('name',          'required'),
                    array('name',          'type',      'type' => 'string'),
                    array('name',          'length',    'min'  => 1, 'max' => 100),
                    array('crewname',          'required'),
                    array('crewname',          'type',      'type' => 'string'),
                    array('crewname',          'length',    'min'  => 1, 'max' => 100),
                ),
                'elements' => array(
                    'name'  => 'Text',
                    'crewname'  => 'Text',
                ),
                'customFields' => array(
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
       
    }
?>
