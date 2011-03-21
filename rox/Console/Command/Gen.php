<?php

/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * undocumented class
 *
 * @package default
 */
class Rox_Console_Command_Gen extends Rox_Console_Command {

    public $model;
    public $controller;
    public $views;
    public $template;
    public $project;
    public $includeAssociations;
    public $indludedAssociations;
    public $genAll;
    public $templateDir = '';

    public function header() {
        $this->hr();
        $this->out(' RoxPHP Generator');
        $this->hr();
    }

    public function run($argc, $argv) {

        if (isset($argv[4]))
            $this->templateDir = $argv[4];
        else
            $this->templateDir = 'basic';

        if ($argv[3] == 'all') {
            $this->genAll = TRUE;
            $this->_generateAll($argv[2]);
            exit;
        }

        $this->genAll = FALSE;

        switch ($argv[2]) {
            case 'controller':
                $this->construct($argv[3]);
                $this->template = new Rox_Console_Command_Template ($this->templateDir, 'controller');
                $this->_generateController($argv[3]);
                break;

            case 'model':
                $this->construct($argv[3]);
                $this->template = new Rox_Console_Command_Template ($this->templateDir,'model');
                $this->_generateModel($argv[3]);
                break;

            case 'views':
                $this->construct($argv[3]);
                $this->template = new Rox_Console_Command_Template ($this->templateDir, 'views');
                $this->_generateViews($argv[3]);
                break;

            case 'migration':
                $this->_generateMigration($argv[3]);
                break;
            case 'all':
                $this->construct($argv[3]);
                $this->template = new Rox_Console_Command_Template ($this->templateDir);
                $this->_generateModel($argv[3]);
                $this->_generateController($argv[3]);
                $this->_generateViews($argv[3]);
                break;
            case 'default':
                $this->out("Invalid argument. -- $argv[2]");
                $this->out("try controller, model, views, migration, or all");
        }

        $this->out("");
        $this->out(" -- ALL DONE! -- ");
        $this->out("");
    }

    function construct($name) {
        $this->package = 'App';

        $this->model = new Rox_Console_Command_Model();
        $this->model->construct($name);

        $this->controller = new Rox_Console_Command_Controller();
        $this->controller->construct($name);

        $this->views = new Rox_Console_Command_Views();
        $this->views->construct($name);

        $this->includedAssociations = array();

        $this->out("");
        $this->out(" -- Generating " . Rox_Inflector::pluralize($this->model->getFriendlyModelName()) . " -- ");
    }

    protected function _generateModel($name) {
        $this->out("");
        $this->out(" -- Generating Model -- ");

        $this->model->generating = TRUE;

        if($this->template->model->includeAssociations)
            $this->model->generateModelAssociations();

        if($this->template->model->includeBehaviors)
            $this->model->addBehaviors();

        $vars = array(
            'model' => $this->model,
            'package_name' => $this->package,
            'year' => date('Y')
        );

        $data = $this->template->renderTemplate('model', $vars);
        $this->_writeFile('/models/' . Rox_Inflector::classify($name) . '.php', $data);
    }

    protected function _generateController($name) {
        $this->out("");
        $this->out(" -- Generating Controller -- ");

        $associatedModels = array();

        if($this->template->controller->includeAssociations){
            //TODO: drop this in the template class. or the controller class

            //check to see if we have any associated models, before we bother asking to include them
            $associations = $this->model->getAssociations(array('hasMany', 'hasOne', 'hasAndBelongsToMany'));

            if (empty($associations['hasMany']) && empty($associations['hasOne']) && empty($associations['hasAndBelongsToMany']))
                $hasAssociations = FALSE;
            else
                $hasAssociations = TRUE;

            if ($hasAssociations) {
                //First, ask if we should include the associated models in the controller.
                if (($this->includeAssociations || $this->_includeAssociations())) {

                    if(count( $associations['hasMany']) + count($associations['hasOne']) + count($associations['hasAndBelongsToMany'])==1){
                        $this->includedAssociations = $associations;
                        $this->out("*Included one associated model*");
                    }else{
                        do {
                            $this->out("Do you want to select which models to include (else include all)?(y/N)");
                            $answer = strtolower($this->in());
                        } while (!in_array($answer, array('', 'y', 'n')));

                        if ($answer == 'y') {
                            $this->includedAssociations = $this->_whichAssociations();
                        } else {
                            $this->includedAssociations = $associations;
                        }
                    }
                }
            }

            //Set the original model so we can set it back.
            $origModel = $this->model;
            $associations = array_merge($this->includedAssociations, $this->model->getAssociations(array('belongsTo')));


            //Now we include all the associated models in an array we pass to the controller.tpl
            foreach (@$associations as $assocType) {
                foreach ($assocType as $assocModel => $options) {
                    $associatedModels[$assocModel] = new Rox_Console_Command_Model;
                    $associatedModels[$assocModel]->construct($assocModel);
                    if (!file_exists(ROX_APP_PATH . '/models/' . $associatedModels[$assocModel]->getClassName() . '.php') && !$this->genAll && $hasAssociations) {
                        do {
                            $this->out("");
                            $this->out("Model " . $associatedModels[$assocModel]->getClassName() . ".php does not exist.");
                            $this->out("Generate it now? (Y/n)");
                            $answer = strtolower($this->in());
                        } while (!in_array($answer, array('', 'y', 'n')));

                        if ($answer != 'n') {
                            $this->model = $associatedModels[$assocModel];
                            $this->_generateModel($associatedModels[$assocModel]->getClassName());
                        }
                    }
                }
            }
            $this->model = $origModel;
        }


        $vars = array(
            'controller' => $this->controller,
            'model' => $this->model,
            'associatedModels' => $associatedModels,
            'package_name' => $this->package,
            'year' => date('Y'),
            'includeAssociations' => $this->includeAssociations,
            'includedAssociations' => $this->includedAssociations
        );

        $data = $this->template->renderTemplate('controller', $vars);
        $this->_writeFile('/controllers/' . $this->controller->getFilename() . '.php', $data);
    }

