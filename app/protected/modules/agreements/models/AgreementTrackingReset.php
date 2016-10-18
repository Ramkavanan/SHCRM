<?php

    /**
     * Agreement Reset model have the elements and its relation as well as rules.
     *     
     */
    class AgreementTrackingReset extends RedBeanModel
    {
        public static function getModuleClassName() {
            return 'AgreementTrackingResetModule';
        }

        public static function canSaveMetadata() {
            return true;
        }

        public static function getDefaultMetadata() {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'members' => array(
                    'reset_date',
                    'reset_number',
                    'gpm',
                ),
                'rules' => array(
                    array('reset_number', 'type', 'type' => 'integer'),
                    array('gpm', 'type', 'type' => 'float'),
                    array('reset_date',  'type',  'type'  => 'date'),
                ),
                'relations' => array(
                    'agreement' => array(RedBeanModel::HAS_ONE, 'Agreement'),
                ),       
            );
            return $metadata;
        }
        
        
    }
?>
