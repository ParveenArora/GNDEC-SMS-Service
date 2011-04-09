<?php
require_once('config.php');
$str = $_GET['q'];
$conn = mysql_connect("localhost",$db_username,$db_password);

mysql_select_db("adbook",$conn);

$result = mysql_query("SELECT CONCAT(contact.firstname,' ',contact.middlename,' ',contact.lastname,' ',',',address.phone1) AS rphj from address,contact WHERE address.id=contact.id AND CONCAT(contact.firstname,'',contact.middlename,'',contact.lastname) LIKE '".$str."%' LIMIT 0,15") or die(mysql_error());

while($row = mysql_fetch_assoc($result)) {
	echo $row['rphj']."\n";
}

?>
