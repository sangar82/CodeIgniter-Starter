<?php
require_once(APPPATH . '/controllers/test/Toast.php');

class Users_test extends Toast
{

    var $m_all_count;
    var $m_email_user;
    var $m_user_id;


    function __construct() // change constructor method name
    {
        parent::__construct(__FILE__); // Remember this

        $this->load->model('user');
        $this->load->model('group');
        $this->load->model('usersgroup');
        $this->load->library('sangar_auth');
        $this->load->config('sangar_auth');

        $this->m_email_user =   'test@test.com';
        $this->m_password   =   'password'; 

        $this->m_data = array(
                        'username'      =>  'UsernameTest',
                        'email'         =>  $this->m_email_user,
                        'first_name'    =>  'First Name Test',
                        'last_name'     =>  'Last Name Test',
                        'password'      =>  User::new_password($this->m_password)
        );

    }


    /**
     * OPTIONAL; Anything in this function will be run before each test
     * Good for doing cleanup: resetting sessions, renewing objects, etc.
     */
    function _pre() {}

    /**
     * OPTIONAL; Anything in this function will be run after each test
     * I use it for setting $this->message = $this->My_model->getError();
     */
    function _post() {}


    function test_pre_count()
    {   
        $this->m_all_count = User::count();
    }


    function test_create()
    {
        $result = $this->sangar_auth->register($this->m_data);

        $this->_assert_true($result);  
    }


    function test_count_after_create()
    {   
        $this->_assert_equals($this->m_all_count + 1, User::count());
    }


    function test_get_user_id()
    {
        $user = User::find_by_email($this->m_email_user);

        $this->m_user_id = $user->id;

        $this->_assert_true($this->m_user_id);
    }

 
    function test_dont_create_same_email_that_other()
    {
        $data = array(
                        'username'      =>  'UsernameTest2',
                        'email'         =>  $this->m_email_user,
                        'first_name'    =>  'First Name Test2',
                        'last_name'     =>  'Last Name Test2',
                        'password'      =>  User::new_password('password')
        );

        $user = $this->sangar_auth->register($data);



        $this->_assert_false($user) ;
    }

    function test_check_user_state()
    {
        $email_activation = $this->config->item('email_activation');

        $user_state = User::find_by_email($this->m_email_user)->active;

        $this->_assert_not_equals($user_state, $email_activation);
    }


    function test_validate_user()
    {
        $email_activation = $this->config->item('email_activation');

        if ($email_activation)
        {
            $user = User::find_by_email($this->m_email_user);

            $validate = User::activate($user->id, $user->activation_code);

            $this->_assert_true($validate);
        }
    }


    function test_validate_login()
    {
        $login = User::validate_login($this->m_data['username'], $this->m_password);

        $this->_assert_true($login);
    }


    function test_logout()
    {
        $logout = User::logout();

        $this->_assert_true($logout);
    }


    function test_forgotten_password()
    {
        $code = $this->sangar_auth->forgotten_password($this->m_email_user);

        $this->_assert_true($code);
    }


    function test_forgotten_password_complete()
    {
        $user = User::find_by_email($this->m_email_user);

        $new_password = $this->sangar_auth->forgotten_password_complete($user->forgotten_password_code);

        $this->m_password   =   $new_password;

        $this->_assert_true($new_password);
    }


    function test_validate_login_after_forgotten_password()
    {
        $login = User::validate_login($this->m_data['username'], $this->m_password);

        $this->_assert_true($login);
    }


    function test_logout_after_forgotten_password()
    {
        $logout = User::logout();

        $this->_assert_true($logout);
    }


    function test_invalid_login()
    {
        $login = User::validate_login($this->m_data['username'], random_string('alnum', 8));

        $this->_assert_false($login);
    }


    function test_deactivate_user()
    {
        $user = User::find_by_email($this->m_email_user);

        $deactivate = User::deactivate($user->id);

        $this->_assert_true($deactivate);
    }


    function test_activate_user()
    {
        $user = User::find_by_email($this->m_email_user);

        $validate = User::activate($user->id, $user->activation_code);

        $this->_assert_true($validate);        
    }


    function test_is_into_default_group()
    {
        $user = User::find_by_email($this->m_email_user);

        $name_default_group = $this->config->item('default_group');

        foreach ($user->groups as $group)
        {
            $array_groups[] = $group->name;
        }

        $this->_assert_true( in_array($name_default_group,$array_groups));
    }


    function test_delete()
    {
        $user = User::find_by_email($this->m_email_user);

        if ($user)
        {
            if ($user->delete()  == TRUE)
            {
                $this->_assert_true(TRUE);   
            }
            else
            {
                $this->_assert_true(FALSE); 
            }         
        }
        else
            $this->_assert_true(FALSE); 
    }


    function test_delete_groups()
    {
        $groups_user = Usersgroup::find_by_user_id($this->m_user_id);

        $this->_assert_false($groups_user);
    }


    function test_count_after_delete()
    {   
        $this->_assert_equals($this->m_all_count, User::count());
    }

}
    