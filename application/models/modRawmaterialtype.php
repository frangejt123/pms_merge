<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModRawmaterialtype extends CI_Model {

	public $NAMESPACE = "raw_material_type";
	private $TABLE = "raw_material_type",
		$FIELDS = array(
		"id" => "raw_material_type.id",
		"description" => "raw_material_type.description",
		"color" => "raw_material_type.color"
	);

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param) {
		$tablefield = "";
		$this->FIELDS["text"] = "raw_material_type.description";

		foreach ($this->FIELDS as $alias => $field) {
			if ($tablefield != "") {
				$tablefield .= ",";
			}
			//Construct table field selection
			$tablefield .= $field . " AS `" . $alias . "`";
			if($param)
				if (array_key_exists($alias, $param)) {
					$this->db->where($field, $param[$alias]);
				}
		}

		$this->db->select($tablefield);
		$this->db->from("raw_material_type");
		$query = $this->db->get();

		return $query;
	}

	function insert($param) {
		$result = array();
		$data = array();

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param)) {
				if ($param[$alias] != "") {
					$data[$field] = $param[$alias];
				}
			}
		}

		if ($this->db->insert('raw_material_type', $data)) {
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

	function update($param) {

		$result = array();
		$data = array();
//        $param["id"] = $param["_server_id"];
		$id = $param["id"];

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param))
				$data[$field] = $param[$alias];
		}

		$this->db->where($this->FIELDS['id'], $id);

		if ($this->db->update('raw_material_type', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}


	function delete($param) {

		$result = array();
		$this->db->where($this->FIELDS['id'], $param["id"]);

		if ($this->db->delete('raw_material_type')) {
			$result["id"] = $param["id"];
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function checkcode($param) {
		$this->db->select("id");
		$this->db->from("raw_material_type");
		if(isset($param["id"])){
			$this->db->where('raw_material_type.id !=', $param["id"]);
		}
		$this->db->where('UCASE(raw_material_type.itemcode) =', $param["itemcode"]);
		$query = $this->db->get();

		return $query;
	}
}
