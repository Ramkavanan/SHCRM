<?php
    /**
     * Class for defining the badge associated with winning Agreement
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class WinAgreementGameBadgeRules extends GameBadgeRules
    {
        public static $valuesIndexedByGrade = array(
            1  => 3,
            2  => 23,
            3  => 5,
            4  => 43,
            5  => 20,
            6  => 63,
            7  => 40,
            8  => 50,
            9  => 60,
            10 => 73,
            11 => 80,
            12 => 90,
            13 => 100
        );

        public static function getPassiveDisplayLabel($value)
        {
            return Zurmo::t('AgreementsModule', '{n} AgreementsModuleSingularLabel won|{n} AgreementsModulePluralLabel won',
                          array_merge(array($value), LabelUtil::getTranslationParamsForAllModules()));
        }

        /**
         * @param array $userPointsByType
         * @param array $userScoresByType
         * @return int|string
         */
        public static function badgeGradeUserShouldHaveByPointsAndScores($userPointsByType, $userScoresByType)
        {
            assert('is_array($userPointsByType)');
            assert('is_array($userScoresByType)');
            if (isset($userScoresByType[AgreementGamificationRules::SCORE_TYPE_WIN_AGREEMENT]))
            {
                return static::getBadgeGradeByValue((int)$userScoresByType[AgreementGamificationRules::SCORE_TYPE_WIN_AGREEMENT]->value);
            }
            return 0;
        }
    }
?>
