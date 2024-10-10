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
    public string $transformerNamespace;

    /**
     * Enable caching if Transformer provides "getCacheKey()" method
     *
     * @var bool
     */
    public bool $enableCache;
}
