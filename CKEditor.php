<?php
/**
 * @link https://github.com/MadAnd/yii2-ckeditor-kcfinder
 * @copyright Copyright (c) 2014 HimikLab, MadAnd
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace MadAnd\ckeditor;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * WYSIWYG HTML input widget with file browser plugin (KCFinder) for Yii2.
 * Using as field in ActiveForm:
 *
 * ```php
 * echo $form->field($model, 'text')->widget(CKEditor::className(), [
 *      'editorOptions' => ['height' => '500px'],
 * ]);
 * ```
 *
 * Using inline:
 *
 * ```php
 * echo CKEditor::widget([
 *      'name' => 'comment',
 *      'value' => 'Please write your comment',
 *      'editorOptions' => ['height' => '500px'],
 * ]);
 * ```
 *
 * Using without KCFinder:
 *
 * ```php
 * echo $form->field($model, 'text')->widget(CKEditor::className(), [
 *      'editorOptions' => ['height' => '500px'],
 *      'enabledKCFinder' => false,
 * ]);
 *
 * @author MadAnd
 * @package MadAnd\ckeditor
 */
class CKEditor extends InputWidget
{
    /**
     * Whether add configuration that enables KCFinder. Defaults to TRUE.
     * @see http://kcfinder.sunhater.com/
     * @var bool
     */
    public $enabledKCFinder = true;

    /**
     * @var array the options for the CKEditor 4 JS plugin
     * @see http://docs.ckeditor.com/#!/guide/dev_installation
     */
    public $editorOptions = [];

    private $configuredSession = false;

    public function init()
    {
        parent::init();

        $this->configureSession();

        $view = $this->getView();
        $id = Json::encode($this->options['id']);

        if ($this->enabledKCFinder) {
            $kcFinderBundle = KCFinderAsset::register($view);
            $kcFinderBaseUrl = $kcFinderBundle->baseUrl;

            // Add KCFinder-specific config for CKEditor
            $this->editorOptions = ArrayHelper::merge(
                $this->editorOptions,
                [
                    'filebrowserBrowseUrl'      => $kcFinderBaseUrl
                        . '/browse.php?opener=ckeditor&type=files',
                    'filebrowserImageBrowseUrl' => $kcFinderBaseUrl
                        . '/browse.php?opener=ckeditor&type=images',
                    'filebrowserFlashBrowseUrl' => $kcFinderBaseUrl
                        . '/browse.php?opener=ckeditor&type=flash',
                    'filebrowserUploadUrl'      => $kcFinderBaseUrl
                        . '/upload.php?opener=ckeditor&type=files',
                    'filebrowserImageUploadUrl' => $kcFinderBaseUrl
                        . '/upload.php?opener=ckeditor&type=images',
                    'filebrowserFlashUploadUrl' => $kcFinderBaseUrl
                        . '/upload.php?opener=ckeditor&type=flash',
                    'allowedContent'            => true,
                ]
            );
        }

        $jsData = "CKEDITOR.replace($id";
        $jsData .= empty($this->editorOptions) ? '' : (', ' . Json::encode($this->editorOptions));
        $jsData .= ").on('blur', function(){this.updateElement(); jQuery(this.element.$).trigger('blur');});";

        $view->registerJs($jsData);
        CKEditorAsset::register($view);
    }

    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
    }

    private function configureSession() {
        if ($this->configuredSession) {
            return;
        }

        if (isset(Yii::$app->params['KCFinderConfig'])) {
            $kcFinderConfig = Yii::$app->params['KCFinderConfig'];

            if (!is_array($kcFinderConfig)) {
                throw new \RuntimeException('KCFinderConfig param must be an array.');
            }

            foreach (['uploadURL', 'uploadDir'] as $confKey) {
                if (isset($kcFinderConfig[$confKey])) {
                    $kcFinderConfig[$confKey] = Yii::getAlias($kcFinderConfig[$confKey]);
                }
            }

            if (isset(Yii::$app->session['KCFINDER'])) {
                Yii::$app->session['KCFINDER'] = ArrayHelper::merge(
                    (array) Yii::$app->session['KCFINDER'],
                    $kcFinderConfig
                );
            } else {
                Yii::$app->session['KCFINDER'] = $kcFinderConfig;
            }
        }

        $this->configuredSession = true;
    }
}
