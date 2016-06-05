CKEditor + KCFinder for Yii 2
=====

WYSIWYG text editor widget with integrated file browser.

**This fork is not supported!** And is not exported to Packagist. If you still wish to add it
to your project, read the following section.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Add this repository to your project's `composer.json`:
```
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/MadAnd/yii2-ckeditor-kcfinder"
  }
],
```

If your `composer.json` has `stability` option set to `stable` (this is a
default value, if not set explicitly), run the following command:

```
php composer.phar require --prefer-dist "MadAnd/yii2-ckeditor-kcfinder:dev-master" \
    "ckeditor/ckeditor:@dev" "sunhater/kcfinder:@dev"
```

If your `composer.json` has `stability` option set to `dev`, the following command is sufficient:

```
php composer.phar require --prefer-dist "MadAnd/yii2-ckeditor-kcfinder:*"
```

Usage
-----

```php
\MadAnd\ckeditor\CKEditor::widget();
```

Possible settings (with default values)
---------------------------------------

```php
  'type' => CKEditor::TYPE_STANDARD,
  'height'=>'200px',
  'language'=>Yii::$app->language,
```
