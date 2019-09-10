<html>
  <head>
    <title>Hourly Sales</title>
  </head>
  <body>
<!-- Begin script -->
  <script type="text/javascript">
<!-- Open new window for receipt details -->
    function gotoReceiptList(hr) {
      var loc = "./dd_checks.php?dateBegin=" + hr;
      window.open(loc);
    }
  </script>
<!-- End script -->

  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    <input type="hidden" name="dateBegin">
    <input type="hidden" name="dateEnd">
  </form>

<?php
// Connect to DB
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

#$query = "SELECT hour(check_time) AS hour, count(*) AS orders, sum(net_sale) AS sales
#FROM receipt
#WHERE check_time>'" . $begin_date . " 00:00:00' AND
#      check_time<'" . $end_date . " 23:59:00'
#GROUP BY hour(check_time);";

$query = "call HourlySummary('" . $begin_date . " 00:00:00', '" . $end_date . " 23:59:00')";

$result = mysqli_query($conn, $query) or die('Query failed: ' . mysql_error());

# Begin hourly table

#echo "$query<p>";
echo "
<!--
  <table id=hours border=0 cellpadding=3>
  <table border=0 cellpadding=3>
-->
  <table>
    <tr>
      <th>Hour</th>
      <th>Orders</th>
      <th>Sales</th>
    </tr>";

while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
  # Create date & time for href link
  $hour = $row['hour'];
  $linkDateTime = "$begin_date $hour:00";

  # Fix time.
  if ($row['hour'] > 12){
    $row['hour'] -= 12;
    $ampm = 'pm';
  }
  else if ($row['hour'] == 12){
    $ampm = 'pm';
  }
  else {
    $ampm = 'am';
  }

  echo "
    <tr onClick=\"gotoReceiptList('$linkDateTime')\">
      <td>" . $row['hour'] . $ampm . "</td>
      <td align=right>" . $row['orders'] . "</td>
      <td>\$" . $row['sales'] . "</td>
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
