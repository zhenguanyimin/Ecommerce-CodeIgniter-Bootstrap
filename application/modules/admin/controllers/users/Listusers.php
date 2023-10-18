<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Listusers extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
    }

    public function index()
    {
        $this->login_check();

        $data = array();
        $head = array();
        $head['title'] = 'Administration - Admin Users';
        $head['description'] = '!';
        $head['keywords'] = '';
        $id = null;
        if(isset($_GET['id'])){
            $id = $_GET['id'];
        }
        $data['users'] = $this->Users_model->getUsers($id);
        $data['controller'] = $this;
        
        $this->load->view('_parts/header', $head);
        $this->load->view('users/listUsers', $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to Admin Users List');
    }

    public function getUsersOrders($id)
    {
        return $this->Users_model->getUsersOrders($id);
    }
}
