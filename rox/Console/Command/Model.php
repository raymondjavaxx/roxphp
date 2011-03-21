<?php

/** Rox Console Command Model
 *
 * Class used to generate models using gen model ModelName
 */
class Rox_Console_Command_Model extends Rox_Console_Command {

    protected $_associations;
    protected $_className;
    public $_friendlyModelName;
    protected $_tableName;
    protected $_displayAttribute;
    protected $_attributes;
    protected $_modelVarName;
    protected $_pluralModelVarName;
    protected $_dataSource;
    protected $_name;
    protected $_behaviors;
    private $possHasOne;
    public $generating;

    function construct($name) {
        $this->_className = Rox_Inflector::classify($name);
        $this->_friendlyModelName = Rox_Inflector::humanize(Rox_Inflector::classify($name));
        $this->_tableName = Rox_Inflector::tableize($this->_className);
        $this->_dataSource = Rox_ConnectionManager::getDataSource();
        $this->_attributes = $this->_getAttributesFromTable($this->_tableName);
        //$this->_displayAttribute = $this->getDisplayAttribute();
        $this->_modelVarName = Rox_Inflector::singularize(Rox_Inflector::tableize($name));
        $this->_pluralModelVarName = Rox_Inflector::pluralize($this->_modelVarName);
        $this->_name = $name;
    }

    public function getAssociations(array $types = array('all'), $toString = FALSE) {
        if (file_exists(ROX_APP_PATH . '/models/' . $this->_className . '.php') && !$this->generating) {
            $model = new $this->_className;
            return $this->_associations = $model->getAssociations($types);
        }

        if (!empty($this->_associations)) {
            if (in_array('all', $types)){
                if($toString)
                    return $this->_associationString($this->_associations);
                return $this->_associations;
            }

            else {
                $associations = array();

                foreach ($types as $type) {
                    $associations[$type] = $this->_associations[$type];
                }
                if($toString)
                    return $this->_associationString($associations);

                return $associations;
            }
        }

        $this->out ("");
        $this->out("ERROR: $this->_className not found!");
        $this->out("You must create the model first!");
        $this->out("Try php rox.php gen model $this->_className OR php rox.php gen all $this->_className");
        $this->out("");

        die();
    }

    public function getFriendlyModelName() {
        return $this->_friendlyModelName;
    }

    public function getTableName() {
        return $this->_tableName;
    }

    public function getDisplayAttribute() {
        if (!empty($this->_displayAttribute))
            return $this->_displayAttribute;

        if (file_exists(ROX_APP_PATH . '/models/' . $this->_className . '.php') && !$this->generating) {
            $model = new $this->_className;
            return $this->_displayAttribute = $model->getDisplayAttribute();
        }

        return $this->_generateDisplayField();
    }

    public function getAttributes() {
        return $this->_attributes;
    }

    public function getModelVarName() {
        return $this->_modelVarName;
    }

    public function getPluralModelVarName() {
        return $this->_pluralModelVarName;
    }

    public function getClassName() {
        return $this->_className;
    }

    public function getName() {
        return $this->_name;
    }

    public function getBehaviors($toString = FALSE){
        if($toString)
            return $this->_behaviorString($this->_behaviors);

        return $this->_behaviors;
    }

    public function addBehaviors(){
        do {
            $this->out("Do you want to add behaviors to your model?(Y/n)");
            $answer = strtolower($this->in());
        }while(!in_array($answer, array('', 'y', 'n')));

        $quit = FALSE;
        if ($answer != 'n'){
            do{
                $this->out("Type in the name of your behaviors. (separated by comma)");
                $behaviors = $this->in();
            }while($behaviors == '');

            $behaviors = explode(",", $behaviors);
            foreach($behaviors as $behavior){
                $this->_behaviors[] = trim($behavior);
            }
        }
    }


    /** Protected Functions * */

