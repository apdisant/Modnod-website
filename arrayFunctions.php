<?php
function arrayAverage ($arr)
{
   $count = count($arr);
   foreach($arr as $value)
   {
      $sum = $sum + $value;
   }
   $average = $sum / $count;

   return $average;
}    
?>
