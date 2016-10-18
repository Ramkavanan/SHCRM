<?php
    /**
     * This select agreement related attributes when search the agreement
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementAttributeForm extends HasOneModelAttributeForm
    {
        public static function getAttributeTypeDisplayName()
        {
            return Zurmo::t('AgreementsModule', 'Agreement');
        }

        public static function getAttributeTypeDisplayDescription()
        {
            return Zurmo::t('AgreementsModule', 'An agreement field');
        }

        public function getAttributeTypeName()
        {
            return 'Agreement';
        }
    }
?>
