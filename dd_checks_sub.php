<html>
  <head>
    <title>Receipts List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/styleDashBoard.css" />
  </head>

  <body>
<!-- Begin script -->
  <script type="text/javascript">
<!-- Open new window for receipt details -->
    function gotoReceiptDetails(rID) {
      var loc = "./dd_view_check.php?checkID=" + rID;
      window.open(loc);
    }
  </script>
<!-- End script -->

  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    From: <input type="date" name="dateBegin">
    To: <input type="date" name="dateEnd">
    <input type="hidden" name="discountName">
    <input type="hidden" name="orderType">
    <input type="submit">
  </form>

<?php
// Connecting, selecting database
$link = mysql_connect('tiny', 'ddUser', 'ddd@t@')
    or die('Could not connect: ' . mysql_error());
#echo 'Connected successfully';
mysql_select_db('dd_data') or die('Could not select database');

# If date(s) is/are set, use it/them. Otherwise, default to today.
# Also, if dateBegin has a timestamp too, we just want the checks
# from that hour.

# Separate date and time. We're looking for a timestamp.
list($date, $time) = split(" ", $_GET['dateBegin']);
list($hr, $min, $sec) = split(":", $time);
$_GET['dateBegin'] = $date;

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

# Fix date for printing to screen. Check for endDate. Use if there.
$print_begin_date = date_create_from_format("Y-m-d", $begin_date);
$print_begin_date = date_format($print_begin_date, "n/j/Y");

# End date there?
if($_GET['dateEnd']) {
  if($_GET['dateBegin'] != $_GET['dateEnd']) {
    $print_end_date = date_create_from_format("Y-m-d", $end_date);
    $print_end_date = date_format($print_end_date, "n/j/Y");

    echo "Current Date: " . $print_begin_date . " to " . $print_end_date . "<p>\n";
  }
  else {
    echo "Current Date: " . $print_begin_date . "<p>\n";
  }
}
else {
  echo "Current Date: " . $print_begin_date . "<p>\n";
}

# Apend timestamps for query
# If timestamp is passed in, send end date to the next hour, not day.
if($time) {
echo "Got at timestamp: " . $_GET['dateBegin'] . "<p>\n";
  $begin_date .= " $time";
  $end_date .= " $hr:59:00";
}
else {
  $begin_date .= " 00:00:00";
  $end_date .= " 23:59:00";
}

echo "Query timestamps: " . $begin_date ." " . $end_date . "<p>\n";

// Select checks. Date field must be quoted. Hence, the double quoting.
// Has a discount name been passed?

# Special for hidden form input types. Fucking stupid.
if($_GET['discountName'] != undefined && $_GET['discountName'] != NULL) {
$discount_name = $_GET['discountName'];
echo "Discount Name: $discount_name\n";

  $query = "SELECT receipt.id, receipt.check_num, receipt.check_time, receipt.cust_name, receipt.net_sale, receipt.sales_tax, (receipt.net_sale+receipt.sales_tax) total, receipt.order_type 
  FROM discount_item, discount_listing, receipt 
  WHERE
       discount_item.id=discount_listing.discount_item_id AND 
       receipt.id=discount_listing.receipt_id AND
       receipt.check_time>='" . $begin_date . "' AND 
       receipt.check_time<='" . $end_date . "'AND
       discount_item.name='" . $discount_name . "'
  ORDER BY receipt.check_num;";
}
else if($_GET['orderType'] != undefined && $_GET['orderType'] != NULL) {
$order_type = $_GET['orderType'];
echo "Order Type: $order_type\n";

  $query = "SELECT id, check_num, check_time, cust_name, net_sale, sales_tax, (net_sale+sales_tax) total, order_type
  FROM receipt
  WHERE check_time>='" . $begin_date . "' AND
        check_time<='" . $end_date . "' AND
        order_type='" . $order_type . "'
  ORDER BY check_num;";

}
else {
#  $query = "SELECT id, check_num, check_time, cust_name, net_sale, sales_tax, (net_sale+sales_tax) total, order_type
#  FROM receipt
#  WHERE check_time>='" . $begin_date . "' AND
#        check_time<='" . $end_date . "';";
  $query = "call CheckInfoAll('" . $begin_date . "', '" . $end_date . "')";
}

