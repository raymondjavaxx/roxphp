<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Rox\Http;

use \Rox\Http\Request\ParamCollection;
use \Rox\Http\Request\ServerParamCollection;
use \Rox\Http\Request\Normalizer;

/**
 * Request
 *
 * @package Rox
 */
class Request
{
    /**
     * Query params
     *
     * @var \Rox\Http\Request\ParamCollection
     */
    public $query;

    /**
     * Request params
     *
     * @var \Rox\Http\Request\ParamCollection
     */
    public $data;

    /**
     * Server params
     *
     * @var \Rox\Http\Request\ServerParamCollection
     */
    public $server;

    /**
     * HTTP request headers
     *
     * @var \Rox\Http\Request\ParamCollection
     */
    public $headers;

    /**
     * HTTP request method
     *
     * @var string
     */
    protected $method;

    /**
     * Constructor
     *
     * @param array $query 
     * @param array $data 
     * @param array $server 
     */
    public function __construct(array $query = [], array $data = [], array $server = []) {
        $this->query   = new ParamCollection($query);
        $this->data    = new ParamCollection($data);
        $this->server  = new ServerParamCollection($server);
        $this->headers = new ParamCollection($this->server->getHeaders());
    }

    /**
     * Creates a new request object
     *
     * @return \rox\http\Request
     */
    public static function fromGlobals()
    {
        return new static($_GET, $_POST, $_SERVER);
    }

    /**
     * Returns the HTTP request method
     * 
     * @return string
     */
    public function method()
    {
        if ($this->method === null) {
            $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));

            if ($this->method === 'POST') {
                $this->method = strtoupper($this->data->get('_method', 'POST'));
            }
        }

        return $this->method;
    }

    /**
     * Returns the raw request body
     *
     * @return string
     */
    public function rawBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * Check if the request method matches a given HTTP method
     *
     * @param string $method 
     * @return boolean
     */
    public function is($method)
    {
        return (strcmp($this->method(), $method) === 0);
    }

    /**
     * Return true if the HTTP request method is POST
     * 
     * @return boolean
     */
    public function isPost()
    {
        return $this->is('POST');
    }

    /**
     * Return true if the HTTP request method is GET
     * 
     * @return boolean
     */
    public function isGet()
    {
        return $this->is('GET');
    }

    /**
     * Return true if the HTTP request method is PUT
     *
     * @return boolean
     */
    public function isPut()
    {
        return $this->is('PUT');
    }

    /**
     * Return true if the HTTP request method is DELETE
     *
     * @return boolean
     */
    public function isDelete()
    {
        return $this->is('DELETE');
    }

    /**
     * Returns true if the page was requested via AJAX
     * 
     * @return boolean
     */
    public function isAjax()
    {
        return $this->headers['x-requested-with'] === 'XMLHttpRequest';
    }

    /**
     * Returns true if the page was requested through SSL
     * 
     * @return boolean
     */
    public function isSSL()
    {
        if ($this->headers['x-forwarded-proto'] === 'https') {
            // SSL terminated by load balancer/reverse proxy
            return true;
        }

        $ssl = $this->server->get('HTTPS');
        return $ssl === true || $ssl === 'on';
    }
}
