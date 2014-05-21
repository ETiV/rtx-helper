<?php
$visitorIP = $_SERVER['REMOTE_ADDR'];
$visitorIP = trim($visitorIP);

define("LOCAL_IP_PREFIX",           "127.0.0.");
define("IPLIMIT_CONFIGFILE_NAME",   "nrhIPLimit.json");
define("KEY_IP_ALLOWED",            "IPAllowed");
define("KEY_LIMIT_ENABLED",         "Enabled");

function GetIPLimitConfigFilePath()
{
	$path = dirname(__FILE__); // current dir
	$path .= "\\";
	$path .= IPLIMIT_CONFIGFILE_NAME;

	return $path;
}

function IsVisitorLimited($visitorIP)
{
	if (strpos($visitorIP, LOCAL_IP_PREFIX) !== false)
	{
		return false;
	}

	$iplimitConfigFilePath = GetIPLimitConfigFilePath();

	if (!file_exists($iplimitConfigFilePath))
	{
		return false;
	}

	$isLimitEnabled = false;
	$arPermittedIP = array();
	GetIPLimitInfo($iplimitConfigFilePath, $isLimitEnabled, $arPermittedIP);

	if ($isLimitEnabled == false)
	{
		return false;
	}

	if (in_array($visitorIP, $arPermittedIP, false))
	{
		return false;
	}

	return true;
}

function GetIPLimitInfo($iplimitConfigFilePath, &$isLimitEnabled, &$arPermittedIP)
{
	$isLimitEnabled = false;

	$json_string = file_get_contents($iplimitConfigFilePath);
	$aJSON = json_decode($json_string, true);

	$isLimitEnabled = $aJSON[ KEY_LIMIT_ENABLED ];
	$arPermittedIP = $aJSON[ KEY_IP_ALLOWED ];
}

if (IsVisitorLimited($visitorIP))
{
	echo json_encode(array(
		'status' => '403',
		'message' => 'Your IP Address Has Been Limited. Please Ask System Administrator For More Help.'
	));
	exit;
}

?>