echo "<p>\n";
#echo "$query \n";

$result = mysql_query($query) or die('Query failed: ' . mysql_error());

// Print results as text
echo "<table id='receipts' border=0 cellpadding=3>
       <tr>
<!--
         <th>ID</th> 
-->
         <th>Check #</th>
         <th>Time</th>
         <th>Name</th>
         <th>Sales</th>
         <th>Tax</th>
         <th>Total</th>
         <th>Type</th>
       </tr>\n";

while($row = mysql_fetch_array($result)) {

  # Separate date and time. Fix time to 12hr.
  list($date, $time) = split(" ", $row[2]);
  list($hr, $min, $sec) = split(":", $time);

  if($hr == 12) {
    $time = $hr . ":" . $min . "pm";
  }
  else if($hr < 12) {
    $time = $hr . ":" . $min . "am";
  }
  else {
    $time = $hr-12 . ":" . $min . "pm";
  }

echo "<tr onClick=\"gotoReceiptDetails($row[0])\">
<!--
        <td>$row[0]</td>
        <td><a href='./dd_view_check.php?checkID=$row[0]' target='_blank'>$row[1]</a></td>
-->
        <td>$row[1]</td>
        <td>$time</td>
        <td>$row[3]</td>
        <td>$$row[4]</td>
        <td>$$row[5]</td>
        <td>$$row[6]</td>
        <td>$row[7]</td>
      </tr>\n";
}

echo "</table>

      <p>\n";

$query = "SELECT hour(check_time) AS hour, count(*) AS orders, sum(net_sale) AS sales
FROM receipt 
WHERE check_time>='" . $begin_date . " 00:00:00' AND
      check_time<='" . $end_date . " 23:59:00'
GROUP BY hour(check_time);";

$result = mysql_query($query) or die('Query failed: ' . mysql_error());

## Begin hourly table
#echo "<table id=summary border=0 cellpadding=3>
#        <tr>
#          <th>Hour</th>
#          <th>Orders</th>
#          <th>Sales</th>
#        </tr>";
#
#while($row = mysql_fetch_array($result)) {
#  # Fix time.
#  if ($row[0] > 12){
#    $row[0] -= 12;
#    $ampm = 'pm';
#  }
#  else if ($row[0] == 12){
#    $ampm = 'pm';
#  }
#  else {
#    $ampm = 'am';
#  }
#
#  echo "  
#          <tr> 
#            <td>" . $row[0] . $ampm . "</td>
#            <td align=right>" . $row[1] . "</td>
#            <td>\$" . $row[2] . "</td>
#          </tr>";
#}
#
#echo "</table>
#      <p>";
#
## Select discounts, counts, and totals
#$query = "SELECT discount_item.name, count(*) AS number, sum(discount_item.price)*-1 AS total
#FROM discount_item, discount_listing, receipt 
#WHERE
#     discount_item.id=discount_listing.discount_item_id AND 
#     receipt.id=discount_listing.receipt_id AND
#     receipt.check_time>'" . $begin_date . " 00:00:00' AND 
#     receipt.check_time<'" . $end_date . " 23:59:00' 
#GROUP BY discount_item.name;";
#
#$result = mysql_query($query) or die('Query failed: ' . mysql_error());
#
## Begin discounts table
#echo "<table id=summary border=0 cellpadding=3>
#        <tr>
#          <th align=left>Discounts</th>
#          <th>&nbsp;</th>
#          <th>&nbsp;</th>
#        </tr>";
#
#while($row = mysql_fetch_array($result)) {
#  echo "  
#          <tr> 
#            <td>" . $row[0] . "</td>
#            <td align=right>" . $row[1] . "</td>
#            <td>\$" . $row[2] . "</td>
#          </tr>";
#}
#
#echo "</table>
#      <p>";

// Free resultset
mysql_free_result($result);

// Closing connection
mysql_close($link);
?>


  </body>
</html>
