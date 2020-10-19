<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {
	function __construct(){
		$this->userTbl		= 'users';
		$this->planTbl	= 'plans';
		$this->subscripTbl	= 'user_subscriptions';
	}
	
	/*
     * Update user data to the database
     * @param data array
     * @param id specific user
     */
    public function updateUser($data, $id){
		$update = $this->db->update($this->userTbl, $data, array('id' => $id));
		return $update?true:false;
    }
	
	/*
     * Fetch order data from the database
     * @param id returns a single record
     */
    public function getSubscription($id){
        $this->db->select('s.*, p.name as plan_name, p.price as plan_price, p.currency as plan_price_currency, p.interval');
        $this->db->from($this->subscripTbl.' as s');
		$this->db->join($this->planTbl.' as p', 'p.id = s.plan_id', 'left');
        $this->db->where('s.id', $id);
        $query  = $this->db->get();
        return ($query->num_rows() > 0)?$query->row_array():false;
    }
    
    /*
     * Insert subscription data in the database
     * @param data array
     */
    public function insertSubscription($data){
        $insert = $this->db->insert($this->subscripTbl,$data);
        return $insert?$this->db->insert_id():false;
    }
	
	/*
     * Fetch plans data from the database
     * @param id returns a single record if specified, otherwise all records
     */
    public function getPlans($id = ''){
        $this->db->select('*');
        $this->db->from($this->planTbl);
        if($id){
            $this->db->where('id', $id);
            $query  = $this->db->get();
            $result = ($query->num_rows() > 0)?$query->row_array():array();
        }else{
            $this->db->order_by('id', 'asc');
            $query  = $this->db->get();
            $result = ($query->num_rows() > 0)?$query->result_array():array();
        }
        
        // return fetched data
        return !empty($result)?$result:false;
    }
}