<?php
    /**
     * An inline edit view for a note.
     *
     */
    class AgreementProductInlineEditView extends InlineEditView
    {
        protected $viewContainsFileUploadElement = true;

        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'SaveAndPostNoteToProfileButton'),
                        ),
                    ),
                 
                    'nonPlaceableAttributeNames' => array(
                        'latestDateTime',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_FIRST,
                    'panels' => array(
                        array(
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
                            ),
                        )
               
                    ),
                ),
            );
            return $metadata;
        }

        /**
         * Override to change the editableTemplate to place the label above the input.
         * @see DetailsView::resolveElementDuringFormLayoutRender()
         */
        protected function resolveElementDuringFormLayoutRender(& $element)
        {
            if ($element->getAttribute() == 'description')
            {
                $element->editableTemplate = '<td colspan="{colspan}">{content}{error}</td>';
            }
            elseif ($element instanceOf ActivityItemsElement)
            {
                $element->editableTemplate = '{content}{error}';
            }
            elseif ($element instanceOf DerivedExplicitReadWriteModelPermissionsElement)
            {
                $element->editableTemplate = '<td colspan="{colspan}">' .
                                             '<div class="permissions-box">{label}<br/>{content}{error}</div></td>';
            }
            elseif ($element instanceOf FilesElement)
            {
                $element->editableTemplate = '<td colspan="{colspan}">' .
                                             '<div class="file-upload-box">{content}{error}</div></td>';
            }
            else
            {
                $element->editableTemplate = '<td colspan="{colspan}">{label}<br/>{content}{error}</td>';
            }
        }

        /**
         * Override to allow the latest activities portlet, if it exists to be refreshed.
         * (non-PHPdoc)
         * @see InlineEditView::renderConfigSaveAjax()
         */
        protected function renderConfigSaveAjax($formName)
        {
            // Begin Not Coding Standard
            return ZurmoHtml::ajax(array(
                    'type' => 'POST',
                    'data' => 'js:$("#' . $formName . '").serialize()',
                    'url'  =>  $this->getValidateAndSaveUrl(),
                    'update' => '#' . $this->uniquePageId,
                    'complete' => "function(XMLHttpRequest, textStatus){
                        //find if there is a latest activities portlet
                        $('.LatestActivitiesForPortletView').each(function(){
                            $(this).find('.pager').find('.refresh').find('a').click();
                        });}"
                ));
            // End Not Coding Standard
        }

        protected function doesLabelHaveOwnCell()
        {
            return false;
        }
    }
?>
