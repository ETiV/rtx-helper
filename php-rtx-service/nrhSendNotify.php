<?PHP
header('Content-Type: text/html; charset=utf-8');
require_once "nrhIPLimit.php";

define("SEND_TO_ALL", "NRH_SEND_NOTIFY_TO_ALL");

// ------ PREPARE NOTIFY PAYLOADs
$receivers = $_POST["receivers"];
$title     = $_POST["title"];
$msg       = $_POST["msg"];
$delaytime = $_POST["delaytime"];

$encoding  = $_POST["encoding"];

if(strlen($encoding) == 0)
{
	$encoding  = "UTF-8";
}

if (strlen($receivers) == 0)
{
	$receivers = "";
}

if(strlen($title) == 0)
{
	$title     = "通知";
}

if(strlen($msg) == 0)
{
	$msg       = "";
}

if(strlen($delaytime) == 0)
{
	$delaytime = 0;
}

$title = iconv($encoding, "GB2312", $title);
$msg = iconv($encoding, "GB2312", $msg);

// ------ DO SEND
$php_errormsg = NULL;

$ObjApi= new COM("Rtxserver.rtxobj");
$objProp= new COM("Rtxserver.collection");
$Name = "ExtTools";
$ObjApi->Name = $Name;

$objProp->Add("MsgID", "1");
$objProp->Add("Type", "0");
$objProp->Add("AssType", "0");

$objProp->Add("Title", $title);
$objProp->Add("msgInfo", $msg);
$objProp->Add("DelayTime", $delaytime);

if ($receivers == SEND_TO_ALL)
{
	$objProp->Add("Username", "all");
	$objProp->Add("SendMode", "1");
}
else
{
	$objProp->Add("Username", $receivers);
}

try {
	$Result = @$ObjApi->Call2(0x2100, $objProp);
	// !----- SEND OVER

	$errstr = iconv("GB2312", "UTF-8", $php_errormsg);
	$ret    = array();
	if(strcmp($nullstr, $errstr) == 0)
	{
		$ret['status']  = 200;
		$ret['message'] = 'OK';
	}
	else
	{
		$ret['status']  = 400;
		$ret['message'] = $errstr;
	}

	echo json_encode($ret);
} catch (Exception $e) {
	echo json_encode(array('status' => 500, 'message' => 'Notify Not Sent. Some Error Ocurred.'));
}

?>