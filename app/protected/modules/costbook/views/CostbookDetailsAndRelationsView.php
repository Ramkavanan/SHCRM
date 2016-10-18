<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CostbookDetailsAndRelationsView
 *
 * @author ideas2it
 */
class CostbookDetailsAndRelationsView extends ConfigurableDetailsAndRelationsView{
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
                                    'type' => 'CostbookDetailsPortlet',
                                ),
                               array(
                                    'type' => 'NoteInlineEditForPortlet',
                                ),
                                array(
                                    'type' => 'CostbookLatestActivitiesForPortlet',
                                ),
                            )
                        ),
                        array(
                            'rows' => array(
                                array(
                                     'type' => 'UpcomingMeetingsForCostbookCalendar',
                                    ),
                                array(
                                     'type' => 'OpenTasksForCostbookRelatedList',
                                    ),
                                array(
                                     'type' => 'ContactsForCostbookRelatedList',
                                    )
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
            $content = parent::renderContent();
            TasksUtil::resolveShouldOpenToTaskForDetailsAndRelationsView();
            return $content;
        }
}
