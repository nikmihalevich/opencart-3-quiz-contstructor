<?php
class ModelExtensionModuleQuizNik extends Model {
    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "quiz_questions` (
			  `question_id` int(11) NOT NULL AUTO_INCREMENT,
			  `language_id` int(11) NOT NULL,
			  `question_text_on_page` TEXT DEFAULT NULL,
			  `question_text` TEXT DEFAULT NULL,
			  `answers` TEXT DEFAULT NULL,
			  PRIMARY KEY (`question_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "quiz_results` (
			  `result_id` int(11) NOT NULL AUTO_INCREMENT,
			  `language_id` int(11) NOT NULL,
			  `result_if_points_less` int(11) NOT NULL,
			  `result_text_result` TEXT DEFAULT NULL,
			  `result_bonus_link_text` TEXT DEFAULT NULL,
			  `result_bonus_link` TEXT DEFAULT NULL,
			  PRIMARY KEY (`result_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci
		");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "quiz_questions`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "quiz_results`");
    }

    public function addQuestion($data) {
        if ($data['question_text']) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "quiz_questions` SET `language_id` = '" . (int)$data['language_id'] . "', `question_text_on_page` = '" . $this->db->escape($data['question_text_on_page']) . "', `question_text` = '" . $this->db->escape($data['question_text']) . "', `answers` = '" . $this->db->escape(json_encode($data['answers'], true)) . "'");
        }
    }

    public function editQuestion($question_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "quiz_questions` SET `question_text_on_page` = '" . $this->db->escape($data['question_text_on_page']) . "', `question_text` = '" . $this->db->escape($data['question_text']) . "', `answers` = '" . $this->db->escape(json_encode($data['answers'], true)) . "' WHERE `question_id` = '" . (int)$question_id . "'");
    }

    public function deleteQuestion($question_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "quiz_questions` WHERE `question_id` = '" . (int)$question_id . "'");
    }

    public function getQuestions() {
        $questions_data = array();

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_questions` ORDER BY `question_id`");

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

    public function addResult($data) {
        if (!empty($data['result_if_points_less'])) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "quiz_results` SET `language_id` = '" . (int)$data['language_id'] . "', `result_if_points_less` = '" . (int)$data['result_if_points_less'] . "', `result_text_result` = '" . $this->db->escape($data['result_text_result']) . "', `result_bonus_link_text` = '" . $this->db->escape($data['result_bonus_link_text']) . "', `result_bonus_link` = '" . $this->db->escape($data['result_bonus_link']) . "'");
        }
    }

    public function editResult($result_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "quiz_results` SET `result_if_points_less` = '" . $this->db->escape($data['result_if_points_less']) . "', `result_text_result` = '" . $this->db->escape($data['result_text_result']) . "', `result_bonus_link_text` = '" . $this->db->escape($data['result_bonus_link_text']) . "', `result_bonus_link` = '" . $this->db->escape($data['result_bonus_link']) . "' WHERE `result_id` = '" . (int)$result_id . "'");
    }

    public function deleteResult($result_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "quiz_results` WHERE `result_id` = '" . (int)$result_id . "'");
    }

    public function getResults() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_results` ORDER BY `result_id`");

        return $query->rows;
    }

    public function getResult($result_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quiz_results` WHERE `result_id` = '" . (int)$result_id . "'");

        return $query->row;
    }
}