    /** _generateModelAssociations
     *
     * @param <array> $type
     * @return <array>
     */
    public function generateModelAssociations($type = array('all')) {
        $this->out("Generating Model Associations...");
        $this->out("");
        $this->_associations['belongsTo'] = $this->_generateBelongsTo($this->_className);
        $this->_associations['hasMany'] = $this->_generateHasMany($this->_className);
        $this->_associations['hasOne'] = $this->_generateHasOne($this->_className);
        //$this->_associations['hasAndBelongsToMany'] = $this->_generateHasAndBelongsToMany($this->_className);
        $this->_associations['hasAndBelongsToMany'] = array();

        $this->out("");

        return $this->_associations;
    }

    protected function _generateBelongsTo($name) {
        $pos_assoc = array();
        foreach ($this->_attributes as $field_name => $field) {
            if (strpos($field_name, '_id') && $field_name != 'parent_id' && $field_name != $this->_tableName . '_id' && $field_name != $this->_modelVarName) {
                $assoc_modelVarName = Rox_Inflector::singularize(str_replace('_id', '', $field_name));
                $assoc_pluralModelVarName = Rox_Inflector::pluralize($assoc_modelVarName);
                $assoc_modelClassName = Rox_Inflector::camelize(Rox_Inflector::classify($assoc_modelVarName));

                if (in_array($assoc_pluralModelVarName, $this->_getAllTables()))
                    $pos_assoc[$assoc_modelVarName] = array(
                        'className' => $assoc_modelClassName,
                        'foreignKey' => $field_name
                    );
            }
        }
        return($this->_confirmAssoc($name, $pos_assoc, 'BelongsTo'));
    }

    protected function _generateHasOne($name) {
        $pos_assoc = array();
        if (!empty($this->possHasOne))
            foreach ($this->possHasOne as $modelName => $options) {
                $model = Rox_Inflector::singularize($modelName);
                $pos_assoc[$model] = $options;
            }
        return($this->_confirmAssoc($name, $pos_assoc, 'HasOne'));
    }

    protected function _generateHasMany($name) {
        $pos_assoc = array();

        foreach ($this->_getAllTables() as $table) {
            if (!(strpos($table, $this->_tableName) === false))
                continue;
            $attributes = $this->_dataSource->generateAttributeMapFromTable($table);
            foreach ($attributes as $field_name => $field) {
                if ($this->_tableName . '_id' == $field_name || Rox_Inflector::singularize($this->_tableName) . '_id' == $field_name)
                    $pos_assoc[$table] = array(
                        'className' => Rox_Inflector::camelize(Rox_Inflector::classify($table)),
                        'foreignKey' => $field_name
                    );
            }
        }

        $conf_assoc = $this->_confirmAssoc($name, $pos_assoc, 'HasMany');
        $this->possHasOne = array_diff_assoc($pos_assoc, $conf_assoc);

        return($conf_assoc);
    }

    protected function _generateHasAndBelongsToMany($name) {
        $pos_assoc = array();
        $tables = array();

        foreach ($this->_getAllTables() as $table) {
            if (!(strpos($table, $this->_tableName . '_') === FALSE) ||
                    !(strpos($table, '_' . $this->_tableName) === FALSE) ||
                    !(strpos($table, $this->_modelVarName . '_') === FALSE) ||
                    !(strpos($table, '_' . $this->_modelVarName) === FALSE)
            ) {
                if (in_array($table, $this->getAssociations(array('hasMany'), TRUE)))
                    continue;

                $joinTable = $table;

                $table = str_replace('_' . $this->_modelVarName, '', str_replace('_' . $this->_tableName, '', str_replace($this->_tableName . '_', '', str_replace($this->_modelVarName . '_', '', $table))));
                $tables[] = $table;
                $options = array(
                    'className' => Rox_Inflector::classify($table),
                    'joinModel' => Rox_Inflector::classify($joinTable),
                    'joinTable' => $joinTable,
                    'foreignKey' => $this->_modelVarName . '_id',
                    'assocForeignKey' => $table . '_id'
                );
                $pos_assoc[$table] = $options;
            }
        }

        return($this->_confirmAssoc($name, $pos_assoc, 'hasAndBelongsToMany'));

        $tables = array();
        if (!empty($associations)) {
            foreach ($associations as $association) {
                $tables[][$association] = ($pos_assoc[$association]);
            }
        }
        return $tables;
    }

