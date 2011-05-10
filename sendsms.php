<?php



/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.04d
 *   
 *************************************************************
 *
 *  list.php
 *  Lists address book entries. This is the main page.
 *
 *************************************************************/

// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);
	session_start();
	

// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** CHECK FOR LOGIN **
	checkForLogin();

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
	
// ** END INITIALIZATION *******************************************************

	// CREATE THE LIST.	
	$list = new ContactList();
	
	// THIS PAGE TAKES SEVERAL GET VARIABLES
	// ie. list.php?group_id=6&page=2&letter=c&limit=20
	if ($_GET['groupid'])         $list->group_id = $_GET['groupid'];
	if ($_GET['page'])            $list->current_page = $_GET['page'];
	if (isset($_GET['letter']))   $list->current_letter = $_GET['letter'];	
	if (isset($_GET['limit']))    $list->max_entries = $_GET['limit'];	

	// Set group name (group_id defaults to 0 if not provided)
	$list->group_name();

	// ** RETRIEVE CONTACT LIST BY GROUP **
	$r_contact = $list->retrieve($_SESSION['username']);
	$signature = strtoupper($_SESSION['username']);
	$nos=str_replace("-","",$_POST['phone']);
	echo "<link href='styles.css' rel='stylesheet' type='text/css'>";
echo "<link rel='stylesheet' media='all' type='text/css' href='css/ui-lightness/jquery-ui-1.8.11.custom.css' />";
echo "<script type='text/javascript' src='jquery-1.5.min.js'></script>";
echo "<script type='text/javascript' src='harbhag_predictive.js'></script>";
echo "<script type='text/javascript' src='jquery_ui.js'></script>";
echo "<script type='text/javascript' src='datetime.js'></script>";
echo "<script>
  $(document).ready(function(){
    $('#dt').datetimepicker({dateFormat:'yy-mm-dd',
		showSecond:true,
		timeFormat: 'hh:mm:ss',
		separator: ','});
  });
  </script>";
?>
<HTML>
<HEAD>
	<TITLE><?php echo "$lang[TITLE_TAB] - $lang[TITLE_LIST]"?></TITLE>
	
	
	<LINK REL="stylesheet" HREF="styles.css" TYPE="text/css">
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="EXPIRES" CONTENT="-1">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['CHARSET']?>">

<script language="javascript" type="text/javascript">
	

function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}
</script>


</HEAD>

<BODY onLoad="document.goToEntry.goTo.focus();">
<A NAME="top"></A>
<P>
<CENTER>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
		<TR><TD><IMG SRC="images/title.png" WIDTH=570 HEIGHT=90 ALT="" BORDER=0></TD></TR>	
  <TR>
    <TD>
        
        
<?php
if(isset($_POST['schedule']) or $_POST['schedule_send']=='schedule') {
	echo "<table><form action='insert.php' method='post'>";
	echo "<tr><td></td><td>For multiple Numbers Use commas to separate</td></tr>";
	echo "<tr><td>Enter Number</td><td><input type='text' name='mobile' id='search' value='".$nos."' onkeyup='searchSuggest()' /></td></tr>";
	echo "<tr><td><div id='suggestions'></div></tr></td>";
	echo "<tr><td>Message</td><td><textarea name='msgdata' rows='10' cols='40' onKeyDown='limitText(this.form.msgdata,this.form.countdown,140)' 
onKeyUp='limitText(this.form.limitedtextarea,this.form.countdown,140)'>
</textarea></td></tr>
<tr><td><font size='1'>(Maximum characters: 140)</td></td>
<tr><td>You have</td><td><input readonly type='text' name='countdown' size='3' value='140'> characters left.</td></tr>";
echo "<tr><td>Time</td><td><input type='text' name='datetime' id='dt' /></td></tr>";
echo "<input type='hidden' name='schedule' id='sch' />";
echo "<tr><td><input type='submit' value='Schedule' onclick='return validate_mobile(\"schedule\")' /></td></form>";
echo "<td><form action='list.php' method='post'><input type='submit' value='Home'/></form></td></tr></table>";
}
else {
$sql ="SELECT fullname FROM users WHERE username='".$_SESSION['username']."'";
$sql2 ="SELECT mobile FROM users WHERE username='".$_SESSION['username']."'";
$result = mysql_query($sql);
$result2 = mysql_query($sql2);
$fullname = mysql_fetch_array($result);
$mobileno = mysql_fetch_array($result2);
if(isset($_POST['resend'])){
	$nos = $_POST['resend_mobile'];
}

?>

<?php
echo "<form action='insert.php' method='post'>";
echo "Enter Number  <input type='text' name='receiver' value='".$nos."' id='search' onkeyup='searchSuggest()' />";
echo "<div id='suggestions'></div>";
?>
<br>
Enter Message
<br>
<?php if(isset($_POST['resend'])){?>
<textarea name="msgdata" rows="10" cols="40" onKeyDown="limitText(this.form.msgdata,this.form.countdown,140);" 
onKeyUp="limitText(this.form.limitedtextarea,this.form.countdown,140);">
<?php echo $_POST['resend_msgdata']; ?>
</textarea><br>
<font size="1">(Maximum characters: 140)<br>
You have <input readonly type="text" name="countdown" size="3" value="140"> characters left.</font>
<br /><br>
<?php }
else
{?>
<textarea name="msgdata" rows="10" cols="40" onKeyDown="limitText(this.form.msgdata,this.form.countdown,140);" 
onKeyUp="limitText(this.form.limitedtextarea,this.form.countdown,140);">---
<?php echo $fullname[0]; ?>
(<?php echo $mobileno[0]; ?>)
</textarea><br>
<font size="1">(Maximum characters: 140)<br>
You have <input readonly type="text" name="countdown" size="3" value="140"> characters left.</font>
<br /><br>
<?php
} ?>
</td></tr></table>
<div id='send_sms_bottons'>
<table>
<tr><td><input type="image" src="images/send_sms.png" onclick='return validate_mobile("simple")' />
</form></td><td>
<a href='list.php'><img src="images/addressbook.png" /></a>
</td></tr></table>
</div>
</body>
</html>
<?php } ?>
