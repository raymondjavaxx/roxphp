!<?php
echo "<?php
/**
 * {$controller->getClassName()} controller
 *
 * @package {$package_name}
 * @copyright (C) {$year}
 */
class {$controller->getFileName()} extends ApplicationController {

	/**
	 * /{$controller->getName()}
	 *
	 * @return void
	 */
	public function indexAction() {
		\${$model->getPluralModelVarName()} = {$model->getClassName()}::model()->paginate(array(
			'page' => \$this->request->getQuery('page', 1)
		));

		\$this->set(compact('{$model->getPluralModelVarName()}'));
	}

	/**
	 * /{$controller->getName()}/view/1
	 *
	 * @param integer \$id
	 * @return void
	 */
	public function viewAction(\$id = null) {
		\${$model->getModelVarName()} = {$model->getClassName()}::model()->find(\$id);
		\$this->set(compact('{$model->getModelVarName()}')); ";
		foreach($model->getAssociations(array('belongsTo')) as $type=>$association){
			foreach($association as $assocModel=>$options){
				echo "\n\t\t\$this->set('".$associatedModels[$assocModel]->getPluralModelVarName() ."', ".$associatedModels[$assocModel]->getClassName()."::model()->findList('".$associatedModels[$assocModel]->getDisplayAttribute()."'));";
			}
		}

echo "
        }

	/**
	 * /{$controller->getName()}/add
	 *
	 * @return void
	 */
	public function addAction() {
		\${$model->getModelVarName()} = new {$model->getClassName()};

		if (\$this->request->isPost()) {
			\${$model->getModelVarName()}->setData(\$this->request->getPost('{$model->getModelVarName()}'));
			if(\${$model->getModelVarName()}->valid()){ ";

			if(preg_match("/.*_file/i", join(",", array_keys($model->getAttributes())))){
                            
echo "
				//Upload the files
				if(!empty(\$_FILES['{$model->getModelVarName()}'])){
					\${$model->getModelVarName()}->uploadFiles(array('model'=>'{$model->getModelVarName()}', 'path'=>'{$model->getPluralModelVarName()}'));
				}
				";
			}

			echo "
				if (\${$model->getModelVarName()}->save()) {";
				if($includeAssociations){
					//Here, check if associated models have file uploads
					foreach($includedAssociations as $assocType=>$association){
						if(empty($association))
							continue;

						foreach($association as $assocModel=>$options){
							$modelObj = $associatedModels[$assocModel];
							
							$noAttributes = 0;
							foreach($modelObj->getAttributes() as $attribute=>$options){
								if ( ! (in_array($attribute, array('id', 'created_at', 'updated_at')) || $attribute == $model->getModelVarName() .'_id' || $attribute == $model->getPluralModelVarName().'_id'))
									$noAttributes++;
								if ($noAttributes > 0)
									break;
							}
							if($noAttributes == 0)
                                                            continue;	
                                                        echo "\n\t\t//{$modelObj->getClassName()}";
					if($assocType == 'hasMany'){
                                            if(preg_match("/.*_file/i", join(",", array_keys($modelObj->getAttributes()))))
                                                echo "\n\t\t\${$model->getModelVarName()}->uploadAndSave(array('model'=>'{$modelObj->getModelVarName()}', 'path'=>'{$modelObj->getPluralModelVarName()}', 'data'=>\$this->request->getPost('{$modelObj->getModelVarName()}')));\n";
                                            else{
                                            echo "
                    foreach(\$this->request->getPost('{$modelObj->getModelVarName()}') as \${$modelObj->getModelVarName()}_post):
                        \${$modelObj->getModelVarName()} = new {$modelObj->getClassName()};
                        \${$modelObj->getModelVarName()}->setData(\${$modelObj->getModelVarName()}_post);
                        \${$modelObj->getModelVarName()}->{$model->getModelVarName()}_id = \${$model->getModelVarName()}->id;
                        \${$modelObj->getModelVarName()}->save();\n
                         endforeach;\n";

                                            }

                            } 
                            else if($assocType == 'hasOne'){
				                echo "
                    \${$modelObj->getModelVarName()} = new {$modelObj->getClassName()};
                    \${$modelObj->getModelVarName()}->{$model->getModelVarName()}_id = \${$model->getModelVarName()}->id;
                    \${$modelObj->getModelVarName()}->uploadFiles(array('model'=>'{$modelObj->getModelVarName()}', 'path'=>'{$modelObj->getPluralModelVarName()}'));
                    \${$modelObj->getModelVarName()}->setData(\$this->request->getPost('{$modelObj->getModelVarName()}'));
                    \${$modelObj->getModelVarName()}->save();
                                               ";
                            }
                        } //end foreach association 2
                    } //end foreach associationType
                } //End if includeAssociation

echo "
				}
				\$this->flash('success', 'The {$model->getFriendlyModelName()} has been created.');
				\$this->redirect('/{$controller->getName()}/view/' . \${$model->getModelVarName()}->id);
			} else {
				\$this->flash('error', 'Could not create the {$model->getFriendlyModelName()}. Please try again.');
			}
		}

		\$this->set(compact('{$model->getModelVarName()}')); ";
                foreach($model->getAssociations(array('belongsTo', 'hasAndBelongsToMany')) as $type=>$association){
                        foreach($association as $assocModel=>$options){
                                echo "\n\t\t\$this->set('".$associatedModels[$assocModel]->getPluralModelVarName() ."', ".$associatedModels[$assocModel]->getClassName()."::model()->findList('".$associatedModels[$assocModel]->getDisplayAttribute()."'));";
                        }
                }
echo"
        }

