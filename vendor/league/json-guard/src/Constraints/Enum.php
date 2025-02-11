<?php

namespace League\JsonGuard\Constraints;

use League\JsonGuard\Assert;
use League\JsonGuard\ValidationError;

class Enum implements PropertyConstraint
{
    const KEYWORD = 'enum';

    /**
     * {@inheritdoc}
     */
    public static function validate($value, $parameter, $pointer = null)
    {
        Assert::type($parameter, 'array', self::KEYWORD, $pointer);

        if (is_object($value)) {
            foreach ($parameter as $i) {
                if (is_object($i) && $value == $i) {
                    return null;
                }
            }
        } else {
            if (in_array($value, $parameter, true)) {
                return null;
            }
        }

        return new ValidationError(
            'Value {value} is not one of: {choices}',
            self::KEYWORD,
            $value,
            $pointer,
            ['choices' => $parameter, 'value' => $value]
        );
    }
}
