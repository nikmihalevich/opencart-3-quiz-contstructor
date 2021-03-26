<?php
class ControllerExtensionModuleQuizNik extends Controller {
	public function index() {
		$this->load->language('extension/module/quiz_nik');

        $this->load->model('setting/setting');
        $this->load->model('extension/module/quiz_nik');

        $module_info = $this->model_setting_setting->getSetting('module_quiz_nik');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/quiz_nik')
        );

        if ($module_info['module_quiz_nik_status']) {
            if (isset($this->request->get['question_id'])) {
                $questions = $this->model_extension_module_quiz_nik->getQuestions();
                $question = $this->model_extension_module_quiz_nik->getQuestion($this->request->get['question_id']);

                if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                    if ($this->request->post['language_id'] != $this->config->get('config_language_id')) {
                        unset($this->session->data['answers']);

                        $questions = $this->model_extension_module_quiz_nik->getQuestions();

                        if (!empty($questions)) {
                            $this->response->redirect($this->url->link('extension/module/quiz_nik', '&question_id=' . $questions[0]['question_id']));
                        } else {
                            $this->response->redirect($this->url->link('extension/module/quiz_nik'));
                        }
                    }
                    // we go next
                    if ($this->request->get['question_id'] > $this->request->post['question_id']) {
                        $this->session->data['answers'][$this->request->post['question_id']] = $this->request->post['answer'];
                    } else { // we go back
                        unset($this->session->data['answers'][$this->request->post['question_id']]);
                    }
                }

                $elementKey = array_search($question, $questions);

                if ($elementKey != 0) {
                    $prevElementKey = $elementKey - 1;
                    $prevElement = $questions[$prevElementKey];
                    $data['prev'] = strip_tags(html_entity_decode($this->url->link('extension/module/quiz_nik', '&question_id=' . $prevElement['question_id'])));
                } else {
                    $data['prev'] = false;
                    $data['back'] = strip_tags(html_entity_decode($this->url->link('extension/module/quiz_nik')));
                }

                if ($elementKey != (count($questions) - 1)) {
                    $nextElementKey = $elementKey + 1;
                    $nextElement = $questions[$nextElementKey];
                    $data['next'] = strip_tags(html_entity_decode($this->url->link('extension/module/quiz_nik', '&question_id=' . $nextElement['question_id'])));
                } else {
                    $data['next'] = false;
                    $data['finish'] = strip_tags(html_entity_decode($this->url->link('extension/module/quiz_nik/result')));
                }

                $question['question_text_on_page'] = html_entity_decode($question['question_text_on_page']);
                $question['question_text'] = html_entity_decode($question['question_text']);

                $data['question'] = $question;
            } else {
                $data['quiz_description'] = isset($module_info['module_quiz_nik_description'][$this->config->get('config_language_id')]) ? $module_info['module_quiz_nik_description'][$this->config->get('config_language_id')] : array();

                if (isset($data['quiz_description']['text'])) {
                    $data['quiz_description']['text'] = html_entity_decode($data['quiz_description']['text']);
                }

                $questions = $this->model_extension_module_quiz_nik->getQuestions();

                if (!empty($questions)) {
                    $data['start_button'] = $this->url->link('extension/module/quiz_nik', '&question_id=' . $questions[0]['question_id']);
                }
            }

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('extension/module/quiz_nik', $data));
        } else {
            $this->document->setTitle($this->language->get('text_error'));

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
	}

	public function result() {
        $this->load->language('extension/module/quiz_nik');
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/quiz_nik')
        );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->session->data['answers'])) {

            $this->load->model('extension/module/quiz_nik');

            $this->session->data['answers'][$this->request->post['question_id']] = $this->request->post['answer'];
            $questions = $this->model_extension_module_quiz_nik->getQuestions();

            $userPoints = 0;

            foreach ($questions as $question) {
                foreach ($question['answers'] as $answer) {
                    if ($answer->answer == $this->session->data['answers'][$question['question_id']]) {
                        $userPoints += (int)$answer->points;
                    }
                }
            }

            $result = $this->model_extension_module_quiz_nik->getResultByPoints($userPoints);

            if (!empty($result)) {
                $result['result_text_result'] = html_entity_decode($result['result_text_result']);
            }

            $result['back'] = $this->url->link('extension/module/quiz_nik');

            unset($this->session->data['answers']);

            $data['result'] = $result;
            $data['points'] = $userPoints;

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('extension/module/quiz_nik', $data));
        } else {
            $this->document->setTitle($this->language->get('text_error'));

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
}