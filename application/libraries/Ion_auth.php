<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 * @Year            04-March-2016
 * @package         CodeIgniter
 * @subpackage      Auth services
 * @category        Ion Auth
 * @author          TrivialWorks
 * @link            http://trivialworks.com/
 * @Requirements    PHP5 or above
 */

class Ion_auth
{
	/**
	 * account status ('not_activated', etc ...)
	 *
	 * @var string
	 **/
	protected $status;

	/**
	 * extra where
	 *
	 * @var array
	 **/
	public $_extra_where = array();

	/**
	 * extra set
	 *
	 * @var array
	 **/
	public $_extra_set = array();

	/**
	 * __construct
	 *
	 * @return void
	 * @author Trivial
	 **/
	public function __construct()
	{
            
		$this->load->config('ion_auth', TRUE);
		$this->load->library('email');
		$this->load->library('session');
		//$this->lang->load('auth/ion_auth');
                
		$this->load->model('ion_auth_model');
                
		$this->load->helper('cookie');

		// Load IonAuth MongoDB model if it's set to use MongoDB,
		// We assign the model object to "ion_auth_model" variable.
		$this->config->item('use_mongodb', 'ion_auth') ?
			$this->load->model('ion_auth_mongodb_model', 'ion_auth_model') :
			$this->load->model('ion_auth_model');

		//auto-login the user if they are remembered
		if (!$this->logged_in() && get_cookie('identity') && get_cookie('remember_code'))
		{
			$this->ion_auth_model->login_remembered_user();
		}

		$email_config = $this->config->item('email_config', 'ion_auth');

		if (isset($email_config) && is_array($email_config))
		{
			$this->email->initialize($email_config);
		}

		$this->ion_auth_model->trigger_events('library_constructor');
		
		
		define("DEMO", 0);
		
		// Site setting
		$SETTING = $this->ion_auth_model->get_setting();
		define("LOGO", $SETTING->logo);		
		define("SITE_NAME", $SETTING->site_name);
		define("VERSION", $SETTING->version);
		define("THEME", $SETTING->theme);
		if(get_cookie('sma_language')) {
			if(file_exists('language/'.get_cookie('sma_language').'/rest_controller_lang.php') && is_dir('language/'.get_cookie('sma_language'))) {
                        
			$this->lang->load(get_cookie('sma_language'));
			define("LANGUAGE", get_cookie('sma_language'));
			}
		} 
                else {
                   
			$this->lang->load('rest_controller_lang');
			define("LANGUAGE", $SETTING->language);                       
		}
				
		
		if ($this->logged_in()) {
			
		if($df = $this->ion_auth_model->getDateFormat($SETTING->dateformat)) {
			define("JS_DATE", $df->js);
			define("PHP_DATE", $df->php);
			define("MYSQL_DATE", $df->sql);
		} else {
			define("JS_DATE", 'mm-dd-yyyy');
			define("PHP_DATE", 'm-d-Y');
			define("MYSQL_DATE", '%m-%d-%Y');
		}
			
		
		define("CURRENCY_PREFIX", $SETTING->currency_prefix);
		define("NO_OF_ROWS", $SETTING->no_of_rows);
		define("TOTAL_ROWS", $SETTING->total_rows);
		define("ROWS_PER_PAGE", $SETTING->rows_per_page);
		define("RESTRICT_USER", $SETTING->restrict_user);
		define("ALERT_NO", $this->ion_auth_model->get_total_results());
		define("CAL_OPT", $SETTING->restrict_calendar);
                define("BSTATESAVE", 0);
                $user = $this->user()->row();                           
                define("NAME", $user->firstName." ".$user->lastName);
                define("USER_NAME", $user->userName);
                define("USER_ID", $user->userID);
		if ($cal = $this->ion_auth_model->getEvents()) {
			$cal_d = '';
			foreach($cal as $dt) {
					$cal_d .= '<li><a href="#" onClick="javascript:event.preventDefault();" style="cursor:default;"><strong>'.date(PHP_DATE, strtotime($dt->date)).':</strong><br>'.$dt->data.'</span></a></li>';
			}
				
			define("UP_EVENTS", $cal_d);
		} else {
			define("UP_EVENTS", NULL);
		}
			
		}
                define("AUTHOR", 'TRIVIALWORKS');	
				
	}
	

	/**
	 * __call
	 *
	 * Acts as a simple way to call model methods without loads of stupid alias'
	 *
	 **/
	public function __call($method, $arguments)
	{
           
		if (!method_exists( $this->ion_auth_model, $method) )
		{
			throw new Exception('Undefined method Ion_auth::' . $method . '() called');
		}

		return call_user_func_array( array($this->ion_auth_model, $method), $arguments);
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 * I can't remember where I first saw this, so thank you if you are the original author. -Militis
	 *
	 * @access	public
	 * @param	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}


