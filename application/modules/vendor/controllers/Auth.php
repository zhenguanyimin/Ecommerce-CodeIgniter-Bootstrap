<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Auth extends VENDOR_Controller
{

    private $registerErrors = array();
    CONST VENDOR_STATUS_OFFLINE = 0;    
    CONST VENDOR_STATUS_ONLINE = 1;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Auth_model', 'Vendorprofile_model'));
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
        $url = @file_get_contents("http://whois.pconline.com.cn/ipJson.jsp?ip=$ip&json=true");
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
        show_404();
    }

    public function login()
    {
        $data = array();
        $head = array();
        $head['title'] = lang('vendor_login');
        $head['description'] = lang('vendor_login');
        $head['keywords'] = '';

        if (isset($_POST['login'])) {
            $result = $this->verifyVendorLogin();
            if ($result == false) {
                $this->session->set_flashdata('login_error', lang('login_vendor_error'));
                redirect(LANG_URL . '/vendor/login');
            } else {
                $remember_me = false;
                if (isset($_POST['remember_me'])) {
                    $remember_me = true;
                }
                $this->setLoginSession($_POST['u_email'], $remember_me);
                $_POST['email'] = $_POST['u_email'];
                $_POST['online_status'] = self::VENDOR_STATUS_ONLINE;
                $_POST['login_at'] = time();
                $this->Vendorprofile_model->updateVendorLoginStatus($_POST);
                
                $result = $this->checkVendorInfoComplete();
                if(!$result){
                    $this->session->set_flashdata('vendor_info_warning', lang('vendor_info_warning'));
                    redirect(LANG_URL . '/vendor/profile');
                }
                else{
                    redirect(LANG_URL . '/vendor/me');
                }                
            }
        }
        $this->load->view('_parts/header_auth', $head);
        $this->load->view('auth/login', $data);
        $this->load->view('_parts/footer_auth');
    }

    private function verifyVendorLogin()
    {
        return $this->Auth_model->checkVendorExsists($_POST);
    }

    private function checkVendorInfoComplete()
    {
        $vendor = $this->Vendorprofile_model->getVendorInfoFromEmail($_POST['u_email']);
        if ($vendor != null) {
            if(time() - strtotime($vendor['created_at']) > 2*3600 && !$this->vendorInfoValidate($vendor)){
                return false;
            }
            return true;
        }
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
        if (mb_strlen(trim($post['email'])) == 0) {
            $errors[] = lang('invalid_email');
        }
        if (mb_strlen(trim($post['vendor_phone'])) == 0) {
            $errors[] = lang('invalid_phone');
        }
        if (mb_strlen(trim($post['vendor_IDCard'])) == 0) {
            $errors[] = lang('vendor_IDCard_empty');
        }
        if (mb_strlen(trim($post['vendor_weixin'])) == 0) {
            $errors[] = lang('vendor_weixin_empty');
        }
        if(!empty($errors)){
            return false;
        }
        return true;
    }
    
    public function register()
    {
        $data = array();
        $head = array();
        $head['title'] = lang('vendor_register');
        $head['description'] = lang('vendor_register');
        $head['keywords'] = '';
        if (isset($_POST['register'])) {
            $result = $this->registerVendor();
            if ($result == false) {
                $this->session->set_flashdata('error_register', $this->registerErrors);
                $this->session->set_flashdata('email', $_POST['u_email']);
                redirect(LANG_URL . '/vendor/register');
            } else {
                $this->setLoginSession($_POST['u_email'], false);
                redirect(LANG_URL . '/vendor/me');
            }
        }
        $this->load->view('_parts/header_auth', $head);
        $this->load->view('auth/register', $data);
        $this->load->view('_parts/footer_auth');
    }

    private function registerVendor()
    {
        $errors = array();
        if (mb_strlen(trim($_POST['u_password'])) == 0) {
            $errors[] = lang('please_enter_password');
        }
        if (mb_strlen(trim($_POST['u_password_repeat'])) == 0) {
            $errors[] = lang('please_repeat_password');
        }
        if ($_POST['u_password'] != $_POST['u_password_repeat']) {
            $errors[] = lang('passwords_dont_match');
        }
        if (!filter_var($_POST['u_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang('vendor_invalid_email');
        }
        $count_emails = $this->Auth_model->countVendorsWithEmail($_POST['u_email']);
        if ($count_emails > 0) {
            $errors[] = lang('vendor_email_is_taken');
        }
        if (!empty($errors)) {
            $this->registerErrors = $errors;
            return false;
        }
        $this->Auth_model->registerVendor($_POST);
        return true;
    }

    public function forgotten()
    {
        if (isset($_POST['u_email'])) {
            $vendor = $this->Vendorprofile_model->getVendorInfoFromEmail($_POST['u_email']);
            if ($vendor != null) {
                $myDomain = $this->config->item('base_url');
                $newPass = $this->Auth_model->updateVendorPassword($_POST['u_email']);
                $this->sendmail->sendTo($_POST['u_email'], 'Admin', 'New password for ' . $myDomain, 'Hello, your new password is ' . $newPass);
                $this->session->set_flashdata('login_error', lang('new_pass_sended'));
                redirect(LANG_URL . '/vendor/login');
            }
        }

        $data = array();
        $head = array();
        $head['title'] = lang('user_forgotten_page');
        $head['description'] = lang('recover_password');
        $head['keywords'] = '';

        $this->load->view('_parts/header_auth', $head);
        $this->load->view('auth/recover_pass', $data);
        $this->load->view('_parts/footer_auth');
    }

}
