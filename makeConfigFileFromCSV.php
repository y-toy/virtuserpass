<?php

// Convert a CSV file to config.inc.php of Virtuserpass plugin
//
// USAGE : php ./makeConfigFileFromCSV.php csv-file-path [1|0] [1|0] [1|0]
// ex. php ./makeConfigFileFromCSV.php ./config/sample.csv 1 0 0
// paramater 1 : a path of your csv file
// paramater 2 : when set 1, user passowrds will be hashed.
// paramater 3 : when set 1, e-mail passowrds will be encrypted.
// paramater 4 : if 0 was set, users can not allow to log in with e-mail addresses. users are needed to use "username" and "password" which were set in the config file.
//
// CSV file format : username, password, e-mail address, e-mail's password.
//                   see sample.csv
//
// Reccomended to delete your CSV file after using this tool.

// paramaters
$csvFile = $argv[1];
$virtuserpass_scramble = false;
$virtuserpass_email_scramble = false;
$virtuserpass_allow_email_login = false;
if (isset($argv[2]) && $argv[2] == 1){ $virtuserpass_scramble = true; }
if (isset($argv[3]) && $argv[3] == 1){ $virtuserpass_email_scramble = true; }
if (isset($argv[4]) && $argv[4] == 1){ $virtuserpass_allow_email_login = true; }

// read input csv file
$fp = fopen($csvFile, 'r');
if ($fp === false){ die('Could not read the CSV file. Check the path. Probably not exists.'); }

$aryAllUserData = array();
while($readData = fgetcsv($fp)){
	if (count($readData) != 4){ continue; }
	for ($i=0;$i < 4;$i++){
		$readData[$i] = trim($readData[$i]);
		if ($readData[$i] == ''){ die('Wrong data line detected. [' . print_r($readData, true) . ']'); }
	}
	if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $readData[2])){ die('Wrong data line detected. [' . print_r($readData, true) . ']'); }

	$aryAllUserData[] = $readData;
}
fclose($fp);

$lenAllUserData = count($aryAllUserData);
if ($lenAllUserData == 0){ die('No data found in the CSV file.'); }

// write the config file
echo 'Number of users to set : ' . $lenAllUserData . PHP_EOL;

$outData = '<?php' . PHP_EOL . PHP_EOL;
$outData .= '$config[\'virtuserpass_scramble\']=' . (($virtuserpass_scramble)?'true':'false') . ';' . PHP_EOL;
$outData .= '$config[\'virtuserpass_email_scramble\']=' . (($virtuserpass_email_scramble)?'true':'false') . ';' . PHP_EOL;
$outData .= '$config[\'virtuserpass_allow_email_login\']=' . (($virtuserpass_allow_email_login)?'true':'false') . ';' . PHP_EOL . PHP_EOL;
$outData .= '$config[\'virtuserpass\'] = array(' . PHP_EOL;

for ($i=0;$i < $lenAllUserData;$i++){
	$aUserData = $aryAllUserData[$i];
	if ($virtuserpass_scramble){ $aUserData[1] = md5($aUserData[1]); }
	if ($virtuserpass_email_scramble){ $aUserData[3] = openssl_encrypt($aUserData[3], 'AES-128-CBC', 'bT8Xx4sRE4Yr', 0, 'Zn7rAEBWdj3kJVGa'); }
	$outData .= '\'' . $aUserData[0] . '\'=>array(\'' . $aUserData[1] . '\',\'' . $aUserData[2] . '\',\'' . $aUserData[3] . '\'),' . PHP_EOL;

	echo $aUserData[0] . PHP_EOL;
}

$outData .= ');' . PHP_EOL;

$ret = file_put_contents('./config/config.inc.php',$outData);
if (!$ret){ die('Failed to out put the data to config.inc.php. It might be opend by another one.'); }

echo 'Done!' . PHP_EOL;
