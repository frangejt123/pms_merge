<?php
session_start();
defined('BASEPATH') or exit('No direct script access allowed');

class Rawmaterial extends CI_Controller
{
	public function index()
	{
		if (isset($_SESSION["rgc_username"])) {
			$this->load->view('rawmaterial');
		} else {
			$this->load->view('login');
		}
	}

	public function getAll()
	{
		$this->load->model('modRawmaterial', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$res = $this->modRawmaterial->getAll($param)->result_array();

		// foreach($res as $ind => $row){
		// 	$res[$ind]["parent_description"] = "";
		// 	if(!is_null($row["parent_id"])){
		// 		$parent_id["id"] = $row["parent_id"];
		// 		$desc = $this->modUom->getAll($parent_id)->row_array();
		// 		$res[$ind]["parent_description"] = $desc["description"];
		// 	}else{
		// 		$res[$ind]["parent_id"] = "";
		// 	}
		// }

		echo json_encode($res);
	}

	public function getType()
	{
		$this->load->model('modRawmaterial', "", TRUE);
		$this->load->model('modRawmaterialtype', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$res = $this->modRawmaterialtype->getAll($param)->result_array();

		// foreach($res as $ind => $row){
		// 	$res[$ind]["parent_description"] = "";
		// 	if(!is_null($row["parent_id"])){
		// 		$parent_id["id"] = $row["parent_id"];
		// 		$desc = $this->modUom->getAll($parent_id)->row_array();
		// 		$res[$ind]["parent_description"] = $desc["description"];
		// 	}else{
		// 		$res[$ind]["parent_id"] = "";
		// 	}
		// }

		echo json_encode($res);
	}

	public function insert()
	{
		$this->load->model('modRawmaterial', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modRawmaterial->insert($param);

		echo json_encode($result);
	}

	public function update()
	{
		$this->load->model('modRawmaterial', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$result = $this->modRawmaterial->update($param);

		echo json_encode($result);
	}

	public function delete()
	{
		$param = $this->input->post(NULL, TRUE);
		$this->load->model('modRawmaterial', "", TRUE);

		$res = $this->modRawmaterial->delete($param);
		echo json_encode($res);
	}

	public function checkcode()
	{
		$param = $this->input->post(NULL, TRUE);
		$this->load->model('modRawmaterial', "", TRUE);
		$param["itemcode"] = ucwords($param["itemcode"]);

		$res = $this->modRawmaterial->checkcode($param)->num_rows();
		echo $res;
	}

	public function savetypechanges()
	{
		$this->load->model('modRawmaterialtype', "", TRUE);

		$param = $this->input->post(NULL, TRUE);
		$err = 0;
		foreach ($param as $ind => $row) {
			$row["description"] = $row["value"];
			$param[$ind]["description"] = $row["value"];
			$res = [];
			if ($row["method"] == "new") {
				$res = $this->modRawmaterialtype->insert($row);
				$param[$ind]["id"] = $res["id"];
			} else if ($row["method"] == "edit") {
				$res = $this->modRawmaterialtype->update($row);
			} else {
				$res = $this->modRawmaterialtype->delete($row);
			}
			if (!$res) {
				$err++;
			}
		}

		$result = array();
		if ($err == 0) {
			$result["success"] = true;
			$result["data"] = $param;
		} else {
			$result["success"] = false;
		}

		print_r(json_encode($result));
	}
}
