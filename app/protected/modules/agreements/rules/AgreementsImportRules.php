<?php
    /**
     * Defines the import rules for importing into the opportunities module.
     *
     * @author Ramachnadran.K (ramakavanan@gmail.com)
     */
    class AgreementsImportRules extends ImportRules
    {
        public static function getModelClassName()
        {
            return 'Agreement';
        }

        /**
         * Override to block out additional attributes that are not importable
         * @return array
         
        public static function getNonImportableAttributeNames()
        {
            return array_merge(parent::getNonImportableAttributeNames(), array('probability'));
        }*/
    }
?>