    protected function _confirmAssoc($name, $pos_assoc, $type) {
        $defaults = array(
            'className' => $this->_className,
            'foreignKey' => $this->_modelVarName . '_id',
            'table' => Rox_Inflector::tableize($this->_className),
        );

        $associations = array();
        foreach ($pos_assoc as $model => $assoc) {
            do {
                $this->out("$name $type " . Rox_Inflector::camelize($model) . "? (Y/n)");
                $answer = strtolower($this->in());
            } while (!in_array(strtolower($answer), array('', 'y', 'n')));

            if (strtolower($answer) == 'n') {
                continue;
            } else {
                $options = array_merge($defaults, $assoc);
                $associations[$model] = $options;
            }
        }

        return $associations;
    }

    protected function _getAllTables() {
        return($this->_dataSource->listTables());
    }

    protected function _getAttributesFromTable($table=null) {
        if (!$table)
            $table = $this->_tableName;
        $columns = $this->_dataSource->describe($this->_tableName);

        foreach ($columns as $col) {
            $type = strtolower($col['Type']);
            $name = $col['Field'];

            if ($type == 'tinyint(1)') {
                $type = 'boolean';
            } else if (strpos($col['Type'], 'int') !== false) {
                $type = 'integer';
            } else if (strpos($col['Type'], 'char') !== false) {
                $type = 'string';
            } else if (preg_match('/^decimal|float|double/', $col['Type']) === 1) {
                $type = 'float';
            } else if ($type == 'blob') {
                $type = 'binary';
            } else if ($col['Type'] == 'text') {
                $type = 'textarea';
            }

            $required = ($col['Null'] == 'NO') ? TRUE : FALSE;
            $fieldMap[$name] = array('type' => $type, 'required' => $required);
        }

        return $fieldMap;
    }

    protected function _generateDisplayField($table = null) {
        //TODO: check if this model belongs to another. if so, assume the display_field MIGHT be $parentModel_id
        if (!$table) {
            $table = $this->_tableName;
            $attributes = $this->_attributes;
        }
        else
            $attributes = $this->_dataSource->generateAttributeMapFromTable($table);

        $posKeys = array('title', 'name', 'username', 'label', $table, Rox_Inflector::singularize($table));
        foreach ($attributes as $attribute => $options) {
            if (strstr($attribute, $this->_modelVarName) || strstr($attribute, $this->_pluralModelVarName) || in_array($attribute, $posKeys))
                return $attribute;
        }

        do {
            $this->out("No display field could be found for $table. Choose one from below.");
            foreach ($attributes as $attribute => $type) {
                $this->out($attribute);
            }

            $answer = strtolower($this->in());
        } while (empty($answer));

        return $answer;
    }

    protected function _associationString($associations, $indent = 1){
        $string = '';
        foreach($associations as $type=>$association){
            $tables = '';
            foreach($association as $table=>$options){
                $tables .= "\n\t\t'$table' => array(\r";
                foreach($options as $key=>$option){
                    $tables.= "\t\t\t'$key'=>'$option',\r";
                }
                $tables .= "\t\t),";
            }
            if($tables != '')
                $string .= "\tprotected \$_$type = array(".substr($tables, 0, strlen($tables)-1).
                "\r\t);\r";
        }
        return $string;

    }

    protected function _behaviorString($behaviors){


        if(empty($behaviors))
            return NULL;
        $string = '';
        foreach($behaviors as $behavior){
            $string .= "'$behavior', ";
        }
        return "\n\tpublic \$_behaviors = array(" . substr($string, 0, strlen($string)-2) . ");\n";
    }

}

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              