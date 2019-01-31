<?php require_once('../Connections/Ublq.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "/S0CkEtLaYeR/index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "/S0CkEtLaYeR/error.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "InsertTvChannels")) {
  $insertSQL = sprintf("INSERT INTO arti100blqs (Title, codes, daters, cates, keywds) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Title'], "text"),
                       GetSQLValueString($_POST['codes'], "text"),
                       GetSQLValueString($_POST['daters'], "text"),
                       GetSQLValueString($_POST['cates'], "text"),
                       GetSQLValueString($_POST['keywds'], "text"));

  mysql_select_db($database_Ublq, $Ublq);
  $Result1 = mysql_query($insertSQL, $Ublq) or die(mysql_error());

  $insertGoTo = "main.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$maxRows_Larticles = 20;
$Page_Larticles = 0;
if (isset($_GET['Page_Larticles'])) {
  $Page_Larticles = $_GET['Page_Larticles'];
}
$startRow_Larticles = $Page_Larticles * $maxRows_Larticles;

mysql_select_db($database_Ublq, $Ublq);
$query_Larticles = "SELECT * FROM arti100blqs";
$query_limit_Larticles = sprintf("%s LIMIT %d, %d", $query_Larticles, $startRow_Larticles, $maxRows_Larticles);
$Larticles = mysql_query($query_limit_Larticles, $Ublq) or die(mysql_error());
$row_Larticles = mysql_fetch_assoc($Larticles);

if (isset($_GET['Total_Larticles'])) {
  $Total_Larticles = $_GET['Total_Larticles'];
} else {
  $all_Larticles = mysql_query($query_Larticles);
  $Total_Larticles = mysql_num_rows($all_Larticles);
}
$totalPages_Larticles = ceil($Total_Larticles/$maxRows_Larticles)-1;

$queryString_Larticles = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "Page_Larticles") == false && 
        stristr($param, "Total_Larticles") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Larticles = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Larticles = sprintf("&Total_Larticles=%d%s", $Total_Larticles, $queryString_Larticles);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Articles</title>
<link href="/ublqstyle.css" rel="stylesheet" type="text/css" />

</head>

<body>
<table width="1003" border="0" cellpadding="0" cellspacing="0" class="Backgrounding">
  <tr>
    <td width="27" height="110">&nbsp;</td>
    <td width="976"><img src="../imagine/logo.gif" width="136" height="70" /></td>
  </tr>
