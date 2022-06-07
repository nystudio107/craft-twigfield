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
use nystudio107\twigfield\helpers\Autocomplete;
use yii\web\Response;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class AutocompleteController extends Controller
{
    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $result = Autocomplete::generateAutocompletes();

        return $this->asJson($result);
    }
}
