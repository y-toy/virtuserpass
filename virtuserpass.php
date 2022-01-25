<?php

/**
 * This plugin allows you to login using your favorite username and password to your Roundcube. You will no longer need to use your email address and password to login.
 * This plugin internally converts the username and password used to log in to the email address and the password set in the config file.
 * You can encrypt the passwords in the config file if you want. (Just easy trick, it's better than no security.)
 *
 * - Needed openssl php functions if you want to encrypt passwords in the config file,
 *
 * @license GNU GPLv3+
 * @author Y.Toyama
 */
class virtuserpass extends rcube_plugin {
	private $userPassScramble;
	private $emailPassScramble;
	private $allowEmailLogin;
	private $users;

    /**
     * Plugin initialization
     */
    function init(){

        $RCMAIL = rcmail::get_instance();

		$this->userPassScramble = false;
		$this->emailPassScramble = false;
		$this->allowEmailLogin = false;
		$this->users = null;
		if(!file_exists("./plugins/virtuserpass/config/config.inc.php")){ return; }
		$this->load_config('config/config.inc.php');
		$this->users = $RCMAIL->config->get('virtuserpass');
		$this->userPassScramble = $RCMAIL->config->get('virtuserpass_scramble');
		$this->emailPassScramble = $RCMAIL->config->get('virtuserpass_email_scramble');
		$this->allowEmailLogin = $RCMAIL->config->get('virtuserpass_allow_email_login');

		$this->add_hook('authenticate', array($this, 'authenticate'));

    }

	function authenticate($args){

		$bFound = false;
		if ($this->users != null){
			if (array_key_exists($args['user'], $this->users)) {
				$vupInfo = $this->users[$args['user']];
				$pass = ($this->userPassScramble)?md5($args['pass']):$args['pass'];
				$emailPass = ($this->emailPassScramble)?$this->decrypt($vupInfo[2]):$vupInfo[2];
				if ($pass == $vupInfo[0]){
					$args['user'] = $vupInfo[1];
					$args['pass'] = $emailPass;
					$bFound = true;
				}
			}
		}
		if (!$this->allowEmailLogin && !$bFound){
			$args['pass'] = '';
		}

		return $args;
	}

	function decrypt($data){
		return openssl_decrypt($data, 'AES-128-CBC', 'bT8Xx4sRE4Yr', 0, 'Zn7rAEBWdj3kJVGa');
	}

}
