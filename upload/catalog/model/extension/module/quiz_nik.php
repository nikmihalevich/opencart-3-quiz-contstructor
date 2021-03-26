<?php
class ModelExtensionModuleQuizNik extends Model {
    public function getQuestions() {
        $questions_data = array();

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_questions` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `question_id`");

        foreach ($query->rows as $key => $result) {
            $result['answers'] = json_decode($result['answers']);
            $questions_data[$key] = $result;
        }

        return $questions_data;
    }

    public function getQuestion($question_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_questions` WHERE `question_id` = '" . (int)$question_id . "'");


        $question_data = $query->row;
        $question_data['answers'] = json_decode($question_data['answers']);

        return $question_data;
    }

    public function getResults() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_results` ORDER BY `result_id`");

        return $query->rows;
    }

    public function getResult($result_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_results` WHERE `result_id` = '" . (int)$result_id . "'");

        return $query->row;
    }

    public function getResultByPoints($points) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_results` WHERE `result_if_points_less` > '" . (int)$points . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `result_if_points_less`");

        return $query->row;
    }
}