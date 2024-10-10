<?php
/**
 * Transform plugin for Craft CMS 3.x
 *
 * Transform Craft Element and field data structures
 *
 * @link      https://www.fork.de
 * @copyright Copyright (c) 2020 Fork Unstable Media GmbH
 */

namespace fork\transform\services;

use Craft;
use craft\base\Component;
use fork\transform\exceptions\MissingTransformerException;
use fork\transform\Transform;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use yii\web\BadRequestHttpException;

/**
 * Data Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Fork Unstable Media GmbH
 * @package   Transform
 * @since     1.0.0
 */
class Data extends Component
{
    /**
     * The fractal manager instance
     *
     * @var Manager
     */
    public Manager $fractal;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->fractal = new Manager();
        // default is DataArraySerializer with nested 'data' attribute (maybe useful too if meta a pagination is included?)
        $this->fractal->setSerializer(new ArraySerializer());
    }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Transform::$plugin->data->transform()
     *
     * @param $element
     * @param TransformerAbstract|string|null $transformer
     * @return array
     * @throws BadRequestHttpException
     * @throws MissingTransformerException
     */
    public function transform($element, $transformer = null): array
    {
        $transformer = $this->getTransformerInstance($transformer);

        $request = Craft::$app->getRequest();
        $ignoreCache = $request->getIsLivePreview() || $request->getToken();

        if (Transform::$plugin->settings->enableCache && !$ignoreCache && method_exists($transformer, 'getCacheKey')) {
            $cacheKey = $transformer->getCacheKey($element);
            $cached = Craft::$app->getCache()->get($cacheKey) ?: null;

            if ($cached) {
                return $cached;
            } else {
                $elementsService = Craft::$app->getElements();
                $elementsService->startCollectingCacheInfo();

                $resource = new Item($element, $transformer);
                $data = $this->fractal->createData($resource)->toArray();

                [$dependency, ] = $elementsService->stopCollectingCacheInfo();

                Craft::$app->getCache()->set($cacheKey, $data, null, $dependency);

                return $data;
            }
        } else {
            $resource = new Item($element, $transformer);

            return $this->fractal->createData($resource)->toArray();
        }
    }

    /**
     * @param TransformerAbstract|string|null $transformer
     * @return TransformerAbstract|null
     * @throws MissingTransformerException
     */
    private function getTransformerInstance($transformer = null): ?TransformerAbstract
    {
        if (is_string($transformer)) {
            $namespace = Transform::$plugin->settings->transformerNamespace;
            if ($namespace) {
                $className = $namespace . '\\' . $transformer . 'Transformer';
                if (class_exists($className)) {
                    $transformer = new $className();
                } else {
                    throw new MissingTransformerException('No Transformer found: ' . $className);
                }
            }
        }

        return $transformer;
    }
}
