<?php
/**
 * {$controller->getFriendlyName()} controller
 *
 * @package {$package_name}
 * @copyright (C) {$year}
 */
class {$controller->getFileName()} extends ApplicationController {

	/**
	 * GET /{$controller->getName()}
	 *
	 * @return void
	public function indexAction() {
		${$model->getPluralModelVarName()} = {$model->getClassName()}::model()->paginate(array(
	 */
	public function indexAction() {
		${$model->getPluralModelVarName()} = {$model->getClassName()}::model()->paginate(array(
			'page' => $this->request->getQuery('page', 1)
		));

		$this->set(compact('{$model->getPluralModelVarName()}'));
	}

	/**
	 * GET /{$controller->getName()}/1
	 *
	 * @param integer $id
	 * @return void
	 */
	public function viewAction($id = null) {
		${$model->getModelVarName()} = {$model->getClassName()}::model()->find($id);
		$this->set(compact('{$model->getModelVarName()}'));
	}

	/**
	 * GET /{$controller->getName()}/new
	 * POST /{$controller->getName()}
	 *
	 * @return void
	 */
	public function addAction() {
		${$model->getModelVarName()} = new {$model->getClassName()};

		if ($this->request->isPost()) {
			${$model->getModelVarName()}->setData($this->request->data('{$model->getModelVarName()}'));
			if (${$model->getModelVarName()}->save()) {
				$this->flash('success', 'The {$model->getFriendlyModelName()} has been created.');
				$this->redirect('/{$controller->getName()}/' . ${$model->getModelVarName()}->id);
			} else {
				$this->flash('error', 'Could not create the {$model->getFriendlyModelName()}. Please try again.');
			}
		}

		$this->set(compact('{$model->getModelVarName()}'));
	}

	/**
	 * GET /{$controller->getName()}/1/edit
	 * PUT /{$controller->getName()}/1
	 *
	 * @param integer $id
	 * @return void
	 */
	public function editAction($id = null) {
		${$model->getModelVarName()} = {$model->getClassName()}::model()->find($id);

		if ($this->request->isPut()) {
			$newData = $this->request->data('{$model->getModelVarName()}');
			if (${$model->getModelVarName()}->updateAttributes($newData)) {
				$this->flash('success', 'The {$model->getFriendlyModelName()} has been updated.');
				$this->redirect('/{$controller->getName()}/' . ${$model->getModelVarName()}->id);
			} else {
				$this->flash('error', 'Could not update {$model->getFriendlyModelName()}. Please try again.');
			}
		}

		$this->set(compact('{$model->getModelVarName()}'));
	}

	/**
	 * DELETE /{$controller->getName()}/1
	 *
	 * @param integer $id
	 * @return void
	 */
	public function deleteAction($id = null) {
		if ($this->request->isDelete()) {
			${$model->getModelVarName()} = {$model->getClassName()}::model()->find($id);
			${$model->getModelVarName()}->delete();
			$this->flash('success', 'The {$model->getFriendlyModelName()} has been deleted.');
			$this->redirect('/{$controller->getName()}');
		}
	}
}