<?php

class User extends ActiveRecord\Model
{
    //callbacks
    static $after_destroy = array('delete_groups_user');

    //relations
    static $has_many = array(
        array('groups', 'through' => 'usersgroups', 'order'=>'id asc'),
        array('usersgroups')
    );

    //validations
    static $validates_presence_of = array(
      array('username', 'message'=>'The name can not be blank.'),
      array('email', 'message'=>'The email can not be blank.'),
      array('first_name', 'message'=>'The first name can not be blank.'),
      array('last_name', 'message'=>'The last name can not be blank.'),
      array('password', 'message'=>'The password can not be blank.')
    );


    static $validates_uniqueness_of = array(
        array('email', 'The email has to be unique'),
        array('username', 'The email has to be unique')
    );


    //methods
    function new_password($plaintext)
    {   
        return self::hash_password($plaintext);
    }


    function hash_password($password)
    {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        $hash = hash('sha256', $salt . $password);

        return $salt . $hash;
    }


    private function validate_password($password)
    {
        $salt = substr($this->password, 0, 64);
        $hash = substr($this->password, 64, 64);

        $password_hash = hash('sha256', $salt . $password);

        return $password_hash == $hash;
    }


    public static function validate_login($username, $password)
    {
        $user = User::find_by_username($username, array('active' => 1));

        if($user && $user->validate_password($password))
        {
            User::login($user->id);

            return $user;
        }
        else
            return FALSE;
    }


    public static function login($user_id)
    {
        $CI =& get_instance();
        $CI->session->set_userdata('user_id', $user_id);
    }


    public static function logout()
    {
        $CI =& get_instance();
        $CI->session->sess_destroy();

        return TRUE;
    }


    /**
     * activate
     *
     * @return void
     * @author Mathew
     **/
    public function activate($id, $code = false)
    {
        if ($code !== FALSE)
        {

            $user = User::find_by_activation_code($code);

            if ( ! $user )
            {
                return FALSE;
            }


            $data = array(
                'activation_code' => NULL,
                'active'          => 1
            );

            $user->update_attributes($data);
        }
        else
        {
            $user = User::find_by_id($id);

            $data = array(
                'activation_code' => NULL,
                'active'          => 1
            );

            $user->update_attributes($data);
        }


        if ( $user->is_valid() )
        {
            return TRUE;
        }
        
        if ( $user->is_invalid() )
        {
            return FALSE;
        }
    }


    /**
     * Deactivate
     *
     * @return void
     * @author Mathew
     **/
    public function deactivate($id = NULL)
    {
        if (!isset($id))
        {
            return FALSE;
        }

        $activation_code       = sha1(md5(microtime()));

        $data = array(
            'active'            =>  0,
            'activation_code'   =>  $activation_code,
        );

        $user = User::find($id);
        $user->update_attributes($data);

        if ($user->is_valid())
        {
            return $activation_code;
        }

        if ($user->is_invalid())
        {
            return FALSE;
        }
            
    }



    /**
     * Insert a forgotten password key.
     *
     * @return bool
     * @author Mathew
     * @updated Ryan
     **/
    public function forgotten_password($identity)
    {
        if (empty($identity))
        {
            return FALSE;
        }

        $key = self::hash_password(microtime().$identity);
                
        $user = User::find_by_email($identity);

        if ($user)
        {
            $data = array(
                'forgotten_password_code' => $key
            );
            
            $user->update_attributes($data);
            
            if ($user->is_valid())
            {
                return $key;
            }   
            
            
            if ($user->in_invalid())
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
     * Forgotten Password Complete
     *
     * @return string
     * @author Mathew
     **/
    public function forgotten_password_complete($code, $salt=FALSE)
    {
        if (empty($code))
        {
            return FALSE;
        }

        $user = User::find_by_forgotten_password_code($code);

        if ($user)
        {
            $password = random_string('alnum', 8);

            $data = array(
                'password'                => self::new_password($password),
                'forgotten_password_code' => NULL,
                'active'                  => 1,
             );

             $user->update_attributes($data);

             if ($user->is_valid())
             {
                return $password;     
             }

             if ($user->is_invalid())
             {
                return false;     
             }
        }

        return FALSE;
    }  
    

    /**
     * Generates a random salt value.
     *
     * @return void
     * @author Mathew
     **/
    public function salt()
    {
        return substr(md5(uniqid(rand(), true)), 0, 16);
    }  


    public function delete_groups_user()
    {
        Usersgroup::delete_all(array('conditions' => array('user_id' => $this->id)));
    }

}
