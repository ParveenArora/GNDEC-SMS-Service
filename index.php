<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.04d
 *    
 *  
 *************************************************************
 *
 *  index.php
 *  Welcome screen
 *  
 *************************************************************/

// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once('classes.php');

// ** START SESSION **
	session_start();
	$datetimenow = date("y-m-d H:i:s");
	$alphabets = array('a','b','c','d','e','f','g','h','i','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z');
for($i=0;$i<=2;$i++) {
	$rand_no[] = rand(2,9);
	$rand_al[] = $alphabets[rand(0,24)];
}
$system_answer = $rand_al[0].$rand_no[0].$rand_al[1].$rand_no[1].$rand_al[2].$rand_no[2];

// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
	$status = new SmsStatus();

	// ** FIGURE OUT WHAT'S GOING ON
	switch($_GET['mode']) {

		// **LOGOUT **
		case "logout":
			session_destroy();
			require_once('languages/' . $options->language . '.php');			
			// PRINT MESSAGE
			$errorMsg = $lang[MSG_LOGGED_OUT];
			header("location: index.php"); //required to force site language to override user language at sign in screen
			break;

		// ** AUTHENTICATE A USER
		case "auth":
			if($_POST['response']!= $_POST['system_answer']) {
				$errorMsg = "Incorrect Response";
				break;
			}
			else {
			// LOOK FOR USERNAME AND PASSWORD IN THE DATABASE.
			$usersql = "SELECT username, usertype, password, is_confirmed FROM " . TABLE_USERS . " AS users WHERE username='" . $_POST['username'] . "' AND password=MD5('" . $_POST['password'] . "') LIMIT 1";
			$r_getUser = mysql_query($usersql, $db_link)
				or die(ReportSQLError($usersql));
			$numrows = mysql_num_rows($r_getUser);
		    $t_getUser = mysql_fetch_array($r_getUser); 
		    
			// THE USERNAME IS FOUND AND ACCOUNT IS CONFIRMED
			if (($numrows != 0) && ($t_getUser['is_confirmed'] == 1)) {
				$usersmslimit = new UserSMSLimit();
				
				// REGISTER SESSION VARIABLES
				$_SESSION['username'] = $t_getUser['username'];
				$_SESSION['usertype'] = $t_getUser['usertype'];
				if (!isset($_SESSION['abspath'])) {
					$_SESSION['abspath'] = dirname($_SERVER['SCRIPT_FILENAME']);
				}
				$usersmslimit->UpdateLastLogin($datetimenow,$t_getUser['username'],$t_getUser['usertype']);

				// REDIRECT TO LIST
				header("Location: list.php");
				exit();
				
			}

			// ACCOUNT MUST BE CONFIRMED
			elseif (($numrows != 0) && ($t_getUser['is_confirmed'] != 1)) {
				// END SESSION
				session_destroy();
				// PRINT ERROR MESSAGE AND LOGIN SCREEN
				$errorMsg = $lang[ERR_USER_CONFIRMED_NOT];
			}

			// WRONG USERNAME
			else {
				// END SESSION
				session_destroy();
				// PRINT ERROR MESSAGE AND LOGIN SCREEN
				$errorMsg ="Incorrect Username/Password";
			}
			break;
		}
		
		// ** REGISTER A NEW USER
		case "register":
			header("Location: " . FILE_REGISTER);
			exit();
			break;
		
		// ** LOST PASSWORD
		case "lostpwd":
			header("Location: " . FILE_REGISTER . "?mode=lostpwd");
			exit();
			break;
		
		// ** FORCE LOGIN
		case "login":
			// This must be set to bypass the redirection to list if requireLogin is off.
			$forceLoginScreen = 1;
			break;

		// ** DEFAULT CASE
		default:
			if ($forceLoginScreen != 1) {
				// ** IF THERE IS A USER LOGGED IN, THEY DON'T NEED TO BE HERE. REDIRECT TO LIST
				if (isset($_SESSION['username']) && isset($_SESSION['usertype']) && ($_SESSION['abspath'] == dirname($_SERVER['SCRIPT_FILENAME'])) ) {
					header("Location: list.php");
					exit();
				}
				// ** IF AUTHENTICATION IS TURNED OFF (via config.php)
				// Set the user type to "guest" and proceed to list.
				// If a user is already logged in, the above code will redirect to list before
				// getting to here.
				if (($options->requireLogin != 1) && ($enableLogin!=1)) {
					// REGISTER SESSION VARIABLES
					$_SESSION['username'] = "@auth_off";
					$_SESSION['usertype'] = "guest";
					$_SESSION['abspath'] = dirname($_SERVER['SCRIPT_FILENAME']);
					// REDIRECT TO LIST
					header("Location: list.php");
					exit();
				}
			}

	// END SWITCH
	}
	

