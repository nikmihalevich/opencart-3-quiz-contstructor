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

//        $this->db->query("
//			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "quiz_results` (
//			  `paypal_order_id` int(11) NOT NULL AUTO_INCREMENT,
//			  `order_id` int(11) NOT NULL,
//			  `date_added` DATETIME NOT NULL,
//			  `date_modified` DATETIME NOT NULL,
//			  `capture_status` ENUM('Complete','NotComplete') DEFAULT NULL,
//			  `currency_code` CHAR(3) NOT NULL,
//			  `authorization_id` VARCHAR(30) NOT NULL,
//			  `total` DECIMAL( 10, 2 ) NOT NULL,
//			  PRIMARY KEY (`paypal_order_id`)
//			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci
//		");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "quiz_questions`");
//        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "paypal_order`");
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

    public function editPayPalOrderStatus($order_id, $capture_status) {
        $this->db->query("UPDATE `" . DB_PREFIX . "paypal_order` SET `capture_status` = '" . $this->db->escape($capture_status) . "', `date_modified` = NOW() WHERE `order_id` = '" . (int)$order_id . "'");
    }
}
