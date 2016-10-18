<?php
    /**
     * Workflow rules to be used with the Agreement module.
     * 
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementsWorkflowRules extends SecuredWorkflowRules
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
