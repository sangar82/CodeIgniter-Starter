<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Sangar Auth
* Modified for work with php-activerecord
* Author: Sangar
* 		  sangar1982@gmail.com
* 		  @sangar1982
* 
* Name Old:  Ion Auth
*
* Author: Ben Edmunds
*		  ben.edmunds@gmail.com
*         @benedmunds
*
* Added Awesomeness: Phil Sturgeon
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  10.01.2009
*
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
*
* Requirements: PHP5 or above
*
*/

require_once FCPATH.'sparks/php-activerecord/0.0.2/vendor/php-activerecord/ActiveRecord.php'; 

class Sangar_auth
{
	/**
	 * CodeIgniter global
	 *
	 * @var string
	 **/
	protected $ci;

	/**
	 * __construct
	 *
	 * @return void
	 * @author Ben
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('email', array('mailtype' => 'html'));
		$this->ci->load->library('session');
		$this->ci->load->library('encrypt');
		$this->ci->load->helper('cookie');
		$this->ci->load->helper('string');
	}


	public function register($data)
	{
		$email_activation = $this->ci->config->item('email_activation');

		$user = new User($data);
		$user->save();

		// run insert model to write data to db
		if ( $user->is_valid() )
		{
			//add a new users to default group
			$default = $this->ci->config->item('default_group');
			$group_default = Group::find_by_name($default);

			$group = new Usersgroup(array('user_id' => $user->id, 'group_id' => $group_default->id));
			$group->save();

			//watch if there is a email activation
			if ( $email_activation )
			{
				$activation_code = User::deactivate($user->id);

				if (!$activation_code)
				{
					return FALSE;
				}

				$data = array(
					'identity'   => $user->email,
					'id'         => $user->id,
					'email'      => $user->email,
					'password'	 => 'password', 
					'activation' => $activation_code,
				);

				$message = $this->ci->load->view($this->ci->config->item('email_templates').$this->ci->config->item('email_activate'), $data, true);

				$this->ci->email->clear();
				$this->ci->email->set_newline("\r\n");
				$this->ci->email->from($this->ci->config->item('admin_email'), $this->ci->config->item('site_title'));
				$this->ci->email->to($user->email);
				$this->ci->email->subject($this->ci->config->item('site_title') . ' - '. lang('web_auth_act'));
				$this->ci->email->message($message);

				if ($this->ci->email->send() == TRUE)
				{
					return TRUE;
				}
				else
				{
					return FALSE;		
				}
					
			}
			else
			{
				return TRUE;
			}		
		}

		if ( $user->is_invalid() )
		{
			return FALSE;
		}
	}


	public function in_group($check_group, $id=false)
	{

		//if no id was passed use the current users id
		$id || $id = $this->ci->session->userdata('user_id');

		$user = User::find($id);

		$users_groups = $user->groups;

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

		
	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function logged_in()
	{
		return (bool) $this->ci->session->userdata('user_id');
	}


	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function is_admin()
	{
		$admin_group = $this->ci->config->item('admin_group');

		return $this->in_group($admin_group);
	}	


	/**
	 * forgotten password feature
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password($identity)    //changed $email to $identity
	{
		$code = User::forgotten_password($identity);

		if ( $code )
		{
			$user = User::find_by_email($identity);

			if ($user) 
			{
				$data = array(
					'forgotten_password_code' => $code
				);

				$message = $this->ci->load->view($this->ci->config->item('email_templates').$this->ci->config->item('email_forgot_password'), $data, true);
				$this->ci->email->clear();
				$this->ci->email->set_newline("\r\n");
				$this->ci->email->from($this->ci->config->item('admin_email'), $this->ci->config->item('site_title'));
				$this->ci->email->to($user->email);
				$this->ci->email->subject($this->ci->config->item('site_title') . ' - '. lang('web_auth_voc'));
				$this->ci->email->message($message);

				if ($this->ci->email->send())
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}


	/**
	 * forgotten_password_complete
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code)
	{
		$user = User::find_by_forgotten_password_code($code);

		if (!$user)
		{
			return FALSE;
		}

		$new_password = User::forgotten_password_complete($code);

		if ($new_password)
		{
			$data = array(
				'identity'     => $user->email,
				'new_password' => $new_password
			);

			$message = $this->ci->load->view($this->ci->config->item('email_templates').$this->ci->config->item('email_forgot_password_complete'), $data, true);

			$this->ci->email->clear();
			$this->ci->email->set_newline("\r\n");
			$this->ci->email->from($this->ci->config->item('admin_email'), $this->ci->config->item('site_title'));
			$this->ci->email->to($user->email);
			$this->ci->email->subject($this->ci->config->item('site_title') . ' - '. lang('web_auth_np'));
			$this->ci->email->message($message);

			if ($this->ci->email->send())
			{
				return $new_password;
			}
			else
			{
				return FALSE;
			}
		}

		return FALSE;
	}


	private function fingerprint()
	{	
		$fingerprint = $this->ci->input->ip_address();
		$fingerprint .= (isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:'UserAgent';
		
		return md5($fingerprint);
	}


	public function checktime()
	{  
		$time = $this->ci->config->item('user_session_time');

		$actual = mktime();
		
		$last_check = $this->ci->session->userdata('user_time_check');
		
		if ( ($actual - $last_check) > $time )
		{
			$result = FALSE;
		}
		else
		{
			$this->ci->session->set_userdata('user_time_check', $actual);
			$result = TRUE;
		}
		
		return $result;
	}	


	function check()
	{
		$correct = FALSE;
	
		if($this->ci->session->userdata('fingerprint'))
		{
			if( $this->ci->session->userdata('fingerprint') == self::fingerprint())
			{
				$correct = self::checktime();
			}
		}

		return $correct;
	}


	function register_session()
	{
		$this->ci->session->set_userdata('fingerprint', self::fingerprint());
		$this->ci->session->set_userdata('user_time_check', time());
	}



}