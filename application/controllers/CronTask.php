<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CronTask extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /*
     * 运行定时任务，比如检查买家、卖家是否离线
     * 
     */
    public function runCronTask()
    {
        log_message("debug", "runCronTask");
        $this->handleUserOffline();
        $this->handleVendorOffline();
    }
    
    public function handleUserOffline()
    {
        log_message("debug", "handleUserOffline");
        $this->Public_model->handleUserOffline();
    }

    public function handleVendorOffline()
    {
        log_message("debug", "handleVendorOffline");
        $this->Public_model->handleVendorOffline();
    }
    
    public function handleVendorFundSettle()
    {
        log_message("debug", "handleVendorFundSettle");
        redirect(LANG_URL . '/checkout/vendorFundSettle');
    }    
}

