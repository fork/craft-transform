<?php

namespace fork\transform\helpers;

use Craft;
use yii\web\BadRequestHttpException;

class CacheHelper
{
    /**
     * @return bool
     * @throws BadRequestHttpException
     */
    public static function ignoreCache(): bool
    {
        $request = Craft::$app->getRequest();

        return $request->getIsLivePreview()
            || $request->getIsPreview()
            || $request->getToken()
            || Craft::$app->getConfig()->getGeneral()->devMode;
    }
}
