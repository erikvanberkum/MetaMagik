<?php

namespace Kanboard\Plugin\MetaMagik\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Validator\TaskValidator;

/**
 * Task Validator
 *
 * Extends the core TaskValidator and only adds MetaMagik's "required custom
 * field" check on top of it. All other validation (including future changes
 * in Kanboard core) is inherited, so this class no longer needs to be kept in
 * sync with core by hand.
 *
 * @package  Kanboard\Plugin\MetaMagik
 * @author   Frederic Guillot (original core validator)
 * @author   Craig Crosby
 */
class NewTaskValidator extends TaskValidator
{
    /**
     * Validate task creation
     *
     * @access public
     * @param  array    $values           Form values
     * @param  boolean  $from_api         Skip custom field requirements for API calls
     * @return array    $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values, $from_api = false)
    {
        list($valid, $errors) = parent::validateCreation($values);

        if (! $from_api) {
            list($valid, $errors) = $this->mergeRequiredMetadata($valid, $errors, $values);
        }

        return array($valid, $errors);
    }

    /**
     * Validate task modification (form)
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        list($valid, $errors) = parent::validateModification($values);

        return $this->mergeRequiredMetadata($valid, $errors, $values);
    }

    /**
     * Check required MetaMagik custom fields and merge the result with the
     * outcome of the core validation.
     *
     * @param  boolean $valid
     * @param  array   $errors
     * @param  array   $values
     * @return array
     */
    protected function mergeRequiredMetadata($valid, array $errors, array $values)
    {
        if (empty($values['project_id'])) {
            return array($valid, $errors);
        }

        $rules = array();

        foreach ($this->metadataTypeModel->getReqs($values['project_id']) as $req) {
            $rules[] = new Validators\Required('metamagikkey_' . $req, t('The field is required'));
        }

        if (empty($rules)) {
            return array($valid, $errors);
        }

        $v = new Validator($values, $rules);

        if (! $v->execute()) {
            $valid = false;
            $errors = array_merge($errors, $v->getErrors());
        }

        return array($valid, $errors);
    }
}
