<div align="left">
  <img width="600" title="Craft Transform" src="https://github.fork.de/CraftTransform_031120.svg">
</div>

**Table of contents**

- [Features](#features)
- [Requirements](#requirements)
- [Setup](#setup)
- [Usage](#usage)
- [Roadmap](#roadmap)

<!-- /TOC -->

---

## Features

- Transform Craft CMS contents to custom data structures
- Create custom Transformer classes for your components
- Cache contents on a Transformer basis (via providing `getCacheKey`)

## Requirements

- Craft CMS >= 3.5.x

## Setup

**1. Install**

Install the package

```sh
cd /path/to/project
composer require fork/craft-transform
```

**2. Configure namespace and create transformers**

- Copy the example `config.php` to your Craft config directory and rename it to `transform.php`
- Specify the namespace to your custom Transformer classes (in your project plugin/module). Here's an example:

```php
<?php

return [
    '*' => [
        'transformerNamespace' => '\company\project\transformers'
    ],
    'dev' => [
        'enableCache' => false,
    ],
    'staging' => [
        'enableCache' => true,
    ],
    'production' => [
        'enableCache' => true,
    ],
];
```

In your project plugin/module create a `transformers` directory to put your transformers. Here is an example Transformer class:

```php
<?php

namespace company\project\transformers;

use Craft;
use League\Fractal\TransformerAbstract;

class FooterTransformer extends TransformerAbstract {

    public function transform()
    {
        $footer = Craft::$app->globals->getSetByHandle('footer');
        $footerNavGlobals = $footer->footerNavigationElements->all();
        $footerLinks = [];

        foreach ($footerNavGlobals as $linkEntry) {
            $link = [
                'href' => $linkEntry->navigationLink->getUrl(),
                'name' => $linkEntry->navigationLink->getText(),
                'slug' => $linkEntry->navigationLink->hasElement() ? $linkEntry->navigationLink->getElement()->slug : $linkEntry->navigationLink->getUrl(),
                'target' => $linkEntry->navigationLink->getTarget()
            ];
            $footerLinks[] = $link;
        }

        return $footerLinks;
    }

}
```

## Usage

In your templates you can use `craft.transform.getData()`. The first parameter is optional. It could be your `entry` to get the data from.
Also it could be `null`. The second parameter must match with the corresponding Transformer class. E.g. pass `'Footer'` to use the `FooterTransformer`.

```twig
{% set articleData = {
    headline: entry.title,
    contentBlocks: craft.transform.getData(entry, 'ContentBlocks')
} %}

{% include '@components/templates/article-page/article-page.twig' with {
    header: craft.transform.getData(null, 'Header'),
    headline: articleData.headline,
    contentBlocks: articleData.contentBlocks,
    footer: craft.transform.getData(null, 'Footer')
} only %}
```


## Roadmap

- [x] Caching
- [x] Logo
- [ ] Settings maybe (instead of config file)

---

<div align="center">
  <img src="https://github.fork.de/heart.png" width="38" height="41" alt="Fork Logo" />

  <p>Brought to you by <a href="https://www.fork.de">Fork Unstable Media GmbH</a></p>
</div>
