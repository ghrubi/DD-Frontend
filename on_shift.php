<html>
  <head>
    <title>Who's On Shift</title>
  </head>
  <body>
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    <input type="hidden" name="dateBegin">
    <input type="hidden" name="dateEnd">
  </form>

<?php
include 'connect_to_db.php';

# If date(s) is/are set, use it/them. Otherwise, default to today.
if($_GET['dateBegin']) {
#  echo "Got a dateBegin: " . $_GET['dateBegin'] . "<p>\n";
  $begin_date = $_GET['dateBegin'];
}
else {
  $begin_date =  date('Y-m-d');
}
if($_GET['dateEnd']) {
#  echo "Got a dateEnd: " . $_GET['dateEnd'] . "<p>\n";
  $end_date = $_GET['dateEnd'];
}
else {
  $end_date =  $begin_date;
}

# Query time clock
#$query = "SELECT *
#FROM WhoWorked
#WHERE clock_in>'" . $begin_date . " 00:00:00' AND
#      clock_in<'" . $end_date . " 23:59:00';";

$query = "call WhosWorkingSummary('" . $begin_date . " 00:00:00', '" . $end_date . " 23:59:00')"; 

$result = mysqli_query($conn, $query) or die('Query failed: ' . mysql_error());

# Begin time clock table
echo "
<!--
  <table id=summary border=0 cellpadding=3>
  <table border=0 cellpadding=3>
-->
  <table>
    <tr>
      <th>Name</th>
      <th>Clock In</th>
      <th>Clock Out</th>
      <th>Hours</th>
      <th>Total</th>
    </tr>";

while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
  # Fix date and times for clock in/outs. 
  list($in_date, $in_time) = split(" ", $row['clock_in']);
  list($in_date_y, $in_date_m, $in_date_d) = split("-", $in_date);
  list($in_time_h, $in_time_m, $in_time_s) = split(":", $in_time);
  
  $in_date = "$in_date_m/$in_date_d";
  $in_time = "$in_time_h:$in_time_m";

  $out_date = null;
  $out_time = null;

  # Clock out could be null.
  if($row['clock_out']) {
    list($out_date, $out_time) = split(" ", $row['clock_out']);
    list($out_date_y, $out_date_m, $out_date_d) = split("-", $out_date);
    list($out_time_h, $out_time_m, $out_time_s) = split(":", $out_time);

    $out_date = "$out_date_m/$out_date_d";
    $out_time = "$out_time_h:$out_time_m";
  }

  # Separate first and last name. Use only first.
  list($first, $last) = split(" ", $row['name']);
  echo "
    <tr>
      <td>" . $first . "</td>
      <td nowrap>" . $in_date . " " . $in_time . "</td>
      <td nowrap>" . $out_date . " " . $out_time . "</td>
      <td>" . number_format($row['hours'], 2) . "</td>
      <td>\$" . number_format($row['total'], 2) . "</td>
    </tr>";
}

echo "
  </table>
  <p>";

// Flush query buffer
flushQuery();

// Disconnect from DB
include 'disconnect_from_db.php';
?>


  </body>
</html>
