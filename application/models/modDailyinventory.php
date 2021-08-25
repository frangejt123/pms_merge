<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModDailyinventory extends CI_Model
{

    public $NAMESPACE = "daily_inventory";
    private $TABLE = "daily_inventory",
        $FIELDS = array(
            "id" => "daily_inventory.id",
            "period" => "daily_inventory.period",
            "branch_id" => "daily_inventory.branch_id",
            "user_id" => "daily_inventory.user_id",
            "status" => "daily_inventory.status"
        );

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param)
    {
        $this->FIELDS["branch"] = "branch.branch_name";
        $this->FIELDS["userfname"] = "user.firstname";
        $this->FIELDS["userlname"] = "user.lastname";

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
        $this->db->from("daily_inventory");
        $this->db->join('branch', 'branch.branch_id = daily_inventory.branch_id');
        $this->db->join('user', 'user.id = daily_inventory.user_id');
        $this->db->order_by('daily_inventory.period', 'DESC');

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

        if ($this->db->insert('daily_inventory', $data)) {
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

        if ($this->db->update('daily_inventory', $data)) {
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

        if ($this->db->delete('daily_inventory')) {
            $result["id"] = $param["id"];
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }
}
