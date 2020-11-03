<?php

namespace fork\transform\models;

use craft\base\Model;


class Settings extends Model
{
    /**
     * The namespace for the custom transformer classes
     *
     * @var string
     */
    public $transformerNamespace;

    /**
     * Enable caching if Transformer provides "getCacheKey()" method
     *
     * @var bool
     */
    public $enableCache;
}

