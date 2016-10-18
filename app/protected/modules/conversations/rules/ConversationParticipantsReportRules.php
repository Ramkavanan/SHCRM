<?php
  
    /**
     * Report rules to be used with the Conversation module.
     */
    class ConversationParticipantsReportRules extends ActivitiesReportRules
    {
        /**
         * @return array
         */
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'Conversation' => array(
                    'nonReportable' =>
                        array('person','conversation'),
                )
            );
            return array_merge(parent::getDefaultMetadata(), $metadata);
        }
    }
?>