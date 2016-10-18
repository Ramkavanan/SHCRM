<?php

    /**
     * Class defining rules for Agreement gamification behavior.
     *
     * @author Ramachnadran.K (ramakavanan@gmail.com)
     */
    class AgreementGamificationRules extends GamificationRules
    {
        /**
         * @var string
         */
        const SCORE_CATEGORY_WIN_AGREEMENT = 'WinAgreement';

        /**
         * @var string
         */
        const SCORE_TYPE_WIN_AGREEMENT   = 'WinAgreement';

        /**
         * (non-PHPdoc)
         * @see GamificationRules::scoreOnSaveModel()
         */
        public function scoreOnSaveModel(CEvent $event)
        {
            parent::scoreOnSaveModel($event);
                 $scoreType = static::SCORE_TYPE_WIN_AGREEMENT;
                $category  = static::SCORE_CATEGORY_WIN_AGREEMENT;
                $gameScore = GameScore::resolveToGetByTypeAndPerson($scoreType, Yii::app()->user->userModel);
                $gameScore->addValue();
                $saved = $gameScore->save();
                if (!$saved)
                {
                    throw new FailedToSaveModelException();
                }
                GamePointUtil::addPointsByPointData(Yii::app()->user->userModel,
                               static::getPointTypeAndValueDataByCategory($category));
        }

        /**
         * @see parent::getPointTypesAndValuesForCreateModel()
         */
        public static function getPointTypesAndValuesForCreateModel()
        {
            return array(GamePoint::TYPE_SALES => 10);
        }

        /**
         * @see parent::getPointTypesAndValuesForUpdateModel()
         */
        public static function getPointTypesAndValuesForUpdateModel()
        {
            return array(GamePoint::TYPE_SALES => 10);
        }

        /**
         * @return Point type/value data for a user changing the stage of an opportunity to 'Closed Won'
         */
        public static function getPointTypesAndValuesForWinOpportunity()
        {
            return array(GamePoint::TYPE_SALES => 50);
        }
    }
?>
