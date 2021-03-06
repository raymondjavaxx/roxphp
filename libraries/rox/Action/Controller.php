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

namespace Rox\Action;

use \rox\Rox;
use \Rox\Utils\Inflector;
use \rox\Router;
use \rox\http\Request;
use \rox\http\Response;

/**
 * Controller
 *
 * @package Rox
 */
class Controller
{
    /**
     * Page title
     *
     * @var string
     */
    public $pageTitle = 'RoxPHP';

    /**
     * Layout name
     *
     * @var string
     */
    public $layout = 'default';

    /**
     * List of helpers to be automatically loaded when rendering
     *
     * @var array
     */
    public $helpers = [];

    /**
     * Request object
     *
     * @var \rox\http\Request
     */
    public $request;

    /**
     * Response object
     *
     * @var \rox\http\response
     */
    public $response;

    /**
     * Request params
     *
     * @var array
     */
    public $params;

    /**
     * View variables
     *
     * @var array  
     */
    protected $viewVars = [];

    /**
     * Constructor
     *
     * @param \rox\http\Request $request  The HTTP request
     * @param \rox\http\Response $response  The mutable HTTP response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Renders the current action
     */
    public function render() {
        $this->set('rox_page_title', $this->pageTitle);

        foreach ($this->helpers as $helper) {
            $varName = Inflector::lowerCamelize($helper);
            $this->set($varName, Rox::getHelper($helper));
        }

        $viewPath = $this->params['controller'];
        if (!empty($this->params['namespace'])) {
            $simpleControllerName = substr($this->params['controller'], strlen($this->params['namespace']) + 1);
            $viewPath = $this->params['namespace'] . '/' . $simpleControllerName;
        }

        $viewName = $this->params['action'];

        $view = new \rox\template\View($this->viewVars);
        $view->response = $this->response;
        $view->params   = $this->params;

        $this->response->body = $view->render($viewPath, $viewName, $this->layout);
    }

    /**
     * Sets a view variable
     *
     * @param string|array $varName
     * @param mixed $value
     */
    public function set($varName, $value = null)
    {
        if (is_array($varName)) {
            $this->viewVars += $varName;
            return;
        }

        $this->viewVars[$varName] = $value;
    }

    /**
     * undocumented function
     *
     * @param string $type
     * @param string $message 
     */
    public function flash($type, $message) {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = array();
        }
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Sends redirect headers and exit
     *
     * @param string $url
     */
    protected function redirect($url, $options = array())
    {
        $defaults = ['status' => 301];
        $options += $defaults;

        $location = preg_match('/^([a-z0-9]+):\/\//', $url) === 1
            ? $url : Router::url($url);

        $this->response->status = $options['status'];
        $this->response->header('Location', $location);
        $this->response->render();
        exit;
    }

    /**
     * Redirects to referer
     *
     * @param string $default
     */
    protected function redirectToReferer($default = '/') {
        $url = $this->request->headers->get('referer', $default);
        $this->redirect($url);
    }

    // ------------------------------------------------
    //  Callbacks
    // ------------------------------------------------

    /**
     * Before-filter callback
     *
     * @return void
     */
    public function beforeFilter()
    {

    }

    /**
     * After-filter callback
     *
     * @return void
     */
    public function afterFilter()
    {

    }
}
