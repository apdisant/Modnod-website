<?php
//The point of this file is only to pull the data from every single photon in the database once, it will then be run repeatedly through cron
   //session_start();
   include ("top.php");
   $debug = 0;
   require_once ("connect.php");
   /*
   if (!$_SESSION["Username"]){
      print "<p><a href='login.php'>Please login first</a></p>"; 
      die;
   }
   */

##############################################################################
      $todaysDate = date("Y-m-d");
      //get an array with all Device IDs
      $sql = 'Select pkDeviceID ';
      $sql .= 'from tblDevices; ';
      if ($debug) print "<p>sql " .$sql. "<br>";

      $stmt = $db->prepare($sql);
      
      $stmt->execute();

      $MyDeviceIDs = $stmt->fetchAll();


      foreach ($MyDeviceIDs as $MDI) //should now have each username. access token and device ID
      {
         $CurrentDeviceID = $MDI['pkDeviceID'];
         if($debug)
         {
            echo "<br>Current Device ID: " .$CurrentDeviceID;
         }

         for ($i = 1; $i < 7; $i++)
         {
            $sql = 'SELECT AVG(fldValue'.$i.'), MIN(fldValue'.$i.'), MAX(fldValue'.$i.') ';
            $sql .= 'FROM tblSensorData ';
            $sql .= 'where fkDeviceID = "' .$CurrentDeviceID. '"';
            $sql .= 'and fldDay = CURDATE();';

            if ($debug) print "<br>sql " .$sql. "<br>";

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $arraySensorValues = $stmt->fetchAll();

            //grab the average, min and max
            foreach ($arraySensorValues as $ASV)
            {
               $vmin = $ASV['MIN(fldValue'.$i.')'];
               $vmax = $ASV['MAX(fldValue'.$i.')'];
               $vavg = $ASV['AVG(fldValue'.$i.')'];
            }

            if ($debug)
            {
               echo "<br>";
               echo "V$i Min: " .$vmin;
               echo "<br>";
               echo "V$i Max: " .$vmax;
               echo "<br>";
               echo "V$i Avg: " .$vavg;
            }
            
            //Now attempt to insert this data
            $db -> beginTransaction();

            $sql = 'INSERT INTO tblSensorDaily SET fkDeviceID = "' .$CurrentDeviceID.'",';
            $sql .= 'fldSensor'.$i.'Min = ' .$vmin. ',';
            $sql .= 'fldSensor'.$i.'Max = ' .$vmax. ',';
            $sql .= 'fldSensor'.$i.'Avg = ' .$vavg. ',';
            $sql .= 'fldDay = CURDATE();';


            $stmt = $db -> prepare($sql);
            if($debug) print "<p> sql: " .$sql;
            $stmt ->execute();
            $dataEntered = $db->commit();
         }
      }
//print '</ol>'
//include ("footer.php");
?>
