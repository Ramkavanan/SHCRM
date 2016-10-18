<?php

    class AgreementProductsModalListView extends ModalListView
    {
        public static function getDefaultMetadata()   {
            $metadata = array(
                'global' => array(
                    'derivedAttributeTypes' => array(
                        'name',
                    ),
                    'panels' => array(
                        array(
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text', 'isLink' => true),
                                            ),
                                        ),
                                    )
                                )
                            ),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

	protected function renderScripts()     {
           parent::renderScripts();
           Yii::app()->clientScript->registerScriptFile(Yii::app()->getAssetManager()->publish(
                    Yii::getPathOfAlias('application.modules.agreementProducts.elements.assets')) . '/AgreementProductTemplateUtils.js',
                CClientScript::POS_END);
        }
    }
?>
