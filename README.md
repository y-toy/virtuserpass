# Roundcube virtual users and passwords
This plugin allows you to login using your favorite username and password to your Roundcube. You don't need to use your email address and password to login.

[Jpapanese](https://blog.gogodiet.net/itsupport/272/2022/01/24/)

## Install
1. Place this plugin folder into plugins directory of Roundcube
2. Add virtuserpass to $config['plugins'] in your Roundcube config

Ex.
```
cd (your roundcube installed folder)/plugins
git clone https://github.com/y-toy/virtuserpass virtuserpass

vim ../config/config.inc.php
---
# Add "virtuserpass" to $config['plugins']
$config['plugins'] =  array('virtuserpass');
# or
# $config['plugins'][] =  'virtuserpass';
---
```
## Config

Make a config file "config.inc.php" under the "config" folder of this plugin.
A typical example would look like this.
```
// main setting
$config['virtuserpass'] = array(
	'joe'=>array('967Joe123','joe@biggy.co.jp','emailPass1234'),
	'c.becky'=>array('pass1234','c.becky@biggy.co.jp','emailPass5678'),
	'ricky.c'=>array('1234test','ricky@biggy.co.jp','emailPass7777'),
);

// Options
$config['virtuserpass_scramble']=false;
$config['virtuserpass_email_scramble']=false;
$config['virtuserpass_allow_email_login']=true;
```
The above setting has three accounts, "joe", "c.becky" and "ricky.c". "Joe" can use "joe" as your username and "967Joe123" as the password to login for email address 'joe@biggy.co.jp' and the password 'emailPass1234'.

### $config['virtuserpass']
This is accounts setting. Set the user name and password to be used for login and the email information to be converted internally. You are able to set as many as you want.
```
$config['virtuserpass'] = array(
	'username' => array('password', 'email address', 'email password'),
	// and so on...
);
```

### $config['virtuserpass_scramble']
Set this true if you want to hide your login passwords from your config file. You need to set MD5 hash password to "$config['virtuserpass']"'s accountpassowrd. The easy way to set this up, use "makeConfigFileFromCSV.php" tool mentioned below.

### $config['virtuserpass_email_scramble']
Set this true if you want to hide your e-mail passwords from your config file. You need to use "makeConfigFileFromCSV.php" tool to set this up. It is less effective as security, but better than nothing.

### $config['virtuserpass_allow_email_login']
When setting this true, you are able to login with your e-mail address too.
When false, you are not allowed to login with your e-mail address.

## Tool
"makeConfigFileFromCSV.php" is a tool to convert a large number of accounts from a CSV file to config file.

### USAGE
```
php ./makeConfigFileFromCSV.php csv-file-path [1|0] [1|0] [1|0]
```
param 1 : a path of your csv file
param 2 : when set 1, user passowrds will be hashed.
param 3 : when set 1, e-mail passowrds will be encrypted.
param 4 : if 0 was set, users can not allow to log in with e-mail addresses.


### How to use
1. Create a CSV file with four columns: user name, password, email address, and the email password. See the sample.csv under the config folder of this plugin.
2. Run this tool like below. The config file will be made.
```
cd (your roundcube installed folder)/plugins/virtuserpass
php ./makeConfigFileFromCSV.php your-csv-file-path 1 1 1
```
3. Remove the CSV file for security reasons.
