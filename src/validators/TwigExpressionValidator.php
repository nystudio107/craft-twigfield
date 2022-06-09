<?php
/**
 * Twigfield for Craft CMS
 *
 * Provides a twig editor field with Twig & Craft API autocomplete
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace nystudio107\twigfield\validators;

use Craft;
use Exception;
use nystudio107\twigfield\events\RegisterTwigValidatorVariablesEvent;
use yii\base\Model;
use yii\validators\Validator;
use function is_string;

/**
 * @author    nystudio107
 * @package   Twigfield
 * @since     1.0.0
 */
class TwigExpressionValidator extends Validator
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterTwigValidatorVariablesEvent The event that is triggered to allow
     *        you to register Variables to be passed down to the Twig context during
     *        Twig template validation
     *
     * ```php
     * use nystudio107\twigfield\validators\TwigExpressionValidator;
     * use nystudio107\twigfield\events\RegisterTwigValidatorVariablesEvent;
     * use yii\base\Event;
     *
     * Event::on(TwigExpressionValidator::class,
     *     TwigExpressionValidator::EVENT_REGISTER_TWIG_VALIDATOR_VARIABLES,
     *     function(RegisterTwigValidatorVariablesEvent $event) {
     *         $event->variables['variableName'] = $variableValue;
     *     }
     * );
     * ```
     */
    const EVENT_REGISTER_TWIG_VALIDATOR_VARIABLES = 'registerTwigfieldAutocompletes';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        /** @var Model $model */
        $value = $model->$attribute;
        $error = null;
        if (!empty($value) && is_string($value)) {
            try {
                $event = new RegisterTwigValidatorVariablesEvent([
                    'variables' => [],
                ]);
                $this->trigger(self::EVENT_REGISTER_TWIG_VALIDATOR_VARIABLES, $event);
                Craft::$app->getView()->renderString($value, $event->variables);
            } catch (Exception $e) {
                $error = Craft::t(
                    'twigfield',
                    'Error rendering template string -> {error}',
                    ['error' => $e->getMessage()]
                );
            }
        } else {
            $error = Craft::t('twigfield', 'Is not a string.');
        }
        // If there's an error, add it to the model, and log it
        if ($error) {
            $model->addError($attribute, $error);
            Craft::error($error, __METHOD__);
        }
    }
}
