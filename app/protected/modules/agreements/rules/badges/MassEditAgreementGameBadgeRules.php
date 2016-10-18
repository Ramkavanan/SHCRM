<?php
    /**
     * Class for defining the badge associated with mass editing Agreement
     *
     * @author Ramachandran.K (ramakavanan@gmail.com)
     */
    class MassEditAgreementGameBadgeRules extends MassEditModelsGameBadgeRules
    {
        public static function getPassiveDisplayLabel($value)
        {
            return Zurmo::t('AgreementsModule', '{n} AgreementsModuleSingularLabel mass updated|{n} AgreementsModulePluralLabel mass updated',
                          array_merge(array($value), LabelUtil::getTranslationParamsForAllModules()));
        }

        public static function badgeGradeUserShouldHaveByPointsAndScores($userPointsByType, $userScoresByType)
        {
            return static::badgeGradeUserShouldHaveByPointsAndScoresByModelClassName($userPointsByType, $userScoresByType, 'Agreement');
        }
    }
?>
