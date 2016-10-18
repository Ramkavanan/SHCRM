<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class DepartmentReference extends Item {
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
            return 'DepartmentReferencesModule';
        }

		public static function getDeptRefById($id) {
			return self::makeModels(ZurmoRedBean::find('departmentreference', "id =:id", array(':id' => $id)));
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
                    'description',
                    'burdonCost',
                    'prevBurdonCost',
                    'laborCost',
                    'prevLaborCost',
                ),
               
                'rules' => array(
                    array('name',           'required'),
                    array('name',           'type',           'type'  => 'string'),
                    array('name',           'length',         'max'   => 100),
                    array('description',    'type',           'type'  => 'string'),
                    array('burdonCost',           'required'),
                    array('laborCost',           'required'),
                    array('burdonCost',   'length',         'max'   => 18),
                    array('burdonCost',   'numerical',      'precision' => 2),
                    array('burdonCost',   'type',           'type'   => 'float'),
                    array('prevBurdonCost',   'length',         'max'   => 18),
                    array('prevBurdonCost',   'numerical',      'precision' => 2),
                    array('prevBurdonCost',   'type',           'type'   => 'float'),
                    array('laborCost',   'length',         'max'   => 18),
                    array('laborCost',   'numerical',      'precision' => 2),
                    array('laborCost',   'type',           'type'   => 'float'),
                    array('prevLaborCost',   'length',         'max'   => 18),
                    array('prevLaborCost',   'numerical',      'precision' => 2),
                    array('prevLaborCost',   'type',           'type'   => 'float'),
                ),
                 'relations' => array(
                    'costbook'      => array(static::HAS_MANY, 'Costbook'),
                ),
                'elements' => array(
                    'description'   => 'TextArea',
                    'burdonCost'       => 'Decimal',
                    'prevBurdonCost'    => 'Decimal',
                    'laborCost' => 'Decimal',
                    'prevLaborCost' => 'Decimal',
                ),
                'defaultSortAttribute' => 'createdDateTime',
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
                    'name'              => Zurmo::t('DepartmentReferencesModule', 'Name',  $params, null, $language),
                    'description'       => Zurmo::t('DepartmentReferencesModule', 'Description',  $params, null, $language),
                    'burdonCost'           => Zurmo::t('DepartmentReferencesModule', 'Department Overhead',  $params, null, $language),
                    'laborCost'           => Zurmo::t('DepartmentReferencesModule', 'Labor Cost',  $params, null, $language),
                )
            );
        }
}
?>
