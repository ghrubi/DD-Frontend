<html>
  <head>
    <title>Roll Counts</title>
  </head>
  <body>
<!-- Begin script -->
  <script type="text/javascript">
<!-- Open new window for receipt details -->
    function gotoReceiptList(date,discDesc) {
      var loc = "./dd_checks.php?dateBegin=" + date + "&discountName=" + discDesc;
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

# Select roll counts 
#$query = "SELECT
#     (CASE
#          WHEN order_item.name REGEXP '(LG|KG)' THEN 'LG'
#          WHEN order_item.name REGEXP '(HALF|KID)' THEN 'HALF' 
#      END) as ROll, 
#     SUM(order_listing.order_item_quantity) as COUNT
#FROM order_listing, order_item, receipt
#WHERE order_listing.order_item_id=order_item.id AND
#     order_listing.receipt_id=receipt.id AND 
#     receipt.check_time>'" . $begin_date . " 00:00:00' AND
#     receipt.check_time<'" . $end_date . " 23:59:00' AND
#     order_item.name REGEXP '^(LG|KG|HALF|KID).*(CHZ|HOAG|MOTOWN|SOUTH|PIZ|CHILI|PAT|WEST|JOE|GIL|SIZZLIN|PIG)+.*$' AND
#     order_item.name NOT REGEXP '(TWIST|FF)' 
#GROUP BY ROLL;";

$query = "call RollCountSummary('" . $begin_date . " 00:00:00', '" . $end_date . " 23:59:00')";

$result = mysqli_query($conn, $query) or die('Query failed: ' . mysql_error());

# Begin roll counts table
#echo "$query<p>";
echo "
<!--
  <table id=discounts border=0 cellpadding=3>
  <table border=0 cellpadding=3>
-->
  <table>
    <tr>
      <th align=left>Roll Counts</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>";

while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
  echo "
          <tr>
            <td nowrap>" . $row['roll'] . "</td>
            <td align=right>" . $row['count'] . "</td>
            <td>&nbsp;</td>
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
