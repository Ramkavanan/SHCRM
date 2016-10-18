<?php

    class AgreementProductsForAgreementRelatedListView extends AgreementProductsRelatedListView
    {
        /**
         * Override the panels and toolbar metadata.
         */
        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            /**$metadata['global']['toolbar']['elements'][] =
                                array('type'                 => 'AgreementProductSelectFromRelatedListAjaxLink',
                                    'portletId'              => 'eval:$this->params["portletId"]',
                                    'relationAttributeName'  => 'eval:$this->getRelationAttributeName()',
                                    'relationModelId'        => 'eval:$this->params["relationModel"]->id',
                                    'relationModuleId'       => 'eval:$this->params["relationModuleId"]',
                                    'uniqueLayoutId'         => 'eval:$this->uniqueLayoutId',
                                //TODO: fix this 'eval' of $this->uniqueLayoutId above so that it can properly work being set/get from DB then getting evaluated
                                //currently it will not work correctly since in the db it would store a static value instead of it still being dynamic
                                    'ajaxOptions' => 'eval:static::resolveOptionsForSelectList()',
                                    'htmlOptions' => array( 'id' => 'SelectAgreementProductsForAgreementFromRelatedListLink',
                                                            'live' => false) //This is there are no double bindings
            );*/
            $metadata['global']['panels'] = array(
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
                        ),
                       
                    ),
                ),
            );
            return $metadata;
        }

        public function renderPortletHeadContent()
        {
            return $this->renderWrapperAndActionElementMenu(Zurmo::t('Core', 'Options'));
        }

        protected function getRelationAttributeName()
        {
            return 'agreement';
        }

        public static function getDisplayDescription()
        {
            return 'AgreementProduct';
        }

        protected static function resolveOptionsForSelectList()
        {
            $title = 'AgreementProduct';

            return ModalView::getAjaxOptionsForModalLink($title);
        }

        public static function getAllowedOnPortletViewClassNames()
        {
            return array('AgreementDetailsAndRelationsView');
        }
    }
?>
