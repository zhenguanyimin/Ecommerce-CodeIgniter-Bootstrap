<?php

class Public_model extends CI_Model
{

    private $showOutOfStock;
    private $showInSliderProducts;
    private $multiVendor;
    private $vendorOrderId = 1233;
    // 待支付
    const PAYSTATUS_PENDING = 10;

    // 支付成功
    const PAYSTATUS_SUCCESS = 20;
    
    // 未发货
    const NOT_DELIVERED = 10;

    // 已发货
    const DELIVERED = 20;
    
    // 未收货
    const NOT_RECEIVED = 10;

    // 已收货
    const RECEIVED = 20;
    
    // 进行中
    const NORMAL = 10;

    // 已取消
    const CANCELLED = 20;

    // 待取消
    const APPLY_CANCEL = 21;

    // 已完成
    const COMPLETED = 30;
    
    //订单类型   
    const QUERY_ORDER_TYPE_ALL = -1;
    const QUERY_ORDER_TYPE_DELIVERY = 10;
    const QUERY_ORDER_TYPE_RECEIPT = 20;
    const QUERY_ORDER_TYPE_UNPAY = 30;
    const QUERY_ORDER_TYPE_COMPLETED = 40;
    const QUERY_ORDER_TYPE_CANCELED = 50;
    const QUERY_ORDER_TYPE_AFTERSALES = 60;    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->Model('Home_admin_model');
        $this->showOutOfStock = $this->Home_admin_model->getValueStore('outOfStock');
        $this->showInSliderProducts = $this->Home_admin_model->getValueStore('showInSlider');
        $this->multiVendor = $this->Home_admin_model->getValueStore('multiVendor');

