<?
session_start();
$debug = 1;
$uploadsDir = 'files';
if ($debug) print "<p>DEBUG MODE IS ON</p>";
if ($debug) print "<p> session username: " . $_SESSION["Username"]. "</p>";

$baseURL = "https://www.uvm.edu/~apdisant/";
$folderPath = "cs148/assignment5.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "add.php";

require_once("connect.php");

//$Note = "";
//$Recipient = "";
//$Deadline = "";

$DeviceID = "";
$Nickname = "";
$Location = "";

###################################################################################


if (isset($_POST["btnSubmit"])) {

    /*
    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }
    */

   $DeviceID = htmlentities($_POST["txtDeviceID"], ENT_QUOTES, "UTF-8"); //make all characters UTF-8
   if($debug) print "<p> Device ID: " .$DeviceID. "</p>";

   $Nickname = htmlentities($_POST["txtNickname"], ENT_QUOTES, "UTF-8"); //make all characters UTF-8

   $Location = htmlentities($_POST["txtLocation"], ENT_QUOTES, "UTF-8"); //make all characters UTF-8

   $date = date('Y-m-d H:i:s');
##################################################################################
   include ("validation_functions.php");
   $errorMsg = array();
    
   if (!$errorMsg)
   {
      if ($debug) print "<p>Form is Valid</p>";

   $primaryKey = "$DeviceID";
            if ($debug) print "<p>pk = " .$primaryKey;
   $dataEntered = false;
   
    try {
            $db->beginTransaction();

            $sql = 'INSERT INTO tblDevices SET pkDeviceID="' . $DeviceID . '", ';
            $sql .= 'fkUsername="' .$_SESSION["Username"]. '",';
            $sql .= 'fldNickname="' .$Nickname . '",';
            $sql .= 'fldDateAdded="' .$date . '";';

            //$sql .= '
            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql ". $sql;

            $stmt->execute();

        $dataEntered = $db->commit();
            if ($debug) print "<p>transaction complete";
        } catch (PDOExecption $e) {
            $db->rollback();
            if ($debug) print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }

            $db->beginTransaction();
            $sql = 'INSERT INTO tblSensors SET fkDeviceID="' .$DeviceID.'", ';
            $sql .= 'fldSensor1Type = 1, ';
            $sql .= 'fldSensor1Nickname = "photo resistor"; ';

            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql " .$sql;

            $stmt -> execute();

            $dataEntered = $db->commit();
            if ($debug) print "<p> transaction complete </p>";


        // If the transaction was successful, give success message
        if ($dataEntered) 
        {
            if ($debug) print "<p>data entered now prepare keys ";
            //#################################################################
            // create a key value for confirmation

            $sql = "SELECT fldTimePosted FROM tblDevices WHERE pkNoteId=" . $primaryKey;
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $dateSubmitted = $result["fldTimePosted"];

            $key1 = sha1($dateSubmitted);
            $key2 = $primaryKey;
         } //data entered
     } // no errors
} //ends if form submitted

#######################################################################################
// Begin creating actual form
#######################################################################################

include ("top.php");

   $ext = pathinfo(basename($_SERVER['PHP_SELF']));
   $file_name = basename($_SERVER['PHP_SELF'], '.' . $ext['extension']);
     if ($debug) print '<body id="' . $file_name . '">';

include ("header.php");
?> 
   
   <section id="main">

        <?
//############################################################################
//
//  In this block  display the information that was submitted and do not 
//  display the form.
//
        if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {
            print "<p>Note Submitted ";

        } else {
//#############################################################################
//
// Here we display any errors that were on the form
//

            print '<div id="errors">';

            if ($errorMsg) {
                echo "<ol>\n";
                foreach ($errorMsg as $err) {
                    echo "<li>" . $err . "</li>\n";
                }
                echo "</ol>\n";
            }

            print '</div>';
            ?>


<form action="<? print $_SERVER['PHP_SELF']; ?>"
                  
                  method="post"
                  enctype="multipart/form-data"
                  id="frmAddDevice">
                <fieldset class="add">
                    <legend>add a note</legend>

<p>Enter the device ID of your particle photon.</p>
<p>(This can be found using particle build after initial setup)</p>
<p><textarea id="txtDeviceID" name="txtDeviceID" class="element text medium<? if($noteERROR) echo ' mistake'; ?>" type="textarea" rows = "1" cols="85" wrap-"none" maxlength="24" value="<?php echo $DeviceID;?>" placeholder="Device ID" onfocus="this.select()" tabindex="30"/>
</textarea></p>
Create a nickname for this device.<br>

                    <textarea id ="txtNickname" name="txtNickname" class="element text medium<?php if ($NoteERROR) echo ' mistake'; ?>" type="textarea" rows="1" cols="85" wrap="hard" maxlength="20" onfocus="this.select()"  tabindex="30"/><? print $Note;?>
</textarea>

<?/*
                </fieldset>
<fieldset>
   <table width="350" border="0" cellpadding="1" cellspacing="1" class="box">
   <tr>
   <td width="246">
   <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
   <input name="userfile" type="file" id="userfile">
   </td>
   </tr>
   </table>
</fieldset>
*/?>

<fieldset class="buttons">
                    <input type="hidden" name="redirect" value="form.php">
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="submit" tabindex="991" class="button">
                    <input type="reset" id="butReset" name="butReset" value="Reset Form" tabindex="993" class="button" onclick="reSetForm()" >
                </fieldset>
</form>


 <?php
        } // end body submit
        ?>
    </section>



<?
        include ("footer.php");
?>

