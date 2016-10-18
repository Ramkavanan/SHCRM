<?php
    /**
     * Report rules to be used with the Jobschedule module.
     *
     */
    class JobSchedulingAgmtReportRules extends SecuredReportRules
    {
        /**
         * @return array
         */
        public static function getDefaultMetadata()
        {
            $metadata = array();
            return array_merge(parent::getDefaultMetadata(), $metadata);
        }
    }
?>
