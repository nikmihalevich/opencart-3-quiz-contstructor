<?php
class ModelExtensionModuleQuizNik extends Model {
	public function getOrder($order_id) {
		
		$query = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
		
		return $query->row['order_status_id'];
		
	}
}