<?php
class User extends iframe\DB\DBTable {
	private $sql;
	var $id = 0;
	
	//Configs
	var $cookie_expire = 0;
	var $cookie_prefix_for_site = '';
	
	//The constructor
	//Get the details of the current user on every page load.
	function __construct() {
		$this->sql = iframe\App::$db;
		$config = iframe\App::$config;
		$this->cookie_prefix_for_site = unformat($config['app_name']) . '_';
		$this->cookie_expire = time() + (60*60*24*30);//Will expire in 30 days
		parent::__construct("User");

		if(isset($_SESSION['user_id']) and isset($_SESSION['user_name'])) {
			$this->setCurrentUser($_SESSION['user_id'], $_SESSION['user_name']);
			return; //User logged in already.
		}
		
		//This is a User who have enabled the 'Remember me' Option - so there is a cookie in the users system
		if(isset($_COOKIE[$this->cookie_prefix_for_site.'username']) and $_COOKIE[$this->cookie_prefix_for_site.'username'] and isset($_COOKIE[$this->cookie_prefix_for_site.'password_hash'])) {

			$user_details = $this->sql->getAssoc("SELECT id,name FROM User WHERE username='" . $_COOKIE[$this->cookie_prefix_for_site . 'username'] . "' "
											. " AND MD5(CONCAT(password,'#c*2u!'))='" . $_COOKIE[$this->cookie_prefix_for_site . 'password_hash'] . "'");
		
			if($user_details) { //If it is valid, store it in session
				$this->setCurrentUser($user_details['id'], $_COOKIE[$this->cookie_prefix_for_site . 'username'], $user_details['name']);
				
			} else { //The user details in the cookie is invalid - force a logout to clear cookie
				$this->logout();
			}
		} else {
			unset($_SESSION['user_id']);
			unset($_SESSION['user_name']);
		}
	}

	/**
	 * Login the user with the username and password given as the argument
	 */
	function login($username,$password,$remember=0) {
		$this->id = -1;
		
		$user_details = $this->sql->getAssoc("SELECT id,name FROM User WHERE username='$username' AND password='$password'");
		if(!$user_details) { //Query did not run correctly
			showMessage("Invalid Username/Password", "user/login.php", "error");

		} else {
			//Store the necessy stuff in the sesson
			$this->setCurrentUser($user_details['id'],$username,$user_details['name']);

			//Keep some token in the cookie so as to login the user automatically the next time
			if($remember) {
				setcookie($this->cookie_prefix_for_site . 'username', $username, $this->cookie_expire, '/');
				setcookie($this->cookie_prefix_for_site . 'password_hash', md5($password.'#c*2u!'), $this->cookie_expire,'/');
			}
		}
		
		return $this->id;
	}

	/**
	 * Logout the user. If the user have set the 'remember me' option, that will be reset as well.
	 */
	function logout() {
		$_SESSION['user_id'] = '';
		unset($_SESSION['user_id']);
		
		//Remove the remember me cookies as well.
		if(isset($_COOKIE[$this->cookie_prefix_for_site . 'username']) or isset($_COOKIE[$this->cookie_prefix_for_site . 'password_hash'])) {
			setcookie($this->cookie_prefix_for_site . 'username','',time()-1,'/');
			setcookie($this->cookie_prefix_for_site . 'password_hash','',time()-1,'/');
			unset($_COOKIE[$this->cookie_prefix_for_site . 'username']);
			unset($_COOKIE[$this->cookie_prefix_for_site . 'password_hash']);
		}
	}
	
	/**
	 * Sets the current user.
	 */
	function setCurrentUser($user_id, $username = '', $real_name = '') {
		if($user_id > 0) {
			$_SESSION['user_id'] = $this->id = $user_id;
			$_SESSION['user_name'] = ($real_name) ? $real_name : $username ;
		}
	}
	
	/**
	 * Registers the user with the details provided in the arguments. If the specified username is already taken, an error will be shown.
	 */
	function register($username, $password, $name, $email) {
		global $QUERY;
		
		//Check if the username is already taken.
		$result 	= $this->sql->getSql("SELECT id FROM User WHERE username='$username'");
		$username_taken = $this->sql->fetchNumRows($result);
	
		if ($username_taken == 0) {
			$errors = check(array(
				array('name'=>'username','is'=>'empty'),
				array('name'=>'password','is'=>'empty'),
				array('name'=>'password','is'=>'not','value'=>$_REQUEST['confirm_password'],'error'=>"Password and Confirm password fields don't match"),
				array('name'=>'name','is'=>'empty'),
				array('name'=>'email','is'=>'empty'),
			),2);
			
			if($errors) $QUERY['error'] = $errors;
			else {
				$this->newRow();
				$this->field['username'] = $username;
				$this->field['password'] = $password;
				$this->field['name'] = $name;
				$this->field['email'] = $email;
				$this->field['added_on'] = 'NOW()';
				$id = $this->save();
				$this->setCurrentUser($id, $username, $name);
				return $id;
			}
		} else {
			$QUERY['error'] = "User with username '$username' already exists.";
		}

		return false;
	}
	
	/**
	 * Edits the current user's profile.
	 */
	function update($id, $password, $name, $email, $url) {
		global $QUERY;
		
		$errors = check(array(
			array('name'=>'username','is'=>'empty'),
			array('name'=>'password','is'=>'not','value'=>$_REQUEST['confirm_password'],'error'=>"Password and Confirm password fields don't match"),
			array('name'=>'name','is'=>'empty'),
			array('name'=>'email','is'=>'empty'),
		),2);
			
		if($errors) $QUERY['error'] = $errors;
		else {
			$this->newRow($id);
			if($password) $this->field['password'] = $password;
			$this->field['name'] = $name;
			$this->field['email'] = $email;
	
			return $this->save();
		}
	}
	
	/// Returns the details of the current user as a associative array.
	function getDetails($id = 0) {
		$id = ($id) ? $id : $this->id; 
		return $this->find($id);
	}
	
	/**
	 * Emails the password of the user whose email OR username is provided as the argument. 
	 * The argument must be given as a associative array.  If no such user is found, an error 
	 * will be shown.
	 * Example: $user->passwordRetrival(array('username'=>'binnyva'));
	 * 	OR
	 * 	$user->passwordRetrival(array('email'=>'binnyva@gmail.com'));
	 */
	 function passwordRetrival($data) {
		$config = iframe\App::$config;
	 	
	 	if(isset($data['username'])) {
		 	extract($this->sql->getAssoc("SELECT name,username,password,email FROM User WHERE username='$data[username]'"));
	 	} elseif(isset($data['email'])) {
	 		extract($this->sql->getAssoc("SELECT name,username,password,email FROM User WHERE email='$data[email]'"));
	 	} else {
	 		showMessage("Please provide either the username or the password.", "forgot_password.php", "error");
	 	}
	 	
	 	if(!$username) showMessage("No user found with the given email", "forgot_password.php", "error");
	 	if(!$email) showMessage("The specified account don't have an email address", "forgot_password.php", "error");
	 	
	 	$display_name = ($name) ? $name : $username;
	 	$email_message = "Hi $display_name,

Someone(hopefully you) requested that we send your password to the email you have chosen. 
So here is the login details for your account at $config[site_title]...

Username : $username
Password : $password

Thanks,
$config[site_title] Team";

		mail($email, "Password for $config[site_title] Account", $email_message);
		return true;
	 }
}
