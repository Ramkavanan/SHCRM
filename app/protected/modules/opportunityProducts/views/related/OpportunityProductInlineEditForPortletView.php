<?php
    /**
     * Class used for wrapping a note inline edit view into a portlet ready view.
     */
    class OpportunityProductInlineEditForPortletView extends InlineEditForPortletView
    {
        public function getTitle()
        {
            $title  = Zurmo::t('OpportunitiesModule', 'Opportunity Products');
            return $title;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array(
                                'perUser' => array(
                                    'title' => "Opportunity Products",
                            ));
            return $metadata;
        }

        protected function renderInlineEditContent()
        {
            if (null != $messageContent = RequiredAttributesValidViewUtil::
                                         resolveValidView('OpportunitiesModule', $this->getInlineEditViewClassName()))
            {
                $message = Zurmo::t('NotesModule', 'The NotesModulePluralLabel form cannot be displayed.',
                           LabelUtil::getTranslationParamsForAllModules());
                $message .= '<br/>' . $messageContent . '<br/><br/>';
                return $message;
            }
            $note         = new OpportunityProduct();
            //$note->activityItems->add($this->params["relationModel"]);
            $inlineViewClassName = $this->getInlineEditViewClassName();

            $urlParameters = array('redirectUrl' => $this->getPortletDetailsUrl()); //After save, the url to go to.
            $uniquePageId  = get_called_class();
            $inlineView    = new $inlineViewClassName($note, 'default', 'notes', 'inlineCreateSave',
                                                      $urlParameters, $uniquePageId);
            return $inlineView->render();
        }

        public function getInlineEditViewClassName()
        {
            return 'OpportunityProductInlineEditView';
        }

        /**
         * The view's module class name.
         */
        public static function getModuleClassName()
        {
            return 'OpportunitiesModule';
        }

        public static function getAllowedOnPortletViewClassNames()
        {
            return array('OpportunityDetailsAndRelationsView');
        }
    }
?>
