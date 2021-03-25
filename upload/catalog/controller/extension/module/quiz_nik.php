<?php
class ControllerExtensionModuleQuizNik extends Controller {
	public function index() {
		$this->load->language('extension/module/quiz_nik');

        $this->load->model('setting/setting');
        $this->load->model('extension/module/quiz_nik');

        $module_info = $this->model_setting_setting->getSetting('module_quiz_nik');

        $data = $module_info['module_quiz_nik_description'][$this->config->get('config_language_id')];

//        var_dump($data);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/quiz_nik')
        );

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/module/quiz_nik', $data));
	}
}