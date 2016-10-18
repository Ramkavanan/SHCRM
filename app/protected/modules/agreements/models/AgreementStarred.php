<?php
    /**
     * Agreement Searchview to display the dynamic search options
     * to agreement listview page
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class AgreementStarred extends BaseStarredModel
    {
        public static function getDefaultMetadata()
        {
            $metadata = parent::getDefaultMetadata();
            $metadata[__CLASS__] = array(
                'relations' => array(
                    static::getRelationName()     => array(static::HAS_ONE,  static::getRelatedModelClassName()),
                ),
                'indexes' => static::getIndexesDefinition(),
            );
            return $metadata;
        }

        public static function getModuleClassName()
        {
            return 'AgreementsModule';
        }

        /**
         * Returns the display name for the model class.
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getLabel($language = null)
        {
            return Zurmo::t('AgreementsModule', 'Agreement Starred', array(), null, $language);
        }

        /**
         * Returns the display name for plural of the model class.
         * @param null | string $language
         * @return dynamic label name based on module.
         */
        protected static function getPluralLabel($language = null)
        {
            return Zurmo::t('AgreementsModule', 'Agreements Starred', array(), null, $language);
        }

        protected static function getRelationName()
        {
            return 'agreement';
        }
    }
?>
