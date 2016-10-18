<?php
    /**
     * Sanitizer for relation attributes that are derived (casted down) from a real relation.  Specific for handling
     * agreement derived attribute values.
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementDerivedIdValueTypeSanitizerUtil extends ModelDerivedIdValueTypeSanitizerUtil
    {
        protected static function getDerivedModelClassName()
        {
            return 'Agreement';
        }
    }
?>
