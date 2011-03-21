<?php

/*
 * Rox_Console_Command_Template
 *
 * Class used for working with templates when generating files from the command line
 */

class Rox_Console_Command_Template extends Rox_Console_Command {

    protected $_dir;

    /*
     * Variables to hold the template objects
     */
    public $model;
    public $controller;
    public $views;

    public function __construct($name = '', $files='all') {
        $this->_dir = ROX_FRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'Console' . DIRECTORY_SEPARATOR . 'Command' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $name;
        if (!is_dir($this->_dir)) {
            $this->out("Cannot find template directory.");
            $this->out("Make sure you have created one at " . $this->_dir);
            exit();
        }

        if ($files == 'all') {
            $this->model = new ModelTemplate($this->_dir);
            $this->controller = new ControllerTemplate($this->_dir);
            $this->views = new ViewsTemplate($this->_dir);
        } else if ($files == 'model')
            $this->model = new ModelTemplate($this->_dir);
        else if ($files == 'controller')
            $this->controller = new ControllerTemplate($this->_dir);
        else if ($files == 'views')
            $this->views = new ViewsTemplate($this->_dir);
    }

    public function renderTemplate($name, $vars = array(), $file=null, $data=null) {
        if (strstr($name, '/')) {
            $string = explode('/', $name);
            $runCode = $this->$string[0]->$string[1]->runCode;
            $file = $this->$string[0]->$string[1]->file;
        } else {
            $data = $this->$name->data;
            $runCode = $this->$name->runCode;
            $file = $this->$name->file;
        }

        if ($runCode) {
            extract($vars, EXTR_SKIP);

            ob_start();
            require $file;
            $data = ob_get_clean();

            $data = substr($data, 1);
        } else {
            foreach ($vars as $k => $v) {
                if (is_object($v)) {
                    while ($pos = strpos($data, '{$' . $k . '->')) {
                        $pos += 2;
                        $variable = substr($data, $pos, strpos($data, '}', $pos) - $pos);
                        $var = explode('->', $variable);
                        if (strpos($var[1], '(')) {
                            //No Args
                            if (strpos($var[1], '()'))
                                $var[1] = substr($var[1], 0, strlen($var[1]) - 2);
                            else {
                                $var[1] = substr($var[1], 0, strlen($var[1]) - 1);
                                $argString = trim(substr($var[1], strpos($var[1], '(') + 1, strlen($var[1]) - 1));
                                $array = preg_grep('/(array\().*(\))/', $argString);

                                print_r($array);

                                $array = preg_split('/[\s,]/', $argString);
                                //$args = preg_split('/[\s,]+/', $argString);

                                print_r($array);
                            }


                            //TODO: get function args

                            $replace = call_user_func_array(array($v, $var[1]), array(array('all'), TRUE));
                        } else {
                            $replace = $v->$var[0]->$var[1];
                        }
                        $data = str_replace('{$' . $variable . '}', $replace, $data);
                    }
                    continue;
                } else if (strpos($data, '{$' . $k . '}')) {
                    $data = str_replace('{$' . $k . '}', $v, $data);
                }
            }
        }

        return $data;
    }

}

class ModelTemplate extends GenericTemplate {

    public $includeBehaviors;
    public $behaviors;

    public function __construct($dir) {
        parent::__construct($dir);
    }

}

class ControllerTemplate extends GenericTemplate {

    public function __construct($dir) {
        parent::__construct($dir);
    }

}

class ViewsTemplate extends GenericTemplate {

    public $_files;

    public function __construct($dir) {
        $templates = array('add', 'edit', 'index', 'view');

        foreach ($templates as $template) {
            $this->$template = new ViewTemplate($dir, $template);

            if ($this->includeAssociations || $this->$template->includeAssociations) {
                $this->includeAssociations = TRUE;
            }
        }
    }

    protected function _missingFile($name, $file) {
        $this->out("Cannot find file $name");
        $this->out("Make sure you have created one at " . $file);
        exit();
    }

}

class ViewTemplate extends ViewsTemplate {

    protected $_name;
    public $file;

