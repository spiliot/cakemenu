<?php
App::uses('CakemenuAppController', 'Cakemenu.Controller');
App::uses('Menu', 'Cakemenu.Model');
App::uses('Cakemenu', 'Cakemenu.Helper/View');

class CakemenuController extends CakemenuAppController {
	public $name = 'Cakemenu';
	public $uses = array('Cakemenu.Menu');
	public $helpers = array('Cakemenu.Cakemenu');

	public function index() {
		$menu_list = $this->Menu->generateTreeList(null, null, null, '&nbsp;&nbsp;&nbsp;');
		//Get links
		$links = $this->Menu->find('list', array('fields'=>array('id', 'link')));
		
		$this->set('menu_list', $menu_list);
		$this->set('links', $links);
	}
	
	public function preview($type = null){
		$this->set('type', $type);
		$menu = $this->Menu->find('threaded');
		$this->set('menu', $menu);
	}
	
	public function move($id = null, $direction = 'down'){
		if($direction == 'down'){
			$this->Menu->moveDown(intval($id));
		} else {
			$this->Menu->moveUp(intval($id));
		}
		$this->redirect(array('action'=>'index'));
	}
	
	public function recover(){
		$this->Menu->recover($this->Menu);
		$this->redirect(array('action'=>'index'));
	}

	public function edit($id = null) {
		if (!empty($this->data)) {
			if ($this->Menu->save($this->data)) {
				$this->Session->setFlash(sprintf(__('The %s has been saved'), 'cakemenu'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(sprintf(__('The %s could not be saved. Please, try again.'), 'cakemenu'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Menu->read(null, $id);
		}
		$parents = $this->Menu->generateTreeList(null, null, null, '___');
		$this->set(compact('parentCakemenus', 'parents'));
	}

	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(sprintf(__('Invalid id for %s'), 'Menu'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Menu->removeFromTree($id)) {
			$this->Menu->delete($id);
			$this->Session->setFlash(sprintf(__('%s deleted'), 'Menu'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(sprintf(__('%s was not deleted'), 'Menu'));
		$this->redirect(array('action' => 'index'));
	}
}
?>