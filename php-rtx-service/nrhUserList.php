<?php
header('Content-Type: text/html; charset=utf-8');
require_once "nrhIPLimit.php";

$connstr = "Driver={Microsoft access Driver (*.mdb)};DBQ=../db/rtxdb.mdb";

$conn = @new COM("ADODB.Connection") or die ("ADO连接失败!");
$conn->Open($connstr);
$rs = @new COM("ADODB.RecordSet");
$sql ="SELECT ID, UserName FROM Sys_user WHERE AccountState <> 1 OR AccountState IS NULL ORDER BY ID;";
$rs->Open($sql, $conn, 1, 3);

$rs->MoveFirst();

$result = array();

while(!$rs->EOF)
{
  $idField   = $rs->Fields(0);
  $id        = $idField->value;

  $nameField = $rs->Fields(1);
  $name      = $nameField->value;

  $name = iconv("GB2312", "UTF-8", $name);
  array_push($result, array('id' => $id, 'name' => $name));
  $rs->MoveNext();
}

$rs->close();
echo json_encode(array('status' => 200, 'message' => 'OK', 'list' => $result));
?>