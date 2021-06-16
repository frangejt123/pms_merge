<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {
	public function index()
	{
		
		if(isset($_SESSION["rgc_username"])){
           $data["poscount"] = $_SESSION["rgc_poscount"];
	      $this->load->view('product');
	    }else{
	      $this->load->view('login');
	    }
	}

	public function getAll(){
		$this->load->model('modProduct', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $res = $this->modProduct->getAll($param)->result_array();

        echo json_encode($res);
	}

	public function getParent(){
		$this->load->model('modKit', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $res["product"] = $this->modKit->getParent($param)->result_array();
        //$res["uom"] = $this->modUom->getAll($param)->result_array();
        //if(isset($param["product_id"]))
        	//$res["child"] = $this->modProduct->getChild($param)->num_rows();

        echo json_encode($res);
	}

	public function checkProductExists(){
		$this->load->model('modProduct', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $count = $this->modProduct->getAll($param)->num_rows();
        echo $count;
	}

	public function saveProduct(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modKit', "", TRUE);
        $param = $this->input->post(NULL, "true");

		$result = $this->modProduct->insert($param);

        if(isset($param["product_kit"]))
			if(count($param["product_kit"]) > 0){
				foreach($param["product_kit"] as $ind => $row){
					$row["parent_id"] = $ind;
					$row["product_id"] = $param["id"];
					$this->modKit->insert($row);
				}
			}

        echo json_encode($result);
	}

	public function updateProduct(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modKit', "", TRUE);
        $param = $this->input->post(NULL, "true");

		if(isset($param["product_kit"]))
			if(count($param["product_kit"]) > 0){
				foreach($param["product_kit"] as $ind => $row){
					if($row["mode"] == "new"){
						unset($row["id"]);
						$row["parent_id"] = $param["id"];
						$this->modKit->insert($row);
					}
					if($row["mode"] == "edited"){
						$this->modKit->update($row);
					}
					if($row["mode"] == "deleted"){
						$this->modKit->delete($row);
					}
				}
			}

        $result = $this->modProduct->update($param);

        echo json_encode($result);
	}

	public function getKit(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modKit', "", TRUE);
		$param = $this->input->post(NULL, "true");

		$result = $this->modKit->getAll($param)->result_array();
		echo json_encode($result);
	}

	public function delete(){
	    $param = $this->input->post(NULL, TRUE);
	    $this->load->model('modProduct', "", TRUE);

	    $res = $this->modProduct->delete($param);
	    echo json_encode($res);
	}

	public function uploadmasterlist(){
	    $this->load->model('modProduct', "", TRUE);
		$param = $this->input->post(NULL, "true");
        $filedata = isset($_FILES["csvfile"]) ? $this->parse_data($_FILES["csvfile"]) : null;

		$productmaster = $this->modProduct->getAll($param)->result_array();
		
		$productdata = [];
		foreach($productmaster as $ind => $row){
			$productdata[$row["id"]] = $row;
		}

		$changes = [];
		foreach($filedata['productlist'] as $ind => $row){
			
			$action = "";
			if(array_key_exists($ind, $productdata)){
				$db_data = $productdata[$row["product_code"]];
				if(strtoupper($row["description"]) !== strtoupper($db_data["description"]) || $row["price"] !== $db_data["price"]){

						$changes[$row["product_code"]] = array(
							'old_data' => array(
								'id' => $db_data["id"],
								'description' => $db_data["description"],
								'price' => $db_data["price"],
							),
							'new_data' => array(
								'id' => $row["product_code"],
								'description' => $row["description"],
								'price' => $row["price"],
							),
							'action' => 'UPDATE'
						);
				} 
			} else {
				$changes[$row["product_code"]] = array(
					'old_data' => array(
						'id' => '',
						'description' => '',
						'price' => 0,
					),
					'new_data' => array(
						'id' => $row["product_code"],
						'description' => $row["description"],
						'price' => $row["price"],
					),
					'action' => 'INSERT'
				);
			}
		};

	    echo json_encode($changes);
	}

	public function applychanges(){
		$this->load->model('modProduct', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$err = 0;
		foreach($param as $ind => $row){
			$res;
			if($row["action"] == "INSERT"){
				$row['data']['uom'] = '1';
				$res = $this->modProduct->insert($row['data']);
			}else if($row["action"] == "UPDATE"){
				$res = $this->modProduct->update($row['data']);
			}
			if(!$res["success"]){
				$err++;
			}
		}

		if($err > 0){
			print_r("error");
		}else{
			print_r("success");
		}
	}

	public function parse_data($path){

        if(!is_null($path)){
            $file = fopen($path['tmp_name'], "r");
            $row_data = array();
            while (!feof($file)) {
                $csv = fgetcsv($file);
				
				if(isset($csv[0])){
					if ($csv[0] != "id") {
						$row_data[$csv[0]]["product_code"] = $csv[0];
						$row_data[$csv[0]]["description"] = $csv[1];
						$row_data[$csv[0]]["price"] = $csv[3];
					}
				}
				
            }
			fclose($file);

            $data = array(
                "productlist" => $row_data,
            );
            return $data;
        }
	}

	public function exportdata(){
		$this->load->model('modProduct', "", TRUE);
        $param = $this->input->post(NULL, "true");

        $products = $this->modProduct->getAll($param)->result_array();

		$columnHeader = '';  
		$columnHeader = "CODE,DESCRIPTION,PRICE"; 
		$setData = '';

		foreach($products as $ind => $row){
			$value = $row['id'] . ',' . $row['description'] . ',' . $row['price'] ; 
			$setData .= trim($value) . "\n";  
		}

		header("Content-type: application/octet-stream");  
		header("Content-Disposition: attachment; filename=product_data.csv");  
		header("Pragma: no-cache");  
		header("Expires: 0");  
		
		echo ucwords($columnHeader) . "\n" . $setData . "\n";  
	}
}
