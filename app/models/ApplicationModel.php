<?php
	abstract class ApplicationModel extends Rox_ActiveRecord {
 
	public function findList($displayAttribute = null) {
		if(!$displayAttribute)
			$displayAttribute = $this->_displayAttribute;
		$results = array();
		foreach ($this->findAll() as $record) {
			$results[$record->id] = $record->{$displayAttribute};
		}
		return $results;
	}
		
	public function getAssociations($options){
		$associations = array();
		
		if(in_array('all', $options)){
			return(array(
					'hasMany'=>$this->_hasMany, 
					'hasOne'=>$this->_hasOne, 
					'belongsTo'=>$this->_belongsTo, 
					'hasAndBelongsToMany'=>$this->_hasAndBelongsToMany
				)
			);
		} else{ 
			if(in_array('hasMany', $options))
				$associations['hasMany'] = $this->_hasMany;
			if(in_array('hasOne', $options))
				$associations['hasOne'] = $this->_hasOne;
			if(in_array('belongsTo', $options))
				$associations['belongsTo'] = $this->_belongsTo;
			if(in_array('hasAndBelongsToMany', $options))
				$associations['hasAndBelongsToMany'] = $this->_hasAndBelongsToMany;
		}
		
		return $associations;

	}

	public function getDisplayAttribute(){
		return $this->_displayAttribute;
	}
}
?>