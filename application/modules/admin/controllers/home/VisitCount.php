<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class VisitCount extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->login_check();

        $data = array();
        $head = array();
        $head['title'] = 'Administration - Visit Count';
        $head['description'] = '!';
        $head['keywords'] = '';
        $id = null;
        if(isset($_GET['id'])){
            $id = $_GET['id'];
        }
        $data['users'] = $this->Public_model->getVisitHistory();
        $data['controller'] = $this;
        
        $this->load->view('_parts/header', $head);
        $this->load->view('home/visitCount', $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to Admin Visit Count');
    }
}
