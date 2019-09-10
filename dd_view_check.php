<html>
  <head>
    <title>Recipt Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/styleDashBoard.css" />
  </head>
  <body>

  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    Check #: <input type="input" name="checkNum">
    <br> OR <br>
    Check Name: <input type="input" name="checkName">
    <input type="hidden" name="checkID">
    <input type="submit">
  </form>

<?php
// Define functions
function getOrderItems($checkID) {
  $query = "SELECT item, price, quantity
FROM OrderDetailItems
WHERE check_id=" . $checkID . ";";

  return $query;
}

function getOrderDiscounts($checkID) {
  $query = "SELECT item, price, quantity
FROM OrderDetailDiscounts
WHERE check_id=" . $checkID . ";";

  return $query;
}
  
  
// Connecting, selecting database
require 'dbconnect.php';

// Select check details
if ($_GET['checkNum']) {
  $query = "SELECT id, check_num, check_time, cust_name
FROM receipt
WHERE check_num=" . $_GET['checkNum'] . ";";

}
else if ($_GET['checkName']) {
  $query = "SELECT id, check_num, check_time, cust_name
FROM receipt
WHERE cust_name LIKE '%" . $_GET['checkName'] . "%';";

}
else if ($_GET['checkID']) {
  $query = "SELECT id, check_num, check_time, cust_name, net_sale, sales_tax, (net_sale+sales_tax) total, order_type
FROM receipt
WHERE id=" . $_GET['checkID'] . ";";

}

#echo "$query\n";
$result = mysqli_query($conn, $query) or die('Query failed: ' . mysql_error());

// Printing results in HTML
echo "<table id=summary border=0 cellpadding=3>\n";
while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    # First query is receipt info heading
    echo "<tr>\n";
      echo "<th>" . $line['check_num'] . "</th>\n";
      echo "<th>" . $line['check_time'] . "</th>\n";
      echo "<th>" . $line['cust_name'] . "</th>\n";
    echo "\t</tr>\n";

    // Get check item details by id.
    # Second query is for order items
    $query_items =  getOrderItems($line['id']);
#echo "Query Items: $query_items\n";
    $items = mysqli_query($conn, $query_items) or die('Query failed: ' . mysql_error());
    
    while ($row = mysqli_fetch_array($items, MYSQL_ASSOC)) {
      echo "<tr>\n";
      if($row['quantity']>1) {
        echo "<td>" . $row['quantity'] . "</td>\n";
      }
      else {
        echo "<td>&nbsp;</td>\n";
      }
        echo "<td>" . $row['item'] . "</td>\n";
        echo "<td>" . $row['price'] . "</td>\n";
      echo "\t</tr>\n";
    }

    // Get check discount details by id.
    # Third query is for any discounts
    $query_items =  getOrderDiscounts($line['id']);
#echo "Query Items: $query_items\n";
    $items = mysqli_query($conn, $query_items) or die('Query failed: ' . mysql_error());
    
    while ($row = mysqli_fetch_array($items, MYSQL_ASSOC)) {
      echo "<tr>\n";
        echo "<td>&nbsp;</td>\n";
        echo "<td>" . $row['item'] . "</td>\n";
        echo "<td>" . $row['price'] . "</td>\n";
      echo "\t</tr>\n";
    }

    // Add footer totals from $line of first query
    echo "<tr>\n";
      echo "<td>&nbsp;</td>\n";
      echo "<td>&nbsp;</td>\n";
      echo "<td>--------------</td>\n";
    echo "\t</tr>\n";
    echo "<tr>\n";
      echo "<td>&nbsp;</td>\n";
      echo "<td align=right>Subtotal: </td>\n";
      echo "<td>" . $line['net_sale'] . "</td>\n";
    echo "\t</tr>\n";
    echo "<tr>\n";
      echo "<td>&nbsp;</td>\n";
      echo "<td align=right>Tax: </td>\n";
      echo "<td>" . $line['sales_tax'] . "</td>\n";
    echo "\t</tr>\n";
    echo "<tr>\n";
      echo "<td>&nbsp;</td>\n";
      echo "<td align=right>Total: </td>\n";
      echo "<td>" . $line['total'] . "</td>\n";
    echo "\t</tr>\n";

}
echo "</table>\n";


// Free resultset
mysqli_free_result($result);
mysqli_free_result($items);

// Closing connection
mysqli_close($conn);
?>


  </body>
</html>

