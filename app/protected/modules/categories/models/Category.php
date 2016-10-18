<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Category extends Item {
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
        return 'CategoriesModule';
    }

    public static function canSaveMetadata()
    {
        return true;
    }

    public static function getCategoryByName($name) {
        return  self::makeModels(ZurmoRedBean::find('category', "name = :name ", array(':name' => $name)));
    }

    public static function getCategoryByNameEdit($name, $id) {
        return  self::makeModels(ZurmoRedBean::find('category', "name = :name AND id != :id", array(':name' => $name, ':id' => $id)));
    }

    public static function isCategoryInUseById($categoryId)
    {
        $categories = Category::getById($categoryId);
        $rows = ZurmoRedBean::getAll('SELECT * FROM customfieldvalue WHERE multiplevaluescustomfield_id IN (SELECT category_multiplevaluescustomfield_id FROM costbook) AND VALUE="'.$categories->name.'"');
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
                'targetgpm',
            ),
            'relations' => array(
               'costbook'       => array(static::HAS_ONE,   'Costbook'),
            ),    
           'rules' => array(
                array('code',   'type',     'type'  => 'string'),
                array('name',   'required'),
                array('name',    'match',  'pattern' => '/^[a-zA-Z0-9]+( [a-zA-Z0-9]+)*$/', 'message' => 'Invalid Name'),
                array('name',   'type',     'type'  => 'string'),
                array('name',   'length',   'max'   => 100),
                array('targetgpm',   'required'),
                array('targetgpm',   'required'),
                array('targetgpm',   'type',     'type'  => 'integer'),
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
                'name'       => Zurmo::t('CategoriesModule', 'Name',  $params, null, $language),
                'code'       => Zurmo::t('CategoriesModule', 'Code',  $params, null, $language),
                'targetgpm'  => Zurmo::t('CategoriesModule', 'Target GPM (%)',  $params, null, $language),
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
