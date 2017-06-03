<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2012 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2012 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Rox\Http\Request;

class ServerParamCollection extends ParamCollection
{
    /**
     * Returns all HTTP request headers
     *
     * Returned headers are normalized to lowercase:
     *
     *     array(
     *         'content-type' => 'application/json',
     *         'user-agent' => 'Mozilla/5 ...',
     *         'accept-language' => 'en-us,en;q=0.5',
     *         ...
     *     )
     *
     * @return associative array containing HTTP request headers and their values
     */
    public function getHeaders()
    {
        $headers = [];

        foreach ($this->data as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }
}
