<html>
  <head>
    <title>Summary</title>
  </head>
  <body>
<!-- Begin script -->
  <script type="text/javascript">
<!-- Open new window for receipt details -->
    function gotoReceiptListT(date,orderType) {
      var loc = "./dd_checks_sub.php?dateBegin=" + date + "&orderType=" + orderType;
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
  echo "Got a dateBegin: " . $_GET['dateBegin'] . "<p>\n";
  $begin_date = $_GET['dateBegin'];
}
else {
  $begin_date =  date('Y-m-d');
}
if($_GET['dateEnd']) {
  echo "Got a dateEnd: " . $_GET['dateEnd'] . "<p>\n";
  $end_date = $_GET['dateEnd'];
}
else {
  $end_date =  $begin_date;
}

// Call GetSummary Proc. Date file must be quoted. Hence, the double quoting.
#$query = 'CALL GetSummaryRange(\'' . $begin_date . '\', \'' . $end_date . '\', @netSales, @salesCount, @discounts, @oloSales, @oloCount, @laborPct)';
#$query_rs = 'SELECT @netSales, @salesCount, @discounts, @oloSales, @oloCount, @laborPct';

$query = "CALL GetSummaryRange2('" . $begin_date . "', '" . $end_date . "')";

#echo $query . "<p>";

$result = mysqli_query($conn, $query) or die('Query failed: ' . mysql_error());
#$result = mysqli_query($conn, $query_rs) or die('Query result set failed: ' . mysql_error());

$retVals = mysqli_fetch_array($result, MYSQL_ASSOC);

// Print results as text
#echo "$query<p>";
echo "
<!--
  <table id=summary border=0 cellpadding=3>
  <table border=0 cellpadding=3>
-->
  <table>
    <tr>
      <th>Summary</th>
      <th>&nbsp; </th>
      <th>&nbsp; </th>
    </tr>
    <tr>
      <td>Sales:</td>
      <td align=right>" . $retVals['sales_count'] . "</td>
      <td>\$" . number_format($retVals['net_sales'], 2) . "</td>
    </tr>
    <tr id=discounts onClick=\"gotoReceiptListT('$begin_date', 'Online')\">
      <td>Online:</td>
      <td align=right>" . $retVals['olo_count'] . "</td>
      <td>\$" . $retVals['olo_sales'] . "</td>
    </tr>
    <tr>
      <td>Labor:</td>
      <td>&nbsp; &nbsp;</td>
      <td>" . $retVals['labor_pct'] . "%</td>
    </tr>
    <tr>
      <td>Discounts:</td>
      <td>&nbsp; &nbsp;</td>
      <td>\$" . $retVals['discounts'] . "</td>
    </tr>
  </table>
  <p>";

// Flush query buffer
flushQuery();

// Disconnect from DB
include 'disconnect_from_db.php';
?>


  </body>
</html>
