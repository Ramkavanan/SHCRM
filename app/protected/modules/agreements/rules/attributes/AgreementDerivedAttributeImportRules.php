<?php
    /**
     * Import rules for when an Agreement is not a specific attribute on a model, but most likely a casted down
     * model of an existing attribute.  An example would be in the activity model and the activityItems relation.
     * This relation does not point to a casted down model, but ultimately refers to it.
     * @see DefaultModelNameIdDerivedAttributeMappingRuleForm
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementDerivedAttributeImportRules extends ModelDerivedAttributeImportRules
    {
        public static function getSanitizerUtilTypesInProcessingOrder()
        {
            return array('AgreementDerivedIdValueType');
        }
    }
?>
