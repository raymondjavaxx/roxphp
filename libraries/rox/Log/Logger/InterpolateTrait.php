<?php

namespace Rox\Log\Logger;

trait InterpolateTrait
{

    protected function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = [];

        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

}
