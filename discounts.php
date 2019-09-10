<html>
  <head>
    <title>Disounts</title>
  </head>
  <body>
<!-- Begin script -->
  <script type="text/javascript">
<!-- Open new window for receipt details -->
    function gotoReceiptListD(date,discDesc) {
      var loc = "./dd_checks_sub.php?dateBegin=" + date + "&discountName=" + discDesc;
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

# Select discounts, counts, and totals
#$query = "SELECT discount_item.name, count(*) AS number, sum(discount_item.price)*-1 AS total
#FROM discount_item, discount_listing, receipt
#WHERE
#     discount_item.id=discount_listing.discount_item_id AND
#     receipt.id=discount_listing.receipt_id AND
#     receipt.check_time>'" . $begin_date . " 00:00:00' AND
#     receipt.check_time<'" . $end_date . " 23:59:00'
#GROUP BY discount_item.name;";

$query = "call DiscountSummary('" . $begin_date . " 00:00:00', '" . $end_date . " 23:59:00')";

$result = mysqli_query($conn, $query) or die('Query failed: ' . mysql_error());

# Begin discounts table
echo "
<!--
  <table id=discounts border=0 cellpadding=3>
  <table border=0 cellpadding=3>
-->
  <table>
    <tr>
      <th align=left>Discounts</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>";

while($row = mysqli_fetch_array($result)) {
  echo "
          <tr onClick=\"gotoReceiptListD('$begin_date', '$row[0]')\">
            <td nowrap>" . $row[0] . "</td>
            <td align=right>" . $row[1] . "</td>
            <td>\$" . $row[2] . "</td>
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
