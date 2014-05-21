<?PHP
header('Content-Type: text/html; charset=utf-8');
require_once "nrhIPLimit.php";

$receiver = $_GET["username"];

$encoding = $_GET["encoding"];

if (strlen($encoding) == 0)
{
	$encoding = 'UTF-8';
}

$receiver = iconv($encoding, 'GB2312', $receiver);

//取该用户状态
$state = 12;
$ObjApi= new COM("Rtxserver.rtxobj");
$objProp= new COM("Rtxserver.collection");

$objProp->Add("Username", $receiver);
try {
	@$Result = @$ObjApi->Call2(0x2001, $objProp);

	$errstr = $php_errormsg;

	if(strcmp($nullstr, $errstr) == 0)
	{
	  $state = intVal($Result);
	}

	echo json_encode(array('status' => 200, 'message' => 'OK', 'state' => $state));
} catch (Exception $e) {
	echo json_encode(array('status' => 500, 'message' => 'Failed To Get User By Username.'));
}



?>