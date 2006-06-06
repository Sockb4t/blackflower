<?php
  $subsys="cad";
  
  require_once('db-open.php');
  require_once('session.inc');

  if (isset($_POST['unit']))  {
    $post_pend="";
    $unit = MysqlClean($_POST, 'unit', 20);

    if (isset($_POST['message']))
      $message = MysqlClean($_POST, 'message', 255);
    else 
      die("Internal error: POST message value missing.");

    if (isset($_SESSION['username']) && $_SESSION['username'] != '') {
      $creator = $_SESSION['username'];
    }
    else {
      $creator = '';
    }

    if (isset($_POST['message_type']))
      $message_type = MysqlClean($_POST, 'message_type', 20);
    else
      $message_type = "";

    $query = "INSERT INTO messages (ts,unit,message,creator,message_type) VALUES (NOW(), UPPER('$unit'), '$message', '$creator', '$message_type')";
    mysql_query($query) or die("Query failed : " . mysql_error());
    mysql_close($link);
   
    if (isset($_POST["hour"]))
      $post_pend .= "&hour=". MysqlClean($_POST, "hour", 20);
    # TODO: is this code still used?  there should also be a 'date' in post_pend.

    header("Location: cad.php$post_pend");
  }
?> 

<HTML>
<HEAD>
  <TITLE>Dispatch :: Log Viewer</TITLE>
  <LINK REL=StyleSheet HREF="style.css" TYPE="text/css" MEDIA="screen, print">
<?php include('include-clock.php') ?>
  <SCRIPT type="text/javascript">
  <!--
function resizeMe()
{
  document.cookie="width="+ window.innerWidth;
  document.cookie="height="+ window.innerHeight;
  window.location.reload();
  return false;
}
// -->
</SCRIPT>
</HEAD>

<BODY vlink=blue link=blue alink=cyan onload="displayClockStart()" onunload="displayClockStop()" onresize="resizeMe()">
<?php include('include-title.php') ?>

<form name="myform" action="cad.php" method="post">
<table cellspacing=0 cellpadding=0 style="padding-bottom: 0"> 
<tr valign=bottom>
   <td class="text" align="right"><font size="-2"><br>Add new message at:</font></td>
   <td width="10"><font size="-2">&nbsp;</font></td>
   <td class="text"><font size="-2"><br>Select unit from list:</td>
 <?php
      if (isset($_COOKIE['cad_show_message_type']) && $_COOKIE['cad_show_message_type'] == 'yes') {
   print '<td width="10"><font size="-2">&nbsp;</font></td>';
   print '<td class="text"><font size="-2"><br>Message Type:</td>';
   }
   ?>

   <td width="10"><font size="-2">&nbsp;</font></td>
   <td class="text"><font size="-2"><br>Enter message</td>
</tr>
<tr>
   <td align="right"><input type="text" name="displayClock" size="8"></td>
   <td></td>
   <td colspan="2">
     <select tabindex="3" style="width:150px" name="unit">
     <option selected value=""></option>
<?php
      $unitquery = "SELECT unit FROM units ORDER BY unit ASC";
      $unitresult = mysql_query($unitquery) or die("In query: $unitquery<br>\nError: " . mysql_error());
      while ($line = mysql_fetch_array($unitresult, MYSQL_ASSOC)) {
         echo "<option value=\"". $line["unit"] ."\">". $line["unit"] ."</option>\n";
      }
      mysql_free_result($unitresult);
 ?>
     </select>
   </td>

 <?php
      if (isset($_COOKIE['cad_show_message_type']) && $_COOKIE['cad_show_message_type'] == 'yes') {
        print '<td colspan="2">';
        print '<select tabindex="4" style="width:150px" name="message_type">';
        print '<option selected value=""></option>';
        $typequery = "SELECT message_type FROM message_types ORDER BY message_type ASC";
        $typeresult = mysql_query($typequery) or die("In query: $typequery<br>\nError: " . mysql_error());
        while ($line = mysql_fetch_array($typeresult, MYSQL_ASSOC)) {
           print "<option value=\"". $line["message_type"] ."\">". $line["message_type"] ."</option>\n";
        }
        mysql_free_result($typeresult);
        print "</select>\n</td>\n";
      }
 ?>

   <td><input tabindex="5" type="text" name="message" size=80 maxlength=250>
</tr>
<tr>
   <td colspan="4"></td>
   <?php if (isset($_COOKIE['cad_show_message_type']) && $_COOKIE['cad_show_message_type'] == 'yes') print "<td colspan=2></td>";?>
   <td align="right"><input tabindex="5" type="submit" value="Save New Entry"><input tabindex="6" type="reset" value="Clear"></td>
 </tr>
</table>

<iframe name="log" src="cad-log-frame.php" 
        width=<?php print htmlentities($_COOKIE['width'])-30?> 
        height=<?php print htmlentities($_COOKIE['height'])-190?>

        marginheight=0 marginwidth=0 frameborder=0> </iframe>
<?php
   include('include-footer.php');
   mysql_close($link);
?>
</form></body></html>