<?php

    /**
     * Wrapper view for displaying an agreement's latest activities feed.
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementLatestActivitiesForPortletView extends LatestActivitiesForPortletView
    {
        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            return array_merge($metadata, array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type'                   => 'CreateConversationFromRelatedListLink',
                                  'routeParameters'         =>
                                    array('relationAttributeName'    => 'notUsed',
                                            'relationModelClassName' => 'Agreement',
                                            'relationModelId'        => 'eval:$this->params["relationModel"]->id',
                                            'relationModuleId'       => 'agreements',
                                            'redirectUrl'            => 'eval:Yii::app()->request->getRequestUri()')
                        ),
                    ),
                ),
            )));
        }

        public function getLatestActivitiesViewClassName()
        {
            return 'LatestActivitiesForAgreementListView';
        }

        public static function hasRollupSwitch()
        {
            return true;
        }

        public static function getAllowedOnPortletViewClassNames()
        {
            return array('AgreementDetailsAndRelationsView');
        }
    }
?>