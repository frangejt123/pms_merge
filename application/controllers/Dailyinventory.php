<?php
session_start();
defined('BASEPATH') or exit('No direct script access allowed');

class Dailyinventory extends CI_Controller
{
	public function index()
	{
		if (isset($_SESSION["rgc_username"])) {
			$data["poscount"] = $_SESSION["rgc_poscount"];
			$this->load->view('daily_inventory');
		} else {
			$this->load->view('login');
		}
	}

	public function getAll()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$param['branch_id'] = $_SESSION['rgc_branch_id'];

		$res = $this->modDailyinventory->getAll($param)->result_array();

		foreach ($res as $ind => $row) {
			$res[$ind]["period"] = date('m/d/Y', strtotime($row['period']));
		}

		echo json_encode($res);
	}

	public function detail()
	{
		$this->load->model('modDailyinventorydetail', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$res = $this->modDailyinventorydetail->getAll($param)->result_array();

		echo json_encode($res);
	}

	public function getParent()
	{
		$this->load->model('modKit', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$res["Dailyinventory"] = $this->modKit->getParent($param)->result_array();
		//$res["uom"] = $this->modUom->getAll($param)->result_array();
		//if(isset($param["Dailyinventory_id"]))
		//$res["child"] = $this->modDailyinventory->getChild($param)->num_rows();

		echo json_encode($res);
	}

	public function checkDailyinventoryExists()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$count = $this->modDailyinventory->getAll($param)->num_rows();
		echo $count;
	}

	public function saveDailyinventory()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$this->load->model('modDailyinventorydetail', "", TRUE);
		$this->load->model('modKit', "", TRUE);
		$this->load->model('modUser', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$dailyinvparam = array();
		$dailyinvparam['period'] = date('Y-m-d', strtotime($param['perioddate']));
		$dailyinvparam['branch_id'] = $_SESSION['rgc_branch_id'];
		$dailyinvparam['user_id'] = $_SESSION['rgc_id'];

		$di_result = $this->modDailyinventory->insert($dailyinvparam);

		$err = 0;
		if ($di_result['success'])
			foreach ($param["detail"] as $ind => $row) {
				$row['period_id'] = $di_result['id'];
				$didetail_result = $this->modDailyinventorydetail->insert($row);
				if (!$didetail_result['success'])
					$err++;
			}

		$userparam = array();
		$userparam['id'] = $_SESSION['rgc_id'];
		$userdata = $this->modUser->getAll($userparam)->row_array();
		array_push($di_result, $userdata);

		if ($err == 0) {
			echo json_encode($di_result);
		}
	}

	public function updateDailyinventory()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$this->load->model('modDailyinventorydetail', "", TRUE);
		$param = $this->input->post(NULL, "true");

		if (isset($param["detail"]))
			foreach ($param["detail"] as $ind => $row) {
				if ($row["mode"] == "new") {
					$row["rawmat_id"] = $row["id"];
					unset($row["id"]);
					$this->modDailyinventorydetail->insert($row);
				}
				if ($row["mode"] == "update") {
					$this->modDailyinventorydetail->update($row);
				}
			}

		$param['daily_inv']['period'] = date('Y-m-d', strtotime($param['daily_inv']['period']));

		$result = $this->modDailyinventory->update($param['daily_inv']);
		echo json_encode($result);
	}

	public function approve()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$result = $this->modDailyinventory->update($param);
		echo json_encode($result);
	}

	public function getKit()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$this->load->model('modKit', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$result = $this->modKit->getAll($param)->result_array();
		echo json_encode($result);
	}

	public function delete()
	{
		$param = $this->input->post(NULL, TRUE);
		$this->load->model('modDailyinventory', "", TRUE);

		$res = $this->modDailyinventory->delete($param);
		echo json_encode($res);
	}

	public function uploadmasterlist()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$filedata = isset($_FILES["csvfile"]) ? $this->parse_data($_FILES["csvfile"]) : null;

		$Dailyinventorymaster = $this->modDailyinventory->getAll($param)->result_array();

		$Dailyinventorydata = [];
		foreach ($Dailyinventorymaster as $ind => $row) {
			$Dailyinventorydata[$row["id"]] = $row;
		}

		$changes = [];
		foreach ($filedata['Dailyinventorylist'] as $ind => $row) {
			$action = "";
			if (array_key_exists($ind, $Dailyinventorydata)) {
				$db_data = $Dailyinventorydata[$row["Dailyinventory_code"]];
				if (strtoupper($row["description"]) !== strtoupper($db_data["description"]) || $row["price"] !== $db_data["price"]) {

					$changes[$row["Dailyinventory_code"]] = array(
						'old_data' => array(
							'id' => $db_data["id"],
							'description' => $db_data["description"],
							'price' => $db_data["price"],
						),
						'new_data' => array(
							'id' => $row["Dailyinventory_code"],
							'description' => $row["description"],
							'price' => $row["price"],
						),
						'action' => 'UPDATE'
					);
				}
			} else {
				$changes[$row["Dailyinventory_code"]] = array(
					'old_data' => array(
						'id' => '',
						'description' => '',
						'price' => 0,
					),
					'new_data' => array(
						'id' => $row["Dailyinventory_code"],
						'description' => $row["description"],
						'price' => $row["price"],
					),
					'action' => 'INSERT'
				);
			}
		};

		echo json_encode($changes);
	}

	public function applychanges()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$this->load->model('modBranchPrice', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$param = json_decode($param['data'], true);
		$err = 0;
		foreach ($param as $ind => $row) {
			$res = "";
			if ($row["action"] == "INSERT") {
				$row['data']['uom'] = '1';
				$res = $this->modBranchPrice->insert($row['data']);
			} else if ($row["action"] == "UPDATE") {
				$res = $this->modBranchPrice->update($row['data']);
			}
			if (!$res["success"]) {
				$err++;
			}
		}

		if ($err > 0) {
			print_r("error");
		} else {
			print_r("success");
		}
	}

	public function parse_data($path)
	{

		if (!is_null($path)) {
			$file = fopen($path['tmp_name'], "r");
			$row_data = array();
			while (!feof($file)) {
				$csv = fgetcsv($file);

				if (isset($csv[0])) {
					if ($csv[0] != "id") {
						$row_data[$csv[0]]["Dailyinventory_code"] = $csv[0];
						$row_data[$csv[0]]["description"] = $csv[1];
						$row_data[$csv[0]]["price"] = $csv[2];
					}
				}
			}
			fclose($file);

			$data = array(
				"Dailyinventorylist" => $row_data,
			);
			return $data;
		}
	}

	public function exportdata()
	{
		$this->load->model('modDailyinventory', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$Dailyinventorys = $this->modDailyinventory->getAll($param)->result_array();

		$columnHeader = '';
		$columnHeader = "CODE,DESCRIPTION,PRICE";
		$setData = '';

		foreach ($Dailyinventorys as $ind => $row) {
			$value = $row['id'] . ',' . $row['description'] . ',' . $row['price'];
			$setData .= trim($value) . "\n";
		}

		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=Dailyinventory_data.csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo ucwords($columnHeader) . "\n" . $setData . "\n";
	}
}