?>
<HTML>
<HEAD>
	<TITLE> <?php  echo "$lang[TITLE_WELCOME] - $lang[TITLE_TAB]" ?></TITLE>
	<LINK REL="stylesheet" HREF="styles.css" TYPE="text/css">
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="EXPIRES" CONTENT="-1">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['CHARSET']?>">	
</HEAD>
<BODY onload="document.login.username.focus();">
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH="100%" HEIGHT="100%">
<TBODY>
<TR><TD ALIGN="center">
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
<TBODY>
	<TR><TD><IMG SRC="images/title.png" WIDTH=570 HEIGHT=90 ALT="" BORDER=0></TD></TR>
	<TR>
		<TD CLASS="data"><CENTER>
		<FORM NAME="login" METHOD="post" ACTION="index.php?mode=auth">
<?php
	// PRINT LOGIN MESSAGE
	if ($options->msgLogin != "") {
		echo("<P>$options->msgLogin\n");
	}
	// PRINT ERROR MESSAGES
	if ($errorMsg != "") {
		echo("<P><FONT COLOR=\"#FF0000\"><B>$errorMsg</B></FONT>\n");
	}
?>
		<P><B><?php echo $lang[LBL_USERNAME]?></B>
		<BR><INPUT TYPE="text" SIZE=20 CLASS="formTextbox" NAME="username">
		<P><B><?php echo $lang[LBL_PASSWORD]?></B>
		<BR><INPUT TYPE="password" SIZE=20 CLASS="formTextbox" NAME="password">
		<P><B>Captcha</B>
		<BR><?php echo "<img src='image_create.php?txt=".$system_answer."' />"; ?>
		<P><B>Response</B>
		<BR><INPUT TYPE="Text" SIZE=20 CLASS="formTextbox" NAME="response" autocomplete="off">
			<input type='hidden' name='system_answer' value="<?php echo $system_answer; ?>">
		<P><INPUT TYPE="submit" CLASS="formButton" NAME="loginSubmit" VALUE="<?php echo $lang[BTN_LOGIN]?>">
<?php
	if ($options->allowUserReg == 1) {
		echo("<P><A HREF=\"" .FILE_INDEX. "?mode=register\">$lang[MSG_REGISTER_LOST]</A>\n");
	}
	if ($options->requireLogin != 1) {
		echo("	<P><A HREF=\"" . FILE_LIST ."\">$lang[GUEST]</A>\n");


	}


echo "<br><br><br><br><br>";
	echo "<b>Know The Developers : <a href='credits.html' target='_blank'>Credits</a></h3>";

?>
</FORM><p>
	<?php
	if($options->service_status!=3) {
		if($status->bearerbox==true && $status->sqlbox==true) {
			echo "<div id='service_status_ok'>Status: SMS Service is Running OK</div>";
		}
		if($status->bearerbox==false or $status->sqlbox==false) {
			echo "<div id='service_status_down'>Status: SMS Service is currently Down</div>";
		}
	}
	else {
			echo "<div id='service_status_maintenance'>Status: SMS Service is currently under maintenance</div>";
	}
	?>
</TBODY>
</TABLE>
</TD></TR>
</TBODY>
</TABLE>
</BODY>
</HTML>
