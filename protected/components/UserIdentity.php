<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */class UserIdentity extends CUserIdentity
{
    private $_id;
 
    public function authenticate()
    {
        
        $username=strtolower($this->username);
        $userpass= $this->password;
     //   $user = User::model()->findByAttributes(array('username'=>$this->username));
       $user=User::model()->find('LOWER(username)=?',array($username));
   //    $password=User::model()->find('password=?',array($password1));
       if($user===null || $user->password != $userpass )
           echo "wrong username";
//        $this->errorCode=self::ERROR_USERNAME_INVALID;
  //          else if(!$user->validatePassword($this->password))
    //        echo $this->password;
        //echo "wrong pass";
    //      $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
            
            $this->_id=$user->id;
            $this->username=$user->username;
            $this->errorCode=self::ERROR_NONE;
        }
        return $this->errorCode==self::ERROR_NONE;
    }
 
    public function getId()
    {
        return $this->_id;
    }
}
     /*   
          $users=array(
			// username => password
			'demo'=>'demo',
			'admin'=>'admin',
		);
		if(!isset($users[$this->username]))
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		elseif($users[$this->username]!==$this->password)
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
                {
			$this->errorCode=self::ERROR_NONE;
                        return !$this->errorCode;
                }
*/