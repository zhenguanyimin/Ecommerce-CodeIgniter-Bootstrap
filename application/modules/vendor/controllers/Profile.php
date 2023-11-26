<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Profile extends VENDOR_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Vendorprofile_model');
        $visit_history = array();
        $visit_history['remote_addr'] = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'';
        $visit_history['request_uri'] = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']:'';
        $visit_history['remote_location'] = $this->ip_address($visit_history['remote_addr']);
        $visit_history['http_referer'] = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:'';
        $visit_history['user_name'] = $this->vendor_name? $this->vendor_name:'';
        $visit_history['email'] = '';
        $this->Public_model->setVisitHistory($visit_history);          
    }

     /**
     *  调用淘宝API根据IP查询地址
     */
    public function ip_address($ip)
    {
        $url = file_get_contents("http://whois.pconline.com.cn/ipJson.jsp?ip=$ip&json=true");
        $UTF8_RESP= iconv("GBK", "UTF-8", $url);         
        $res1 = json_decode($UTF8_RESP,true);
        $data =$res1;       
        if ($data) {
            return array_key_exists('addr', $data)? $data['addr']: 'unknown';
        } else {
            return 'unknown';
        }
    }
    
    public function index()
    {

        $data = array();
        $head = array();
        $head['title'] = lang('vendor_profile');
        $head['description'] = lang('vendor_profile');
        $head['keywords'] = '';
        if (isset($_POST['setVendorInfo'])) {
            $errors = $this->vendorInfoValidate($_POST);
            if (!empty($errors)) {
                $this->session->set_flashdata('submit_error', $errors);
            } else {
                $_POST['vendor_id'] = $this->vendor_id;
                $result = $this->Vendorprofile_model->updateVendorInfo($_POST);
                if ($result === true) {
                    $result_msg = lang('vendor_info_updated');
                } else {
                    $result_msg = lang('vendor_info_update_err');
                }
                $this->session->set_flashdata('result_publish', $result_msg);               
            }            
        }        
        $data['vendorInfo'] = $this->Vendorprofile_model->getVendorInfoFromId($this->vendor_id);
        $this->load->view('_parts/header', $head);
        $this->load->view('profile', $data);
        $this->load->view('_parts/footer');
    }

    private function vendorInfoValidate($post)
    {
        $errors = array();
        if (mb_strlen(trim($post['vendor_alipay_account'])) == 0) {
            $errors[] = lang('vendor_alipay_account_empty');
        }
        if (mb_strlen(trim($post['vendor_real_name'])) == 0) {
            $errors[] = lang('vendor_real_name_empty');
        }
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang('invalid_email');
        }
        $post['vendor_phone'] = preg_replace("/[^0-9]/", '', $post['vendor_phone']);
        if (mb_strlen(trim($post['vendor_phone'])) == 0) {
            $errors[] = lang('invalid_phone');
        }
        if (mb_strlen(trim($post['vendor_IDCard'])) == 0) {
            $errors[] = lang('vendor_IDCard_empty');
        }
        if (mb_strlen(trim($post['vendor_weixin'])) == 0) {
            $errors[] = lang('vendor_weixin_empty');
        }
        return $errors;
    }
    
    public function logout()
    {
        unset($_SESSION['logged_vendor']);
        delete_cookie('logged_vendor');
        redirect(LANG_URL . '/vendor/login');
    }
    
    public function logoff()
    {
        $this->Vendorprofile_model->updateVendorStatus($this->vendor_id, 2);
        unset($_SESSION['logged_vendor']);
        delete_cookie('logged_vendor');
        redirect(LANG_URL . '/vendor/login');
    }
}
