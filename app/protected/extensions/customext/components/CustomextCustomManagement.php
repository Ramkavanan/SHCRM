<?php
    /*********************************************************************************
     * San Brar
     ********************************************************************************/
    /**
     * Specific custom management for the zurmo zoo project.
     */
    class CustomextCustomManagement extends CustomManagement
    {
        /**
         * (non-PHPdoc)
         * @see CustomManagement::runBeforeInstallationAutoBuildDatabase()
         */
        public function runBeforeInstallationAutoBuildDatabase(MessageLogger $messageLogger)
        {            
            CustomextInstallUtil::resolveCustomMetadataAndLoad();
        }

        /**
         * (non-PHPdoc)
         * @see CustomManagement::resolveIsCustomDataLoaded()
         */
        public function resolveIsCustomDataLoaded()
        {           
            CustomextInstallUtil::resolveCustomMetadataAndLoad();
        }
    }
?>
