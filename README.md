Text Helpers
============
Text Helpers

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist dkulinich/yii2-texthelpers "*"
```

or add

```
"dkulinich/yii2-texthelpers": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
use dkulinich\texthelpers\ExtCoreTextHelper;

// Cut string at words
<?= ExtCoreTextHelper::cutTextAtWords($text, $max_length = NULL); ?>
// Transliterate string
<?= ExtCoreTextHelper::transliterate_cyr($text, $replaceWhitespaces = '_', $acceptedSymbols = '0-9a-zA-Z_'); ?>