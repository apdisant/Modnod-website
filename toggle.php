<?php
   session_start();
   include ("top.php");
   $debug = 0;
   require_once ("connect.php");
   if (!$_SESSION["Username"]){
      print "<p><a href='login.php'>Please login first</a></p>"; 
      die;
   }

?>
<p id = "add">
   <a href ="addDevice.php">Add New Device</a>
</p>
<ol class = "MyNotes">
<?
##############################################################################
//Get The access token from the currentl logged in User
   $sql = 'SELECT fldAccessToken ';
   $sql .= 'FROM tblUser ';
   $sql .= 'Where pkUsername = "' .$_SESSION["Username"]. '"';
   if ($debug) print "<p>sql ".$sql;

   $stmt = $db->prepare($sql);
   $stmt->Execute();
   $MyAccessToken = $stmt->fetchAll();

   foreach ($MyAccessToken as $MAT) 
   {
      $AccessToken = $MAT['fldAccessToken'];
   }

   if($debug) print "<p>AccessToken: " .$AccessToken;

   //Find all devices owned by the currently logged in user
   $sql = 'Select pkDeviceID ';
   $sql .= 'from tblDevices ';
   $sql .= 'where fkUsername = "' .$_SESSION["Username"]. '"';
   if ($debug) print "<p>sql " .$sql;

   $stmt = $db->prepare($sql);
   
   $stmt->execute();

   $MyDeviceIDs = $stmt->fetchAll();
   if($debug){ print "<pre>"; print_r($NotesToMe); print "</pre>";}

   foreach ($MyDeviceIDs as $MDI) //get the Username, Nickname and Device IDS of all devices owned by specified user
       {
       $sql = 'select fkUsername, fldNickname, pkDeviceID ';
       $sql .= 'from tblDevices ';
       $sql .= 'where pkDeviceID = "' .$MDI['pkDeviceID']. '"';
       if ($debug) print "<p>sql " .$sql;

       $stmt = $db->prepare($sql);

       $stmt->execute();

       $DevicesFull = $stmt->fetchAll();
       if($debug){ print "<pre>"; print_r($DevicesFull); print "</pre>";}



       foreach ($DevicesFull as $DF) //Get individual devices DeviceIDs
       {
         print '<li class="note"> Device: ' .$DF['fldNickname']. '';
         if($debug) print "<p> Device ID: " .$MDI['pkDeviceID']. ' ';
         if($debug) print "<p> Access Token: " .$AccessToken. ' ';

         //Insert Form for on/off and REST request here
         ?>
         <br>
         <form action="https://api.particle.io/v1/devices/<?php echo $DF['pkDeviceID']?>/led?access_token=<?php echo $AccessToken?>" method="POST">
            Tell your device what to do!
            <br>
            <input type="radio" name="args" value="on">Turn the LED on.
            <br>
            <input type="radio" name="args" value="off">Turn the LED off.
            <br>
            <br>
            <input type="submit" value="Do it!">
         </form>
         <br>
        
         <?php
         //attempting to redo post with curl commands

         //$setLEDURL = "https://api.particle.io/v1/devices/" .$DF['pkDeviceID']."/led?access_token=" .$AccessToken;
         $setLEDURL = "https://api.particle.io/v1/devices/" .$DF['pkDeviceID']."/led?access_token=" .$AccessToken;

         if($debug) 
         {
            echo $setLEDURL;
            echo "<br>";
         }

         $postLED = curl_init($setLEDURL);
         curl_setopt($postLED, CURLOPT_POST, 1); 
         $ledOnOff = http_build_query(array('AccessToken' => $AccessToken, 'led' => '1') );
         curl_setopt($postLED, CURLOPT_RETURNTRANSFER, true);


         //$server_output = curl_exec($postLED);
         if($debug)
         { 
            echo $server_output;
            echo "<br>";
         }
         curl_close($postLED);

         $readPhotoURL = "https://api.particle.io/v1/devices/" .$DF['pkDeviceID']."/analogvalue/?access_token=" .$AccessToken;

         if($debug) echo $readPhotoURL;

         //The following three methods perform the exact same function

/*
         echo "<br>";

         $testFopen = fopen($readPhotoURL,"r");
         echo (fread($testFopen,12800000));

         echo "<br>";

         $html = file_get_contents($readPhotoURL);
         echo $html;
*/
         echo "<br>";

         $pr = curl_init($readPhotoURL);
         curl_setopt($pr, CURLOPT_RETURNTRANSFER, true);
         $html = curl_exec($pr);
         if ($debug)
         {
            echo $html;
            echo "<br>";
         }

         //trying to decode the get data
         //$prJSONARRAY = get_defined_constants(var_dump(json_decode($html,true)));
         $prJSONArray = json_decode($html,true);
         if ($debug)
         {
            echo $prJSONArray;
            echo "<br>";
         }

         foreach($prJSONArray as $PRA => $value)
         {
            if($PRA == "result")
            {
               $prValue = $value;
               echo 'Photo Resistor value: ' .$value. ' ' ;
               if($prValue < 3000)
               {
                  echo "It's bright in here!";
               }
               else
               {
                  echo "The lights are off";
               }
            }
         }

         //question on next line, the question mark + how to autoredirect back + executing curl
       }
       }
print '</ol>'
//include ("footer.php");
?>
