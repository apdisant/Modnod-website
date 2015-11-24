<?php
   include ("top.php");
   include ("connect.php");
   /*
   if($_SESSION['Username'])
   {
      print "<p>You are Logged in as " .$_SESSION['Username']. "</p>";
      header('Location: http://http://www.uvm.edu/~apdisant/cs275/modnod/dashboard.php');
   }
   */
?>
<p> To use this site first make sure you are registered. This can be accomplished at this page:
   <a href ="register.php">Register</a>
</p>
<br>
<p> After you have registered go to:
   <a href = "login.php">login</a>
   To login to your account and see and create messages.
</p>
<br>
<?
//include ("footer.php");
?>