</table>
<table width="1003" height="199" border="0" cellpadding="0" cellspacing="0" class="Backgrounding_downing">
  <tr>
    <td height="199"><table width="865" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="709" height="114"><div align="center"><a href="tv.php"><img src="../imagine/Pics-Folder.png" alt="Television Management Section" width="100" height="100" border="0" /></a></div></td>
        <td width="709"><div align="center"><a href="rad.php"><img src="../imagine/Music-Folder.png" alt="Radio Management Section" width="100" height="100" border="0" /></a></div></td>
        <td width="709"><div align="center"><a href="mov.php"><img src="../imagine/Movie-Folder.png" alt="Movies Management Section" width="100" height="100" border="0" /></a></div></td>
        <td width="709"><div align="center"><a href="gam.php"><img src="../imagine/Games-folder.png" alt="Games Management Section" width="100" height="100" border="0" /></a></div></td>
        <td width="709"><div align="center"><a href="art.php"><img src="../imagine/Documents-Folder.png" alt="Articles Management Section" width="100" height="100" border="0" /></a></div></td>
        <td width="709"><div align="center"><a href="mob.php"><img src="../imagine/Tentacles-Folder.png" alt="Mobile Management Section" width="100" height="100" border="0" /></a></div></td>
      </tr>
      <tr>
        <td height="19" class="headering_black"><div align="center"><a href="tv.php" class="headering_Bluelink">Television</a></div></td>
        <td class="headering_black"><div align="center"><a href="rad.php" class="headering_Bluelink">Radio</a></div></td>
        <td class="headering_black"><div align="center"><a href="mov.php" class="headering_Bluelink">Movies</a></div></td>
        <td class="headering_black"><div align="center"><a href="gam.php" class="headering_Bluelink">Games</a></div></td>
        <td class="headering_black"><div align="center"><a href="art.php" class="headering_Bluelink">Articles</a></div></td>
        <td class="headering_black"><div align="center"><a href="mob.php" class="headering_Bluelink">Mobile</a></div></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="1003" border="0" cellpadding="0" cellspacing="0" class="centertop">
  <tr>
    <td height="28" valign="bottom"><table width="263" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="45"></td>
        <td width="211" valign="top"><table width="210" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="210" height="1" bgcolor="#FFFFFF"></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="1003" border="0" cellpadding="0" cellspacing="0" class="center">
  <tr>
    <td valign="top"><table width="1003" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="46" height="148">&nbsp;</td>
        <td valign="top" bgcolor="#FFFFFF"><table width="888" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="840" height="28" class="headering_black" style="padding-left:6px;">Ublq 2.0 Articles :</td>
            <td width="64" valign="top">&nbsp;</td>
          </tr>
        </table>
              <table width="897" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="901" height="219" valign="top"><table width="894" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="894"><div align="center"><table width="894" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="219" valign="top"><table width="893" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="567"><form action="<?php echo $editFormAction; ?>" id="InsertTvChannels" name="InsertTvChannels" method="POST">
                    <table border="0" align="left" cellpadding="4" cellspacing="0">
                      <tr>
                        <td width="76" height="33" bgcolor="#FFFFFF" class="graytext"><div align="left">Title :</div></td>
                        <td width="303" bgcolor="#FFFFFF"><div align="left">
                          <input name="Title" type="text" class="editbar" id="Title" size="44" />
                        </div></td>
                      </tr>
                      <tr>
                        <td bgcolor="#FFFFFF" class="graytext"><div align="left">Discription :</div></td>
                        <td bgcolor="#FFFFFF"><div align="left">
                          <textarea name="codes" cols="88" rows="14" class="editbar" id="codes"></textarea>
                        </div></td>
                      </tr>
                      <tr>
                        <td height="33" bgcolor="#FFFFFF" class="graytext"><div align="left">Date :</div></td>
                        <td bgcolor="#FFFFFF"><div align="left">
                          <input name="daters" type="text" class="editbar" id="daters" value="<? echo date("jS / F / Y") ?>" size="44"/>
                        </div></td>
                      </tr>
                      <tr>
                        <td height="33" bgcolor="#FFFFFF" class="graytext"><div align="left">Category :</div></td>
                        <td bgcolor="#FFFFFF"><div align="left">
                          <select name="cates" class="editbar" id="cates">
                            <option value="news" selected="selected">News</option>
                            <option value="entertainment">Entertainment</option>
                            <option value="movies">Movies</option>
                            <option value="games">Games</option>
                          </select>
                        </div></td>
                      </tr>
                      <tr>
                        <td height="33" bgcolor="#FFFFFF" class="graytext"><div align="left">Keywords :</div></td>
                        <td bgcolor="#FFFFFF"><div align="left">
                          <input name="keywds" type="text" class="editbar" id="keywds" size="44" maxlength="40" />
                        </div></td>
                      </tr>
                      <tr>
                        <td bgcolor="#FFFFFF"><div align="left"></div></td>
                        <td bgcolor="#FFFFFF"><div align="left">
                          <input name="button" type="submit" class="searchbutton" id="button" value="Submit" />
                        </div></td>
                      </tr>
                    </table>
                                    
                    
                    
                    <input type="hidden" name="MM_insert" value="InsertTvChannels" />
                  </form>&nbsp;</td>
                      <td width="326" valign="top"><div align="center"><img src="../imagine/Umbrella.png" width="256" height="256" /></div></td>
                    </tr>
                  </table>                  </td>
              </tr>
                <tr>
                  <td valign="top"><img src="../imagine/Scissor Cut.gif" width="682" height="20" /><br /><?php if ($Total_Larticles > 0) { // Show if recordset not empty ?>
            <table width="894" border="0" cellpadding="2" cellspacing="0" bgcolor="#F9F9F9">
              <tr>
                <td width="76" height="28" bgcolor="#002137" class="headering"><div align="center" class="shopleftheading">
                  <div align="center">ids : </div>
                </div></td>
                <td width="440" bgcolor="#002137" class="headering"><div align="left">Channel Name</div></td>
                <td width="103" bgcolor="#002137" class="headering"><div align="center">Delete</div></td>
                <td width="137" bgcolor="#002137" class="headering"><div align="center">Category</div></td>
                <td width="118" bgcolor="#002137" class="headering"><div align="center">Update</div></td>
              </tr>
              <?php do { ?>
              <tr>
                <td height="32" bgcolor="#F2F4F9" class="normal_textlinkRed"><div align="center"><?php echo $row_Larticles['ids']; ?></div></td>
                <td height="32" bgcolor="#FFFFFF" class="text3"><div align="left"><?php echo $row_Larticles['Title']; ?></div></td>
                <td width="103" height="32" bgcolor="#FFFFFF" class="normal_textlink"><div align="center" class="styletextinglinkshop">
                    
                          <div align="center">
                            <?php if ($Total_Larticles > 0) { // Show if recordset not empty ?>
                            <a href="includes/del_art.php?ids=<?php echo $row_Larticles['ids']; ?>" class="more">Del It </a>
                            <?php } // Show if recordset not empty ?>
                          </div>
                </div></td>
                <td width="137" height="32" bgcolor="#FFFFFF" class="text"><div align="left" class="text3">
                  <div align="center"><?php echo $row_Larticles['cates']; ?></div>
                </div></td>
                <td width="118" height="32" bgcolor="#FFFFFF" class="normal_textlink"><div align="center" class="styletextinglinkshop">
                  <div align="center"><a href="edit_art.php?ids=<?php echo $row_Larticles['ids']; ?>" class="more">Edit</a></div>
                </div></td>
              </tr>
              <tr>
                <td height="1" colspan="6" bgcolor="#F2F4F9"></td>
              </tr>
              <?php } while ($row_Larticles = mysql_fetch_assoc($Larticles)); ?>
            </table>
            <img src="../imagine/Scissor Cut.gif" width="682" height="20" />
            <?php } // Show if recordset not empty ?>
            <?php if ($Total_Larticles > 0) { // Show if recordset not empty ?>
            <table border="0" width="0">
              <tr>
                <?php if ($Page_Larticles > 0) { // Show if not first page ?>
                <td width="50" align="center" class="reportchannel">
                  <div align="center">
                    <?php if ($Page_Larticles > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?Page_Larticles=%d%s", $currentPage, 0, $queryString_Larticles); ?>" class="footerlink">First</a>
                    <?php } // Show if not first page ?>
                    </div></td>
                <td width="70" align="center" class="reportchannel">
                  <div align="center">
                    <?php if ($Page_Larticles > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?Page_Larticles=%d%s", $currentPage, max(0, $Page_Larticles - 1), $queryString_Larticles); ?>" class="footerlink">Previous</a>
                    <?php } // Show if not first page ?>
                    </div></td>
                <?php } // Show if not first page ?>
                <?php if ($Page_Larticles < $totalPages_Larticles) { // Show if not last page ?>
                <td width="50" align="center" class="reportchannel">
                  <div align="center">
                    <?php if ($Page_Larticles < $totalPages_Larticles) { // Show if not last page ?>
                    <a href="<?php printf("%s?Page_Larticles=%d%s", $currentPage, min($totalPages_Larticles, $Page_Larticles + 1), $queryString_Larticles); ?>" class="footerlink">Next</a>
                    <?php } // Show if not last page ?>
                    </div></td>
                <td width="50" align="center" class="reportchannel">
                  <div align="center">
                    <?php if ($Page_Larticles < $totalPages_Larticles) { // Show if not last page ?>
                    <a href="<?php printf("%s?Page_Larticles=%d%s", $currentPage, $totalPages_Larticles, $queryString_Larticles); ?>" class="footerlink">Last</a>
                    <?php } // Show if not last page ?>                
                    </div></td>
                <?php } // Show if not last page ?>
              </tr>
            </table>
          <?php } // Show if recordset not empty ?></td>
              </tr>
            </table></div></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td valign="top"><div align="center"><img src="../imagine/Scissor Cut.gif" width="682" height="20" /></div></td>
                </tr>
            </table></td>
        <td width="48">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="1003" border="0" cellpadding="0" cellspacing="0" class="centerbot">
  <tr>
    <td height="7" valign="top"><table width="263" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="45"></td>
        <td width="211" valign="top"><table width="210" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="210" height="4" bgcolor="#FFFFFF"></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="1003" border="0" cellpadding="0" cellspacing="0" class="footer_downing">
  <tr>
    <td height="12"></td>
  </tr>
</table>
<table width="200" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="12"></td>
  </tr>
</table>
<table width="596" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="22"><div align="center"><a href="main.php" class="footerlink">Home</a><span class="footerlinkline"> ¦ </span><a href="setting.php" class="footerlink">Setting</a><span class="footerlinkline"> ¦ </span><a href="<?php echo $logoutAction ?>" class="footerlink">Logout</a></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($Larticles);
?>