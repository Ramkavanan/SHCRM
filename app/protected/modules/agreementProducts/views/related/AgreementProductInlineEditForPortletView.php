<?php
    /**
     * Class used for wrapping a note inline edit view into a portlet ready view.
     */
    class AgreementProductInlineEditForPortletView extends InlineEditForPortletView
    {
        public function getTitle()
        {
            $title  = Zurmo::t('AgreementsModule', 'Agreement Products');
            return $title;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array(
                                'perUser' => array(
                                    'title' => "Agreement Products",
                            ));
            return $metadata;
        }

        protected function renderInlineEditContent()
        {
            if (null != $messageContent = RequiredAttributesValidViewUtil::
                                         resolveValidView('AgreementsModule', $this->getInlineEditViewClassName()))
            {
                $message = Zurmo::t('NotesModule', 'The NotesModulePluralLabel form cannot be displayed.',
                           LabelUtil::getTranslationParamsForAllModules());
                $message .= '<br/>' . $messageContent . '<br/><br/>';
                return $message;
            }
            $note         = new AgreementProduct();
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
            return 'AgreementProductInlineEditView';
        }

        /**
         * The view's module class name.
         */
        public static function getModuleClassName()
        {
            return 'AgreementsModule';
        }

        public static function getAllowedOnPortletViewClassNames()
        {
            return array('AgreementDetailsAndRelationsView');
        }
    }
?>
