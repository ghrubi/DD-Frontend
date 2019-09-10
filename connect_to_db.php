<?php
// Check for a connection. If not, make one.
$CurrentDBConnection = false;
if($conn == null){
  #echo "DB conn: is null\n";
  require 'dbconnect.php';
  $CurrentDBConnection = true;
}
?>
