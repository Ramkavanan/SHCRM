<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2015 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2015. All rights reserved".
     ********************************************************************************/

    /**
     * Caches RedBean models. If caching is configured it the cached models
     * outlive requests. Either way the models are cached in php for the duration
     * of the current request. This allows multiple references to the same cached
     * model, whether it came out of the memcache or not, to reference the same
     * php object.
     */
    class RedBeanModelsCache extends ZurmoCache
    {
        const MAX_MODELS_CACHED_IN_MEMORY = 100;

        private static $modelIdentifiersToModels = array();

        public static $cacheType = 'M:';

        /**
         * Get a cached model.
         */
        public static function getModel($modelIdentifier)
        {
            assert('is_string($modelIdentifier)');
            assert('$modelIdentifier != ""');
            if (static::supportsAndAllowsPhpCaching() && isset(static::$modelIdentifiersToModels[$modelIdentifier]))
            {
                return static::$modelIdentifiersToModels[$modelIdentifier];
            }
            if (static::supportsAndAllowsMemcacheByModelIdentifier($modelIdentifier))
            {
                $prefix = static::getCachePrefix($modelIdentifier);
                $model = static::getCachedValueAndValidateChecksum($prefix . $modelIdentifier);
                if ($model !== false && $model instanceof RedBeanModel)
                {
                    static::$modelIdentifiersToModels[$modelIdentifier] = $model;
                    return $model;
                }
            }
            throw new NotFoundException();
        }

        /**
         * Cache a model maintaining the in memory model
         * cache to a limited size.
         */
        public static function cacheModel(RedBeanModel $model)
        {
            $modelIdentifier = $model->getModelIdentifier();
            if (static::supportsAndAllowsPhpCaching())
            {
                static::$modelIdentifiersToModels[$modelIdentifier] = $model;
                if (count(static::$modelIdentifiersToModels) > static::MAX_MODELS_CACHED_IN_MEMORY)
                {
                    static::$modelIdentifiersToModels = array_slice(static::$modelIdentifiersToModels,
                                                                  count(static::$modelIdentifiersToModels) -
                                                                    static::MAX_MODELS_CACHED_IN_MEMORY);
                }
            }
            if (static::supportsAndAllowsMemcacheByModel($model))
            {
                $prefix = static::getCachePrefix($modelIdentifier);
                static::cacheValueAndChecksum($prefix . $modelIdentifier, $model);
            }
        }

        /**
         * Forget a cached model.
         */
        public static function forgetModel(RedBeanModel $model)
        {
            $modelIdentifier = $model->getModelIdentifier();
            static::forgetModelByIdentifier($modelIdentifier);
        }

        public static function forgetModelByIdentifier($modelIdentifier)
        {
            if (static::supportsAndAllowsPhpCaching())
            {
                unset(static::$modelIdentifiersToModels[$modelIdentifier]);
            }
            if (static::supportsAndAllowsMemcacheByModelIdentifier($modelIdentifier))
            {
                $prefix = static::getCachePrefix($modelIdentifier);
                Yii::app()->cache->delete($prefix . $modelIdentifier);
            }
        }

        /**
         * Forget all cached models.
         * @param $onlyForgetPhpCache is for testing only. It is for
         * artificially creating situations where memcache must be
         * accessed for testing memcache and RedBeanModel serialization.
         */
        public static function forgetAll($onlyForgetPhpCache = false)
        {
            if (static::supportsAndAllowsPhpCaching())
            {
                static::$modelIdentifiersToModels = array();
            }
            if (!$onlyForgetPhpCache)
            {
                static::clearMemcacheCache();
            }
        }

        /**
         * TODO: Only forget by model.
         * @param $modelClassName - string.
         */
        public static function forgetAllByModelType($modelClassName)
        {
            assert('is_string($modelClassName)');
            static::forgetAll();
        }

        /**
         * Used for testing purposes if you need to clear out just the php caching.
         */
        public static function forgetAllModelIdentifiersToModels()
        {
            static::$modelIdentifiersToModels = array();
        }

        protected static function supportsAndAllowsMemcacheByModel(RedBeanModel $model)
        {
            $className  = get_class($model);
            return static::supportsAndAllowsMemcacheByModelClassName($className);
        }

        protected static function supportsAndAllowsMemcacheByModelClassName($modelClassName)
        {
            return ($modelClassName::allowMemcacheCache() && static::supportsAndAllowsMemcache());
        }

        protected static function supportsAndAllowsMemcacheByModelIdentifier($modelIndetifier)
        {
            $className  = RedBeanModel::getModelClassNameByIdentifier($modelIndetifier);
            return static::supportsAndAllowsMemcacheByModelClassName($className);
        }
    }
?>