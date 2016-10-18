<?php
    /**
     * Class for defining the badge associated with creating a new Agreement
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class CreateAgreementGameBadgeRules extends GameBadgeRules
    {
        public static $valuesIndexedByGrade = array(
            1  => 3,
            2  => 23,
            3  => 33,
            4  => 43,
            5  => 53,
            6  => 63,
            7  => 73,
            8  => 83,
            9  => 93,
            10 => 100,
            11 => 133,
            12 => 233,
            13 => 333
        );

        public static function getPassiveDisplayLabel($value)
        {
            return Zurmo::t('AgreementsModule', '{n} AgreementsModuleSingularLabel created|{n} AgreementsModulePluralLabel created',
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
            if (isset($userScoresByType['CreateAgreement']))
            {
                return static::getBadgeGradeByValue((int)$userScoresByType['CreateAgreement']->value);
            }
            return 0;
        }
    }
?>
