<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\controllers;

use craft\web\Controller;
use nystudio107\twigfield\Twigfield;
use yii\web\Response;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class AutocompleteController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action): bool
    {
        if (Twigfield::$settings->allowFrontendAccess) {
            $this->allowAnonymous = 1;
        }

        return parent::beforeAction($action);
    }

    /**
     * Return all of the autocomplete items in JSON format
     *
     * @param string $fieldType
     * @return Response
     */
    public function actionIndex(string $fieldType = Twigfield::DEFAULT_FIELD_TYPE): Response
    {
        $result = Twigfield::getInstance()->autocomplete->generateAutocompletes($fieldType);

        return $this->asJson($result);
    }
}
