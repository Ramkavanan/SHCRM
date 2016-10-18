<?php
    /**
     * Class for defining the badge associated with searching Agreements
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class SearchAgreementsGameBadgeRules extends SearchModelsGameBadgeRules
    {
        public static function getPassiveDisplayLabel($value)
        {
            return Zurmo::t('AgreementsModule', '{n} AgreementsModuleSingularLabel search completed|{n} AgreementsModuleSingularLabel searches completed',
                          array_merge(array($value), LabelUtil::getTranslationParamsForAllModules()));
        }

        public static function badgeGradeUserShouldHaveByPointsAndScores($userPointsByType, $userScoresByType)
        {
            return static::badgeGradeUserShouldHaveByPointsAndScoresByModelClassName($userPointsByType, $userScoresByType, 'Agreement');
        }
    }
?>