	/**
	 * /{$controller->getName()}/edit/1
	 *
	 * @param integer \$id
	 * @return void
	 */
	public function editAction(\$id = null) {
		\${$model->getModelVarName()} = {$model->getClassName()}::model()->find(\$id);

		if (\$this->request->isPost()) {
			\$newData = \$this->request->getPost('{$model->getModelVarName()}');
                        if(\${$model->getModelVarName()}->valid()){ ";



			if(preg_match("/.*_file/i", join(",", array_keys($model->getAttributes())))){

echo "
				//Upload the files
				if(!empty(\$_FILES['{$model->getName()}'])){
					//Change the path to where you want to upload to, relative to webroot
					\$uploads = \$this->uploadFiles('{$model->getPluralModelVarName()}', array('model'=>'{$model->getName()}'));
					if(\$uploads){
						foreach(\$uploads as \$key=>\$file){
							\${$model->getName()}->\$file['fieldName'] = \$file['rel_path'].\$file['file_name'];
						}
					}
					unset(\$_FILES['{$model->getName()}']);
				}
				";
			}

			echo "
				if (\${$model->getModelVarName()}->updateAttributes(\$newData)) {";
                                    if($includeAssociations){
					//Here, check if associated models have file uploads
					foreach($includedAssociations as $assocType=>$association){
						if(empty($association))
							continue;

						foreach($association as $assocModel=>$options){
							$modelObj = $associatedModels[$assocModel];

                                    echo "
                                        //{$modelObj->getClassName()}
                                        \$newData = \$this->request->getPost('{$modelObj->getModelVarName()}');
                                        \${$modelObj->getModelVarName()} = {$modelObj->getClassName()}::model()->find(\$newData['id']);
                                        ";

							if(preg_match("/.*_file/i", join(",", array_keys($modelObj->getAttributes())))){
								//If they do, we upload them
					echo "
					//Upload files for associated models
					if(!empty(\$_FILES)){
                                            //Change the path to where you want to upload the files, relative to webroot
                                            \$uploads = \$this->uploadFiles('{$modelObj->getPluralModelVarName()}', array('model'=>'".$modelObj->getModelVarName()."'));
                                            if(\$uploads){
                                                foreach(\$uploads as \$key=>\$file){
                                                    \$field = \$file['fieldName'];
                                                    \$newData[\$field] = \$file['rel_path'].\$file['file_name'];
                                                }
                                            }
					}
					";

                                                        } //End file Preg match
                                    echo "
                                        if(count(\$newData)>1)
                                            \${$modelObj->getModelVarName()}->updateAttributes(\$newData);
                                        ";

                                                }//end foreach association 2
                                        } //end foreach associationType
                                    } //end if includeAss

echo "
                                }
				\$this->flash('success', 'The {$model->getFriendlyModelName()} has been updated.');
				\$this->redirect('/{$controller->getName()}/view/' . \${$model->getModelVarName()}->id);
			} else {
				\$this->flash('error', 'Could not update {$model->getFriendlyModelName()}. Please try again.');
			}
		}

		\$this->set(compact('{$model->getModelVarName()}'));";
                foreach($model->getAssociations(array('belongsTo')) as $type=>$association){
                        foreach($association as $assocModel=>$options){
                                echo "\n\t\t\$this->set('".$associatedModels[$assocModel]->getPluralModelVarName() ."', ".$associatedModels[$assocModel]->getClassName()."::model()->findList('".$associatedModels[$assocModel]->getDisplayAttribute()."'));";
                        }
                }
echo "
	}

	/**
	 * /{$controller->getName()}/delete/1
	 *
	 * @param integer \$id
	 * @return void
	 */
	public function deleteAction(\$id = null) {
		if (\$this->request->isPost()) {
			\${$model->getModelVarName()} = {$model->getClassName()}::model()->find(\$id);
			\${$model->getModelVarName()}->delete();
			\$this->flash('success', 'The {$model->getFriendlyModelName()} has been deleted.');
			\$this->redirect('/{$controller->getName()}');
		}
	}
} ";
                        
