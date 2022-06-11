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
class TwigObjectTemplateValidator extends Validator
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterTwigValidatorVariablesEvent The event that is triggered to allow
     *        you to register Variables to be passed down to the Twig context during
     *        Twig template validation
     *
     * ```php
     * use nystudio107\twigfield\validators\TwigTemplateValidator;
     * use nystudio107\twigfield\events\RegisterTwigValidatorVariablesEvent;
     * use yii\base\Event;
     *
     * Event::on(TwigTemplateValidator::class,
     *     TwigTemplateValidator::EVENT_REGISTER_TWIG_VALIDATOR_VARIABLES,
     *     function(RegisterTwigValidatorVariablesEvent $event) {
     *         $event->object = $myObject;
     *         $event->variables['variableName'] = $variableValue;
     *     }
     * );
     * ```
     */
    const EVENT_REGISTER_TWIG_VALIDATOR_VARIABLES = 'registerTwigValidatorVariables';

    /**
     * @var mixed The object that should be passed into `renderObjectTemplate()` during the template rendering
     */
    public $object = null;

    /**
     * @var array Variables in key => value format that should be available during the template rendering
     */
    public $variables = [];

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
                    'object' => $this->object,
                    'variables' => $this->variables,
                ]);
                $this->trigger(self::EVENT_REGISTER_TWIG_VALIDATOR_VARIABLES, $event);
                Craft::$app->getView()->renderObjectTemplate($value, $event->object, $event->variables);
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
