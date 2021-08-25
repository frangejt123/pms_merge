<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModDailyinventorydetail extends CI_Model
{

	public $NAMESPACE = "daily_inventory_detail";
	private $TABLE = "daily_inventory_detail",
		$FIELDS = array(
			"id" => "daily_inventory_detail.id",
			"period_id" => "daily_inventory_detail.daily_inventory_id",
			"rawmat_id" => "daily_inventory_detail.rawmat_id",
			"qty" => "daily_inventory_detail.qty"
		);

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param)
	{
		$tablefield = "";

		foreach ($this->FIELDS as $alias => $field) {
			if ($tablefield != "") {
				$tablefield .= ",";
			}
			//Construct table field selection
			$tablefield .= $field . " AS `" . $alias . "`";
			if ($param)
				if (array_key_exists($alias, $param)) {
					$this->db->where($field, $param[$alias]);
				}
		}

		$this->db->select($tablefield);
		$this->db->from("daily_inventory_detail");
		$query = $this->db->get();

		return $query;
	}

	function insert($param)
	{
		$result = array();
		$data = array();

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param)) {
				if ($param[$alias] != "") {
					$data[$field] = $param[$alias];
				}
			}
		}

		if ($this->db->insert('daily_inventory_detail', $data)) {
			//$result_row = $this->db->query("SELECT LAST_INSERT_ID() AS `id`")->result_object();
			$result["id"] = $this->db->insert_id();
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function update($param)
	{

		$result = array();
		$data = array();
		//        $param["id"] = $param["_server_id"];
		$id = $param["id"];

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param))
				$data[$field] = $param[$alias];
		}

		$this->db->where($this->FIELDS['id'], $id);

		if ($this->db->update('daily_inventory_detail', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}


	function delete($param)
	{

		$result = array();
		$this->db->where($this->FIELDS['id'], $param["id"]);

		if ($this->db->delete('daily_inventory_detail')) {
			$result["id"] = $param["id"];
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function checkcode($param)
	{
		$this->db->select("id");
		$this->db->from("daily_inventory_detail");
		if (isset($param["id"])) {
			$this->db->where('daily_inventory_detail.id !=', $param["id"]);
		}
		$this->db->where('UCASE(daily_inventory_detail.itemcode) =', $param["itemcode"]);
		$query = $this->db->get();

		return $query;
	}
}
