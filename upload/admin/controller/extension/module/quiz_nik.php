<?php
class ControllerExtensionModuleQuizNik extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/quiz_nik');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_quiz_nik', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['add_question_button'] = $this->url->link('extension/module/quiz_nik/getQuestionForm', 'user_token=' . $this->session->data['user_token'], true);
		$data['edit_question_button'] = $this->url->link('extension/module/quiz_nik/getQuestionForm', 'user_token=' . $this->session->data['user_token'] . '&question_id=', true);
		$data['delete_question_button'] = $this->url->link('extension/module/quiz_nik/deleteQuestion', 'user_token=' . $this->session->data['user_token'] . '&question_id=', true);

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['module_quiz_nik_description'])) {
            $data['module_quiz_nik_description'] = $this->request->post['module_quiz_nik_description'];
        } else {
            $data['module_quiz_nik_description'] = $this->config->get('module_quiz_nik_description');
        }

		if (isset($this->request->post['module_quiz_nik_status'])) {
			$data['module_quiz_nik_status'] = $this->request->post['module_quiz_nik_status'];
		} else {
			$data['module_quiz_nik_status'] = $this->config->get('module_quiz_nik_status');
		}

        $this->load->model('extension/module/quiz_nik');

		$data['questions'] = $this->model_extension_module_quiz_nik->getQuestions();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/quiz_nik', $data));
	}

	public function getQuestionForm() {
        $this->load->language('extension/module/quiz_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('extension/module/quiz_nik');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['answers'] = array();
            $this->request->post['language_id'] = $this->request->get['language_id'];
            foreach ($this->request->post['question_answers'] as $k => $answer) {
                if (!empty($answer)) {
                    $this->request->post['answers'][] = array(
                        'answer' => $answer,
                        'points' => $this->request->post['question_answers_points'][$k] ? $this->request->post['question_answers_points'][$k] : 0
                    );
                }
            }

            if (!isset($this->request->get['question_id'])) {
                $this->model_extension_module_quiz_nik->addQuestion($this->request->post);
            } else {
                $this->model_extension_module_quiz_nik->editQuestion($this->request->get['question_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'], true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->request->get['question_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $question_info = $this->model_extension_module_quiz_nik->getQuestion($this->request->get['question_id']);
        }

        if (!isset($this->request->get['question_id'])) {
            $data['action'] = $this->url->link('extension/module/quiz_nik/getQuestionForm', 'user_token=' . $this->session->data['user_token'] . '&language_id=' . $this->request->get['language_id'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/quiz_nik/getQuestionForm', 'user_token=' . $this->session->data['user_token'] . '&language_id=' . $question_info['language_id'] . '&question_id=' . $this->request->get['question_id'], true);
        }

        $data['cancel'] = $this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['question_text_on_page'])) {
            $data['question_text_on_page'] = $this->request->post['question_text_on_page'];
        } elseif (!empty($question_info)) {
            $data['question_text_on_page'] = $question_info['question_text_on_page'];
        } else {
            $data['question_text_on_page'] = '';
        }

        if (isset($this->request->post['question_text'])) {
            $data['question_text'] = $this->request->post['question_text'];
        } elseif (!empty($question_info)) {
            $data['question_text'] = $question_info['question_text'];
        } else {
            $data['question_text'] = '';
        }

        if (isset($this->request->post['answers'])) {
            $data['answers'] = $this->request->post['answers'];
        } elseif (!empty($question_info)) {
            $data['answers'] = $question_info['answers'];
        } else {
            $data['answers'] = array();
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/quiz_add_question_nik', $data));
    }

    public function deleteQuestion() {
	    if (isset($this->request->get['question_id'])) {
            $this->load->model('extension/module/quiz_nik');

            $this->model_extension_module_quiz_nik->deleteQuestion($this->request->get['question_id']);

            $this->response->redirect($this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'], true));
        }
    }

    public function getResultForm() {
        $this->load->language('extension/module/quiz_nik');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('extension/module/quiz_nik');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['answers'] = array();
            $this->request->post['language_id'] = $this->request->get['language_id'];
            foreach ($this->request->post['question_answers'] as $k => $answer) {
                if (!empty($answer)) {
                    $this->request->post['answers'][] = array(
                        'answer' => $answer,
                        'points' => $this->request->post['question_answers_points'][$k] ? $this->request->post['question_answers_points'][$k] : 0
                    );
                }
            }

            if (!isset($this->request->get['question_id'])) {
                $this->model_extension_module_quiz_nik->addQuestion($this->request->post);
            } else {
                $this->model_extension_module_quiz_nik->editQuestion($this->request->get['question_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'], true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->request->get['question_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $question_info = $this->model_extension_module_quiz_nik->getQuestion($this->request->get['question_id']);
        }

        if (!isset($this->request->get['question_id'])) {
            $data['action'] = $this->url->link('extension/module/quiz_nik/getAddQuestionForm', 'user_token=' . $this->session->data['user_token'] . '&language_id=' . $this->request->get['language_id'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/quiz_nik/getAddQuestionForm', 'user_token=' . $this->session->data['user_token'] . '&language_id=' . $question_info['language_id'] . '&question_id=' . $this->request->get['question_id'], true);
        }

        $data['cancel'] = $this->url->link('extension/module/quiz_nik', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['question_text_on_page'])) {
            $data['question_text_on_page'] = $this->request->post['question_text_on_page'];
        } elseif (!empty($question_info)) {
            $data['question_text_on_page'] = $question_info['question_text_on_page'];
        } else {
            $data['question_text_on_page'] = '';
        }

        if (isset($this->request->post['question_text'])) {
            $data['question_text'] = $this->request->post['question_text'];
        } elseif (!empty($question_info)) {
            $data['question_text'] = $question_info['question_text'];
        } else {
            $data['question_text'] = '';
        }

        if (isset($this->request->post['answers'])) {
            $data['answers'] = $this->request->post['answers'];
        } elseif (!empty($question_info)) {
            $data['answers'] = $question_info['answers'];
        } else {
            $data['answers'] = array();
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/quiz_add_question_nik', $data));
    }

    public function install() {
        $this->load->model('extension/module/quiz_nik');

        $this->model_extension_module_quiz_nik->install();
    }

    public function uninstall() {
        if ($this->user->hasPermission('modify', 'extension/module/quiz_nik')) {
            $this->load->model('extension/module/quiz_nik');

            $this->model_extension_module_quiz_nik->uninstall();
        }
    }

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/quiz_nik')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}