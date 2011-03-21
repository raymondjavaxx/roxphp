<?php

class Rox_ActiveRecord_BehaviorCollection{

    var $model = NULL;

    protected $_attached = array();

    protected $_methods = array();

    protected $_disabled = array();

    function setup(Rox_ActiveRecord &$model, $config=array()){
        $this->model = $model;

        foreach($model->_behaviors as $Behavior){
            $this->attached[] = $this->_attach($Behavior, $config);
        }

        unset($this->model);
        return $this;

    }

    protected function _attach($Behavior, $config = array()){
        try {

            $name = Rox_Inflector::classify($Behavior);

            //TODO: add behaviors to include_path, then remove this.
            require_once (ROX_APP_PATH . '/models/behaviors/'. $Behavior .'.php');
            $class = $Behavior .'Behavior';

            if(Rox_Registry::contains($class))
               $this->{$name} = Rox_Registry::getObject ($class);
            else{
                $this->{$name} = new $class();
                Rox_Registry::addObject($class, $this->{$name});
            }

            $this->{$name}->setup($this->model, $config);

            $methods = get_class_methods($this->{$name});
            $parentMethods = array_flip(get_class_methods('Rox_ActiveRecord_Behavior'));

            $callbacks = array(
                    'setup', 'cleanup', 'beforeSave', 'afterSave',
                    'beforeDelete', 'afterDelete'
            );

            foreach ($methods as $m) {
                $this->_methods[$m] = array($m, $name);
            }
         
            if (!in_array($name, $this->_attached)) {
                    $this->_attached[] = $name;
            }
            if (in_array($name, $this->_disabled) && !(isset($config['enabled']) && $config['enabled'] === false)) {
                    $this->enable($name);
            } elseif (isset($config['enabled']) && $config['enabled'] === false) {
                    $this->disable($name);
            }
            return $class;


        } catch (Rox_KeyException $e) {
           //File Not Found
        }
    }

    function disable($name){
        $this->_disabled[] = $name;
    }

    function dispatchMethod(&$model, $method, $params = array()){
        $methods = array_keys($this->_methods);
        foreach($methods as $key=>$value){
                $methods[$key] = strtolower($value);
        }
        $method = strtolower($method);
        $check = array_flip($methods);
        $found = isset($check[$method]);
        $call = null;

        if($found){                
                $methods = array_combine($methods, array_values($this->_methods));
                $call = $methods[$method];
        }
        if (!empty($call)) {
                return $this->{$call[1]}->dispatchMethod($model, $call[0], $params);
        }
        return false;



    }
}