<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Rox_Console_Command_Controller extends Rox_Console_Command_Gen{
	protected $_name;

	protected $_class;

	protected $_friendlyName;

	protected $_fileName;

	function construct($name){
		$this->_name = Rox_Inflector::tableize($name);
		$this->_class = Rox_Inflector::classify($name);
		$this->_friendlyName = Rox_Inflector::humanize($this->_class);
		$this->_fileName = Rox_Inflector::pluralize($this->_class) . 'Controller';
	}

	public function getName() {
	 return $this->_name;
	}

	public function getClassName() {
	 return $this->_class;
	}

	public function getFriendlyName() {
	 return $this->_friendlyName;
	}

	public function getFileName(){
		return $this->_fileName;
	}



}

?>
