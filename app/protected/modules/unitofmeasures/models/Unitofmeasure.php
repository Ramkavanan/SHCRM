<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Unitofmeasure extends Item {
    public function __toString()
    {
        if (trim($this->name) == '')
        {
            return Yii::t('Default', '(Unnamed)');
        }
        return $this->name;
    }

    public static function getModuleClassName()
    {
        return 'UnitofmeasuresModule';
    }

    public static function canSaveMetadata()
    {
        return true;
    }

    public static function getUnitofmeasureByName($name) {
        return  self::makeModels(ZurmoRedBean::find('unitofmeasure', "name = :name ", array(':name' => $name)));
    }

    public static function getUnitofmeasureByNameEdit($name, $id) {
        return  self::makeModels(ZurmoRedBean::find('unitofmeasure', "name = :name AND id != :id", array(':name' => $name, ':id' => $id)));
    }

    public static function isUnitofmeasureInUseById($unitofmeasureId)
    {
        $unitofmeasures = Unitofmeasure::getById($unitofmeasureId);  
        $rows            = ZurmoRedBean::getAll('SELECT * FROM customfield WHERE id IN (SELECT unitofmeasure_customfield_id FROM costbook) AND VALUE="'.$unitofmeasures->name.'"');
        if(count($rows) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDefaultMetadata()
    {
        $metadata = parent::getDefaultMetadata();
        $metadata[__CLASS__] = array(
            'members' => array(
                'name',
                'code',
            ),
           'rules' => array(
                array('code',   'type',     'type'  => 'string'),
                array('name',   'required'),
                array('name',   'type',     'type'  => 'string'),
                array('name',   'length',   'max'   => 100),
                array('name',    'match',  'pattern' => '/^[a-zA-Z0-9]+( [a-zA-Z0-9]+)*$/', 'message' => 'Invalid Name'),
            ),
            'elements' => array(

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
                'name'       => Zurmo::t('UnitofmeasuresModule', 'Name',  $params, null, $language),
                'code'       => Zurmo::t('UnitofmeasuresModule', 'Code',  $params, null, $language),
            )
        );
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if($this->id < 0 ) {
                $this->code = preg_replace('/\s+/', '',$this->name);
            }
        }
        return true;
    }

}
?>
