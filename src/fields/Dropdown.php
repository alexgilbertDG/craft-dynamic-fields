<?php

namespace lewisjenkins\craftdynamicfields\fields;

use lewisjenkins\craftdynamicfields\CraftDynamicFields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

class Dropdown extends Field
{

    public $dropdownOptions = '';
    public $columnType = 'text';

    public static function displayName(): string
    {
        return Craft::t('craft-dynamic-fields', 'Dropdown (dynamic)');
    }

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'craft-dynamic-fields/_components/fields/Dropdown_settings',
            [
                'field' => $this,
            ]
        );
    }

    public function getContentColumnType(): string
    {
        return $this->columnType;
    }

    public function normalizeValue($value, ElementInterface $element = null): string
    {
		$view = Craft::$app->getView();
		$templateMode = $view->getTemplateMode();
		$view->setTemplateMode($view::TEMPLATE_MODE_SITE);

		$variables['element'] = $element;
		$variables['this'] = $this;

    $siteHandle = Craft::$app->request->get('site', null);
		$currentSite = Craft::$app->sites->currentSite;
		if ($siteHandle && $siteHandle !== $currentSite->handle) {
			Craft::$app->sites->setCurrentSite(Craft::$app->sites->getSiteByHandle($siteHandle));
		}

		$options = json_decode('[' . $view->renderString($this->dropdownOptions, $variables) . ']', true);

		$view->setTemplateMode($templateMode);
    if ($siteHandle !== $currentSite->handle) {
			Craft::$app->sites->setCurrentSite($currentSite);
		}

		if ($this->isFresh($element) ) :
			foreach ($options as $key => $option) :
				if (!empty($option['default'])) :
					$value = $option['value'];
				endif;
			endforeach;
		endif;

		return (is_null($value) ? '' : $value);
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
		$view = Craft::$app->getView();
		$templateMode = $view->getTemplateMode();
		$view->setTemplateMode($view::TEMPLATE_MODE_SITE);

		$variables['element'] = $element;
		$variables['this'] = $this;

    $siteHandle = Craft::$app->request->get('site', null);
    $currentSite = Craft::$app->sites->currentSite;
    if ($siteHandle && $siteHandle !== $currentSite->handle) {
      Craft::$app->sites->setCurrentSite(Craft::$app->sites->getSiteByHandle($siteHandle));
    }

		$options = json_decode('[' . $view->renderString($this->dropdownOptions, $variables) . ']', true);

		$view->setTemplateMode($templateMode);
    if ($siteHandle !== $currentSite->handle) {
			Craft::$app->sites->setCurrentSite($currentSite);
		}

		return Craft::$app->getView()->renderTemplate('craft-dynamic-fields/_includes/forms/select', [
            'name' => $this->handle,
            'value' => $value,
            'options' => $options
        ]);
    }
}
