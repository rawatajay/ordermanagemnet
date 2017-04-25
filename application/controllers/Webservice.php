<?php

class Webservice extends CI_Controller {

    private $status = "failed";
    private $isError = FALSE;
    private $message;       
    private $error_code = 0;
    private $record_per_page =10;
    private $userDisbled = 0;
    private $DataArr = array();
    public function __construct() {
        
        parent::__construct();  
        $data['title'] = SITE_TITLE;    
        $this->load->model('Common', 'common');  
    }
    
    /*
    * @description : This function developed for default page
    * @Method name: index
    */
    public function index() {
        $this->load->view('webservices/index', $data);        
    }
    
    /*
    * @description : This function developed to create order
    * @Method name: createOrders
    */
    public function createOrders() {
        
            $inputJSON = file_get_contents('php://input');
            $input= $_POST =json_decode( $inputJSON, TRUE );        
            
            if(!empty($input)){
                $this->form_validation->set_rules('email', ' email', 'trim|required|min_length[3]');
                $this->form_validation->set_rules('user_id', 'user_id ', 'trim|required|numeric');

                if ($this->form_validation->run() != FALSE) {
                    //print_r($input);
                    if(count($input['order_items']) > 0) {
                        $insert_data['email_id']     = $input['email'];                        
                        $insert_data['user_id']      = $input['user_id'];
                        $insert_data['status']       = "created";
                        $insert_data['created_at']   = date("Y-m-d H:i:s");
                        $insert_data['updated_at']   = date("Y-m-d H:i:s");                        
                        $insertId = $this->common->insert('orders',$insert_data);

                        if($insertId > 0){
                            foreach ($input['order_items'] as $okey => $ovalue) {
                                $iteminsert_data['order_id']    = $insertId;                        
                                $iteminsert_data['name']        = $ovalue["name"];
                                $iteminsert_data['price']       = $ovalue["price"];
                                $iteminsert_data['quantity']    = $ovalue["quantity"];
                                $iteminsert_data['created_at']  = date("Y-m-d H:i:s");
                                $iteminsert_data['updated_at']  = date("Y-m-d H:i:s");                        
                                $iteminsertId = $this->common->insert('order_items',$iteminsert_data);
                            }
                            $this->status = "success";
                            $this->message = "Order placed successfully.";
                        } else {
                            $this->message = "Error in placing order.";
                        }
                        
                    } else {
                        $this->message = "No items found in placing order.";
                    }
                } else {
                    if(form_error('email')!=''){
                        $this->message = form_error('email');
                    } else if(form_error('user_id')!='') {
                        $this->message = form_error('user_id');
                    }
                } 
            } else {
                $this->message = "invalid request";
            }
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $this->DataArr, 'message' => strip_tags($this->message)));
            header('Content-Type: application/json');
            echo $data['jsonData'];
    }
    /*
    * @description : This function developed to update order
    * @Method name: updateOrder
    */
    public function updateOrder() {
        
            $inputJSON = file_get_contents('php://input');
            $input= $_POST =json_decode( $inputJSON, TRUE );        
            //print_r($input);die;
            if(!empty($input)){
                $this->form_validation->set_rules('email', ' email', 'trim|required|valid_email');
                $this->form_validation->set_rules('orderId', 'orderId', 'trim|required|numeric');
                $this->form_validation->set_rules('user_id', 'user_id ', 'trim|required|numeric');

                if ($this->form_validation->run() != FALSE) {
                    //print_r($input);
                    if(count($input['order_items']) > 0) {
                        $this->common->update('orders',array("id" => $input['orderId']), array("email_id" => $input['email'],'updated_at' => date("Y-m-d H:i:s")) );                       
                        
                        if(!empty($input['orderId'])){
                            $condition = array('order_id'=>$input['orderId']);          
                            $this->common->delete_by_id('order_items',$condition);                            
                            foreach ($input['order_items'] as $okey => $ovalue) {
                                $iteminsert_data['order_id']    = $input['orderId'];                        
                                $iteminsert_data['name']        = $ovalue["name"];
                                $iteminsert_data['price']       = $ovalue["price"];
                                $iteminsert_data['quantity']    = $ovalue["quantity"];
                                $iteminsert_data['created_at']  = date("Y-m-d H:i:s");
                                $iteminsert_data['updated_at']  = date("Y-m-d H:i:s");                
                                $iteminsertId = $this->common->insert('order_items',$iteminsert_data);
                            }
                            $this->status = "success";
                            $this->message = "Order updated successfully.";
                        } else {
                            $this->message = "Error in placing order.";
                        }
                        
                    } else {
                        $this->message = "No items found in placing order.";
                    }
                } else {
                    if(form_error('email')!=''){
                        $this->message = form_error('email');
                    } else if(form_error('user_id')!='') {
                        $this->message = form_error('user_id');
                    } else if(form_error('orderId')!='') {
                        $this->message = form_error('orderId');
                    }
                } 
            } else {
                $this->message = "invalid request";
            }
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $this->DataArr, 'message' => strip_tags($this->message)));
            header('Content-Type: application/json');
            echo $data['jsonData'];
    }
    /*
    * @description : This function developed to cancel order
    * @Method name: cancelOrder
    */
    public function cancelOrder() {
        
            $inputJSON = file_get_contents('php://input');
            $input= $_POST =json_decode( $inputJSON, TRUE );        
            //print_r($input);die;
            if(!empty($input)){                
                $this->form_validation->set_rules('orderId', 'orderId', 'trim|required|numeric');                

                if ($this->form_validation->run() != FALSE) {                   
                    
                    $this->common->update('orders',array("id" => $input['orderId']), array("status" => "cancelled",'updated_at' => date("Y-m-d H:i:s")) );                        
                    $this->status = "success";
                    $this->message = "Order cancelled successfully.";
                   
                } else {
                    if(form_error('orderId')!='') {
                        $this->message = form_error('orderId');
                    }
                } 
            } else {
                $this->message = "invalid request";
            }
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $this->DataArr, 'message' => strip_tags($this->message)));
            header('Content-Type: application/json');
            echo $data['jsonData'];
    }
    /*
    * @description : This function developed to cancel order
    * @Method name: paymentOrder
    */
    public function paymentOrder() {
        
            $inputJSON = file_get_contents('php://input');
            $input= $_POST =json_decode( $inputJSON, TRUE );        
            //print_r($input);die;
            if(!empty($input)){                
                $this->form_validation->set_rules('orderId', 'orderId', 'trim|required|numeric');                

                if ($this->form_validation->run() != FALSE) {                   
                    
                    $this->common->update('orders',array("id" => $input['orderId']), array("status" => "processed",'updated_at' => date("Y-m-d H:i:s")) );                        
                    $this->status = "success";
                    $this->message = "Order processed successfully.";
                   
                } else {
                    if(form_error('orderId')!='') {
                        $this->message = form_error('orderId');
                    }
                } 
            } else {
                $this->message = "invalid request";
            }
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $this->DataArr, 'message' => strip_tags($this->message)));
            header('Content-Type: application/json');
            echo $data['jsonData'];
    }
    /*
    * @description : This function developed to get order
    * @Method name: getOrder
    */
    public function getOrder() {           
            
            $input= $this->uri->segment(2); 
            if(!empty($input)){
                $orderData = $this->common->_getById("orders" ,array("id" => $input));
                
                if(!empty($orderData)){
                    $orderitemData = $this->common->_getList("order_items" ,array("order_id" => $input));
                    $orderData['order_items'] = $orderitemData;
                    $this->DataArr = $orderData;
                    $this->status = "success";
                    $this->message = "Order details.";
                } else {
                    $this->message = "No record found.";   
                }  
                
            } else {
                $this->message = "invalid request";
            }
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $this->DataArr, 'message' => strip_tags($this->message)));
            header('Content-Type: application/json');
            echo $data['jsonData'];
    }

    /*
    * @description : This function developed to get Order by User
    * @Method name: getOrderbyUser
    */
    public function getOrderbyUser() {           
            $input= $_GET;               
            $input= $input['user']; 
            if(!empty($input)){            
                
                $orderData = $this->common->_getList("orders" ,array("user_id" => $input));
                
                if(!empty($orderData)){

                    foreach ($orderData as $odkey => $odvalue) {
                        
                        $orderitemData = $this->common->_getList("order_items" ,array("order_id" => $odvalue['id']));
                        $orderData[$odkey]['order_items'] = $orderitemData;
                    }
                    $this->DataArr = $orderData;
                    $this->status = "success";
                    $this->message = "Order detail.";
                } else {
                    $this->message = "No record found.";   
                }  
                
            } else {
                $this->message = "invalid request";
            }
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $this->DataArr, 'message' => strip_tags($this->message)));
            header('Content-Type: application/json');
            echo $data['jsonData'];
    }
    /*
    * @description : This function developed to get today Order
    * @Method name: getTodayOrder
    */
    public function getTodayOrder() {
                
        $orderData = $this->common->_getList("orders" , " created_at LIKE '%".date("Y-m-d")."%' ");
        
        if(!empty($orderData)){

            foreach ($orderData as $odkey => $odvalue) {                
                $orderitemData = $this->common->_getList("order_items" ,array("order_id" => $odvalue['id']));
                $orderData[$odkey]['order_items'] = $orderitemData;
            }
            $this->DataArr = $orderData;
            $this->status = "success";
            $this->message = "Order detail.";
        } else {
            $this->message = "No record found.";   
        }  
            
        
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $this->DataArr, 'message' => strip_tags($this->message)));
        header('Content-Type: application/json');
        echo $data['jsonData'];
    }
    

    /*
    * @description : This function developed for login
    * @Method name: login
    */   
    public function login() {
          
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData = $this->input->post(); 
            $DataArr = array();
            $insert_data = array(); 
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');        
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
           
            if ($this->form_validation->run() != FALSE) {                

                $data['email'] = $postData['email'];
                $data['password'] = md5($postData['password']);
                $dataArray = array('email' => $data['email'], 'password' => $data['password']);
                $userData = $this->user_model->loginUser($dataArray);
                 
                /*if($userData['isActive'] == '1'){
                    $code                           = random_string('alnum', 10);
                    $this->common->update('user',array("email" => $postData['email']) , array("code" =>  $code));
                    $this->activationmail($postData['email'],$name,$code );
                     $this->message = 'Your account is not activated yet.kindly, check your mail ( '.$userData['email'].' ) to activate your account or contact to administrator.';                   
                }*/
                if (!empty($userData)) {
                   $DataArr = $userData;
                        
                  $condition = array('userID'=>$userData['userID'],'token'=>$postData['token'], 'deviceType'=>$postData['deviceType']);
                  $device_data =$this->common->check_by_id('userdevice',$condition);


                   if(empty($device_data)){
                           $device['userID']  = $userData['userID'];
                           $device['token'] = $postData['token'];
                           $device['deviceType']  = $postData['deviceType'];
                          $id = $this->common->insert('userdevice',$device);
                    }        

                     
                   $this->common->update('user',array("email" => $postData['email']) , array("lastLoginTime" => date("Y-m-d H:i:s")));
                   $this->status = 'success';
                   $this->message = 'Login Successfully';
                } else {
                   $this->message = 'Email or Password is incorrect.';                  
                }
                
            } else {
            
                 if(form_error('email')!=''){
                    $this->message = form_error('email');
                } else if(form_error('password')!='') {
                    $this->message = form_error('password');
                } 
            } 
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            if($postData['debug']== "1"){
                $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
                $this->load->view('webservices/output', $data);  
            }else{
                echo $data['jsonData'];
            }           
        }         
    }   
    /*
    * @description : This function developed for user logout
    * @Method name: logout
    */
    public function logout() {
          
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData = $this->input->post(); 
            $DataArr = array();
            $insert_data = array(); 
            $this->form_validation->set_rules('userID', 'userID', 'trim|required');        
            $this->form_validation->set_rules('token', 'Token', 'trim|required');        
            $this->form_validation->set_rules('deviceType', 'Device Type', 'trim|required');        
           
           
            if ($this->form_validation->run() != FALSE) {        
                $userID = $postData['userID'];
                $token = $postData['token'];
                $deviceType = $postData['deviceType'];
                $condition = array('userID'=>$userID,'token'=>$token,'deviceType'=>$deviceType);
          
            $this->common->delete_by_id('userdevice',$condition);
            $this->status = 'success';
            $this->message = 'deleted ';  

            } else {
            
                 if(form_error('userID')!=''){
                    $this->message = form_error('userID');
                } else if(form_error('token')!='') {
                    $this->message = form_error('token');
                } 
                else if(form_error('deviceType')!='') {
                    $this->message = form_error('deviceType');
                } 
            } 
        
             $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            if($postData['debug']== "1"){
                $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
                $this->load->view('webservices/output', $data);  
            }else{
                echo $data['jsonData'];
            }
        }     
    }
    /*
    * @description : This function developed for sending activation mail
    * @Method name: activationmail
    */ 
    public function activationmail($email, $name, $code){
        $message = '<table style=" background-color: #f6f6f6;width: 100%; margin: 0;padding: 0;font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;background: #fff;border: 1px solid #e9e9e9; border-radius: 3px;"> <tr><td style="display: block !important;max-width: 600px !important;margin: 0 auto !important;clear: both !important;" width="600"><div style="max-width: 600px;margin: 0 auto;display: block;padding: 20px;"><table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction"><tr><td style="padding: 20px;background: #fff;border: 1px solid #e9e9e9;border-radius: 3px;"><meta itemprop="name" content="Confirm Email"/><table width="100%" cellpadding="0" cellspacing="0"><tr><td style= " font-family: \' Helvetica Neue \', \' Helvetica\', Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;"><h1>Euforia</h2></td></tr><tr><td style= " padding: 0 0 20px;font-family: \' Helvetica Neue \', \' Helvetica\', Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;"><p>Dear, {NAME}</p>Please activate your account address by clicking the link below.<br><br></td></tr><tr><td  style=" padding: 0 0 20px;"> <a href="{LINK}" style="  text-decoration: none;  color: #FFF;  background-color: #348eda;  border: solid #348eda;  border-width: 10px 20px;  line-height: 2;  font-weight: bold;  text-align: center;  cursor: pointer;  display: inline-block;  border-radius: 5px;  text-transform: capitalize;font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;"" itemprop="url">Confirm email address</a></td></tr><tr><td  style=" padding: 0 0 20px;">Thanks,<br/>{SITENAME} Team</td></tr></table></td></tr></table><div class="footer"><table width="100%"><tr></tr></table></div></div></td></tr></table>';

        $link = base_url('activation') . "/" . $code;
        $patternFind1[0] = '/{NAME}/';
        $patternFind1[1] = '/{LINK}/';
        $patternFind1[2] = '/{SITENAME}/';

        $replaceFind1[0] = ucwords($name);
        $replaceFind1[1] = $link;
        $replaceFind1[2] = SITE_TITLE;

        $txtdesc_contact = stripslashes($message);
        $contact_sub = stripslashes($subject);
        $contact_sub = preg_replace($patternFind1, $replaceFind1, $contact_sub);
        $ebody_contact = preg_replace($patternFind1, $replaceFind1, $txtdesc_contact);
        $this->phpmailer->IsSMTP();                                      // set mailer to use SMTP
        $this->phpmailer->Host          = SMTPHOST;  // specify main and backup server
        $this->phpmailer->SMTPAuth      = true;     // turn on SMTP authentication
        $this->phpmailer->SMTPSecure    = "ssl";
        $this->phpmailer->Port          = SMTPPORT;
        $this->phpmailer->Username      = SMTPEMAIL;  // SMTP username
        $this->phpmailer->Password      = SMTPPASS; // SMTP password
        $this->phpmailer->SMTPAuth      = true;
        $this->phpmailer->From          = ADMINEMAIL;
        $this->phpmailer->FromName      = SITE_TITLE;
        $this->phpmailer->AddAddress($email, $name);
                       // name is optional
        $this->phpmailer->AddReplyTo(ADMINEMAIL, SITE_TITLE);

      
        $this->phpmailer->IsHTML(true);                                  // set email format to HTML

        $this->phpmailer->Subject = "Euhporia : Activate your account.";
        $this->phpmailer->Body    = $ebody_contact;
        

        if(!$this->phpmailer->Send())
        {
           echo "Message could not be sent. <p>";
           echo "Mailer Error: " . $this->phpmailer->ErrorInfo;
           exit;
        }       
    }
    /*
    * @description : This function developed for verify activation mail
    * @Method name: checkactivation
    */  
    public function checkactivation() {
        $code = $this->uri->segment(2);        
        $dataArray = array('code' => $code);
         
        $data = $this->user_model->getuserDetail($dataArray);
        
        if ($data) {
            $this->session->set_flashdata('msg', '<div style="color:green;text-align: center;">Your account has been activated successfully. Please login with your email and password.</div>');
            $this->load->view('userEmailActivation');                     
        } else {
            $this->session->set_flashdata('msg', '<div style="color:red;text-align: center;">Link has been expired. Contact to the administrator for futhur query.</div>');
            $this->load->view('userEmailActivation');                      
        }
    }    
    /*
    * @description : This function developed for event Type list
    * @Method name: eventType
    */
    public function eventType() { 
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
            
            $eventType = $this->common->_getList("eventType",""," eventTypeID DESC");
            
            $DataArr = $eventType;
            $this->status    = "success";
            $this->message   = "Event type list.";
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed for send link to reset password
    * @Method name: forgetPassword
    */
    public function forgetPassword(){       
       
        if ($this->input->server('REQUEST_METHOD') === "POST") {
            
            
            $postData = $this->input->post();
            $insert_data = array();
            $DataArr = array();
            $data['email'] = $postData['email'];
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
           
            if ($this->form_validation->run() != FALSE) {
                
                
                $data['email'] = $postData['email'];
                $this->form_validation->set_rules('verify', 'Verify', 'trim|callback_verifyUserEmail');  
             
                if ($this->form_validation->run() != false) {
                    $code = random_string('alnum', 10);
                    $update = $this->user_model->updateUserDetail(array('email' => $postData['email']), array('code' => $code));
                    if ($update) {

                        $message = '<table style=" background-color: #f6f6f6;width: 100%; margin: 0;padding: 0;font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;background: #fff;border: 1px solid #e9e9e9; border-radius: 3px;"> <tr><td style="display: block !important;max-width: 600px !important;margin: 0 auto !important;clear: both !important;" width="600"><div style="max-width: 600px;margin: 0 auto;display: block;padding: 20px;"><table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction"><tr><td style="padding: 20px;background: #fff;border: 1px solid #e9e9e9;border-radius: 3px;"><meta itemprop="name" content="Confirm Email"/><table width="100%" cellpadding="0" cellspacing="0"><tr><td style= " font-family: \' Helvetica Neue \', \' Helvetica\', Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;"><h1>Euforia</h2></td></tr><tr><td style= " padding: 0 0 20px;font-family: \' Helvetica Neue \', \' Helvetica\', Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;">We heard that you lost your  password. Sorry about that!.<br>But don’t worry! You can use the following link within the next day to reset your password<br><br></td></tr><tr><td  style=" padding: 0 0 20px;"> <a href="{LINK}" style="  text-decoration: none;  color: #FFF;  background-color: #348eda;  border: solid #348eda;  border-width: 10px 20px;  line-height: 2;  font-weight: bold;  text-align: center;  cursor: pointer;  display: inline-block;  border-radius: 5px;  text-transform: capitalize;font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;"" itemprop="url">Confirm email address</a></td></tr><tr><td  style=" padding: 0 0 20px;">Thanks,<br/>{SITETITLE} Team</td></tr></table></td></tr></table><div class="footer"><table width="100%"><tr></tr></table></div></div></td></tr></table>';

                        $link = base_url('setpassword') . "/" . $code;
                        $patternFind1[0] = '/{NAME}/';
                        $patternFind1[1] = '/{LINK}/';
                        $patternFind1[2] = '/{SITETITLE}/';

                        $replaceFind1[0] = ucwords($postData['name']);
                        $replaceFind1[1] = $link;
                        $replaceFind1[2] = SITE_TITLE;

                        $txtdesc_contact    = stripslashes($message);                        
                        $contact_sub        = preg_replace($patternFind1, $replaceFind1, $contact_sub);
                        $ebody_contact      = preg_replace($patternFind1, $replaceFind1, $txtdesc_contact);
                        $this->phpmailer->IsSMTP();                                      // set mailer to use SMTP
                        $this->phpmailer->Host          = SMTPHOST;  // specify main and backup server
                        $this->phpmailer->SMTPAuth      = true;     // turn on SMTP authentication
                        $this->phpmailer->SMTPSecure    = "ssl";
                        $this->phpmailer->Port          = SMTPPORT;
                        $this->phpmailer->Username      = SMTPEMAIL;  // SMTP username
                        $this->phpmailer->Password      = SMTPPASS; // SMTP password
                        $this->phpmailer->SMTPAuth      = true;
                        $this->phpmailer->From          = ADMINEMAIL;
                        $this->phpmailer->FromName      = SITE_TITLE;
                        $this->phpmailer->AddAddress($postData['email'], $name);
                                       // name is optional
                        $this->phpmailer->AddReplyTo(ADMINEMAIL, SITE_TITLE);

                      
                        $this->phpmailer->IsHTML(true);                                  // set email format to HTML

                        $this->phpmailer->Subject = "Euforia : Please reset your password.";
                        $this->phpmailer->Body    = $ebody_contact;
                        

                        if(!$this->phpmailer->Send())
                        {
                           echo "Message could not be sent. <p>";
                           echo "Mailer Error: " . $this->phpmailer->ErrorInfo;
                           exit;
                        }
                        $this->status = "success";   
                        $this->message = "Please check your email. Link has been sent on your email.";
                    } else {
                        $this->message = "You are not authorized user.";
                    }                   
                    
                } 
                                           
            }else{
                    if(form_error('email')!=''){
                        
                    $this->message = form_error('email');
                   }
                }
                
                 $data['jsonData'] = json_encode(array('status' => $this->status, 'data'=> $DataArr, 'message' => $this->message));
                  if($postData['debug']== "1"){
                $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
                $this->load->view('webservices/output', $data);  
            }else{
                echo $data['jsonData'];
            } 
        }
    }
    /*
    * @description : This function developed for sverify User Email
    * @Method name: verifyUserEmail
    */
    public function verifyUserEmail() {
        $email = trim($this->input->post('email'));        
        $userData = $this->user_model->getuserBYEmail($email);          
        if ($userData) {
            return true;
        }/* elseif ($userData && $userData['isActive'] == '1') {
            $this->message = 'Your account has not been activated. Please activate your account first.';
            return false;
            //$this->load->view('webservices/output', $data);          
        }*/else { 
            $this->message = 'Your email does not matched with our records.';
            //$this->load->view('webservices/output', $data);         
            return false;
        }
    }
    /*
    * @description : This function developed for view set password
    * @Method name: setpassword
    */
    public function setpassword(){
     
        $data['code'] = $this->uri->segment(2);
        $this->load->view('resetpassword', $data);        
    }
    /*
    * @description : This function developed for view reset password
    * @Method name: resetpassword
    */
    public function resetpassword(){
        $code = $this->uri->segment(3);
        $userData = $this->user_model->getuserDetailBYCode(array('code' => $code));
        if ($userData) {
            if ($this->input->server('REQUEST_METHOD') === "POST") {
                $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
                $this->form_validation->set_rules('password_again', 'Confirm Password', 'trim|required|matches[password]');
               // var_dump($this->form_validation->run());die;
                if ($this->form_validation->run() != FALSE) {
                    $password = md5($this->input->post('password'));                   
                    $updateData = $this->user_model->setPassword(array('userID' => $userData['userID']), $password);

                    if ($updateData) {
                        $this->session->set_flashdata('msg', '<div style="color:green">Your password has been reset successfully.</div>');
                    }
                    redirect('setpassword/'.$code);
                    } else {
                        if(form_error('password')!=''){
                        $this->message = form_error('password');
                        }
                        else if(form_error('password_again')!='') {
                            $this->message = form_error('password_again');
                        } 
                        $this->session->set_flashdata('msg', '<div style="color:red">'.$this->message.'</div>'); 
                    }
              
            }
        } else {
            $this->session->set_flashdata('msg', '<div style="color:red">Your link has been expired.</div>');
            redirect('setpassword/'.$code);
        }
        redirect('setpassword/'.$code);
    }
    /*
    * @description : This function developed send Otp
    * @Method name: sendOtp
    */
    public function sendOtp() {
        $DataArr   = array(); 
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User ID','trim|required');  
            $this->form_validation->set_rules('phonenumber','Phone Number','trim|required|min_length[10]|max_length[15]');  
            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                if(!empty($userData)){
                    $generateOtp = mt_rand(100000, 999999);                    
                    $from = '+13347210201';
                    $to =  $postData['phonenumber'];
                    $message = "Your OTP for euforia is ".$generateOtp;
                    $response = $this->twilio->sms($from, $to, $message);
                    //print_r($response);
                    
                    if($response->IsError){
                       $this->message = $response->ErrorMessage;
                    }
                    else{
                        $this->common->update('user',array("userID" => $postData['userId']), array("otp" => $generateOtp , "otpStatus" => "1" , "phoneNumber" => $postData['phonenumber']) );
                        $otpData = $this->common->_getById('user',array("userID" => $postData['userId']));
                        $DataArr = array("otp" => $otpData['otp'] , "otpStatus" => $otpData['otpStatus']);
                        $this->message = "Otp has been sent successfully to ".$to;
                        $this->status = "success";
                    }

                }else{
                    $this->message = 'Invalid user.';     
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('phonenumber'))
                    $this->message =  form_error ('phonenumber');
            }
        }else{
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed check Otp
    * @Method name: checkOtp
    */
    public function checkOtp() {
        $DataArr   = array(); 
        $otpstatus = "false";
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User ID','trim|required');  
            $this->form_validation->set_rules('otp','OTP','trim|required');  
            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                if(!empty($userData)){
                    
                    if($postData['otp'] == $userData['otp']){
                        $this->common->update('user',array("userID" => $postData['userId']), array("otp" =>'' , "otpStatus" => "2" ) );
                        $this->message = "Otp verified successfully.";
                        $this->status = "success";
                        $otpstatus = "true";
                    } else{
                        $this->message = "You have entered incorrect otp. Please try again.";
                    }

                }else{
                    $this->message = 'Invalid user.';     
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('otp'))
                    $this->message =  form_error ('otp');
            }
        }else{
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'otpstatus' => $otpstatus, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'otpstatus' => $otpstatus, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed create event
    * @Method name: createEvent
    */
    /*public function createEvent(){
        $DataArr   = array(); 
        $postData  = $this->input->post(); 
        //verify otp 
        if(empty($postData['userId'])){
            $this->message =  'User Id field is required.';
        }else if(empty($postData['otp'])){
            $this->message =  'Otp field is required.';
        }
        else{
            $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
            if(!empty($userData)){                    
                if($postData['otp'] == $userData['otp']){
                    
                    $otpStatus = $this->common->update('user',array("userID" => $postData['userId']), array("otp" =>'' , "otpStatus" => "2" ) );
                    if($otpStatus > 0) {
                        // create event
                        $this->form_validation->set_rules('userId','User ID','trim|required|numeric');  
                        $this->form_validation->set_rules('userType','User Type','trim|required|numeric');  
                        $this->form_validation->set_rules('eventname','Event Name','trim|required');  
                        $this->form_validation->set_rules('location','Location','trim|required');  
                        $this->form_validation->set_rules('latitude','Latitude','trim|required');  
                        $this->form_validation->set_rules('longitude','Longitude','trim|required');  
                        $this->form_validation->set_rules('startdate','Start Date','trim|required');  
                        $this->form_validation->set_rules('starttime','Start Time','trim|required');  
                        $this->form_validation->set_rules('enddate','End Date','trim|required');  
                        $this->form_validation->set_rules('endtime','End Time','trim|required'); 
                        $this->form_validation->set_rules('about','About Event','trim|required'); 
                        
                        if(!empty($_FILES['eventImage']) && $_FILES['eventImage']['size'] > 0){
                            
                           $config['upload_path'] = FCPATH.'uploads/eventImage/';
                         
                            $config['allowed_types'] = 'gif|jpg|png|jpeg';
                            $config['max_size'] = 800;
                            $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                            $config['file_name'] = $new_name;
                            $this->load->library('upload', $config);            
                            if($this->upload->do_upload('eventImage')){
                                $image1 =$this->upload->data()['file_name'] ;
                                $image = base_url().'uploads/eventImage/'. $image1; 
                                chmod($this->upload->data()['full_path'],0777);
                            }else   {
                                $error = array('error' => $this->upload->display_errors());
                                echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
                           }
                        }else{
                                echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event image is required" )); die;
                        }

                        if($this->form_validation->run()!=FALSE){
                           
                            if(!empty($userData)){
                                $insert_data['userID']          = $postData['userId'];
                                $insert_data['userType']        = $postData['userType'];
                                $insert_data['longitude']       = $postData['longitude'];
                                $insert_data['latitude']        = $postData['latitude'];
                                $insert_data['eventName']       = $postData['eventname'];                    
                                $insert_data['location']        = $postData['location'];
                                $insert_data['startDateTime']   = date("Y-m-d H:i:s", strtotime($postData['startdate'].$postData['starttime']));
                                $insert_data['status']          = '2';
                                $insert_data['endDateTime']     = date("Y-m-d H:i:s", strtotime($postData['enddate'].$postData['endtime']));
                                $insert_data['about']           = $postData['about']; 
                                $insert_data['createdOn']       = date('Y-m-d H:i:s');
                                $insert_data['eventPictureURL'] = $image;
                                $chID  = $this->common->insert('event',$insert_data);
                                $this->common->update('user',array("userID" => $postData['userId']), array("otp" =>'' , "otpStatus" => "0" ) );
                                $this->message = "Event created successfully.";
                                $this->status = "success";

                            }else{
                                $this->message = 'Invalid user.';     
                            }
                        } else {
                            if(form_error('userId'))
                                $this->message =  form_error ('userId');
                            else if(form_error('userType'))
                                $this->message =  form_error ('userType');
                            else if(form_error('eventname'))
                                $this->message =  form_error ('eventname');
                            else if(form_error('location'))
                                $this->message =  form_error ('location');
                            else if(form_error('latitude'))
                                $this->message =  form_error ('latitude');
                            else if(form_error('longitude'))
                                $this->message =  form_error ('longitude');
                            else if(form_error('startdate'))
                                $this->message =  form_error ('startdate');
                            else if(form_error('starttime'))
                                $this->message =  form_error ('starttime');
                            else if(form_error('enddate'))
                                $this->message =  form_error ('enddate');
                            else if(form_error('endtime'))
                                $this->message =  form_error ('endtime');
                            else if(form_error('about'))
                                $this->message =  form_error ('about');
                        }

                    }else{
                        $this->message = "Your otp code has been expired. Please try again.";    
                    }
                    $this->message = "Event created successfully.";
                    $this->status = "success";
                } else{
                    $this->message = "You have entered incorrect otp. Please try again.";
                }

            }else{
                $this->message = 'Invalid user.';     
            }
        }
        

        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status,  'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }*/
    public function createEvent(){
        $DataArr   = array(); 
        $postData  = $this->input->post(); 
        if($this->input->server('REQUEST_METHOD') === "POST"){
                $this->form_validation->set_rules('userId','User ID','trim|required|numeric');  
                $this->form_validation->set_rules('userType','User Type','trim|required|numeric');  
                $this->form_validation->set_rules('eventname','Event Name','trim|required');  
                $this->form_validation->set_rules('location','Location','trim|required');  
                $this->form_validation->set_rules('latitude','Latitude','trim|required');  
                $this->form_validation->set_rules('longitude','Longitude','trim|required');  
                $this->form_validation->set_rules('startdate','Start Date','trim|required');  
                $this->form_validation->set_rules('starttime','Start Time','trim|required');  
                $this->form_validation->set_rules('enddate','End Date','trim|required');  
                $this->form_validation->set_rules('endtime','End Time','trim|required'); 
                $this->form_validation->set_rules('about','About Event','trim|required'); 
                
                

                if($this->form_validation->run()!=FALSE){

                        if(!empty($_FILES['eventImage']) && $_FILES['eventImage']['size'] > 0){
                        
                        $config['upload_path'] = FCPATH.'uploads/eventImage/';
                     
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                       // $config['max_size'] = 800;
                        $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                        $config['file_name'] = $new_name;
                        $this->load->library('upload', $config);            
                        if($this->upload->do_upload('eventImage')){
                            $image1 =$this->upload->data()['file_name'] ;
                            $image = base_url().'uploads/eventImage/'. $image1; 
                            chmod($this->upload->data()['full_path'],0777);
                        }else   {
                        $error = array('error' => $this->upload->display_errors());
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
                        }
                    }else{
                            echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event image is required" )); die;
                    }

                    $currDate = strtotime(date("Y-m-d H:i:s"));
                    $eventStartDateTime = strtotime(date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime'])));
                    $eventEndDateIme    = strtotime(date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime'])));
                    // check event datetime greater than current datetime 
                    if($currDate > $eventStartDateTime) {
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event start date and time should be more than current date and time" )); die;
                    }
                    //  check event start  datetime  greter than enddatetime
                    if($eventStartDateTime >= $eventEndDateIme){
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event end date and time should be more than  start date and time" )); die;   
                    }  

                    if(empty($image) &&  empty($video)){
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Upload event image or event video." )); die;
                    }

                    $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                    if(!empty($userData)){
                        $insert_data['userID']          = $postData['userId'];
                        $insert_data['userType']        = $postData['userType'];
                        $insert_data['longitude']       = $postData['longitude'];
                        $insert_data['latitude']        = $postData['latitude'];
                        $insert_data['eventName']       = $postData['eventname'];                    
                        $insert_data['location']        = $postData['location'];
                        $insert_data['startDateTime']   = date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime']));
                        $insert_data['status']          = '2';
                        $insert_data['endDateTime']     = date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime']));
                        $insert_data['about']           = $postData['about']; 
                        $insert_data['createdOn']       = date('Y-m-d H:i:s');
                        $insert_data['eventPictureURL'] = $image;
                        $chID  = $this->common->insert('event',$insert_data);
                        $this->common->update('user',array("userID" => $postData['userId']), array("otp" =>'' , "otpStatus" => "0" ) );
                        $this->message = "Event created successfully.";
                        $this->status = "success";

                    }else{
                        $this->message = 'Invalid user.';     
                    }
                } else {
                    if(form_error('userId'))
                        $this->message =  form_error ('userId');
                    else if(form_error('userType'))
                        $this->message =  form_error ('userType');
                    else if(form_error('eventname'))
                        $this->message =  form_error ('eventname');
                    else if(form_error('location'))
                        $this->message =  form_error ('location');
                    else if(form_error('latitude'))
                        $this->message =  form_error ('latitude');
                    else if(form_error('longitude'))
                        $this->message =  form_error ('longitude');
                    else if(form_error('startdate'))
                        $this->message =  form_error ('startdate');
                    else if(form_error('starttime'))
                        $this->message =  form_error ('starttime');
                    else if(form_error('enddate'))
                        $this->message =  form_error ('enddate');
                    else if(form_error('endtime'))
                        $this->message =  form_error ('endtime');
                    else if(form_error('about'))
                        $this->message =  form_error ('about');
                }
            $this->message = "Event created successfully.";
            $this->status = "success";
        } else{
            $this->message = "You have entered incorrect otp. Please try again.";
        }

        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status,  'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }
    /*
    * @description : This function developed create event
    * @Method name: createBusinessEvent
    */
    public function createBusinessEvent(){
        $DataArr   = array(); 
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 

            $this->form_validation->set_rules('userId','User ID','trim|required|numeric');  
            $this->form_validation->set_rules('userType','User Type','trim|required|numeric');  
            $this->form_validation->set_rules('eventname','Event Name','trim|required');  
            $this->form_validation->set_rules('location','Location','trim|required');  
            $this->form_validation->set_rules('latitude','Latitude','trim|required'); 
            $this->form_validation->set_rules('eventType','Event Type','trim|required');  
            $this->form_validation->set_rules('longitude','Longitude','trim|required');  
            $this->form_validation->set_rules('startdate','Start Date','trim|required');  
            $this->form_validation->set_rules('starttime','Start Time','trim|required');  
            $this->form_validation->set_rules('enddate','End Date','trim|required');  
            $this->form_validation->set_rules('endtime','End Time','trim|required'); 
            $this->form_validation->set_rules('about','About Event','trim|required'); 
            $this->form_validation->set_rules('festival','Festival','trim|required'); 
            $this->form_validation->set_rules('age','Age','trim|required|numeric|greater_than[15]'); 
            if($postData['isPayment'] == '1'){
                $this->form_validation->set_rules('price','Price','trim|required|numeric'); 
                $this->form_validation->set_rules('duration','Duration','trim|required|numeric'); 
                $this->form_validation->set_rules('distance','Distance','trim|required|numeric');
                $this->form_validation->set_rules('paymentstatus','Payment Status','trim|required|numeric');
                $this->form_validation->set_rules('transactionId','Transaction Id','trim|required');
                $this->form_validation->set_rules('transactionTimeStamp','Transaction Time Status','trim|required');
            }
            $image = $video = '';
            
           
            if($this->form_validation->run()!=FALSE){
                
                if(!empty($_FILES['eventImage']) && $_FILES['eventImage']['size'] > 0){
                
               $config['upload_path'] = FCPATH.'uploads/eventImage/';
             
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                //$config['max_size'] = 800;
                $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config); 
                $this->upload->initialize($config);           
                if($this->upload->do_upload('eventImage')){
                    $image1 =$this->upload->data()['file_name'] ;
                    $image = base_url().'uploads/eventImage/'. $image1; 
                    chmod($this->upload->data()['full_path'],0777);
                    $config['upload_path'] = '';
                    $config['allowed_types'] = '';
                    $config['file_name'] = '';                             
                }else   {
                    $error = array('error' => $this->upload->display_errors());
                    echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
               }
            }

            if(!empty($_FILES['eventVideo']) && $_FILES['eventVideo']['size'] > 0){
               
               $config['upload_path'] = FCPATH.'uploads/eventVedio/';
             
                $config['allowed_types'] = 'mp4|3gp|flv|webm|wmv|mov';
                //$config['max_size'] = 800;
                $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);            
                if($this->upload->do_upload('eventVideo')){
                    $video1 =$this->upload->data()['file_name'] ;
                    $video = base_url().'uploads/eventVedio/'. $video1; 
                    chmod($this->upload->data()['full_path'],0777);
                }else   {

                    $error = array('error' => $this->upload->display_errors());
                    echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
               }
            }

            if(!empty($_FILES['eventVediothumbnail']) && $_FILES['eventVediothumbnail']['size'] > 0){
               
               $config['upload_path'] = FCPATH.'uploads/eventVediothumbnail/';
             
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                //$config['max_size'] = 800;
                $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);            
                if($this->upload->do_upload('eventVediothumbnail')){
                    $videothumbnailfilename =$this->upload->data()['file_name'] ;
                    $videothumbnail = base_url().'uploads/eventVediothumbnail/'. $videothumbnailfilename; 
                    chmod($this->upload->data()['full_path'],0777);
                }else   {
                    $error = array('error' => $this->upload->display_errors());
                    echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
               }
            }
            $currDate = strtotime(date("Y-m-d H:i:s"));
            $eventStartDateTime = strtotime(date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime'])));
            $eventEndDateIme    = strtotime(date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime'])));
            // check event datetime greater than current datetime 
            if($currDate > $eventStartDateTime) {
                echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event start date and time should be more than current date and time" )); die;
            }
            //  check event start  datetime  greter than enddatetime
            if($eventStartDateTime >= $eventEndDateIme){
                echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event end date and time should be more than  start date and time" )); die;   
            }  

            if(empty($image) &&  empty($video)){
                echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Upload event image or event video." )); die;
            }

               
                $insert_data['userID']          = $postData['userId'];
                $insert_data['userType']        = $postData['userType'];
                $insert_data['longitude']       = $postData['longitude'];
                $insert_data['latitude']        = $postData['latitude'];
                $insert_data['eventName']       = $postData['eventname'];
                $insert_data['eventType']       = $postData['eventType'];
                $insert_data['location']        = $postData['location'];
                $insert_data['startDateTime']   = date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime']));
                $insert_data['status']          = '2';
                $insert_data['endDateTime']     = date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime']));
                $insert_data['about']           = $postData['about']; 
                $insert_data['createdOn']       = date('Y-m-d H:i:s');
                $insert_data['eventPictureURL'] = $image;
                $insert_data['eventVideo']      = $video;
                $insert_data['festival']        = $postData['festival'];
                $insert_data['age']             = $postData['age'];
                $insert_data['eventVediothumbnail'] = $videothumbnail;
                $insert_data['distance']        = $postData['distance'] ? $postData['distance'] :'';
                $insert_data['price']           = $postData['price'] ? $postData['price'] : 0.00;
                $insert_data['period']          = $postData['duration'] ? $postData['duration'] : '';
                $insert_data['isSpotList']      = '1';
                $chID  = $this->common->insert('event',$insert_data);
                
                if(!empty($chID)){

                    if($postData['isPayment'] == '1'){
                        $orderEvent['eventId']              = $chID;
                        $orderEvent['userID']               = $postData['userId'];
                        $orderEvent['eventTitle']           = $postData['eventname'];
                        $orderEvent['location']             = $postData['location'];
                        $orderEvent['paymentstatus']        = $postData['paymentstatus']; 
                        $orderEvent['amount']               = $postData['price'] ? $postData['price'] : 0.00;
                        $orderEvent['transactionId']        = $postData['transactionId']; 
                        $orderEvent['transactiontimeStamp'] = date("Y-m-d H:i:s",strtotime($postData['transactionTimeStamp']));
                        $orderEvent['createdOn']            = date("Y-m-d H:i:s");
                        $orderEvent['paymentMode']          = 'paypal';

                        $orderID  = $this->common->insert('eventOrder',$orderEvent);

                        if($postData['paymentstatus'] == '2'){
                            $isSpotList = '2'; 
                            $this->common->update('event',array("eventId" => $chID), array("isSpotList" =>$isSpotList ) );
                        }
                    }


                    $this->message = "Business event created successfully.";
                    $this->status = "success";

                } else{
                    $this->message = 'Invalid data.';         
                }

                
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('userType'))
                    $this->message =  form_error ('userType');
                else if(form_error('eventname'))
                    $this->message =  form_error ('eventname');
                else if(form_error('location'))
                    $this->message =  form_error ('location');
                else if(form_error('latitude'))
                    $this->message =  form_error ('latitude');
                else if(form_error('longitude'))
                    $this->message =  form_error ('longitude');
                else if(form_error('startdate'))
                    $this->message =  form_error ('startdate');
                else if(form_error('starttime'))
                    $this->message =  form_error ('starttime');
                else if(form_error('enddate'))
                    $this->message =  form_error ('enddate');
                else if(form_error('endtime'))
                    $this->message =  form_error ('endtime');
                else if(form_error('about'))
                    $this->message =  form_error ('about');
                else if(form_error('festival'))
                    $this->message =  form_error ('festival');
                else if(form_error('age'))
                    $this->message =  form_error ('age');
                else if(form_error('price'))
                    $this->message =  form_error ('price');
                else if(form_error('duration'))
                    $this->message =  form_error ('duration');
                else if(form_error('distance'))
                    $this->message =  form_error ('distance');
                else if(form_error('paymentstatus'))
                    $this->message =  form_error ('paymentstatus');
                else if(form_error('transactionId'))
                    $this->message =  form_error ('transactionId');
                else if(form_error('transactionTimeStamp'))
                    $this->message =  form_error ('transactionTimeStamp');                
            }
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status,  'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }
    /*
    * @description : This function developed for festival Type list
    * @Method name: festivalType
    */
    public function festivalList() { 
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
            
            $eventType = $this->common->_getList("festival",""," festivalId DESC");
            
            $DataArr = $eventType;
            $this->status    = "success";
            $this->message   = "Festival type list.";
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed for serach friend
    * @Method name: festivalSearch
    */
    public function searchFriend() { 
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('userType','User Type','trim|required|numeric');
            if($this->form_validation->run()!=FALSE){
               if($postData['userType'] == "2"){
                    $usertype   =   "AND userType='2'";
                    $orderby    =   " businessName ASC";
               }else {
                    $usertype   =   "AND userType='1'";     
                    $orderby    =   " fullName ASC";                
               }
               $serachList = $this->common->_getList("user"," (fullName LIKE '%".$postData['keyword']."%' || businessName LIKE '%".$postData['keyword']."%')  ". $usertype." AND userID !=".$postData['userId']."  AND `userID` not in ( SELECT friendId FROM `friendInvite` WHERE userId =".$postData['userId']."  ) "," ".$orderby."");
                if(!empty($serachList)){
                    $DataArr = $serachList;
                    $this->status    = "success";
                    $this->message   = "Search friend list.";    
                } else {
                    $this->message   = "No search result found.";    
                }
            }else{
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('userType'))
                    $this->message =  form_error ('userType');
            }
            
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed for ivite friend
    * @Method name: inviteFriend
    */
    public function inviteFriend() { 
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('inviteId','Invite Id','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $insert_data['userId']         = $postData['userId'];
                $insert_data['friendId']       = $postData['inviteId'];
                $insert_data['status']         = '1';
                $insert_data['createdOn']      = date('Y-m-d H:i:s');

                $condition  = array('userId'=>$postData['userId'],'friendId'=>$postData['inviteId']);
                $inviteData = $this->common->check_by_id('friendInvite',$condition);

                if(empty($inviteData)){ 
                    $chID  = $this->common->insert('friendInvite',$insert_data);
                     
                    if(!empty($chID)){
                    $this->message = "You have invited your friend successfully.";
                    $this->status = "success";
                    } else{
                        $this->message = 'Invalid data.';         
                    }
                } else {
                        $this->message = 'You have already sent invitation to this user.'; 
                }

            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('inviteId'))
                    $this->message =  form_error ('inviteId');
            }
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed for ivite friend
    * @Method name: inviteFriend
    */
    public function removeFriend() { 
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('friendId','Invite Id','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                
                $condition = array('userId'=>$postData['userId'] , 'friendId'=>$postData['friendId']);
                $_deleteStatus = $this->common->delete_by_id('friendInvite',$condition);
                if($_deleteStatus > 0){ 
                        $this->message = "Friend removed successfully.";
                        $this->status = "success";
                } else {
                        $this->message = 'Invalid User.'; 
                }

            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('inviteId'))
                    $this->message =  form_error ('inviteId');
            }
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed for update user  profile
    * @Method name: updateProfile
    */
    public function updateProfile() { 
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userType','User Type','trim|required|numeric');
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('nickname','Nick name','trim|required');
            //$this->form_validation->set_rules('name','Name','trim|required');            
            //$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phoneNumber','Phone Number','trim|required|numeric|min_length[5]|max_length[12]');

            if($this->form_validation->run()!=FALSE){
                /*if($postData['userType'] == '1'){
                    if(empty($postData['name'])){
                         echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => 'Full name field should not be empty.' )); die;
                    } 
                } else if($postData['userType'] == '2' ){                
                    if(empty($postData['businessName'])){
                         echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => 'Business name field should not be empty.' )); die;
                    } 
                } else*/
                

                $checkUniqueNickname = $this->common->_getById('user',array("nickname" => $postData['nickname'],"userID !=" => $postData['userId'] ));
                if(!empty($checkUniqueNickname)){
                    echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => 'This nickname already in use.' )); die;  
                }                
                
                if(!empty($_FILES['profilepic']) && $_FILES['profilepic']['size'] > 0){
                
                    $config['upload_path'] = FCPATH.'uploads/profileImage/';
                 
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                    $config['file_name'] = $new_name;
                    $this->load->library('upload', $config);            
                    if($this->upload->do_upload('profilepic')){
                        $image1 =$this->upload->data()['file_name'] ;
                        $image = base_url().'uploads/profileImage/'. $image1; 
                        chmod($this->upload->data()['full_path'],0777);
                        }else   {
                            $error = array('error' => $this->upload->display_errors());
                            echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
                       }
                }
                if($image){
                    $insert_data['profilepic']    = $image;
                }
               /* if($postData['userType'] == '1'){
                    $insert_data['fullName']        = $postData['name'];
                }else {
                    $insert_data['businessName']        = $postData['businessName'];
                }*/
                $insert_data['nickname']           = $postData['nickname'];
                $insert_data['phoneNumber']     = $postData['phoneNumber'];
                $insert_data['updatedOn']       = date('Y-m-d H:i:s');

                $condition      = array('userId'=>$postData['userId']);
                $_updateStatus  = $this->common->update('user',$condition, $insert_data );
                if($_updateStatus > 0){ 
                        $DataArr = $this->common->_getById('user',array("userID" => $postData['userId']));
                        $this->message = "Your profile updated successfully.";
                        $this->status = "success";
                } else {
                        $this->message = 'Invalid User.'; 
                }
                
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('userType'))
                    $this->message =  form_error ('userType');
                else if(form_error('nickname'))
                    $this->message =  form_error ('nickname');
                else if(form_error('phoneNumber'))
                    $this->message =  form_error ('phoneNumber');
            }
        }else{
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed for update user  profile
    * @Method name: friendlist
    */
    public function friendlist() { 
        $DataArr    = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
            
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            if($this->form_validation->run() !=FALSE){
                if(isset($postData['pageNum']) && $postData['pageNum']){
                    $pageNum = $postData['pageNum'] - 1;
                } else {
                    $pageNum = 0;
                }
                     
                $offset = $pageNum * TOTALRECORDS; 
                $friendlistData = $this->user_model->_getFriendList($postData['userId'] , array('limit'=>TOTALRECORDS,'offset'=>$offset));
                    if(!empty($friendlistData)){
                    foreach($friendlistData as $friendValue)  {  
                        $returnArr[] = array(
                            'userId'        => $friendValue['userId'],
                            'friendId'      => $friendValue['friendId'],
                            'fullName'      => $friendValue['fullName'],
                            'gender'        => $friendValue['gender'],
                            'email'         => $friendValue['email'],
                            'status'         => $friendValue['status'],
                            'profilepic'    => $friendValue['profilepic']);
                        }
                    $DataArr =  $returnArr;
                    $this->status    = "success";
                    $this->message   = "Friend list.";
                }else{
                    $this->message   = "No record found.";
                }
            }else{
                if(form_error('userId'))
                $this->message =  form_error ('userId');
            }
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        } 
    }
    /*
    * @description : This function developed for accept user  invitation
    * @Method name: acceptFriendInvitation
    */
    public function acceptFriendInvitation() { 
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('friendId','Friend Id','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $condition  = array('userId'=>$postData['userId'],'friendId'=>$postData['friendId']);
                $_updateStatus  = $this->common->update('friendInvite',$condition, array('status' => '2') );

                if($_updateStatus > 0){ 
                    $this->message  = "You have accepted your friend invitation successfully.";
                    $this->status   = "success";
                } else {
                    $this->message  = "We can't accept invitation for this friend."; 
                }

            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('inviteId'))
                    $this->message =  form_error ('inviteId');
            }
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed for accept user  invitation
    * @Method name: acceptFriendInvitation
    */
    public function blockUnblockFriend() { 
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('friendId','Friend Id','trim|required|numeric');
            $this->form_validation->set_rules('fstatus','Block / Unblock ','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){

                if($postData['fstatus'] == '4' || $postData['fstatus'] == '2'){
                    if($postData['fstatus'] == '4'){
                        $statusText = 'blocked'; 
                    } else if($postData['fstatus'] == '2'){
                        $statusText = 'un-blocked'; 
                    }
                    $condition  = array('userId'=>$postData['userId'],'friendId'=>$postData['friendId']);
                    $_updateStatus  = $this->common->update('friendInvite',$condition, array('status' => $postData['fstatus']) );

                    if($_updateStatus > 0){ 
                        $this->message  = "You have ".$statusText." this friend successfully.";
                        $this->status   = "success";
                    } else {
                        $this->message  = "We can't block/unblock this user."; 
                    }
                } else {
                    $this->message  = "Please provide valid block/unblock status."; 
                }   

            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('inviteId'))
                    $this->message =  form_error ('inviteId');
                else if(form_error('fstatus'))
                    $this->message =  form_error ('fstatus');
            }
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to update user password
    * @Method name : updatePassword
    */
    public function updatePassword() {
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('oldPassword','Old Password','trim|required|numeric');
            $this->form_validation->set_rules('newPassword','New Password','trim|required|numeric|min_length[6]');

            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                if(!empty($userData)){
                   $checkPassword = $this->common->_getById('user',array("userID" => $postData['userId'] , 'password' => md5($postData['oldPassword'])));
                   if(!empty($checkPassword)){
                        $_updateStatus  = $this->common->update('user',array("userID" => $postData['userId']), array('password' => md5($postData['newPassword'])));
                        $this->message  = "Password has been updated successfully.";
                        $this->status   = "success";
                   }else{
                        $this->message = 'Old password didn,t match.';
                   }
                }else{
                    $this->message = 'Invalid user.';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('oldPassword'))
                    $this->message =  form_error ('oldPassword');
                else if(form_error('newPassword'))
                    $this->message =  form_error ('newPassword');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to list of event on home sreen
    * @Method name : GetEventsForHomePage
    */
    public function GetEventsForHomePage() {
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('latitude','Latitude','trim|required|numeric');
            $this->form_validation->set_rules('longitude','Longitude','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData   = $this->common->_getById('user',array("userID" => $postData['userId']));
                $homelist   = array();
                $longitude  = $postData['longitude'];
                $latitute   = $postData['latitude'];
                if(!empty($userData)){
                    /*
                    *   get latest 3 business spolist in the 20km area
                    */
                    $spotlistSql = "SELECT *, getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude) AS distancefromMycurrentLocationInKm , NOW() as currentdate FROM event WHERE isSpotList = '2' AND userType = '2' AND  ( NOW() < DATE_ADD(createdOn,INTERVAL period MONTH) ) AND (getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude)  <= ".RANGELIST.") ORDER BY eventId DESC LIMIT 0,3 ;";
                    $spolistData = $this->common->customeQuery($spotlistSql);
                    $homelist['businessSpotlist'] = $spolistData;
                    /*
                    *   get latest 10 friends from
                    */
                    //$friendUserListSql = "SELECT FI.friendInviteId,FI.userId,FI.friendId,FI.status AS friendInvitationStatus,EV.eventId,EV.eventName,EV.longitude,EV.latitude,EV.location,EV.startDateTime,EV.endDateTime,EV.about,EV.status,EV.createdOn,EV.updatedOn,EV.eventPictureURL,EV.eventVideo,EV.userId as eventUserID,EV.age as eventUserAge,EV.userType as eventUserType,EV.avgRating,EV.totalLikes,UR.fullName,UR.gender,UR.age as userAge,UR.profilepic,UR.phoneNumber,UR.eventType,UR.email FROM friendInvite FI LEFT JOIN (  SELECT t1.*     FROM event t1 WHERE t1.createdOn = (SELECT MAX(t2.createdOn) FROM event t2  WHERE t2.userId = t1.userId)) EV ON EV.userId = FI.friendId  LEFT JOIN `user`  UR  ON UR.userID = FI.friendId WHERE FI.userId = ".$postData['userId']." AND FI.status = 2 LIMIT 0,10";
                    $friendUserListSql = "SELECT FI.friendInviteId, FI.userId,FI.friendId,FI.status AS friendInvitationStatus,UR.fullName,UR.gender,UR.age as userAge,UR.profilepic,UR.phoneNumber,UR.eventType,UR.email,EA.eventAttendingId,EA.eventId,EA.createdOn as friendAttendedOn,EV.eventId,EV.eventName,EV.longitude,EV.latitude,EV.location,EV.startDateTime,EV.endDateTime,EV.about,EV.status,EV.createdOn,EV.updatedOn,EV.eventPictureURL,EV.eventVideo,EV.eventVediothumbnail,EV.userId as eventUserID,EV.age as eventUserAge,EV.userType as eventUserType,EV.avgRating,EV.totalLikes FROM friendInvite FI LEFT JOIN `user` UR ON UR.userID = FI.friendId LEFT JOIN (SELECT t1.* FROM `eventAttending` t1 WHERE t1.createdOn = (SELECT MAX(t2.createdOn) FROM `eventAttending` t2 WHERE t2.userId = t1.userId)) as  EA ON EA.userID = FI.friendId RIGHT JOIN `event` EV ON EV.eventId = EA.eventId WHERE FI.userId = ".$postData['userId']." AND FI.status = '2' LIMIT 0,10";



                    $friendEventData = $this->common->customeQuery($friendUserListSql);
                    $homelist['friendEvent'] = $friendEventData;
                    /*
                    *   get latest 10 with no spotlist
                    */
                    //$nonSpotlistSql = "SELECT *, getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude) AS distancefromMycurrentLocationInKm , NOW() as currentdate FROM event WHERE isSpotList = '1' AND userType = '2' AND  ( startDateTime < NOW() AND endDateTime > NOW() )  AND (getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude)  <= ".RANGELIST.") ORDER BY eventId DESC LIMIT 0,10;" ;

                    $nonSpotlistSql = "SELECT *, getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude) AS distancefromMycurrentLocationInKm , NOW() as currentdate FROM event WHERE isSpotList != '2'  AND  endDateTime > NOW()   AND (getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude)  <= ".RANGELIST.") ORDER BY eventId DESC LIMIT 0,10;" ;
                 
                    $nonSpolistData = $this->common->customeQuery($nonSpotlistSql);
                    $homelist['businessNonSpotlist'] = $nonSpolistData;
                    // get follwerList if usertype is business
                    if($userData['userType'] == '2'){   
                         $follwerlistSql = "SELECT follow.followId, follow.businessUserId, follow.followingUserId, user.fullName, user.nickname, user.businessName, user.gender, user.age, user.userType, user.profilepic, user.phoneNumber FROM businessUserFollow follow LEFT JOIN `user` ON user.userID = follow.followingUserId WHERE follow.businessUserId = ".$postData['userId']." ORDER BY follow.followId DESC LIMIT 0,10;" ;
                 
                        $follwerlistData = $this->common->customeQuery($follwerlistSql);
                        $homelist['followerlist'] = $follwerlistData;
                    }

                    if(!empty($homelist)){
                        $DataArr= $homelist;
                        $this->message = "Home Event List";
                        $this->status = "success";
                    }else {
                        $this->message = "No record found.";
                    }
                    
                }else{
                    $this->message = 'Invalid user.';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('latitude'))
                    $this->message =  form_error ('latitude');
                else if(form_error('longitude'))
                    $this->message =  form_error ('longitude');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to event attending
    * @Method name : eventAttend
    */
    public function eventAttend(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $insert_data['userId']         = $postData['userId'];
                $insert_data['eventId']       = $postData['eventId'];
                $insert_data['createdOn']      = date('Y-m-d H:i:s');

                $condition  = array('userId'=>$postData['userId'],'eventId'=>$postData['eventId']);
                $attendData = $this->common->check_by_id('eventAttending',$condition);

                if(empty($attendData)){ 
                    $chID  = $this->common->insert('eventAttending',$insert_data);
                     
                    if(!empty($chID)){
                    $this->message = "You are attending this event.";
                    $this->status = "success";
                    } else{
                        $this->message = 'Invalid data.';         
                    }
                } else {
                        $this->message = 'You have already attending this event.'; 
                }

            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('eventId'))
                    $this->message =  form_error ('eventId');
            }
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }
    /*
    * @description : This function developed to event attending
    * @Method name : eventAttend
    */
    public function eventComment(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');
            $this->form_validation->set_rules('comment','Event Comment','trim|required|min_length[4]');
            

            if($this->form_validation->run()!=FALSE){
                $insert_data['userId']      = $postData['userId'];
                $insert_data['eventId']     = $postData['eventId'];
                $insert_data['comment']     = $postData['comment']; 
                $insert_data['createdOn']   = date('Y-m-d H:i:s');
                $chID  = $this->common->insert('eventComment',$insert_data);
                 
                if(!empty($chID)){
                $this->message = "You have success commented this event.";
                $this->status = "success";
                } else{
                    $this->message = 'Invalid data.';         
                }
               

            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('eventId'))
                    $this->message =  form_error ('eventId');
                else if(form_error('comment'))
                    $this->message = form_error('comment');
            }   
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }
    /*
    * @description : This function developed to event businesSpotlistsEvent
    * @Method name : businesSpotlistsEvent
    */
    public function businesSpotlistsEvent() {
        $DataArr   = array();
        $totalEvent = 0;
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('latitude','Latitude','trim|required|numeric');
            $this->form_validation->set_rules('longitude','Longitude','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData   = $this->common->_getById('user',array("userID" => $postData['userId']));
                $homelist   = array();
                $longitude  = $postData['longitude'];
                $latitute   = $postData['latitude'];
                
                if(!empty($userData)){

                    if(isset($postData['pageNum']) && $postData['pageNum']){
                    $pageNum = $postData['pageNum'] - 1;
                    } else {
                        $pageNum = 0;
                    }
                    $offset = $pageNum * TOTALRECORDS; 
                    
                    $spotlistSql = "SELECT *, getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude) AS   distancefromMycurrentLocationInKm , NOW() as currentdate , (select count(userId) from eventAttending WHERE `eventAttending`.eventId = `event`.eventId) as totalUserAttending   FROM event WHERE isSpotList = '2' AND userType = '2' AND ( NOW() < DATE_ADD(createdOn,INTERVAL period MONTH) ) AND (getDistanceBetweenCoordinates(".$latitute.", ".$longitude.", latitude, longitude) <= 20) ORDER BY eventId DESC LIMIT ".$offset.",".TOTALRECORDS." ;";
                    $spolistData = $this->common->customeQuery($spotlistSql);
                    foreach ($spolistData as $k => $spolistValue){
                        $spolistData[$k]['timeago'] =  $this->common->getTimeDifference($spolistValue['createdOn']);
                    }
                    if(!empty($spolistData)){
                        $DataArr= $spolistData;
                        $this->message = "Business Spotlist Event List";
                        $this->status = "success";
                    }else {
                        $this->message = "No record found.";
                    }
                    
                }else{
                    $this->message = 'Invalid user.';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('latitude'))
                    $this->message =  form_error ('latitude');
                else if(form_error('longitude'))
                    $this->message =  form_error ('longitude');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status,  'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status,  'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to event businesSpotlistsEvent
    * @Method name : businesSpotlistsEvent
    */
    public function eventDetail() {
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            if($this->form_validation->run()!=FALSE){
                $userData   = $this->common->_getById('event',array("eventId" => $postData['eventId']));
              
                if(!empty($userData)){
                    $eventDetailsArr = array();

                    // event details

                    $eventDesciptionSql  ="SELECT ev.*,ev.age as eventAge,user.fullName, user.nickname, user.businessName,  user.gender, user.age, user.userType, user.profilepic, user.phoneNumber, user.eventType, user.email, IF((SELECT count(eventId)  FROM `eventAttending` where `userId`= ".$postData['userId']." and `eventId`=  ".$postData['eventId']." )>0, 1, 0) as isAttending FROM `event` ev LEFT JOIN `user` user ON ev.userId=user.userId WHERE ev.eventId = ".$postData['eventId'].""; 
                    $eventDetailsArr['eventDetail'] =  $this->common->customeQuery($eventDesciptionSql);
                    $eventDetailsArr['eventDetail'][0]['timeago'] =  $this->common->getTimeDifference($eventDetailsArr['eventDetail'][0]['createdOn']);
                    $eventAttendingUserSql = "SELECT ev.eventId as eventId, evAttend.eventAttendingId as eventAttendingId, evAttend.userId as evAttendUserId, user.fullName, user.nickname, user.businessName,  user.gender, user.age, user.userType, user.profilepic, user.phoneNumber, user.eventType, user.email FROM event ev RIGHT JOIN `eventAttending` evAttend  ON evAttend.eventId = ev.eventId LEFT JOIN `user` user ON user.userId = evAttend.userId WHERE ev.eventId= ".$postData['eventId']."";

                    $eventDetailsArr['userAttending'] = $this->common->customeQuery($eventAttendingUserSql);
                    
                    // event comment List

                    $eventcommmentsSql = "SELECT evCmnt.eventCommentId ,evCmnt.userId , evCmnt.comment ,evCmnt.eventId, evCmnt.createdOn, user.fullName, user.nickname, user.businessName, user.gender, user.age, user.userType, user.profilepic, user.phoneNumber, user.eventType, user.email FROM eventComment evCmnt LEFT JOIN `user` user ON evCmnt.userId = user.userID WHERE evCmnt.eventId = ".$postData['eventId']."";

                    $eventDetailsArr['userComments'] = $this->common->customeQuery($eventcommmentsSql);
                    foreach ($eventDetailsArr['userComments'] as $ckey => $cvalue) {
                        $eventDetailsArr['userComments'][$ckey]['timeago'] =  $this->common->getTimeDifference($eventDetailsArr['userComments'][$ckey]['createdOn']);
                    }
                   
                    if(!empty($eventDetailsArr)){
                        $DataArr= $eventDetailsArr;
                        $this->message = "Event Detail";
                        $this->status = "success";
                    }else {
                        $this->message = "No record found.";
                    }
                    
                }else{
                    $this->message = 'Invalid event.';
                }
            } else {
                if(form_error('eventId'))
                    $this->message =  form_error ('eventId'); 
                else  if(form_error('userId'))
                    $this->message =  form_error ('userId');                 
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to event eventAttendingfriendlist
    * @Method name : eventAttendingfriendlist
    */
    public function eventAttendingfriendlist(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData   = $this->common->_getById('event',array("eventId" => $postData['eventId']));
              
                if(!empty($userData)){
                    $eventDetailsArr = array(); 
                    $eventAttendingUserSql = "SELECT evAttend.eventId as eventId, evAttend.userId as userId, user.fullName, user.nickname, user.businessName, user.gender, user.age, user.userType, user.profilepic FROM eventAttending evAttend LEFT JOIN `user` user ON evAttend.userId = user.userId WHERE evAttend.eventId = ".$postData['eventId']." AND user.userId not in (SELECT friendId FROM friendInvite WHERE userId=".$postData['userId']." AND status =4) ";

                    $eventDetailsArr = $this->common->customeQuery($eventAttendingUserSql);
                   
                    if(!empty($eventDetailsArr)){
                        $DataArr= $eventDetailsArr;
                        $this->message = "Attending friend list";
                        $this->status = "success";
                    }else {
                        $this->message = "No record found.";
                    }
                    
                }else{
                    $this->message = 'Invalid event.';
                }
            } else {
                if(form_error('eventId'))
                    $this->message =  form_error ('eventId');                
                if(form_error('friendId'))
                    $this->message =  form_error ('friendId');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        } 
    }
    /*
    * @description : This function developed to event friendeventAttendinglist
    * @Method name : friendeventAttendinglist
    */
    public function friendeventAttendinglist(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('friendId','Friend Id','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData   = $this->common->_getById('user',array("userID" => $postData['friendId']));
              
                if(!empty($userData)){
                    $eventDetailsArr = array(); 
                    $eventAttendingUserSql = "SELECT evAttend.eventId, evAttend.eventAttendingId, evAttend.userId, evAttend.createdOn, ev.* FROM eventAttending evAttend LEFT JOIN `event` ev ON ev.eventId = evAttend.eventId WHERE CASE (ev.isSpotList) WHEN  '2'  THEN     (NOW() < DATE_ADD(ev.createdOn, INTERVAL ev.period MONTH)) ELSE  (ev.startDateTime < NOW() AND ev.endDateTime > NOW()) END AND evAttend.userId = ".$postData['friendId']." ORDER BY evAttend.eventAttendingId DESC";

                    $eventDetailsArr = $this->common->customeQuery($eventAttendingUserSql);
                    foreach ($eventDetailsArr as $k => $eventDetailsValue){
                        $eventDetailsValue[$k]['timeago'] =  $this->common->getTimeDifference($eventDetailsValue['createdOn']);
                    }
                    if(!empty($eventDetailsArr)){
                        $DataArr= $eventDetailsArr;
                        $this->message = "Event List Detail";
                        $this->status = "success";
                    }else {
                        $this->message = "No record found.";
                    }
                    
                }else{
                    $this->message = 'Invalid user.';
                }
            } else {
                if(form_error('friendId'))
                    $this->message =  form_error ('friendId');                
               
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        } 
    }
    /*
    * @description : This function developed to event nonspotlistAndPrivateEventlist
    * @Method name : nonspotlistAndPrivateEventlist
    */
    public function nonspotlistAndPrivateEventlist(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');  
            $this->form_validation->set_rules('duration','Duration','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData   = $this->common->_getById('user',array("userID" => $postData['userId']));
              
                if(!empty($userData)){
                    $eventDetailsArr = array(); 
                    $startDate = $endDate ='';
                    if($postData['duration'] == '1'){
                        $startDate = date("Y-m-d H:i:s" , strtotime("today 00:00:00"));
                        $endDate   = date("Y-m-d H:i:s" , strtotime("today 23:59:59"));
                    } else if($postData['duration'] == '2'){
                        $startDate = date("Y-m-d H:i:s" , strtotime("today 00:00:00"));;
                        $endDate   = date("Y-m-d H:i:s" , strtotime("1 week 23:59:59"));
                    } else if($postData['duration'] == '3'){
                        $startDate = date("Y-m-d H:i:s" , strtotime("today 00:00:00"));;
                        $endDate   = date("Y-m-d H:i:s" , strtotime("1 month 23:59:59"));
                    }
                    //SELECT *, getDistanceBetweenCoordinates(".$postData['latitude'].", ".$postData['longitude'].", latitude, longitude) AS distancefromMycurrentLocationInKm , NOW() as currentdate FROM event WHERE isSpotList != '2'  AND ( startDateTime < NOW() AND endDateTime > NOW() ) AND  userId not in (SELECT friendId FROM friendInvite WHERE `event`.userId=".$postData['userId']." AND status =4) AND createdOn BETWEEN ". $startDate." AND ".$endDate."
                    $eventSql = " CALL getEventListByDurationWithoutDistance(".$postData['userId'].",'". $startDate."','".$endDate."')";
                    //echo $eventSql = "SELECT *, getDistanceBetweenCoordinates(".$postData['latitude'].", ".$postData['longitude'].", latitude, longitude) AS distancefromMycurrentLocationInKm , NOW() as currentdate FROM event WHERE isSpotList != '2'  AND ( startDateTime < NOW() AND endDateTime > NOW() ) AND  userId not in (SELECT friendId FROM friendInvite WHERE `event`.userId=".$postData['userId']." AND status =4) AND createdOn BETWEEN '". $startDate."' AND '".$endDate."'";
                    

                    $eventData = $this->common->customeQuery($eventSql);
                    foreach ($eventData as $k => $eventvalue){
                        $eventData[$k]['timeago'] =  $this->common->getTimeDifference($eventvalue['createdOn']);
                    }
                    if(!empty($eventData)){
                        $DataArr= $eventData;
                        $this->message = "Event List Detail";
                        $this->status = "success";
                    }else {
                        $this->message = "No record found.";
                    }
                    
                }else{
                    $this->message = 'Invalid user.';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                if(form_error('duration'))
                    $this->message =  form_error ('duration');

               
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        } 
    }
    /*
    * @description : This function developed to event blockList
    * @Method name : blockList
    */
    public function blockList(){

        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
              
                $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            if($this->form_validation->run()!=FALSE){
                $blocklistSql = "SELECT friendInv.friendInviteId, friendInv.userId, friendInv.friendId, user.fullName, user.nickname, user.businessName, user.gender, user.age, user.userType, user.profilepic, user.phoneNumber FROM friendInvite friendInv LEFT JOIN `user` ON user.userID = friendInv.friendId WHERE friendInv.userId = ".$postData['userId']." AND friendInv.status ='4';";

                $blocklistArr = $this->common->customeQuery($blocklistSql);
                
                $DataArr = $blocklistArr;
                $this->status    = "success";
                $this->message   = "Blocked user list.";
            }else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');                
               
            }
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to event notificationSetting
    * @Method name : notificationSetting
    */
    public function notificationSetting(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('isGotReachedNotify','Got Reached Notify','trim|required|numeric');
            $this->form_validation->set_rules('isPeopleFollowingYouNotify','People Following You Notify','trim|required|numeric');
            $this->form_validation->set_rules('isEventNearByNotify','Event Near By Notify','trim|required|numeric');
            $this->form_validation->set_rules('isReceivedRequestNotify','Received Request Notify','trim|required|numeric');
            $this->form_validation->set_rules('isInvitationToEventNotify','Invitation To Event Notify','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                if(!empty($userData)){

                    $_updateStatus  = $this->common->update('user',array("userID" => $postData['userId']), array('isGotReachedNotify' => $postData['isGotReachedNotify'] , 'isPeopleFollowingYouNotify' => $postData['isPeopleFollowingYouNotify'] , 'isEventNearByNotify' => $postData['isEventNearByNotify'] , 'isReceivedRequestNotify' => $postData['isReceivedRequestNotify'], 'isInvitationToEventNotify' => $postData['isInvitationToEventNotify']));
                       
                    if($_updateStatus > 0){
                        $DataArr = $this->common->_getById('user',array("userID" => $postData['userId']));
                        $this->message  = "Setting has been updated successfully.";
                        $this->status   = "success";
                    }else{
                        $this->message = 'Invalid data';
                    }
                }else{
                    $this->message = 'Invalid user.';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('isGotReachedNotify'))
                    $this->message =  form_error ('isGotReachedNotify');
                else if(form_error('isPeopleFollowingYouNotify'))
                    $this->message =  form_error ('isPeopleFollowingYouNotify');
                else if(form_error('isEventNearByNotify'))
                    $this->message =  form_error ('isEventNearByNotify');
                else if(form_error('isReceivedRequestNotify'))
                    $this->message =  form_error ('isReceivedRequestNotify');
                else if(form_error(' '))
                    $this->message =  form_error ('isInvitationToEventNotify');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to event followUnfollowFriend
    * @Method name : followUnfollowFriend
    */
    public function followUnfollowFriend(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('businessId','Business Id','trim|required|numeric');
            $this->form_validation->set_rules('fstatus','status','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['businessId']));
                if(!empty($userData && $userData['userType'] == "2")){

                    if($postData['fstatus'] == '1'){
                        $checkfollow = $userData = $this->common->_getById('businessUserFollow',array("followingUserId" => $postData['userId'] , 'businessUserId' => $postData['businessId']));
                        if(empty($checkfollow)){
                            $insert_data['followingUserId']    = $postData['userId'];
                            $insert_data['businessUserId']   = $postData['businessId'];                           
                            $insert_data['createdOn']         = date('Y-m-d H:i:s');
                            $chID  = $this->common->insert('businessUserFollow',$insert_data);
                            $this->message  = "Business followed by you.";
                            $this->status   = "success";
                        } else{
                            $this->message  = "This Business is already followed by you.";    
                        }
                    } else if($postData['fstatus'] == '2'){
                        $condition = array('followingUserId'=>$postData['userId'] , 'businessUserId'=>$postData['businessId']);
                        $_deleteStatus = $this->common->delete_by_id('businessUserFollow',$condition);
                        $this->message  = "Business unfollowed by you.";
                        $this->status   = "success";
                    } else{
                        $this->message  = "Invalid data";
                    }
                    
                }else{
                    $this->message = 'Invalid Business User';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('businessId'))
                    $this->message =  form_error ('businessId');
                else if(form_error('fstatus'))
                    $this->message =  form_error ('fstatus');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to event followlist
    * @Method name : followlist
    */
    public function followlist(){

        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
              
                $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            if($this->form_validation->run()!=FALSE){
                $followerlistSql = "SELECT follow.followId, follow.businessUserId, follow.followingUserId, user.fullName, user.nickname, user.businessName, user.gender, user.age, user.userType, user.profilepic, user.phoneNumber FROM businessUserFollow follow LEFT JOIN `user` ON user.userID = follow.businessUserId WHERE follow.followingUserId = ".$postData['userId']." ORDER BY user.businessName ASC;";

                $followerlistArr = $this->common->customeQuery($followerlistSql);
                if(!empty($followerlistArr)){
                    $DataArr = $followerlistArr;
                    $this->status    = "success";
                    $this->message   = "follower list.";
                }else {
                    $this->message   = "No record found.";
                }
            }else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');                
               
            }
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to eventFriendInvite
    * @Method name : eventFriendInvite
    */
    public function eventFriendInvite(){

        if($this->input->server('REQUEST_METHOD') === "POST") {
            $DataArr   = array();
            $postData  = $this->input->post(); 
              
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('friendId','Friend Id','trim|required|numeric');
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');
            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                if(!empty($userData)){
                    $checkExist = $this->common->_getById('eventInvitation',array("eventId" => $postData['eventId'] , 'invitingUserId' => $postData['userId'] , 'invitedUserId' => $postData['friendId']));
                    if(empty($checkExist)){
                        $insert_data['eventId']         = $postData['eventId'];
                        $insert_data['invitingUserId']  = $postData['userId'];
                        $insert_data['invitedUserId']   = $postData['friendId'];
                        $invite_data['hasAccepted']     = '1';
                        $insert_data['createdOn']       = date('Y-m-d H:i:s');
                        $chID  = $this->common->insert('eventInvitation',$insert_data);
                        $this->message  = "You have send invitation to your friend successfully.";
                        $this->status   = "success";
                    } else{
                        $this->message  = "You have already send invitation to your friend.";    
                    }
                }else{
                    $this->message = 'Invalid User';
                }
            }else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('friendId')) {
                    $this->message =  form_error ('friendId'); 
                } else if(form_error('eventId')) {
                    $this->message =  form_error ('eventId'); 
                }
               
            }
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to acceptEventInvite
    * @Method name : acceptEventInvite
    */
    public function acceptEventInvite(){       
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('eventInvitationId','Event Invitation Id','trim|required|numeric');
           
            if($this->form_validation->run()!=FALSE){
                $checkInviteData = $this->common->_getById('eventInvitation',array("eventInvitationId" => $postData['eventInvitationId']));
                if(!empty($checkInviteData)){
                   
                  
                        $_updateStatus  = $this->common->update('eventInvitation',array("eventInvitationId" => $postData['eventInvitationId']), array('hasAccepted' => '2'));
                        if($_updateStatus > 0){
                            

                            $insert_data['userId']          = $checkInviteData['invitedUserId'];
                            $insert_data['eventId']         = $checkInviteData['eventId'];
                            $insert_data['createdOn']       = date('Y-m-d H:i:s');
                            $insert_data['eventInvitationId']= $checkInviteData['invitingUserId'];
                            $condition  = array('userId'=>$checkInviteData['invitedUserId'],'eventId'=>$checkInviteData['eventId']);
                            $attendData = $this->common->check_by_id('eventAttending',$condition);                           
                            if(empty($attendData)){ 
                                $chID  = $this->common->insert('eventAttending',$insert_data);
                                 
                                if(!empty($chID)){
                                $this->message = "You are attending this event.";
                                $this->status = "success";
                                } else{
                                    $this->message = 'Invalid data.';         
                                }
                            } else {
                                    $this->message = 'You have already attending this event.'; 
                            }
                        } else {
                            $this->message  = "Invitation can't be approved";
                        }
                }else{
                    $this->message = 'Invalid Invitation.';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('eventInvitationId');           
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }   
    }
    /*
    * @description : This function developed to editPrivateEvent
    * @Method name : editPrivateEvent
    */
    public function editPrivateEvent(){

        $DataArr   = array(); 
        $postData  = $this->input->post(); 
        if($this->input->server('REQUEST_METHOD') === "POST"){
                $this->form_validation->set_rules('eventId','Event ID','trim|required|numeric');  
                $this->form_validation->set_rules('eventname','Event Name','trim|required');  
                $this->form_validation->set_rules('location','Location','trim|required');  
                $this->form_validation->set_rules('latitude','Latitude','trim|required');  
                $this->form_validation->set_rules('longitude','Longitude','trim|required');  
                $this->form_validation->set_rules('startdate','Start Date','trim|required');  
                $this->form_validation->set_rules('starttime','Start Time','trim|required');  
                $this->form_validation->set_rules('enddate','End Date','trim|required');  
                $this->form_validation->set_rules('endtime','End Time','trim|required'); 
                $this->form_validation->set_rules('about','About Event','trim|required'); 
                
                

                if($this->form_validation->run()!=FALSE){

                    if(!empty($_FILES['eventImage']) && $_FILES['eventImage']['size'] > 0){
                    
                       $config['upload_path'] = FCPATH.'uploads/eventImage/';
                     
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                       // $config['max_size'] = 800;
                        $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                        $config['file_name'] = $new_name;
                        $this->load->library('upload', $config);            
                        if($this->upload->do_upload('eventImage')){
                            $image1 =$this->upload->data()['file_name'] ;
                            $image = base_url().'uploads/eventImage/'. $image1; 
                            chmod($this->upload->data()['full_path'],0777);
                            $insert_data['eventPictureURL'] = $image;
                        }else   {
                            $error = array('error' => $this->upload->display_errors());
                            echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
                       }
                    }


                    $currDate = strtotime(date("Y-m-d H:i:s"));
                    $eventStartDateTime = strtotime(date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime'])));
                    $eventEndDateIme    = strtotime(date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime'])));
                    // check event datetime greater than current datetime 
                    if($currDate > $eventStartDateTime) {
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event start date and time should be more than current date and time" )); die;
                    }
                    //  check event start  datetime  greter than enddatetime
                    if($eventStartDateTime >= $eventEndDateIme){
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event end date and time should be more than  start date and time" )); die;   
                    }  

                    if(empty($image) &&  empty($video)){
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Upload event image or event video." )); die;
                    }
                     
                        $insert_data['longitude']       = $postData['longitude'];
                        $insert_data['latitude']        = $postData['latitude'];
                        $insert_data['eventName']       = $postData['eventname'];                    
                        $insert_data['location']        = $postData['location'];
                        $insert_data['startDateTime']   = date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime']));
                        $insert_data['status']          = '2';
                        $insert_data['endDateTime']     = date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime']));
                        $insert_data['about']           = $postData['about']; 
                        $insert_data['createdOn']       = date('Y-m-d H:i:s');
                       
                        $chID  = $this->common->update('event',array("eventId" => $postData['eventId']), $insert_data );
                        if($chID > 0){
                            $DataArr = $this->common->_getById('event',array("eventId" => $postData['eventId']));
                            $this->message = "Event updated successfully.";
                            $this->status = "success";
                        } else {
                            $this->message = "Event not updated.";
                        }

                } else {
                    if(form_error('eventId'))
                        $this->message =  form_error ('eventId');
                    else if(form_error('eventname'))
                        $this->message =  form_error ('eventname');
                    else if(form_error('location'))
                        $this->message =  form_error ('location');
                    else if(form_error('latitude'))
                        $this->message =  form_error ('latitude');
                    else if(form_error('longitude'))
                        $this->message =  form_error ('longitude');
                    else if(form_error('startdate'))
                        $this->message =  form_error ('startdate');
                    else if(form_error('starttime'))
                        $this->message =  form_error ('starttime');
                    else if(form_error('enddate'))
                        $this->message =  form_error ('enddate');
                    else if(form_error('endtime'))
                        $this->message =  form_error ('endtime');
                    else if(form_error('about'))
                        $this->message =  form_error ('about');
                }
           
        } else{
            $this->message = "You have entered incorrect otp. Please try again.";
        }

        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status,  'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }
    /*
    * @description : This function developed to editBusinessEvent
    * @Method name : editBusinessEvent
    */
    public function editBusinessEvent(){

        $DataArr   = array(); 
        $postData  = $this->input->post(); 
        if($this->input->server('REQUEST_METHOD') === "POST"){
                $this->form_validation->set_rules('eventId','Event ID','trim|required|numeric');  
                $this->form_validation->set_rules('eventname','Event Name','trim|required');  
                $this->form_validation->set_rules('location','Location','trim|required');  
                $this->form_validation->set_rules('latitude','Latitude','trim|required');  
                $this->form_validation->set_rules('longitude','Longitude','trim|required');  
                $this->form_validation->set_rules('startdate','Start Date','trim|required');  
                $this->form_validation->set_rules('starttime','Start Time','trim|required');  
                $this->form_validation->set_rules('enddate','End Date','trim|required');  
                $this->form_validation->set_rules('endtime','End Time','trim|required'); 
                $this->form_validation->set_rules('about','About Event','trim|required'); 
                $this->form_validation->set_rules('festival','Festival','trim|required'); 
                $this->form_validation->set_rules('age','Age','trim|required|numeric|greater_than[15]'); 
                

                if($this->form_validation->run()!=FALSE){
                    
                    if(!empty($_FILES['eventImage']) && $_FILES['eventImage']['size'] > 0){
                        
                       $config['upload_path'] = FCPATH.'uploads/eventImage/';
                     
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        //$config['max_size'] = 800;
                        $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                        $config['file_name'] = $new_name;
                        $this->load->library('upload', $config);            
                        if($this->upload->do_upload('eventImage')){
                            $image1 =$this->upload->data()['file_name'] ;
                            $image = base_url().'uploads/eventImage/'. $image1; 
                            chmod($this->upload->data()['full_path'],0777);
                            $insert_data['eventPictureURL'] = $image;                        
                        
                        }else   {
                            $error = array('error' => $this->upload->display_errors());
                            echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
                       }
                    }

                    if(!empty($_FILES['eventVideo']) && $_FILES['eventVideo']['size'] > 0){
                        
                       $config['upload_path'] = FCPATH.'uploads/eventVedio/';
                     
                        $config['allowed_types'] = 'mp4|3gp|flv|webm|wmv|mov';
                        //$config['max_size'] = 800;
                        $new_name = md5(strtotime(date('Y-m-d H:i:s')));
                        $config['file_name'] = $new_name;
                        $this->load->library('upload', $config);            
                        if($this->upload->do_upload('eventVideo')){
                            $video1 =$this->upload->data()['file_name'] ;
                            $video = base_url().'uploads/eventVedio/'. $video1; 
                            chmod($this->upload->data()['full_path'],0777);
                            $insert_data['eventVideo']      = $video;
                        }else   {
                            $error = array('error' => $this->upload->display_errors());
                            echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => $error['error'] )); die;
                       }
                    }


                    $currDate = strtotime(date("Y-m-d H:i:s"));
                    $eventStartDateTime = strtotime(date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime'])));
                    $eventEndDateIme    = strtotime(date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime'])));
                    // check event datetime greater than current datetime 
                    if($currDate > $eventStartDateTime) {
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event start date and time should be more than current date and time" )); die;
                    }
                    //  check event start  datetime  greter than enddatetime
                    /*if($eventStartDateTime >= $eventEndDateIme){
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Event end date and time should be more than  start date and time" )); die;   
                    }  

                    if(empty($image) &&  empty($video)){
                        echo json_encode(array("status" => 'failed', 'data' => $DataArr, "message" => "Upload event image or event video." )); die;
                    }*/
                     
                        $insert_data['longitude']       = $postData['longitude'];
                        $insert_data['latitude']        = $postData['latitude'];
                        $insert_data['eventName']       = $postData['eventname'];                    
                        $insert_data['location']        = $postData['location'];
                        $insert_data['startDateTime']   = date("Y-m-d H:i:s", strtotime($postData['startdate'].' '.$postData['starttime']));
                        $insert_data['status']          = '2';
                        $insert_data['endDateTime']     = date("Y-m-d H:i:s", strtotime($postData['enddate'].' '.$postData['endtime']));
                        $insert_data['about']           = $postData['about']; 
                        $insert_data['createdOn']       = date('Y-m-d H:i:s');
                        
                        $insert_data['festival']        = $postData['festival'];
                        $insert_data['age']             = $postData['age'];
                        $chID  = $this->common->update('event',array("eventId" => $postData['eventId']), $insert_data );
                        if($chID > 0){
                            $DataArr = $this->common->_getById('event',array("eventId" => $postData['eventId']));
                            $this->message = "Event updated successfully.";
                            $this->status = "success";
                        } else {
                            $this->message = "Event not updated.";
                        }

                } else {
                    if(form_error('eventId'))
                        $this->message =  form_error ('eventId');
                    else if(form_error('eventname'))
                        $this->message =  form_error ('eventname');
                    else if(form_error('location'))
                        $this->message =  form_error ('location');
                    else if(form_error('latitude'))
                        $this->message =  form_error ('latitude');
                    else if(form_error('longitude'))
                        $this->message =  form_error ('longitude');
                    else if(form_error('startdate'))
                        $this->message =  form_error ('startdate');
                    else if(form_error('starttime'))
                        $this->message =  form_error ('starttime');
                    else if(form_error('enddate'))
                        $this->message =  form_error ('enddate');
                    else if(form_error('endtime'))
                        $this->message =  form_error ('endtime');
                    else if(form_error('about'))
                        $this->message =  form_error ('about');
                    else if(form_error('festival'))
                    $this->message =  form_error ('festival');
                    else if(form_error('age'))
                    $this->message =  form_error ('age');
                }
           
        } else{
            $this->message = "You have entered incorrect otp. Please try again.";
        }

        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status,  'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }
    
    /*
    * @description : This function developed to likeUnlike
    * @Method name : likeUnlike
    */
    public function likeUnlike(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');
            $this->form_validation->set_rules('fstatus','status','trim|required|numeric');

            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                if(!empty($userData)){

                    if($postData['fstatus'] == '1'){
                        $checkfollow  = $this->common->_getById('eventLike',array("userId" => $postData['userId'] , 'eventId' => $postData['eventId']));
                        
                        if(empty($checkfollow)){
                            $insert_data['userId']    = $postData['userId'];
                            $insert_data['eventId']   = $postData['eventId'];                           
                            $insert_data['createdOn'] = date('Y-m-d H:i:s');
                            $chID  = $this->common->insert('eventLike',$insert_data);
                            $this->message  = "Event Liked successfully.";
                            $this->status   = "success";
                        } else{
                            $this->message  = "Event already liked.";    
                        }
                    } else if($postData['fstatus'] == '2'){
                        $condition = array('userId'=>$postData['userId'] , 'eventId'=>$postData['eventId']);
                        $_deleteStatus = $this->common->delete_by_id('eventLike',$condition);
                        $this->message  = "Event unliked successfully.";
                        $this->status   = "success";
                    } else{
                        $this->message  = "Invalid data";
                    }
                    
                }else{
                    $this->message = 'Invalid User';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('businessId'))
                    $this->message =  form_error ('eventId');
                else if(form_error('fstatus'))
                    $this->message =  form_error ('fstatus');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to deleteEvent
    * @Method name : deleteEvent
    */
    public function deleteEvent(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');
            

            if($this->form_validation->run()!=FALSE){
                $userData = $this->common->_getById('user',array("userID" => $postData['userId']));
                if(!empty($userData)){
                   
                    $checkevent = $this->common->_getById('event',array("userId" => $postData['userId'] , 'eventId' => $postData['eventId']));
                    
                    if(!empty($checkevent)){
                        $condition = array('userId'=>$postData['userId'] , 'eventId'=>$postData['eventId']);
                        $_deleteStatus = $this->common->delete_by_id('event',$condition);
                        $this->message  = "Event unliked successfully.";
                        $this->status   = "success";                            
                    } else{
                        $this->message  = "You can't delete this event.";    
                    }
                    
                }else{
                    $this->message = 'Invalid User';
                }
            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('businessId'))
                    $this->message =  form_error ('eventId');
                else if(form_error('fstatus'))
                    $this->message =  form_error ('fstatus');
            }
        }else{
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }  
    }
    /*
    * @description : This function developed to saveEventRating
    * @Method name : saveEventRating
    */
    public function saveEventRating(){
        $DataArr   = array();
        if($this->input->server('REQUEST_METHOD') === "POST") {
            $postData  = $this->input->post(); 
            $this->form_validation->set_rules('userId','User Id','trim|required|numeric');
            $this->form_validation->set_rules('eventId','Event Id','trim|required|numeric');
            $this->form_validation->set_rules('rate','Rating','trim|required|less_than_equal_to[5]');
            

            if($this->form_validation->run()!=FALSE){
                $insert_data['userId']      = $postData['userId'];
                $insert_data['eventId']     = $postData['eventId'];
                $insert_data['rating']      = $postData['rate']; 
                $insert_data['createdOn']   = date('Y-m-d H:i:s');
                $chID  = $this->common->insert('eventUserRating',$insert_data);
                 
                if(!empty($chID)){
                $this->message = "Thank you for rating this event.";
                $this->status = "success";
                } else{
                    $this->message = 'Invalid data.';         
                }
               

            } else {
                if(form_error('userId'))
                    $this->message =  form_error ('userId');
                else if(form_error('eventId'))
                    $this->message =  form_error ('eventId');
                else if(form_error('rate'))
                    $this->message = form_error('rate');
            }   
   
        }else{
            
            
            $this->status = 'fail';
            $this->message = 'Invalid method ';     
            
        }
        $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>$DataArr, 'message' => strip_tags($this->message)));
          if($postData['debug']== "1"){
            $data['jsonData'] = json_encode(array('status' => $this->status, 'data' =>  $DataArr, 'message' => strip_tags($this->message)));
            $this->load->view('webservices/output', $data);  
        }else{
            echo $data['jsonData'];
        }
    }
    
}