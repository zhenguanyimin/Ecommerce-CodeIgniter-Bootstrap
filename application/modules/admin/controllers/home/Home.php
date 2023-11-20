<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Home extends ADMIN_Controller
{
    CONST USER_STATUS_NORMAL = 1;
    CONST USER_STATUS_INVALID = 2;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Orders_model', 'History_model', 'vendor/Vendorprofile_model'));
    }

    public function index()
    {
        $this->login_check();
        $data = array();
        $head = array();
        $head['title'] = 'Administration - Home';
        $head['description'] = '';
        $head['keywords'] = '';
        $data['newOrdersCount'] = $this->Orders_model->ordersCount(true);
        $data['total_amount'] = $this->Orders_model->getTotalAmount();
        $data['total_vendor_share'] = $this->Orders_model->getTotalVendorShare();
        $data['total_commission'] = $this->Orders_model->getTotalCommission();        
        $data['lowQuantity'] = $this->Home_admin_model->countLowQuantityProducts();
        $data['lastSubscribed'] = $this->Home_admin_model->lastSubscribedEmailsCount();
        $data['activity'] = $this->History_model->getHistory(10, 0);
        $data['mostSold'] = $this->Home_admin_model->getMostSoldProducts();
        $data['byReferral'] = $this->Home_admin_model->getReferralOrders();
        $data['ordersByPaymentType'] = $this->Home_admin_model->getOrdersByPaymentType();
        $data['ordersByMonth'] = $this->Home_admin_model->getOrdersByMonth();
        $data['total_login_users'] = $this->Vendorprofile_model->getLoginUsers();
        $data['total_login_vendors'] = $this->Vendorprofile_model->getLoginVendors();
        $data['total_users'] = $this->Vendorprofile_model->getAllUsers();
        $data['total_vendors'] = $this->Vendorprofile_model->getAllVendors();
        $data['total_valid_users'] = $this->Vendorprofile_model->getTotalUsers(self::USER_STATUS_NORMAL);
        $data['total_valid_vendors'] = $this->Vendorprofile_model->getTotalVendors(self::USER_STATUS_NORMAL);        
        $data['payed_orders'] = $this->Orders_model->getPayedOrdersCount();
        $data['unpay_orders'] = $this->Orders_model->getUnPayOrdersCount();
        $data['all_orders'] = $this->Orders_model->getOrdersCount();
        $data['user_visit_count_by_day'] = $this->Public_model->getUserVisitHistoryCountByDay();
        $data['user_visit_count_by_month'] = $this->Public_model->getUserVisitHistoryCountByMonth();
        $data['vendor_visit_count_by_day'] = $this->Public_model->getVendorVisitHistoryCountByDay();
        $data['vendor_visit_count_by_month'] = $this->Public_model->getVendorVisitHistoryCountByMonth();
        $this->load->view('_parts/header', $head);
        $this->load->view('home/home', $data);
        $this->load->view('_parts/footer');
        $this->saveHistory('Go to home page');
    }

    /*
     * Called from ajax
     */

    public function changePass()
    {
        $this->login_check();
        $result = $this->Home_admin_model->changePass($_POST['new_pass'], $this->username);
        if ($result == true) {
            echo 1;
        } else {
            echo 0;
        }
        $this->saveHistory('Password change for user: ' . $this->username);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('admin');
    }

}
