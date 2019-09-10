<html>
  <head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/styleDashBoard.css" />
  </head>
  <body>

<div id=container>
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
    From: <input type="date" name="dateBegin">
    To: <input type="date" name="dateEnd">
    <input type="submit">
  </form>

<?php
// Connecting, selecting database
//require 'dbconnect.php';

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


# Fix date for printing to screen. Check for endDate. Use if there.
$print_begin_date = date_create_from_format("Y-m-d", $begin_date);
$print_begin_date = date_format($print_begin_date, "n/j/Y");

# Set current date text as a link to this page.
$current_date_link = '<a href="dashboard.php">Current Date:</a> ';
# End date there?
echo "  <div id=report_date>\n";
if($_GET['dateEnd']) {
  if($_GET['dateBegin'] != $_GET['dateEnd']) {
    $print_end_date = date_create_from_format("Y-m-d", $end_date);
    $print_end_date = date_format($print_end_date, "n/j/Y");

    echo $current_date_link . $print_begin_date . " to " . $print_end_date . "<p>\n";
  }
  else {
    echo $current_date_link . $print_begin_date . "<p>\n";
  }
}
else {
  echo $current_date_link . $print_begin_date . "<p>\n";
}
echo" </div>\n";

?>
  <div id=summary>
<?php include 'summary.php'; ?>
  </div>

  <div id=staff>
<?php include 'on_shift.php'; ?>
  </div>

  <div id=hours>
<?php include 'hourly.php'; ?>
  </div>

  <div id=discounts>
<?php include 'discounts.php'; ?>
  </div>

  <div id=discounts>
<?php include 'roll_counts.php'; ?>
  </div>

  <div id=discounts>
<?php include 'sand_size_counts.php'; ?>
  </div>

</div>

<?php

//// Close database connection
//// Free resultset
//mysqli_free_result($result);
//
//// Closing connection
//mysqli_close($conn);
?>


  </body>
</html>
