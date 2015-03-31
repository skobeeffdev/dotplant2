<?php

namespace app\components\filters\modelmap;

use yii\db\ActiveQuery;

abstract class AbstractFilter
{
    /**
     * @var string $cacheKeyNameAppend
     * @var \Closure $callbackFilter
     */
    protected $cacheKeyNameAppend = '';
    protected $callbackFilter = null;

    /**
     * @param string $cacheKeyNameAppend
     * @param \Closure $filter
     */
    function __construct($cacheKeyNameAppend = '', \Closure $filter = null)
    {
        if (is_array($cacheKeyNameAppend)) {
            $cacheKeyNameAppend = implode('', $cacheKeyNameAppend);
        } else {
            $cacheKeyNameAppend = (string) $cacheKeyNameAppend;
        }
        $this->cacheKeyNameAppend = trim($cacheKeyNameAppend);

        if ($filter instanceof \Closure) {
            $this->callbackFilter = $filter;
        } else {
            $this->callbackFilter = function($query) {
                return $query;
            };
        }
    }

    /**
     * @return string
     */
    function getCacheKeyPart()
    {
        return $this->cacheKeyNameAppend;
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    function filter(ActiveQuery $query)
    {
        $oldQuery = clone $query;

        $query = call_user_func($this->callbackFilter, $query);

        if ($query instanceof ActiveQuery) {
            return $query;
        }

        return $oldQuery;
    }
}
?>