<?php

    /**
     * Class utilized by 'select' modal popup in the edit view
     */
    class OpportunityProductTemplateSelectFromRelatedListModalListLinkProvider extends SelectFromRelatedListModalListLinkProvider
    {
        /**
         * @param string $attributeString
         * @return string
         */
        public function getLinkString($attributeString)
        {
            $url = Yii::app()->createUrl("opportunityProducts/default/addOpportunityRelation", array('relationModuleId' => $this->relationModuleId,
                                                    'portletId' => $this->portletId,
                                                    'uniqueLayoutId' => $this->uniqueLayoutId));
            $errorInProcess = CJavaScript::quote(Zurmo::t('Core', 'There was an error processing your request'));
            $string  = 'ZurmoHtml::link(';
            $string .= $attributeString . ', ';
            $string .= '"javascript:addProductRowToPortletGridView(\'$data->id\', \'' . $url . '\', \'' . $this->relationAttributeName . '\', \'' . $this->relationModelId . '\'
                , \'' . $this->uniqueLayoutId . '\', \'' . $errorInProcess . '\')"';
            $string .= ')';
            return $string;
        }
    }
?>