    public function __construct($dir, $template) {
        $this->_dir = $dir . DIRECTORY_SEPARATOR . 'views';
        $this->name = $template;
        $file = $this->_dir . DIRECTORY_SEPARATOR . $template . '.tpl';
        if (!file_exists($file))
            $this->_missingFile($template . '.tpl', $file);

        $this->file = $file;

        $this->data = $this->_getData();
        $this->runCode = $this->_runCode();
        $this->includeAssociations = $this->_includeAssociations();
        $this->data = '';
    }

}

/**
 * Abstract Class from which to extend specific templates
 */
abstract class GenericTemplate extends Rox_Console_Command {

    protected $_dir;
    public $file;
    public $data;
    public $runCode;
    public $includeAssociations;

    public function __construct($dir) {
        $this->_dir = $dir;
        $class = get_class($this);

        switch ($class) {
            case 'ModelTemplate':
                $name = 'model';
                break;
            case 'ControllerTemplate':
                $name = 'controller';
                break;
            case 'ViewTemplate':
                $name = 'views/' . $this->_name;
        }

        $this->file = $dir . DIRECTORY_SEPARATOR . $name . '.tpl';

        if (!file_exists($this->file))
            $this->_missingFile($name . '.tpl', $this->file);

        $this->data = $this->_getData();
        $this->runCode = $this->_runCode();
        $this->includeAssociations = $this->_includeAssociations();
        if ($class == 'ModelTemplate' || $class == 'ControllerTemplate')
            $this->includeBehaviors = $this->_includeBehaviors();
    }

    /**
     * Function to output a warning if the file we're looking for isn't there.
     *
     * @param string $name
     * @param string $file
     */
    protected function _missingFile($name, $file) {
        $this->out("Cannot find file $name");
        $this->out("Make sure you have created one at " . $file);
        exit();
    }

    /**
     * Checks for a single '!' at the start of template file to determine
     * if we should run the template as php code or take it as is
     *
     * @return boolean
     */
    protected function _runCode() {
        if (substr($this->data, 0, 1) == '!')
            return TRUE;
        return FALSE;
    }

    /**
     * checks for an occurrence of $model->getAssociations to determine if
     * we should include associations in the template
     *
     * @return boolean
     */
    protected function _includeAssociations() {
        return (bool) strpos($this->data, '$model->getAssociations(');
    }

    private function _includeBehaviors() {
        return (bool) strpos($this->data, '$model->getBehaviors(');
    }

    /**
     * returns the data of the template file
     *
     * @param string $file
     * @return string
     */
    protected function _getData($file=null) {
        if (!$file)
            $file = $this->file;

        $data = file_get_contents($file);
        if ($required = $this->_hasRequired()) {
            $data .= $this->_getRequired($required);
        }

        return($data);
    }

    /**
     * checks if data has any required or included files
     *
     * @param string $data
     * @return array
     */
    protected function _hasRequired($data=null) {
        if (!$data)
            $data = $this->data;

        $strings = array();

        $replaced = array(';', '?>', "'", '"', '(', ')');
        $includes = array('include ', 'include_once ', 'require ', 'require_once ', 'include(', 'include_once(', 'require(', 'require_once(');

        foreach ($includes as $include) {
            $replaced[] = $include;
            $pos = 0;
            while ($pos < strlen($data) && strpos($data, $include, $pos) !== FALSE) {
                $pos = strpos($data, $include, $pos);

                $string = substr($data, $pos, strpos($data, PHP_EOL));
                $string = substr($string, strpos($string, "'"));
                $string = substr($string, strpos($string, '"'));
                $string = trim(str_replace($replaced, "", $string));

                $this->out($string);

                $strings[] = $this->_dir . $string;



                $pos = strpos($data, PHP_EOL, $pos);
            }
        }

        return(!empty($strings)) ? $strings : false;
    }

    /**
     * returns the data from required files
     *
     * @param array $required
     * @return type string
     */
    protected function _getRequired(array $required) {
        $requiredData = '';
        foreach ($required as $file) {
            $requiredData .= file_get_contents($file) . PHP_EOL;
        }
        return $requiredData;
    }

}

?>