    protected function _generateViews($name) {
        $this->out("");
        $this->out(" -- Generating Views -- ");

        $associatedModels = array();
        $templates = array('add', 'edit', 'index', 'view');
        if($this->template->views->includeAssociations){
            //check to see if we have any associated models, before we bother asking to include them
            $associations = $this->model->getAssociations(array('hasMany', 'hasOne', 'hasAndBelongsToMany'));
            if (empty($associations['hasMany']) && empty($associations['hasOne']) && empty($associations['hasAndBelongsToMany']))
                $hasAssociations = FALSE;
            else
                $hasAssociations = TRUE;

            if ($hasAssociations) {
                //Check if we have already asked in the controller to include Associations, if so, just skip this and auto include the included models
                if ($this->includeAssociations || $this->_includeAssociations()) {
                    if (@!$this->includedAssociations) {
                        if(count( $associations['hasMany']) + count($associations['hasOne']) + count($associations['hasAndBelongsToMany'])==1){
                            $this->includedAssociations = $associations;
                            $this->out("*Included one associated model*");
                        }else{
                            do {
                                $this->out("Do you want to select which models to include (else include all)?(y/N)");
                                $answer = strtolower($this->in());
                            } while (!in_array($answer, array('', 'y', 'n')));

                            if ($answer == 'y') {
                                $this->includedAssociations = $this->_whichAssociations();
                            } else {
                                $this->includedAssociations = $associations;
                            }
                        }
                    }
                }
            }

            foreach ($this->model->getAssociations() as $assocType) {
                foreach ($assocType as $assocModel => $options) {
                    $associatedModels[$assocModel] = new Rox_Console_Command_Model;
                    $associatedModels[$assocModel]->construct($assocModel);
                }
            }
        }

        //TODO: assign the template object to vars, use it in templates to handle things like includeAssociations and fileUpload booleans...
        $vars = array(
            'model' => $this->model,
            'controller' => $this->controller,
            'associatedModels' => $associatedModels,
            'package_name' => $this->package,
            'year' => date('Y'),
            'includeAssociations' => $this->includeAssociations,
            'includedAssociations' => $this->includedAssociations
        );

        foreach ($templates as $template) {
            $vars['template'] = $template;
            $data = $this->template->renderTemplate("views/{$template}", $vars, true);
            $folder = Rox_Inflector::tableize($name);
            $this->_writeFile("/views/{$folder}/{$template}.html.tpl", $data);
        }

        $this->_copyFiles();
    }

    protected function _generateMigration($name) {
        $name = Rox_Inflector::underscore($name);

        foreach (glob(ROX_APP_PATH . '/config/migrations/*.php') as $file) {
            if (preg_match("/([0-9]+)_{$name}.php/", $file) == 1) {
                throw new Exception("A migration named {$name} already exists");
            }
        }

        $version = gmdate('YmdHis');

        $data = $this->_renderMigration('migration', array(
                    'class_name' => Rox_Inflector::camelize($name),
                    'year' => date('Y')
                ));

        $this->_writeFile("/config/migrations/{$version}_{$name}.php", $data);
    }

