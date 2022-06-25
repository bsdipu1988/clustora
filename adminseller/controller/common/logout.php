<?php
class ControllerCommonLogout extends Controller {
	public function index() {
		$this->user->logout();

		unset($this->session->data['token']);
		unset($this->session->data['user_id']);

		$this->response->redirect($this->url->link('common/login', '', 'SSL'));
	}
}