	/**
	 * forgotten password feature
	 *
	 * @return mixed  boolian / array
	 * @author Trivial
	 **/
	public function forgotten_password($identity)    //changed $email to $identity
	{
		if ( $this->ion_auth_model->forgotten_password($identity) )   //changed
		{
			$this->ion_auth_model->forgotten_password($identity);
			// Get user information
			$user = $this->where($this->config->item('identity', 'ion_auth'), $identity)->users()->row();  //changed to get_user_by_identity from email
			
			if ($user)
			{
				$data = array(
					'identity'		=> $user->{$this->config->item('identity', 'ion_auth')},
					'forgotten_password_code' => $user->forgotten_password_code,
                                        'name'  => $user->firstName
				);
				if(!$this->config->item('use_ci_email', 'ion_auth'))
				{
					$this->set_message('forgot_password_successful');
					return $data;
				}
				else
				{
					$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_forgot_password', 'ion_auth'), $data, true);
                                        
					$message = stripslashes(str_replace('{SITE_NAME}',SITE_NAME,$message));
                                        //print_r($message);die;
					$this->email->clear();
					$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
					
					$this->email->to($user->email);
					$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_forgotten_password_subject'));
					$this->email->message($message);
					//echo $this->email->send();
					if ($this->email->send())
					{
						//echo $this->email->print_debugger();die;
						$this->set_message('forgot_password_successful');
						return TRUE;
					}
					else
					{
						$this->set_error('forgot_password_unsuccessful');
						return FALSE;
					}
				}
			}
			else
			{
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}
		else
		{
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * forgotten_password_complete
	 *
	 * @return void
	 * @author Trivial
	 **/
	public function forgotten_password_complete($code)
	{
		$this->ion_auth_model->trigger_events('pre_password_change');

		$identity = $this->config->item('identity', 'ion_auth');
		$profile  = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if (!$profile)
		{
			$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$new_password = $this->ion_auth_model->forgotten_password_complete($code, $profile->salt);

		if ($new_password)
		{
			$data = array(
				'identity'     => $profile->{$identity},
				'new_password' => $new_password
			);
			if(!$this->config->item('use_ci_email', 'ion_auth'))
			{
				$this->set_message('password_change_successful');
				$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
				return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_forgot_password_complete', 'ion_auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
				$this->email->to($profile->email);
				$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_new_password_subject'));
				$this->email->message($message);

				if ($this->email->send())
				{
					$this->set_message('password_change_successful');
					$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
					return TRUE;
				}
				else
				{
					$this->set_error('password_change_unsuccessful');
					$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
					return FALSE;
				}

			}
		}

		$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
		return FALSE;
	}

	/**
	 * forgotten_password_check
	 *
	 * @return void
	 * @author Trivial
	 **/
	public function forgotten_password_check($code)
	{
		$profile = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if (!is_object($profile))
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}
		else
		{
			if ($this->config->item('forgot_password_expiration', 'ion_auth') > 0) {
				//Make sure it isn't expired
				$expiration = $this->config->item('forgot_password_expiration', 'ion_auth');
				if (time() - $profile->forgotten_password_time > $expiration) {
					//it has expired
					$this->clear_forgotten_password_code($code);
					$this->set_error('password_change_unsuccessful');
					return FALSE;
				}
			}
			return $profile;
		}
	}

	/**
	 * register
	 *
	 * @return void
	 * @author Trivial
	 **/
	public function register($username, $password, $email, $additional_data = array(), $group_name = array()) //need to test email activation
	{
		$this->ion_auth_model->trigger_events('pre_account_creation');

		$email_activation = $this->config->item('email_activation', 'ion_auth');

		if (!$email_activation)
		{
			$id = $this->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);
			if ($id !== FALSE)
			{
				$this->set_message('account_creation_successful');
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful'));
				return $id;
			}
			else
			{
				$this->set_error('account_creation_unsuccessful');
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful'));
				return FALSE;
			}
		}
		else
		{
			$id = $this->ion_auth_model->register($username, $password, $email, $additional_data, $group_name);

			if (!$id)
			{
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}

			$deactivate = $this->ion_auth_model->deactivate($id);

			if (!$deactivate)
			{
				$this->set_error('deactivate_unsuccessful');
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful'));
				return FALSE;
			}

			$activation_code = $this->ion_auth_model->activation_code;
			$identity        = $this->config->item('identity', 'ion_auth');
			$user            = $this->ion_auth_model->user($id)->row();

			$data = array(
				'identity'   => $user->{$identity},
				'id'         => $user->id,
				'email'      => $email,
				'activation' => $activation_code,
			);
			if(!$this->config->item('use_ci_email', 'ion_auth'))
			{
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
				$this->set_message('activation_email_successful');
					return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
				$this->email->to($email);
				$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
				$this->email->message($message);

				if ($this->email->send() == TRUE)
				{
					$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
					$this->set_message('activation_email_successful');
					return $id;
				}
			}

			$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful', 'activation_email_unsuccessful'));
			$this->set_error('activation_email_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * logout
	 *
	 * @return void
	 * @author Trivial
	 **/
	public function logout($identity=false, $token=false, $call_from=FALSE)
	{
            if($call_from==='api'){
                $device = $this->ion_auth_model->deleteUserDeviceToken($identity,$token);
            }
            else
            {
		$this->ion_auth_model->trigger_events('logout');
		$identity = $this->config->item('identity', 'ion_auth');
		$this->session->unset_userdata($identity);
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('user_id');
		//delete the remember me cookies if they exist
		if (get_cookie('identity'))
		{
			delete_cookie('identity');
		}
		if (get_cookie('remember_code'))
		{
			delete_cookie('remember_code');
		}
		//Recreate the session
		$this->session->sess_destroy();
		//$this->session->sess_regenerate(true);
            }
            $this->set_message('logout_successful');
            return TRUE;
	}

	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Trivial
	 **/
	public function logged_in()
	{
		$this->ion_auth_model->trigger_events('logged_in');

		$identity = $this->config->item('identity', 'ion_auth');

		return (bool) $this->session->userdata($identity);
	}

	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Trivial
	 **/
	public function is_admin()
	{
		$this->ion_auth_model->trigger_events('is_admin');

		$admin_group = $this->config->item('admin_group', 'ion_auth');

		return $this->in_group($admin_group);
	}

	/**
	 * in_group
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function in_group($check_group, $id=false)
	{
		$this->ion_auth_model->trigger_events('in_group');

		$users_groups = $this->ion_auth_model->get_users_groups($id)->result();
		$groups = array();
		foreach ($users_groups as $group)
		{
			$groups[] = $group->name;
		}

		if (is_array($check_group))
		{
			foreach($check_group as $key => $value)
			{
				if (in_array($value, $groups))
				{
					return TRUE;
				}
			}
		}
		else
		{
			if (in_array($check_group, $groups))
			{
				return TRUE;
			}
		}

		return FALSE;
	}
	
	function formatMoney($number, $fractional=TRUE) { 
		if ($fractional) { 
			$number = sprintf('%.2f', $number); 
		} 
		while (true) { 
			$replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number); 
			if ($replaced != $number) { 
				$number = $replaced; 
			} else { 
				break; 
			} 
		} 
		return $number; 
	} 


	public function clear_tags($str)
	{
		return htmlentities(strip_tags($str, '<code><span><div><label><a><br><p><b><i><del><strike><u><img><video><audio><iframe><object><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><code><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h3><h4><h5><h6>'));
	}
        
        public function fsd($inv_date)
	{
            if(JS_DATE == 'dd-mm-yyyy' || JS_DATE == 'dd/mm/yyyy' || JS_DATE == 'dd.mm.yyyy') {
                    $date = substr($inv_date, -4)."-".substr($inv_date, 3, 2)."-".substr($inv_date, 0, 2); 
            } elseif(JS_DATE == 'mm-dd-yyyy' || JS_DATE == 'mm/dd/yyyy' || JS_DATE == 'mm.dd.yyyy') {
                    $date = substr($inv_date, -4)."-".substr($inv_date, 0, 2)."-".substr($inv_date, 3, 2);
            } else {
                $date = $inv_date;
            }
            return $date;
        }
        
        public function generateOTP($length = 6, $chars = '1234567890') 
        { 
            $chars_length = (strlen($chars) - 1); 
            $string = $chars{rand(0, $chars_length)}; 
            for ($i = 1; $i < $length; $i = strlen($string)) 
            { 
                $r = $chars{rand(0, $chars_length)}; 
                if ($r != $string{$i - 1}) $string .= $r; 
            } 
            return $string;
        }
        
        public function sendSMS($data = array()){
            $this->load->library('curl');
            $url = "SMS_API_URL";
            $this->curl->create($url);
            $data['apiKey'] = 'asdfasdfasdfasdf34535345sdfs3453534vbn';
            $data['username'] = 'USER';
            $data['password'] = 'PASSWORD';
            $data['sender'] = 'SENDER';
            // Optional, delete this line if your API is open
            //$this->curl->http_login($username, $password);
            $this->curl->post($data);
            $result = json_decode($this->curl->execute());          
            if(isset($result->status) && $result->status == 'success')
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        
        public function verifyOTPs($data = array()){
            $userRecord = $this->ion_auth_model->getUserByID($data['userID']);
            if($data['otp']===$userRecord->otp && $this->ion_auth_model->update($data['userID'],array('active' => 1,'otp'=>'')))           
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        
        
}

/* End of file ion_auth.php */ 
/* Location: ./libraries/ion_auth.php */