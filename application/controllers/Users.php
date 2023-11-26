<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{

    private $registerErrors = array();
    private $user_id;
    private $num_rows = 5;
    
    CONST USER_STATUS_OFFLINE = 0;    
    CONST USER_STATUS_ONLINE = 1;
    
    const QueryOrderTypeDesc = array(
        -1 => "所有订单" ,
        10 => "待发货" ,
        20 => "待收货" ,
        30 => "未支付" ,
        40 => "已完成" ,                        
        50 => "已取消" ,
        60 => "售后管理",     
    );
        
    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
        $this->load->model(array('vendor/Orders_model'));
        $visit_history = array();
        $visit_history['remote_addr'] = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'';
        $visit_history['request_uri'] = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']:'';
        $visit_history['remote_location'] = $this->ip_address($visit_history['remote_addr']);
        $visit_history['http_referer'] = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:'';
        $visit_history['user_name'] = $this->user_id? $this->user_id:'';
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
        show_404();
    }

    public function login()
    {        
        if (isset($_POST['login'])) {                        
            $result = $this->Public_model->checkPublicUserIsValid($_POST);
            if ($result !== false) {
                $_SESSION['logged_user'] = $result; //id of user
                $_POST['id'] = $result;
                $_POST['online_status'] = self::USER_STATUS_ONLINE;
                $_POST['login_at'] = time();
                $this->Public_model->updateUserLoginStatus($_POST);
                redirect(LANG_URL . '/');
            } else {
                $this->session->set_flashdata('userError', lang('wrong_user'));
            }
        }
        $head = array();
        $data = array();
        $head['title'] = lang('login');
        $head['description'] = lang('login');
        $head['keywords'] = str_replace(" ", ",", $head['title']);
        $this->render('login', $head, $data);
    }

    public function register()
    {
        if (isset($_POST['signup'])) {
            $result = $this->registerValidate();
            if ($result == false) {
                $this->session->set_flashdata('userError', $this->registerErrors);
                redirect(LANG_URL . '/register');
            } else {
                $_SESSION['logged_user'] = $this->user_id; //id of user
                $_POST['id'] = $this->user_id;
                $_POST['online_status'] = self::USER_STATUS_ONLINE;
                $_POST['login_at'] = time();
                $this->Public_model->updateUserLoginStatus($_POST);                
                redirect(LANG_URL . '/');
            }
        }
        $head = array();
        $data = array();
        $head['title'] = lang('register');
        $head['description'] = lang('register');
        $head['keywords'] = str_replace(" ", ",", $head['title']);
        $this->render('signup', $head, $data);
    }

    public function myaccount($page = 0)
    {
	if(!isset($_SESSION['logged_user'])){
	    $this->session->set_flashdata('userErorr', 'you are login out,please login agian');
            $_POST['id'] = $this->user_id;
            $_POST['online_status'] = self::USER_STATUS_OFFLINE;
            $_POST['logout_at'] = time();
            $this->Public_model->updateUserLogoutStatus($_POST);            
	    redirect(LANG_URL . '/login');
	}        
        if (isset($_POST['update'])) {
            $_POST['id'] = $_SESSION['logged_user'];
            $count_emails = $this->Public_model->countPublicUsersWithEmail($_POST['email'], $_POST['id']);
            if ($count_emails == 0) {
                $this->Public_model->updateProfile($_POST);
            }
            redirect(LANG_URL . '/myaccount');
        }
        $head = array();
        $data = array();
        $head['title'] = lang('my_acc');
        $head['description'] = lang('my_acc');
        $head['keywords'] = str_replace(" ", ",", $head['title']);
        $data['userInfo'] = $this->Public_model->getUserProfileInfo($_SESSION['logged_user']);    
        $data['links_pagination'] = pagination('myaccount', 5, 5, 2);
        $this->render('user', $head, $data);
    }

    public function userOrders($page = 0)
    {
	if(!isset($_SESSION['logged_user'])){
	    $this->session->set_flashdata('userErorr', 'you are login out,please login agian');
	    redirect(LANG_URL . '/login');
	}        

        $head = array();
        $data = array();
        $queryOrderType = isset($_GET["queryOrderType"])? $_GET["queryOrderType"]:"-1";
        $head['title'] = self::QueryOrderTypeDesc[$queryOrderType];
        $head['description'] = self::QueryOrderTypeDesc[$queryOrderType];
        $head['keywords'] = str_replace(" ", ",", $head['title']);
        $data['userInfo'] = $this->Public_model->getUserProfileInfo($_SESSION['logged_user']);
//        $rowscount = $this->Public_model->getUserOrdersHistoryCount($_SESSION['logged_user'], $queryOrderType);
        $data['orders_history'] = $this->Public_model->getUserOrdersHistory($_SESSION['logged_user'], $_GET, $page);
        $url = 'userorders?queryOrderType='.$queryOrderType;
//        $data['links_pagination'] = pagination($url, count($data['orders_history']), $this->num_rows, 2);
        $data['links_pagination'] = pagination($url, 5, 5, 2);
        $this->render('user_orders', $head, $data);
    }
    
    public function logout()
    {
        $_POST['id'] = $_SESSION['logged_user'];
        $_POST['online_status'] = self::USER_STATUS_OFFLINE;
        $_POST['logout_at'] = time();
        $this->Public_model->updateUserLogoutStatus($_POST);
        
        unset($_SESSION['logged_user']);    
        redirect(LANG_URL);
    }

    private function registerValidate()
    {
        $errors = array();
        if (mb_strlen(trim($_POST['name'])) == 0) {
            $errors[] = lang('please_enter_name');
        }
        if (mb_strlen(trim($_POST['phone'])) == 0) {
            $errors[] = lang('please_enter_phone');
        }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang('invalid_email');
        }
        if (mb_strlen(trim($_POST['pass'])) == 0) {
            $errors[] = lang('enter_password');
        }
        if (mb_strlen(trim($_POST['pass_repeat'])) == 0) {
            $errors[] = lang('repeat_password');
        }
        if ($_POST['pass'] != $_POST['pass_repeat']) {
            $errors[] = lang('passwords_dont_match');
        }

        $count_emails = $this->Public_model->countPublicUsersWithEmail($_POST['email']);
        if ($count_emails > 0) {
            $errors[] = lang('user_email_is_taken');
        }
        if (!empty($errors)) {
            $this->registerErrors = $errors;
            return false;
        }
        $this->user_id = $this->Public_model->registerUser($_POST);
        return true;
    }

}
