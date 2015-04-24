<?php

namespace app\components;

use app\models\Plugins;
use yii\caching\Cache;
use yii\caching\TagDependency;
use yii\web\UrlRule;

class UrlManager extends \yii\web\UrlManager
{
    public function init()
    {
        parent::init();

        if ($this->cache instanceof Cache) {
            $cacheKey = static::className() . '-CacheKey';
            if (false !== $cacheRules = $this->cache->get($cacheKey)) {
                $this->rules = $cacheRules;
            }
        }
    }

    public function compileRuleCache()
    {
        if ($this->cache instanceof Cache) {
            $cacheKey = static::className() . '-CacheKey';
            $this->cache->set(
                $cacheKey,
                $this->rules,
                0,
                new TagDependency([
                    'tags' => [
                        \devgroup\TagDependencyHelper\ActiveRecordHelper::getCommonTag(Plugins::className())
                    ]
                ])
            );
        }
    }

    public function resetRuleCache()
    {
        if ($this->cache instanceof Cache) {
            $cacheKey = static::className() . '-CacheKey';
            $this->cache->delete($cacheKey);
        }
    }
}
?>