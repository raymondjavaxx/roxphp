<?php
/**
 * {friendly_controller_name} controller
 *
 * @package {package_name}
 * @copyright (C) {year}
 */
class {controller_class} extends ApplicationController {

	/**
	 * GET /{controller_name}
	 *
	 * @return void
	 */
	public function indexAction() {
		${model_var_plural_name} = {model_class}::paginate(array(
			'page' => $this->request->getQuery('page', 1)
		));

		$this->set(compact('{model_var_plural_name}'));
	}

	/**
	 * GET /{controller_name}/1
	 *
	 * @param integer $id 
	 * @return void
	 */
	public function viewAction($id = null) {
		${model_var_name} = {model_class}::find($id);
		$this->set(compact('{model_var_name}'));
	}

	/**
	 * GET /{controller_name}/new
	 * POST /{controller_name}
	 *
	 * @return void
	 */
	public function addAction() {
		${model_var_name} = new {model_class};

		if ($this->request->isPost()) {
			${model_var_name}->setData($this->request->data('{model_name}'));
			if (${model_var_name}->save()) {
				$this->flash('success', 'The {friendly_model_name} has been created.');
				$this->redirect('/{controller_name}/' . ${model_var_name}->id);
			} else {
				$this->flash('error', 'Could not create the {friendly_model_name}. Please try again.');
			}
		}

		$this->set(compact('{model_var_name}'));
	}

	/**
	 * GET /{controller_name}/1/edit
	 * PUT /{controller_name}/1
	 *
	 * @param integer $id 
	 * @return void
	 */
	public function editAction($id = null) {
		${model_var_name} = {model_class}::find($id);

		if ($this->request->isPut()) {
			$newData = $this->request->data('{model_name}');
			if (${model_var_name}->updateAttributes($newData)) {
				$this->flash('success', 'The {friendly_model_name} has been updated.');
				$this->redirect('/{controller_name}/' . ${model_var_name}->id);
			} else {
				$this->flash('error', 'Could not update {friendly_model_name}. Please try again.');
			}
		}

		$this->set(compact('{model_var_name}'));
	}

	/**
	 * DELETE /{controller_name}/1
	 *
	 * @param integer $id 
	 * @return void
	 */
	public function deleteAction($id = null) {
		if ($this->request->isDelete()) {
			${model_var_name} = {model_class}::find($id);
			${model_var_name}->delete();
			$this->flash('success', 'The {friendly_model_name} has been deleted.');
			$this->redirect('/{controller_name}');
		}
	}
}
