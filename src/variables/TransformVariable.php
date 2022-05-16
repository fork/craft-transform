<?php
/**
 * Transform plugin for Craft CMS 3.x
 *
 * Transform Craft Element and field data structures
 *
 * @link      https://www.fork.de
 * @copyright Copyright (c) 2020 Fork Unstable Media GmbH
 */

namespace fork\transform\variables;

use fork\transform\Transform;

/**
 * Transform Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.transform }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Fork Unstable Media GmbH
 * @package   Transform
 * @since     1.0.0
 */
class TransformVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.transform.getData }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.transform.getData(twigValue) }}
     *
     * @param $element
     * @param string|null $transformer
     * @return array
     * @throws \Exception
     */
    public function getData($element, $transformer = null): array
    {
        return Transform::$plugin->data->transform($element, $transformer);
    }
}
