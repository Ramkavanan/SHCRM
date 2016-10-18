<?php

    class AgreementProductSelectFromRelatedListAjaxLinkActionElement extends SelectFromRelatedListAjaxLinkActionElement
    {
        /**
         * @return string
         */
        protected function getDefaultRoute()
        {
            return Yii::app()->createUrl('agreementProducts/' . $this->controllerId . '/selectFromRelatedList/',
                    array(
                        'uniqueLayoutId'          => $this->getUniqueLayoutId(),
                        'portletId'               => $this->getPortletId(),
                        'relationAttributeName'   => $this->params['relationAttributeName'],
                        'relationModelId'         => $this->params['relationModelId'],
                        'relationModuleId'        => $this->params['relationModuleId'],
                        'relationModelClassName'  => $this->getRelationModelClassName(),
                    )
            );
        }

        /**
         * @return string
         */
        protected function getDefaultLabel()
        {
            $params = LabelUtil::getTranslationParamsForAllModules();
            return 'Select';//Zurmo::t('ProductsModule', 'Select ProductTemplatesModuleSingularLabel', $params);
        }
    }
?>
