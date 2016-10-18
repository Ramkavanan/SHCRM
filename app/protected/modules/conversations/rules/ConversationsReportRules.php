<?php

    /**
     * Report rules to be used with the Conversation module.
     */
    class ConversationsReportRules extends ActivitiesReportRules
    {
        /**
         * @return array
         */
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'Conversation' => array(
                    'nonReportable' =>
                        array('files','conversationItems','comments', 'conversationParticipants'),
                )
            );
            return array_merge(parent::getDefaultMetadata(), $metadata);
        }
    }
?>