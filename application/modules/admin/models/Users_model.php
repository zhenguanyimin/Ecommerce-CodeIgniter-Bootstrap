<?php

class Users_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUsers($id = null)
    {
        if($id !== null && (int)$id > 0) {
            $this->db->where('id', $id);
        }
        $query = $this->db->get('users_public');
        return $query;
    }

    public function getUsersOrders($user_id)
    {
        $this->db->from('users_public');
        $this->db->where('users_public.id', $user_id);
        $this->db->join('orders', 'orders.user_id = users_public.id');
        $query = $this->db->get();
        return $query->result_array();
    }
}
