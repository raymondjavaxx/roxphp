<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Rox_ActiveRecord_Behavior{
    
    function setup (&$model, $config=array()){
      

    }

    function _beforeSave(&$model){

    }

    function _afterSave(&$model, $created){
        
    }

    function dispatchMethod(&$model, $method, $params = array()){
        if(method_exists($this, $method)){
            if(empty($params)){
                return $this->{$method}($model);
            }
            switch(count($params)){
                case 1:
                    return $this->{$method}($model, $params[0]);
                    break;
                case 2:
                    return $this->{$method}($model, $params[0], $params[1]);
                    break;
                case 3:
                    return $this->{$method}($model, $params[0], $params[1], $params[2]);
                    break;
                case 4:
                    return $this->{$method}($model, $params[0], $params[1], $params[2], $params[3]);
                    break;
                case 5:
                    return $this->{$method}($model, $params[0], $params[1], $params[2], $params[3], $params[4]);
                    break;
                case 6:
                    return $this->{$method}($model, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
                    break;
                case 7:
                    return $this->{$method}($model, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
                    break;
                case 8:
                    return $this->{$method}($model, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7]);
                    break;
            }
            
        } else return false;
    }


}



?>
