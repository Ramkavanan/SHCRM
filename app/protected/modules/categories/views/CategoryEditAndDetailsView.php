<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class CategoryEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'CancelLink', 'renderType' => 'Edit'),
                            array('type' => 'SaveButton', 'renderType' => 'Edit'),
                            array('type' => 'ListLink', 'renderType' => 'Details', 'label' => "eval:Yii::t('Default', 'Return to List')" ),
                            array('type' => 'EditLink', 'renderType' => 'Details'),
                            array('type' => 'AccountDeleteLink', 'renderType' => 'Details'),
                        ),
                    ),
                    'derivedAttributeTypes' => array(
                        'DateTimeCreatedUser',
                        'DateTimeModifiedUser',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'code', 'type' => 'Text'),
                                            ),
                                        ),

                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'targetgpm', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

        protected function getNewModelTitleLabel()
        {
            return Yii::t('Default', 'Create CategoriesModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }

		// To disable code in view page by Arif
        protected function resolveElementInformationDuringFormLayoutRender(& $elementInformation)
        {
            parent::resolveElementInformationDuringFormLayoutRender($elementInformation);
            $automaticDisable = CategoriesModule::isAutomaticCodeDisabled();
            if ($automaticDisable === false)
            {
                if ($elementInformation['attributeName'] == 'code' )
                {
                    $elementInformation['disabled'] = true;
                }
            }
        }
}
?>
