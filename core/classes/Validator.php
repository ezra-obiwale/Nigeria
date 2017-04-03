<?php

/**
 * Description of Validator
 *
 * @author Ezra Obiwale <contact@ezraobiwale.com>
 */
class Validator {

    private $data;
    private $messages;
    private $rules;
    private $current;

    public function __construct($data, array $rules, array $messages = []) {
        $this->rules = $rules;
        $this->messages = $messages;
        $this->data = $data;
    }

    public function run() {
        foreach ($this->rules as $field => $rule) {
            $rules = explode('|', $rule);
            foreach ($rules as $_rule) {
                $_rule = trim($_rule);
                $parts = explode(':', $_rule);
                if (!$parts[0] || !method_exists($this, $parts[0])) continue;
                $this->current = $parts[0];
                if ($message = $this->{$parts[0]}($field, $parts[1])) return $message;
            }
        }
    }

    private function required($field) {
        $message = $this->getMessage($field, ' is required');
        $value = $this->getValue($field);
        return $value || $value != 0 ? null : $message;
    }

    private function getValue($field) {
        if (strstr($field, '.')) {
            $value = null;
            foreach (explode('.', $field) as $name) {
                if (!$value) $value = $this->data[$name];
                else $value = $value[$name];
                if (!$value || $value == 0) break;
            }
            return $value;
        }
        return $this->data[$field];
    }

    private function getMessage($field, $default) {
        if (is_array($this->messages[$field])) {
            if (array_key_exists($this->current, $this->messages[$field]))
                    return $this->messages[$field][$this->current];
        }
        else if (is_string($this->messages[$field])) return $this->messages[$field];
        return ucfirst(str_replace('_', ' ', str_replace('.', ' ', $field))) . $default;
    }

    private function numeric($field) {
        if (!is_string($this->regex($field, '/[^0-9]/'))) {
            return $this->getMessage($field, ' must be numeric');
        }
    }

    private function regex($field, $pattern, $message = null) {
        return !preg_match($pattern, $this->data[$field]) ?
                $this->getMessage($field, $message ? : ' does not match expectation') :
                null;
    }

    private function match($field, $target) {
        return $this->getValue($field) !== $this->getValue($target) ?
                $this->getMessage($field, " must match $target") : null;
    }

    private function notmatch($field, $target) {
        return $this->getValue($field) === $this->getValue($target) ?
                $this->getMessage($field, " must not match $target") : null;
    }

}
