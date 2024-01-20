<?php

class Auth_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function registerVendor($post)
    {    
        $this->db->trans_begin();
        $input = array(
            'email' => trim($post['u_email']),
            'password' => password_hash($post['u_password'], PASSWORD_DEFAULT)
        );
        if (!$this->db->insert('vendors', $input)) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
        
        $lastId = $this->db->insert_id();
        if (!$this->db->insert('vendors_balances', array(
                    'vendor_id' => $lastId
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
              
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }        
    }

    public function countVendorsWithEmail($email)
    {
        $this->db->where('email', $email);
        return $this->db->count_all_results('vendors');
    }
    
    public function checkVendorExsists($post)
    {
        $this->db->where('email', $post['u_email']);
        $query = $this->db->get('vendors');
        $row = $query->row_array();
        if (empty($row) || !password_verify($post['u_password'], $row['password']) || $row['vendor_status'] == 2) {
            return false;
        }
        return true;
    }

    public function updateVendorPassword($email)
    {
        $newPass = str_shuffle(bin2hex(openssl_random_pseudo_bytes(4)));
        $this->db->where('email', $email);
        if (!$this->db->update('vendors', ['password' => password_hash($newPass, PASSWORD_DEFAULT)])) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
        return $newPass;
    }

}
