<?php
/**
 * {friendly_controller_name} controller
 *
 * @package {package_name}
 * @copyright (C) {year}
 */
class {controller_class} extends ApplicationController {

	/**
	 * /{controller_name}
	 *
	 * @return void
	 */
	public function indexAction() {
		${model_var_plural_name} = {model_class}::model()->paginate(array(
			'page' => $this->request->getQuery('page', 1)
		));

		$this->set(compact('{model_var_plural_name}'));
	}

	/**
	 * /{controller_name}/view/1
	 *
	 * @param integer $id 
	 * @return void
	 */
	public function viewAction($id = null) {
		${model_var_name} = {model_class}::model()->find($id);
		$this->set(compact('{model_var_name}'));
	}

	/**
	 * /{controller_name}/add
	 *
	 * @return void
	 */
	public function addAction() {
		${model_var_name} = new {model_class};

		if ($this->request->isPost()) {
			${model_var_name}->setData($this->request->getPost('{model_name}'));
			if (${model_var_name}->save()) {
				$this->flash('success', 'The {friendly_model_name} has been created.');
				$this->redirect('/{controller_name}/view/' . ${model_var_name}->id);
			} else {
				$this->flash('error', 'Could not create the {friendly_model_name}. Please try again.');
			}
		}

		$this->set(compact('{model_var_name}'));
	}

	/**
	 * /{controller_name}/edit/1
	 *
	 * @param integer $id 
	 * @return void
	 */
	public function editAction($id = null) {
		${model_var_name} = {model_class}::model()->find($id);

		if ($this->request->isPost()) {
			$newData = $this->request->getPost('{model_name}');
			if (${model_var_name}->updateAttributes($newData)) {
				$this->flash('success', 'The {friendly_model_name} has been updated.');
				$this->redirect('/{controller_name}/view/' . ${model_var_name}->id);
			} else {
				$this->flash('error', 'Could not update {friendly_model_name}. Please try again.');
			}
		}

		$this->set('{model_var_name}', ${model_var_name});
	}

	/**
	 * /{controller_name}/delete/1
	 *
	 * @param integer $id 
	 * @return void
	 */
	public function deleteAction($id = null) {
		if ($this->request->isPost()) {
			${model_var_name} = {model_class}::model()->find($id);
			${model_var_name}->delete();
			$this->flash('success', 'The {friendly_model_name} has been deleted.');
			$this->redirect('/{controller_name}');
		}
	}
}
