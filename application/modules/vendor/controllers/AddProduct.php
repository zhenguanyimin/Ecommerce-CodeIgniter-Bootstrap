<?php

/*
 * @Author:    Kiril Kirkov
 *  Gitgub:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AddProduct extends VENDOR_Controller
{
    private $orderId;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            'Products_model',
            'admin/Languages_model',
            'admin/Categories_model',
            'admin/Home_admin_model',
            'admin/Brands_model'
        ));
    }

    public function index($id = 0)
    {
        $trans_load = null;
        if ($id > 0 && $_POST == null) {
            $_POST = $this->Products_model->getOneProduct($id, $this->vendor_id);
            $_POST['grade_id'] = $_POST['grade'];
            $trans_load = $this->Products_model->getTranslations($id);
        }
        if (isset($_POST['setProduct'])) {
            log_message("debug", "is true?".($this->Products_model->productsCount($this->vendor_id)));
            //是否已缴纳商户保证金判断
            if($this->Home_admin_model->getValueStore('vendorBond') > 0 && $this->Vendorprofile_model->getVendorStatus($this->vendor_id) == 1 
                    && $this->Public_model->getBondPayStatus($this->vendor_id) == 0 && $this->Products_model->productsCount($this->vendor_id) > 2){ //需缴纳商户保证金
                $order_info = [
                    "name" => $this->vendor_name,
                    "email" => $_SESSION['logged_vendor'],
                    "phone" => "13988889999",
                    "address" => "商户诚信保证金",
                    "city" => "商户诚信保证金",
                    "post_code" => "123456",
                    "notes" => "商户诚信保证金",
                    "user_id" => $this->vendor_id,
                    "id" => [],
                    'referrer' => "",
                    'clean_referrer' => "",
                    'payment_type' => "alipay",
		    'paypal_status' => "",
		    'alipay_status' => "",
                    'discountCode' => "",
                    'date' => time(),
                    'final_amount' => number_format( $this->Home_admin_model->getValueStore('vendorBond'), 6),
                    'payAmount' => number_format( $this->Home_admin_model->getValueStore('vendorBond'), 6),                    
                    'vendor_share' => number_format( 0.0, 6),
                    "commission" => number_format( 0.0, 6),
                    'shipping_amount' => 0,                    
                    "discountAmount" => number_format( 0.0, 6),                     
                    'order_source' => "20",
                ];
                $order_info["productInfo"]["vendor_id"] = $this->vendor_id; 
                $_SESSION["pay_bond_data"] = $order_info;
                redirect(LANG_URL . '/checkout');
            }
            $_POST['image'] = $this->uploadImage();
            $_POST['vendor_id'] = $this->vendor_id;
            $result = $this->Products_model->setProduct($_POST, $id);
            if ($result === true) {
                $result_msg = lang('vendor_product_published');
            } else {
                $result_msg = lang('vendor_product_publish_err');
            }
            $this->session->set_flashdata('result_publish', $result_msg);
            redirect(LANG_URL . '/vendor/products');
        }
        $data = array();
        $head = array();
        $head['title'] = lang('vendor_add_product');
        $head['description'] = lang('vendor_add_product');
        $head['keywords'] = '';
        $data['languages'] = $this->Languages_model->getLanguages();
        $data['shop_categories'] = $this->Categories_model->getShopCategories();
        $data['otherImgs'] = $this->loadOthersImages();
        $data['showBrands'] = $this->Home_admin_model->getValueStore('showBrands');
        $data['productGrades'] = $this->Public_model->getProductGrades();
        $data["vendor_id"] = $this->vendor_id;
        $data["vendor_name"] = $this->vendor_name;
        if($data['showBrands'] == 1) {
            $data['brands'] = $this->Brands_model->getBrands();
        }
        $data['trans_load'] = $trans_load;
        $this->load->view('_parts/header', $head);
        $this->load->view('add_product', $data);
        $this->load->view('_parts/footer');
    }    
    
    /*
     * Send notifications to users that have nofify=1 in /admin/adminusers
     */

    private function sendNotifications()
    {
        $users = $this->Public_model->getNotifyUsers();
        $myDomain = $this->config->item('base_url');
        if (!empty($users)) {
            $this->sendmail->clearAddresses();
            foreach ($users as $user) {
                $this->sendmail->sendTo($user, 'Admin', 'New order in ' . $myDomain, 'Hello, you have new order. Can check it in /admin/orders');
            }
        }
    }
    
    private function uploadImage()
    {
        $config['upload_path'] = './attachments/shop_images/';
        $config['allowed_types'] = $this->allowed_img_types;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('userfile')) {
            log_message('error', 'Image Upload Error: ' . $this->upload->display_errors());
        }
        $img = $this->upload->data();
        return $img['file_name'];
    }

    /*
     * called from ajax
     */

    public function do_upload_others_images()
    {
        if ($this->input->is_ajax_request()) {
            $upath = '.' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'shop_images' . DIRECTORY_SEPARATOR . $_POST['folder'] . DIRECTORY_SEPARATOR;
            if (!file_exists($upath)) {
                mkdir($upath, 0777);
            }

            $this->load->library('upload');

            $files = $_FILES;
            $cpt = count($_FILES['others']['name']);
            for ($i = 0; $i < $cpt; $i++) {
                unset($_FILES);
                $_FILES['others']['name'] = $files['others']['name'][$i];
                $_FILES['others']['type'] = $files['others']['type'][$i];
                $_FILES['others']['tmp_name'] = $files['others']['tmp_name'][$i];
                $_FILES['others']['error'] = $files['others']['error'][$i];
                $_FILES['others']['size'] = $files['others']['size'][$i];

                $this->upload->initialize(array(
                    'upload_path' => $upath,
                    'allowed_types' => $this->allowed_img_types
                ));
                $this->upload->do_upload('others');
            }
        }
    }

    public function loadOthersImages()
    {
        $output = '';
        if (isset($_POST['folder']) && $_POST['folder'] != null) {
            $dir = 'attachments' . DIRECTORY_SEPARATOR . 'shop_images' . DIRECTORY_SEPARATOR . $_POST['folder'] . DIRECTORY_SEPARATOR;
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    $i = 0;
                    while (($file = readdir($dh)) !== false) {
                        if (is_file($dir . $file)) {
                            $output .= '
                                <div class="other-img" id="image-container-' . $i . '">
                                    <img src="' . base_url('attachments/shop_images/' . htmlspecialchars($_POST['folder']) . '/' . $file) . '" style="width:100px; height: 100px;">
                                    <a href="javascript:void(0);" onclick="removeSecondaryProductImage(\'' . $file . '\', \'' . htmlspecialchars($_POST['folder']) . '\', ' . $i . ')">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                </div>
                               ';
                        }
                        $i++;
                    }
                    closedir($dh);
                }
            }
        }
        if ($this->input->is_ajax_request()) {
            echo $output;
        } else {
            return $output;
        }
    }

    /*
     * called from ajax
     */

    public function removeSecondaryImage()
    {
        if ($this->input->is_ajax_request()) {
            $img = '.' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'shop_images' . DIRECTORY_SEPARATOR . '' . $_POST['folder'] . DIRECTORY_SEPARATOR . $_POST['image'];
            unlink($img);
        }
    }

}
