<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class RouteEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'CancelLink', 'renderType' => 'Edit'),
                            array('type' => 'NextButton', 'renderType' => 'Edit'),
                            array('type' => 'ListLink',
                                'renderType' => 'Details',
                                'label' => "eval:Yii::t('Default', 'Return to List')"
                            ),
                            array('type' => 'EditLink', 'renderType' => 'Details'),
                            array('type' => 'RouteDeleteLink', 'renderType' => 'Details',
                                'htmlOptions'    => array('onClick' => ''),
                                ),
                            array(
                                    'type' => 'TrackViewLink',
                                    'renderType' => 'Details',
                                    'htmlOptions'    => array('onClick' => 'window.open($(this).attr("href"), "popupWindow", "width=950,height=600,scrollbars=yes"); return false;')
                                ),
                            array('type' => 'RouteProductLink', 'renderType' => 'Details'),
                            array('type' => 'RouteCloneLink', 'renderType' => 'Details'),
                        ),
                    ),
                    'derivedAttributeTypes' => array(
                        'DateTimeCreatedUser',
                        'DateTimeModifiedUser',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                                    array(
                                        'title' => 'Route Information',
                                        'rows' => array(
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
                                                            array('attributeName' => 'crewname', 'type' => 'Text'),
                                                        ),
                                                    ),
                                                )
                                            ),
                                            

                                        ),
                                    ),
                                   /* array(
                                        'title' => 'Categories Information',
                                        'rows' => array(
                                            array('cells' =>
                                                array(
                                                    array(
                                                        'elements' => array(
                                                            array('attributeName' => 'category', 'type' => 'MultiSelectDropDown'),
                                                        ),

                                                    ),
                                                )
                                            ),
                                        ),                                        
                                    ), */
                                ),
                ),
            );
            return $metadata;
        }

        protected function getNewModelTitleLabel()
        {
            //return Yii::t('Default', 'Create RoutesModuleSingularLabel',
                                   //  LabelUtil::getTranslationParamsForAllModules());
            return "Create Route - (Step 1 of 4)";
        }
    }   
	
?>