        $this->load->library('encryption');
    }

    public function productsCount($big_get)
    {
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);
        if (!empty($big_get) && isset($big_get['category'])) {
            $this->getFilter($big_get);
        }
        $this->db->where('visibility', 1);
        if ($this->showOutOfStock == 0) {
            $this->db->where('quantity >', 0);
        }
        if ($this->showInSliderProducts == 0) {
            $this->db->where('in_slider', 0);
        }
        if ($this->multiVendor == 0) {
            $this->db->where('vendor_id', 0);
        }
        return $this->db->count_all_results('products');
    }

    public function getNewProducts()
    {
        $this->db->select('vendors.url as vendor_url, products.id, products.quantity, products.image, products.url, products_translations.price, products_translations.title, products_translations.old_price, grade_desc.desc, products.defect_desc, products.shop_categorie');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);
        $this->db->join('grade_desc', 'grade_desc.grade_id = products.grade', 'left');
        $this->db->where('products.in_slider', 0);
        $this->db->where('visibility', 1);
        if ($this->showOutOfStock == 0) {
            $this->db->where('quantity >', 0);
        }
        $this->db->order_by('products.id', 'desc');
        $this->db->limit(5);
        $query = $this->db->get('products');
        return $query->result_array();
    }

    public function getLastBlogs()
    {
        $this->db->limit(5);
        $this->db->join('blog_translations', 'blog_translations.for_id = blog_posts.id', 'left');
        $this->db->where('blog_translations.abbr', MY_LANGUAGE_ABBR);
        $query = $this->db->select('blog_posts.id, blog_translations.title, blog_translations.description, blog_posts.url, blog_posts.time, blog_posts.image')->get('blog_posts');
        return $query->result_array();
    }

    public function getPosts($limit, $page, $search = null, $month = null)
    {
        if ($search !== null) {
            $search = $this->db->escape_like_str($search);
            $this->db->where("(blog_translations.title LIKE '%$search%' OR blog_translations.description LIKE '%$search%')");
        }
        if ($month !== null) {
            $from = intval($month['from']);
            $to = intval($month['to']);
            $this->db->where("time BETWEEN $from AND $to");
        }
        $this->db->join('blog_translations', 'blog_translations.for_id = blog_posts.id', 'left');
        $this->db->where('blog_translations.abbr', MY_LANGUAGE_ABBR);
        $query = $this->db->select('blog_posts.id, blog_translations.title, blog_translations.description, blog_posts.url, blog_posts.time, blog_posts.image')->get('blog_posts', $limit, $page);
        return $query->result_array();
    }
    
    public function getAllExpress()
    {
        $query = $this->db->select('*')->get('express_info');
        return $query->result_array();
    }
  
    public function getProducts($limit = null, $start = null, $big_get = [], $vendor_id = false)
    {
        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }
        if (!empty($big_get) && isset($big_get['category'])) {
            $this->getFilter($big_get);
        }
        $this->db->select('vendors.url as vendor_url, products.id,products.image, products.quantity, products_translations.title, products_translations.price, products_translations.old_price, products.url, grade_desc.desc, products.defect_desc, products.shop_categorie');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->join('grade_desc', 'grade_desc.grade_id = products.grade', 'left');
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);
        $this->db->where('visibility', 1);
        if ($vendor_id !== false) {
            $this->db->where('vendor_id', $vendor_id);
        }
        if ($this->showOutOfStock == 0) {
            $this->db->where('quantity >', 0);
        }
        if ($this->showInSliderProducts == 0) {
            $this->db->where('in_slider', 0);
        }
        if ($this->multiVendor == 0) {
            $this->db->where('vendor_id', 0);
        }
        $this->db->order_by('position', 'asc');
        $query = $this->db->get('products');
        return $query->result_array();
    }

    public function getOneLanguage($myLang)
    {
        $this->db->select('*');
        $this->db->where('abbr', $myLang);
        $this->db->where('status', 1);
        $result = $this->db->get('languages');
        return $result->row_array();
    }

    private function getFilter($big_get)
    {

        if ($big_get['category'] != '') {
            (int) $big_get['category'];
            $findInIds = array();
            $findInIds[] = $big_get['category'];
            $query = $this->db->query('SELECT id FROM shop_categories WHERE sub_for = ' . $this->db->escape($big_get['category']));
            foreach ($query->result() as $row) {
                $findInIds[] = $row->id;
            }
            $this->db->where_in('products.shop_categorie', $findInIds);
        }
        if ($big_get['in_stock'] != '') {
            if ($big_get['in_stock'] == 1)
                $sign = '>';
            else
                $sign = '=';
            $this->db->where('products.quantity ' . $sign, '0');
        }
        if ($big_get['search_in_title'] != '') {
            $this->db->like('products_translations.title', $big_get['search_in_title']);
        }
        if ($big_get['search_in_body'] != '') {
            $this->db->like('products_translations.description', $big_get['search_in_body']);
        }
        if ($big_get['order_price'] != '') {
            $this->db->order_by('products_translations.price', $big_get['order_price']);
        }
        if ($big_get['order_procurement'] != '') {
            $this->db->order_by('products.procurement', $big_get['order_procurement']);
        }
        if ($big_get['order_new'] != '') {
            $this->db->order_by('products.id', $big_get['order_new']);
        } else {
            $this->db->order_by('products.id', 'DESC');
        }
        if ($big_get['quantity_more'] != '') {
            $this->db->where('products.quantity > ', $big_get['quantity_more']);
        }
        if ($big_get['quantity_more'] != '') {
            $this->db->where('products.quantity > ', $big_get['quantity_more']);
        }
        if ($big_get['brand_id'] != '') {
            $this->db->where('products.brand_id = ', $big_get['brand_id']);
        }
        if ($big_get['added_after'] != '') {
            $added_after = \DateTime::createFromFormat('d/m/Y', $big_get['added_after']);
            if($added_after) {
                $time = $added_after->getTimestamp();
                $this->db->where('products.time > ', $time);
            }
        }
        if ($big_get['added_before'] != '') {
            $added_before = \DateTime::createFromFormat('d/m/Y', $big_get['added_before']);
            if($added_before) {
                $time = $added_before->getTimestamp();
                $this->db->where('products.time < ', $time);
            }
        }
        if ($big_get['price_from'] != '') {
            $this->db->where('products_translations.price >= ', $big_get['price_from']);
        }
        if ($big_get['price_to'] != '') {
            $this->db->where('products_translations.price <= ', $big_get['price_to']);
        }
    }

    public function getShopCategories()
    {
        $this->db->select('shop_categories.sub_for, shop_categories.id, shop_categories_translations.name');
        $this->db->where('abbr', MY_LANGUAGE_ABBR);
        $this->db->order_by('position', 'asc');
        $this->db->join('shop_categories', 'shop_categories.id = shop_categories_translations.for_id', 'INNER');
        $query = $this->db->get('shop_categories_translations');
        $arr = array();
        if ($query !== false) {
            foreach ($query->result_array() as $row) {
                $arr[] = $row;
            }
        }
        return $arr;
    }

    public function getRecommendationBooks()
    {
        $this->db->select('recommendation_book.sub_for, recommendation_book.id, recommendation_book_translations.name');
        $this->db->where('abbr', MY_LANGUAGE_ABBR);
        $this->db->order_by('position', 'asc');
        $this->db->join('recommendation_book', 'recommendation_book.id = recommendation_book_translations.for_id', 'INNER');
        $query = $this->db->get('recommendation_book_translations');
        $arr = array();
        if ($query !== false) {
            foreach ($query->result_array() as $row) {
                $arr[] = $row;
            }
        }
        return $arr;
    }
    
    public function getSeo($page)
    {
        $this->db->where('page_type', $page);
        $this->db->where('abbr', MY_LANGUAGE_ABBR);
        $query = $this->db->get('seo_pages_translations');
        $arr = array();
        if ($query !== false) {
            foreach ($query->result_array() as $row) {
                $arr['title'] = $row['title'];
                $arr['description'] = $row['description'];
            }
        }
        return $arr;
    }

    public function getOneProduct($id)
    {
        $this->db->where('products.id', $id);

        $this->db->select('vendors.url as vendor_url, products.*, products_translations.title,products_translations.description, products_translations.price, products_translations.old_price, products.url, shop_categories_translations.name as categorie_name, grade_desc.desc');

        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->join('grade_desc', 'grade_desc.grade_id = products.grade', 'left');
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);

        $this->db->join('shop_categories_translations', 'shop_categories_translations.for_id = products.shop_categorie', 'inner');
        $this->db->where('shop_categories_translations.abbr', MY_LANGUAGE_ABBR);
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->where('visibility', 1);
        $query = $this->db->get('products');
        return $query->row_array();
    }

    public function getCountQuantities()
    {
        $query = $this->db->query('SELECT SUM(IF(quantity<=0,1,0)) as out_of_stock, SUM(IF(quantity>0,1,0)) as in_stock FROM products WHERE visibility = 1');
        return $query->row_array();
    }

    public function getShopItems($array_items)
    {
        $this->db->select('products.id, products.image, products.url, products.quantity, products_translations.price, products_translations.title');
        $this->db->from('products');
        if (count($array_items) > 1) {
            $i = 1;
            $where = '';
            foreach ($array_items as $id) {
                $i == 1 ? $open = '(' : $open = '';
                $i == count($array_items) ? $or = '' : $or = ' OR ';
                $where .= $open . 'products.id = ' . $id . $or;
                $i++;
            }
            $where .= ')';
            $this->db->where($where);
        } else {
            $this->db->where('products.id =', current($array_items));
        }
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'inner');
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Users for notification by email
     */

    public function getNotifyUsers()
    {
        $result = $this->db->query('SELECT email FROM users WHERE notify = 1');
        $arr = array();
        foreach ($result->result_array() as $email) {
            $arr[] = $email['email'];
        }
        return $arr;
    }

    public function setOrder($post)
    {
        $q = $this->db->query('SELECT MAX(order_id) as order_id FROM orders');
        $rr = $q->row_array();
        if ($rr['order_id'] == 0) {
            $rr['order_id'] = 1233;
        }
        $post['order_id'] = $rr['order_id'] + 1;

        $i = 0;
        $post['products'] = array();
        foreach ($post['id'] as $product) {
            $post['products'][$product] = $post['quantity'][$i];
            $i++;
        }
        unset($post['id'], $post['quantity']);
        $post['date'] = time();
        $products_to_order = [];
        if(!empty($post['products'])) {
            foreach($post['products'] as $pr_id => $pr_qua) {
                $products_to_order[] = [
                    'product_info' => $this->getOneProductForSerialize($pr_id),
                    'product_quantity' => $pr_qua
                    ];
            }
        }
        $post['products'] = serialize($products_to_order);
        $this->db->trans_begin();
        if (!$this->db->insert('orders', array(
                    'order_id' => $post['order_id'],
                    'products' => $post['products'],
                    'date' => $post['date'],
                    'referrer' => $post['referrer'],
                    'clean_referrer' => $post['clean_referrer'],
                    'payment_type' => $post['payment_type'],
		    'paypal_status' => @$post['paypal_status'],
		    'alipay_status' => @$post['alipay_status'],
                    'discount_code' => @$post['discountCode'],
                    'user_id' => $post['user_id']
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
        $lastId = $this->db->insert_id();
        if (!$this->db->insert('orders_clients', array(
                    'for_id' => $lastId,
                    'name' => $this->encryption->encrypt($post['name']),
                    'email' => $this->encryption->encrypt($post['email']),
                    'phone' => $this->encryption->encrypt($post['phone']),
                    'address' => $this->encryption->encrypt($post['address']),
                    'city' => $this->encryption->encrypt($post['city']),
                    'post_code' => $this->encryption->encrypt($post['post_code']),
                    'notes' => $this->encryption->encrypt($post['notes'])
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $post['order_id'];
        }
    }
    
    private function getOneProductForSerialize($id)
    {
        $this->db->select('vendors.name as vendor_name, vendors.id as vendor_id, products.*, products_translations.price, grade_desc.desc');
        $this->db->where('products.id', $id);
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->join('grade_desc', 'grade_desc.grade_id = products.grade', 'left');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'inner');
        $this->db->where('products_translations.abbr', MY_DEFAULT_LANGUAGE_ABBR);
        $query = $this->db->get('products');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function setVendorOrder($post)
    {
        $i = 0;
        $post['products'] = array();
        foreach ($post['id'] as $product) {
            $post['products'][$product] = $post['quantity'][$i];
            $i++;
        }

        /*
         * Loop products and check if its from vendor - save order for him
         */
        if($post["order_source"] == 20){
            $post['products'] = serialize([]);
            return $this->insertVendorOrder($post); 
        }
        else{
            foreach ($post['products'] as $product_id => $product_quantity) {
                $productInfo = $this->getOneProduct($product_id);
                if ($productInfo['vendor_id'] > 0) {
                    /*calculate commission and save*/
                    echo $productInfo['price'].$product_quantity.$_POST['discountAmount'];
                    $total_amount = $productInfo['price']*$product_quantity*1.0;
    //                $total_amount = $total_amount - $_POST['discountAmount'];
                    $commission = $total_amount*($this->Home_admin_model->getValueStore('commissonRate')/100);
                    $vendor_share = $total_amount-$commission;
                    $post["total_amount"] = number_format( $total_amount, 6);
                    $post["vendor_share"] = number_format( $vendor_share, 6);
                    $post["commission"] = number_format( $commission, 6);                

                    unset($post['id'], $post['quantity']);
                    $post['date'] = time();
                    $post['products'] = serialize(array($product_id => $product_quantity));
                    $post["productInfo"] = $productInfo;
                    return $this->insertVendorOrder($post);
                }
            }            
        }
    }

    public function insertVendorOrder($post)
    {
        $q = $this->db->query('SELECT MAX(order_id) as order_id FROM vendors_orders');
        $rr = $q->row_array();
        if ($rr['order_id'] == 0) {
            $rr['order_id'] = 1233;
        }
        $post['order_id'] = $rr['order_id'] + 1;
        $this->vendorOrderId = $post['order_id'];
        
        $this->db->trans_begin();
        if (!$this->db->insert('vendors_orders', array(
                    'order_id' => $post['order_id'],
                    'products' => $post['products'],
                    'date' => $post['date'],
                    'referrer' => $post['referrer'],
                    'clean_referrer' => $post['clean_referrer'],
                    'payment_type' => $post['payment_type'],
                    'paypal_status' => @$post['paypal_status'],
                    'alipay_status' => @$post['alipay_status'],
                    'total_amount' => $post["total_amount"],
                    'vendor_share' => $post["vendor_share"],
                    'commission' => $post["commission"],
                    'order_source' => $post["order_source"],
                    'discount_code' => @$post['discountCode'],
                    'vendor_id' => $post["productInfo"]['vendor_id'],
                    'customer_id' => @$post['user_id'],
                    'parent_order_id' => $post['parent_order_id']
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
        $lastId = $this->db->insert_id();
        if (!$this->db->insert('vendors_orders_clients', array(
                    'for_id' => $lastId,
                    'name' => $this->encryption->encrypt($post['name']),
                    'email' => $this->encryption->encrypt($post['email']),
                    'phone' => $this->encryption->encrypt($post['phone']),
                    'address' => $this->encryption->encrypt($post['address']),
                    'city' => $this->encryption->encrypt($post['city']),
                    'post_code' => $this->encryption->encrypt($post['post_code']),
                    'notes' => $this->encryption->encrypt($post['notes'])
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
        }
    }
    
    public function updateVendorOrderAmount($totalAmount, $vendorShare, $commission, $shippingAmount)
    {
        $this->db->where('order_id', $this->vendorOrderId);
        if (!$this->db->update('vendors_orders', array(
                    'total_amount' => $totalAmount,
                    'vendor_share' => $vendorShare,
                    'commission' => $commission,
                    'shipping_amount' => $shippingAmount
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
    }

    public function updateBondPayStatus($vendor_id, $status)
    {
        $this->db->where('id', $vendor_id);
        if (!$this->db->update('vendors', array(
                    'bond_status' => $status)
                )) {
            log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
    }

    public function setActivationLink($link, $orderId)
    {
        $result = $this->db->insert('confirm_links', array('link' => $link, 'for_order' => $orderId));
        return $result;
    }

    public function getSliderProducts()
    {
        $this->db->select('vendors.url as vendor_url, products.id, products.quantity, products.image, products.url, products_translations.price, products_translations.title, products_translations.basic_description, products_translations.old_price');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);
        $this->db->where('visibility', 1);
        $this->db->where('in_slider', 1);
        if ($this->showOutOfStock == 0) {
            $this->db->where('quantity >', 0);
        }
        $query = $this->db->get('products');
        return $query->result_array();
    }

    public function getbestSellers($categorie = 0, $noId = 0)
    {
        $this->db->select('vendors.url as vendor_url, products.id, products.quantity, products.image, products.url, products_translations.price, products_translations.title, products_translations.old_price, grade_desc.desc, products.defect_desc, products.shop_categorie');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->join('grade_desc', 'grade_desc.grade_id = products.grade', 'left');
        if ($noId > 0) {
            $this->db->where('products.id !=', $noId);
        }
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);
        if ($categorie != 0) {
            $this->db->where('products.shop_categorie !=', $categorie);
        }
        $this->db->where('visibility', 1);
        if ($this->showOutOfStock == 0) {
            $this->db->where('quantity >', 0);
        }
        $this->db->order_by('products.procurement', 'desc');
        $this->db->limit(5);
        $query = $this->db->get('products');
        return $query->result_array();
    }

    public function sameCagegoryProducts($categorie, $noId, $vendor_id = false)
    {
        $this->db->select('vendors.url as vendor_url, products.id, products.quantity, products.image, products.url, products_translations.price, products_translations.title, products_translations.old_price, grade_desc.desc, products.defect_desc, products.shop_categorie');
        $this->db->join('products_translations', 'products_translations.for_id = products.id', 'left');
        $this->db->join('vendors', 'vendors.id = products.vendor_id', 'left');
        $this->db->join('grade_desc', 'grade_desc.grade_id = products.grade', 'left');
        $this->db->where('products.id !=', $noId);
        if ($vendor_id !== false) {
            $this->db->where('vendor_id', $vendor_id);
        }
        $this->db->where('products.shop_categorie =', $categorie);
        $this->db->where('products_translations.abbr', MY_LANGUAGE_ABBR);
        $this->db->where('visibility', 1);
        if ($this->showOutOfStock == 0) {
            $this->db->where('quantity >', 0);
        }
        $this->db->order_by('products.id', 'desc');
        $this->db->limit(5);
        $query = $this->db->get('products');
        return $query->result_array();
    }

    public function getOnePost($id)
    {
        $this->db->select('blog_translations.title, blog_translations.description, blog_posts.image, blog_posts.time');
        $this->db->where('blog_posts.id', $id);
        $this->db->join('blog_translations', 'blog_translations.for_id = blog_posts.id', 'left');
        $this->db->where('blog_translations.abbr', MY_LANGUAGE_ABBR);
        $query = $this->db->get('blog_posts');
        return $query->row_array();
    }

    public function getArchives()
    {
        $result = $this->db->query("SELECT DATE_FORMAT(FROM_UNIXTIME(time), '%M %Y') as month, MAX(time) as maxtime, MIN(time) as mintime FROM blog_posts GROUP BY DATE_FORMAT(FROM_UNIXTIME(time), '%M %Y')");
        if ($result->num_rows() > 0) {
            return $result->result_array();
        }
        return false;
    }

    public function getFooterCategories()
    {
        $this->db->select('shop_categories.id, shop_categories_translations.name');
        $this->db->where('abbr', MY_LANGUAGE_ABBR);
        $this->db->where('shop_categories.sub_for =', 0);
        $this->db->join('shop_categories', 'shop_categories.id = shop_categories_translations.for_id', 'INNER');
        $this->db->limit(10);
        $query = $this->db->get('shop_categories_translations');
        $arr = array();
        if ($query !== false) {
            foreach ($query->result_array() as $row) {
                $arr[$row['id']] = $row['name'];
            }
        }
        return $arr;
    }

    public function setSubscribe($array)
    {
        $num = $this->db->where('email', $array['email'])->count_all_results('subscribed');
        if ($num == 0) {
            $this->db->insert('subscribed', $array);
        }
    }

    public function getDynPagesLangs($dynPages)
    {
        if (!empty($dynPages)) {
            $this->db->join('textual_pages_tanslations', 'textual_pages_tanslations.for_id = active_pages.id', 'left');
            $this->db->where_in('active_pages.name', $dynPages);
            $this->db->where('textual_pages_tanslations.abbr', MY_LANGUAGE_ABBR);
            $result = $this->db->select('textual_pages_tanslations.name as lname, active_pages.name as pname')->get('active_pages');
            $ar = array();
            $i = 0;
            foreach ($result->result_array() as $arr) {
                $ar[$i]['lname'] = $arr['lname'];
                $ar[$i]['pname'] = $arr['pname'];
                $i++;
            }
            return $ar;
        } else
            return $dynPages;
    }

    public function getOnePage($page)
    {
        $this->db->join('textual_pages_tanslations', 'textual_pages_tanslations.for_id = active_pages.id', 'left');
        $this->db->where('textual_pages_tanslations.abbr', MY_LANGUAGE_ABBR);
        $this->db->where('active_pages.name', $page);
        $result = $this->db->select('textual_pages_tanslations.description as content, textual_pages_tanslations.name')->get('active_pages');
        return $result->row_array();
    }

    public function changePaypalOrderStatus($order_id, $status)
    {
        $processed = 0;
        if ($status == 'canceled') {
            $processed = 2;
        }
        $this->db->where('order_id', $order_id);
        if (!$this->db->update('orders', array(
                    'paypal_status' => $status,
                    'processed' => $processed
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
    }

    public function changeAlipayPayStatus($order_id, $pay_status)
    {
        $this->db->trans_begin();
        $this->db->where('order_id', $order_id);
        if (!$this->db->update('orders', array(
                    'pay_status' => $pay_status
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
        
        $orderIds = $this->queryChildOrders($order_id);
        foreach($orderIds as $orderId){
             $this->db->where('order_id', $orderId["order_id"]);
            if (!$this->db->update('vendors_orders', array(
                        'pay_status' => $pay_status
                    ))) {
                log_message('error', print_r($this->db->error(), true));
            }            
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }               
    }

    public function queryChildOrders($order_id)
    {
        $this->db->where('parent_order_id', $order_id);
        $this->db->select('order_id');
        $result1 = $this->db->get('vendors_orders');
        return $result1->result_array();        
    }   
    
    public function changeAlipayOrderStatus($order_id, $status)
    {
        $this->db->trans_begin();        
        $this->db->where('order_id', $order_id);
        if (!$this->db->update('orders', array(
                    'order_status' => $status
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
        
        $orderIds = $this->queryChildOrders($order_id);
        foreach($orderIds as $orderId){
            $this->db->where('order_id', $orderId["order_id"]);
            if (!$this->db->update('vendors_orders', array(
                        'order_status' => $status
                    ))) {
                log_message('error', print_r($this->db->error(), true));
            }            
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }                
    }

    function manageQuantitiesAndProcurement($order_id)
    {
        $operator = '-';
        $operator_pro = '+';
        $this->db->select('products');
        $this->db->where('order_id', $order_id);
        $result = $this->db->get('orders');
        $arr = $result->row_array();
        $products = unserialize($arr['products']);
        foreach ($products as $product) {
                if (isset($operator)) {
                    if (!$this->db->query('UPDATE products SET quantity=quantity' . $operator . $product['product_quantity'] . ' WHERE id = ' . $product['product_info']['id'])) {
                        log_message('error', print_r($this->db->error(), true));
                        show_error(lang('database_error'));
                    }
                }
                if (isset($operator_pro)) {
                    if (!$this->db->query('UPDATE products SET procurement=procurement' . $operator_pro . $product['product_quantity'] . ' WHERE id = ' . $product['product_info']['id'])) {
                        log_message('error', print_r($this->db->error(), true));
                        show_error(lang('database_error'));
                    }
                } 
        }
    }
    
    public function updateOrderAmount($order_id, $totalAmount, $vendorShare, $commission, $shippingAmount)
    {
        $this->db->where('order_id', $order_id);
        if (!$this->db->update('orders', array(
                    'total_amount' => $totalAmount,
                    'vendor_share' => $vendorShare,
                    'commission' => $commission,
                    'shipping_amount' => $shippingAmount
                ))) {
            log_message('error', print_r($this->db->error(), true));
        }
    }
    
    public function getCookieLaw()
    {
        $this->db->join('cookie_law_translations', 'cookie_law_translations.for_id = cookie_law.id', 'inner');
        $this->db->where('cookie_law_translations.abbr', MY_LANGUAGE_ABBR);
        $this->db->where('cookie_law.visibility', '1');
        $query = $this->db->select('link, theme, message, button_text, learn_more')->get('cookie_law');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function confirmOrder($md5)
    {
        $this->db->limit(1);
        $this->db->where('link', $md5);
        $result = $this->db->get('confirm_links');
        $row = $result->row_array();
        if (!empty($row)) {
            $orderId = $row['for_order'];
            $this->db->limit(1);
            $this->db->where('order_id', $orderId);
            $result = $this->db->update('orders', array('confirmed' => '1'));
            return $result;
        }
        return false;
    }

    public function getValidDiscountCode($code)
    {
        $time = time();
        $this->db->select('type, amount');
        $this->db->where('code', $code);
        $this->db->where($time . ' BETWEEN valid_from_date AND valid_to_date');
        $query = $this->db->get('discount_codes');
        return $query->row_array();
    }

    public function countPublicUsersWithEmail($email, $id = 0)
    {
        if ($id > 0) {
            $this->db->where('id !=', $id);
        }
        $this->db->where('email', $email);
        return $this->db->count_all_results('users_public');
    }

    public function registerUser($post)
    {
        $this->db->insert('users_public', array(
            'name' => $post['name'],
            'phone' => $post['phone'],
            'email' => $post['email'],
            'password' => md5($post['pass'])
        ));
        return $this->db->insert_id();
    }
    public function getVisitHistory()
    {
        $this->db->order_by('visit_time', 'desc');
        $query = $this->db->get('visit_history');
        return $query;
    }

    public function getProductGrades()
    {
        $this->db->order_by('grade_id', 'desc');
        $query = $this->db->get('grade_desc');
        return $query->result_array();
    }
    
    public function setVisitHistory($post)
    {
        $this->db->insert('visit_history', array(
            'remote_addr' => $post['remote_addr'],
            'request_uri' => urldecode($post['request_uri']),
            'remote_location' => $post['remote_location'],
            'http_referer' => $post['http_referer'],            
            'visit_time' => time(),
            'user_name' => $post['user_name'],
            'email' => $post['email']
        ));
        return $this->db->insert_id();
    }

    public function getUserVisitHistoryCountByDay()
    {
        $this->db->select('count(distinct remote_addr) as count');
        $this->db->where('request_uri not like', "%vendor%");
        $this->db->where('request_uri like', "/%");
        $this->db->where('to_days(date(FROM_UNIXTIME(visit_time))) = to_days(now())');
        $query = $this->db->get('visit_history');
        $result = $query->row_array();
        return $result['count'];
    }
    
    public function getVendorVisitHistoryCountByDay()
    {
        $this->db->select('count(distinct remote_addr) as count');
        $this->db->where('request_uri like', "/vendor%");        
        $this->db->where('to_days(date(FROM_UNIXTIME(visit_time))) = to_days(now())');
        $query = $this->db->get('visit_history');
        $result = $query->row_array();
        return $result['count'];
    }

    public function getUserVisitHistoryCountByMonth()
    {
        $this->db->select('count(distinct remote_addr) as count');
        $this->db->where('request_uri not like', "%vendor%");
        $this->db->where('request_uri like', "/%");
        $this->db->where("MONTH(date(FROM_UNIXTIME(visit_time))) = MONTH(NOW())");
        $query = $this->db->get('visit_history');
        $result = $query->row_array();
        return $result['count'];
    }
    
    public function getVendorVisitHistoryCountByMonth()
    {
        $this->db->select('count(distinct remote_addr) as count');
        $this->db->where('request_uri like', "/vendor%");
        $this->db->where('MONTH(date(FROM_UNIXTIME(visit_time))) = MONTH(NOW())');
        $query = $this->db->get('visit_history');
        $result = $query->row_array();
        return $result['count'];
    }
    
    public function updateUserLoginStatus($post)
    {
        $array = array(
            'online_status' => $post['online_status'],
            'login_at' => $post['login_at']
        );
        $this->db->where('id', $post['id']);
        $this->db->update('users_public', $array);
    }

    public function updateUserLogoutStatus($post)
    {
        $array = array(
            'online_status' => $post['online_status'],
            'logout_at' => $post['logout_at']
        );
        $this->db->where('id', $post['id']);
        $this->db->update('users_public', $array);
    }
    
    public function updateProfile($post)
    {
        $array = array(
            'name' => $post['name'],
            'phone' => $post['phone'],
            'email' => $post['email']
        );
        if (trim($post['pass']) != '') {
            $array['password'] = md5($post['pass']);
        }
        $this->db->where('id', $post['id']);
        $this->db->update('users_public', $array);
    }

    public function checkPublicUserIsValid($post)
    {
        $this->db->where('email', $post['email']);
        $this->db->where('password', md5($post['pass']));
        $query = $this->db->get('users_public');
        $result = $query->row_array();
        if (empty($result)) {
            return false;
        } else {
            return $result['id'];
        }
    }

    public function getUserProfileInfo($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('users_public');
        return $query->row_array();
    }

    public function sitemap()
    {
        $query = $this->db->select('url')->get('products');
        return $query;
    }

    public function sitemapBlog()
    {
        $query = $this->db->select('url')->get('blog_posts');
        return $query;
    }

    public function getUserOrdersHistoryCount($userId)
    {
        $this->db->where('user_id', $userId);
        return $this->db->count_all_results('orders');
    }

    public function getUserOrdersHistory($userId, $big_get, $page)
    {
        $this->db->where('user_id', $userId);
        $this->db->order_by('id', 'DESC');
        $this->db->select('orders.*, orders_clients.name,'
                . ' orders_clients.email, orders_clients.phone,'
                . ' orders_clients.address, orders_clients.city, orders_clients.post_code,'
                . ' orders_clients.notes, discount_codes.type as discount_type, discount_codes.amount as discount_amount,'
                . ' vendors_orders.pay_status, vendors_orders.delivery_status, vendors_orders.receipt_status,vendors_orders.order_status as vendor_order_status,'
                . ' vendors_orders.order_id as child_order_id, vendors_orders.vendor_id, vendors_orders.express_company, vendors_orders.express_no,'
                . ' vendors_orders.products as vendor_products, vendors_orders.pay_type,  vendors_orders.total_amount, vendors.name as vendor_name');
        $this->db->join('orders_clients', 'orders_clients.for_id = orders.id', 'inner');
        $this->db->join('vendors_orders', 'vendors_orders.parent_order_id = orders.order_id', 'inner'); 
        $this->db->join('vendors', 'vendors_orders.vendor_id = vendors.id', 'inner');        
        $this->db->join('discount_codes', 'discount_codes.code = orders.discount_code', 'left');
        // 检索查询条件
        $query = $big_get;        
        $this->queryFilter($query);        
        $result = $this->db->get('orders', $page);
        $result = $result->result_array();
        if(!count($result)) return $result;
        
        foreach($result as $k => $v) {
            $result[$k] = array_map(function($v) {
                $d = $this->encryption->decrypt($v);
                return $d !== false ? $d : $v;
            }, $v);
        }
//        $result['orders_num'] = count($result);
        return $result;
    }

     /**
     * 设置默认的检索数据
     * @param array $query
     * @param array $default
     * @return array
     */
    protected function setQueryDefaultValue(array $query, array $default = []): array
    {
        $data = array_merge($default, $query);
        foreach ($query as $field => $value) {
            // 不存在默认值跳出循环
            if (!isset($default[$field])) continue;
            // 如果传参为空, 设置默认值
            if (empty($value) && $value !== '0') {
                $data[$field] = $default[$field];
            }
        }
        return $data;
    }
        /**
     * 设置检索查询条件
     * @param array $param
     * @return 
     */
    private function queryFilter(array $param)
    {
        // 默认参数
        $params = $this->setQueryDefaultValue($param, [
            'searchType' => '',     // 关键词类型 (10订单号 20客户姓名 30客户手机号 40客户邮箱 50收货人姓名 60收货人手机号 70收货人邮箱)
            'searchValue' => '',    // 关键词内容
            'orderSource' => -1,    // 订单来源
            'payType' => -1,        // 支付方式
            'deliveryType' => -1,   // 配送方式
            'start_time' => '',     // 起始时间
            'end_time' => '',       // 截止时间
            'queryOrderType' => -1,   // 订单类型(-1所有订单 10待发货 20待收货 30未支付 40已完成 50已取消 60售后管理)
        ]);
        // 检索查询条件
        $filter = [];
        // 关键词
        if (!empty($params['searchValue'])) {
            $searchWhere = [
                10 => ['vendors_orders.order_id like', "%{$params['searchValue']}%"],
                20 => ['users_public.name like', "%{$params['searchValue']}%"],
                30 => ['users_public.phone =', (int)$params['searchValue']],
                40 => ['users_public.email like', "%{$params['searchValue']}%"],                        
                50 => ['vendors_orders_clients.receiptor_name like', "%{$params['searchValue']}%"],
                60 => ['vendors_orders_clients.phone=', (int)$params['searchValue']],
                70 => ['vendors_orders_clients.email like', "%{$params['searchValue']}%"],
            ];
            array_key_exists($params['searchType'], $searchWhere) && $filter[] = $searchWhere[$params['searchType']];
        }
        // 起止时间
        if ($params['start_time'] != '') {
            $start_time = \DateTime::createFromFormat('Y-m-d', $params['start_time']);
            if($start_time) {
                $time = $start_time->getTimestamp();
                $filter[] = ['vendors_orders.date >=', $time];
            }
        }        
        if ($params['end_time'] != '') {
            $end_time = \DateTime::createFromFormat('Y-m-d', $params['end_time']);
            if($end_time) {
                $time = $end_time->getTimestamp();
                $filter[] = ['vendors_orders.date <', $time];
            }
        }
        
        // 订单来源
        $params['orderSource'] > -1 && $filter[] = ['order_source =', (int)$params['orderSource']];
        // 支付方式
        $params['payType'] > -1 && $filter[] = ['pay_type =', (int)$params['payType']];
        // 配送方式
        $params['deliveryType'] > -1 && $filter[] = ['delivery_type =', (int)$params['deliveryType']];
        
        //订单查询类型
        if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_DELIVERY){
            $filter[] = ['vendors_orders.pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['vendors_orders.delivery_status =', self::NOT_DELIVERED];
        }
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_RECEIPT){
            $filter[] = ['vendors_orders.pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['vendors_orders.delivery_status =', self::DELIVERED];
            $filter[] = ['vendors_orders.receipt_status =', self::NOT_RECEIVED];
        }
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_COMPLETED){
            $filter[] = ['vendors_orders.pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['vendors_orders.delivery_status =', self::DELIVERED];
            $filter[] = ['vendors_orders.receipt_status =', self::RECEIVED];
            $filter[] = ['vendors_orders.order_status =', self::COMPLETED];
        }
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_CANCELED){
            $filter[] = ['vendors_orders.pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['vendors_orders.delivery_status =', self::DELIVERED];
            $filter[] = ['vendors_orders.receipt_status =', self::RECEIVED];
            $filter[] = ['vendors_orders.order_status =', self::CANCELLED];
        }
        
        else if($params['queryOrderType'] == self::QUERY_ORDER_TYPE_AFTERSALES){
            $filter[] = ['vendors_orders.pay_status =', self::PAYSTATUS_SUCCESS];
            $filter[] = ['vendors_orders.delivery_status =', self::DELIVERED];
            $filter[] = ['vendors_orders.receipt_status =', self::RECEIVED];
            $filter[] = ['vendors_orders.order_status =', self::APPLY_CANCEL];
        }
        
        foreach($filter as $v) {
            $this->db->where($v[0], $v[1]);
        }
    }
}