    protected function _generateAll($arg='all') {
         $this->template = new Rox_Console_Command_Template ($this->templateDir);
        //TODO: check if something (probably) belongs to another model, and suggest only generating the model if so.
        $dataSource = Rox_ConnectionManager::getDataSource();
        $tables = $dataSource->listTables();
        foreach ($tables as $table) {
            $this->includeAssociations = FALSE;

            if (strstr($table, '_')) {
                $linkedTables = explode("_", $table);

                if ((in_array(Rox_Inflector::pluralize($linkedTables[0]), $tables) || in_array($linkedTables[0], $tables)) && in_array($linkedTables[1], $tables))
                    continue;
            }

            $this->out("");
            $this->out(" -- " . Rox_Inflector::humanize(Rox_Inflector::classify($table)) . "  ---- ");
            $this->hr();
            $this->out("");

            if($arg == 'all'){
                do {
                    $this->out('What would you like to generate for this model? (1)');
                    $this->out("0: Nothing");
                    $this->out("1: Everything");
                    $this->out("2: Model");
                    $this->out("3: Controller");
                    $this->out("4: Views");
                    $answer = strtolower($this->in());
                } while (!in_array($answer, array('', '0', '1', '2', '3', '4', '2,3,4', '2,3', '2,4', '3,4')));

                if ($answer == '')
                    $answer = '1';
            }else{
                switch($arg){
                    case 'model':
                        $answer = 2;
                        break;
                    case 'controller':
                        $answer = 3;
                        break;
                    case 'views':
                        $answer = 4;
                        break;
                }
            }

            if ($answer != '0')
                $this->construct($table);
            else
                continue;


            if ($answer == 1) {
                $this->_generateModel($table);
                $this->_generateController($table);
                $this->_generateViews($table);
            }

            if (!(strstr($answer, ',') === false)) {
                $gens = explode(",", $answer);
                foreach ($gens as $gen) {
                    switch (trim($gen)) {
                        case '2':
                            $this->_generateModel($table);
                            break;
                        case '3':
                            $this->_generateController($table);
                            break;
                        case '4':
                            $this->_generateViews($table);
                            break;
                    }
                }
            } else {
                switch ($answer) {
                    case '2':
                        $this->_generateModel($table);
                        break;
                    case '3':
                        $this->_generateController($table);
                        break;
                    case '4':
                        $this->_generateViews($table);
                        break;
                }
            }
        }


        $this->out("");
        $this->hr();
        $this->out("ALL DONE!!!");

        //TODO: it would be pretty sweet to generate an admin nav bar, where users select the models to include and they are added to the top
    }

    protected function _writeFile($file, $data) {
        $absolutePath = ROX_APP_PATH . $file;

        $directory = dirname($absolutePath);
        clearstatcache();
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (file_exists($absolutePath)) {
            do {
                $this->out("File app{$file} already exists. Do you want to overwrite it?(y,N)");
                $answer = strtolower($this->in());
            } while (!in_array($answer, array('', 'y', 'n')));

            if ($answer != 'y') {
                return false;
            }
        }

        $this->out("Writing file: app{$file}");
        return file_put_contents($absolutePath, $data);
    }

    private function _includeAssociations() {
        do {
            $this->out("Do you want to include associated models in your controller (and views)? (Y/n)");
            $answer = strtolower($this->in());
        } while (!in_array($answer, array('', 'y', 'n')));

        if ($answer != 'n')
            return$this->includeAssociations = true;
        else
            return $this->includeAssociations = false;
    }

    private function _whichAssociations() {
        $assoc = array();

        foreach ($this->model->getAssociations(array('hasMany', 'hasOne', 'hasAndBelongsToMany')) as $assocType => $association) {
            foreach ($association as $assocModel => $options) {
                do {
                    $this->out("Do you want to include " . Rox_Inflector::humanize(Rox_Inflector::classify($assocModel)) . "?");
                    $answer = strtolower($this->in());
                } while (!in_array($answer, array('', 'y', 'n')));

                if ($answer != 'n')
                    $assoc[$assocType][$assocModel] = $options;
            }
        }

        return $assoc;
    }

    protected function _renderMigration($name, $vars = array(), $runCode = false) {
		if ($runCode) {
			extract($vars, EXTR_SKIP);

			ob_start();
			require dirname(__FILE__) . '/templates/' . $name . '.tpl';
			$data = ob_get_clean();
		} else {
			$data = file_get_contents(dirname(__FILE__) . '/templates/' . $name . '.tpl');
			foreach ($vars as $k => $v) {
				$data = str_replace('{' . $k . '}', $v, $data);
			}
		}

		return $data;
	}

    protected function _copyFiles(){
        $template_dirs = array(
            'behaviors'=>'/models/behaviors/',
            'layouts'=>'/views/layouts/',
            'elements'=>'/views/elements/',
            'css'=>'/webroot/css/',
            'js'=>'/webroot/js/',
            'img'=>'/webroot/img/',
            'swf'=>'/webroot/swf/',
            'files'=>'/webroot/files/',
            'helpers'=>'/helpers/',
            'vendors'=>'/vendors/'
        );

        foreach($template_dirs as $dir=>$path){
            if(is_dir($fullpath = ROX_FRAMEWORK_PATH.'/Console/Command/templates/'.$this->templateDir.'/views/'.$dir)){
                $this->out(" -- Copying $dir --");
                $dh = opendir($fullpath) or die("couldn't open directory");

                while (!(($file = readdir($dh)) === false )) {
                    if($file == "."  || $file == "..")
                        continue;
                    copy($fullpath."/".$file, ROX_APP_PATH.$path.$file);
                }

                closedir($dh);
            }
        }
    }

}
