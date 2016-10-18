<?php
    /**
     * Portlet view for agreement related views
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementDetailsAndRelationsView extends ConfigurableDetailsAndRelationsView
    {
        public function isUniqueToAPage()
        {
            return true;
        }

        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array(  'type'           => 'AddPortletAjaxLinkOnDetailView',
                                    'uniqueLayoutId' => 'eval:$this->uniqueLayoutId',
                                    'ajaxOptions'    => 'eval:static::resolveAjaxOptionsForAddPortlet()',
                                    'htmlOptions'    => array('id' => 'AddPortletLink',
                                )
                            ),
                        ),
                    ),
                    'columns' => array(
                        array(
                            'rows' => array(
                               array(
                                    'type' => 'AgreementDetailsPortlet',
                                ),
                                 array(
                                    'type' => 'NoteInlineEditForPortlet',
                                ),
                                array(
                                    'type' => 'AgreementLatestActivitiesForPortlet',
                                ),
				array(
                                     'type' => 'AgreementProductsForAgreementRelatedList',
                                ),
                               
                            )
                        ),
                        array(
                            'rows' => array(
                                array(
                                     'type' => 'UpcomingMeetingsForOpportunityCalendar',
                                    ),
                                /**array(
                                     'type' => 'OpenTasksForOpportunityRelatedList',
                                    ),
                                array(
                                     'type' => 'ContactsForOpportunityRelatedList',
                                    ),
				array(
					'type' => 'ProductsForOpportunityRelatedList'				
				),*/
                            )
                        )
                    )
                )
            );
            return $metadata;
        }

        /**
         * Renders content.
         * @return string
         */
        protected function renderContent()
        {
            $content = $this->renderContentOfParent();
            TasksUtil::resolveShouldOpenToTaskForDetailsAndRelationsView();
            return $content;
        }
        
        protected function renderContentOfParent()
        {
            $metadata = parent::getMetadata();
            $portletsAreRemovable   = true;
            $portletsAreMovable     = true;
            $this->resolvePortletConfigurableParams($portletsAreMovable, $portletsAreRemovable);
            $content          = $this->renderActionElementBar(true);
            $viewClassName    = self::getModelRelationsSecuredPortletFrameViewClassName();
            $configurableView = new $viewClassName( $this->controllerId,
                                                    $this->moduleId,
                                                    $this->uniqueLayoutId,
                                                    $this->params,
                                                    $metadata,
                                                    false,
                                                    $portletsAreMovable,
                                                    false,
                                                    parent::getDefaultLayoutType(), // This could be driven by a db value based on layout type id
                                                    $portletsAreRemovable);
            $content          .=  $configurableView->render();
            $content          .= $this->renderScripts();
            return $content;
        }

        protected static function getModelRelationsSecuredPortletFrameViewClassName(){
            
            return 'AgreementRelationsSecuredPortletFrameView';
        }
    }
?>
