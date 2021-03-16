<?php include 'dbconfig.php' ?>

<?php if(manager_logged_in()){ ?>

<?php
  $report_period = $_POST['period'];
  $report_type = $_POST['by'];
  $report_submit = $_POST['report_submit'];
?>

<?php if(isset($report_submit)){ ?>

<?php
  //helper functions
  function unabbreviate($value){
    if($value=='as') $value='All Sales';
    if($value=='p') $value='Products';
    if($value=='v') $value='Vendors';
    if($value=='a') $value = 'All';
    if($value=='pw') $value = 'Past Week';
    if($value=='cm') $value = 'Current Month';
    if($value=='pm') $value = 'Previous Month';
    if($value=='py') $value = 'Previous Year';
    return $value;
  }
  function title($period,$type){
    $period = unabbreviate($period);
    $type = unabbreviate($type);
    return "<h1>Report by <b>$type</b> during period <b>$period</b>:</h1>";
  }
  function table_row($row_data,$headers){
    $str = "<tr class='bb1s'>";
    $num_cols = count($row_data);
    for($x = 0; $x < $num_cols; $x++){
      if($headers) $str .= "<th class='bb1s'>".$row_data[$x]."</th>";
      else $str .= "<td class='bb1s'>".$row_data[$x]."</td>";
    }
    $str .= "</tr>";
    return $str;
  }
  function echo_row($row,$keys,$report_type){
    $row_array = array();
    foreach($keys as $key){
      $row_array[] = $row[$key];
    }

    echo table_row($row_array,false);
  }
  function echo_row_headers($report_type){
    $headers = array();
    if($report_type=='as'){
      $headers[] = "Product Name";
      $headers[] = "Vendor Name";
      $headers[] = "Unit Cost";
      $headers[] = "Current Quantity";
      $headers[] = "Sold Quantity";
      $headers[] = "Sold Unit Price";
      $headers[] = "Subtotal";
      $headers[] = "Profit";
      $headers[] = "Customer Name";
      $headers[] = "Order Date";
    }
    if($report_type=='p'){
      $headers[] = "Product Name";
      $headers[] = "Vendor Name";
      $headers[] = "Unit Cost";
      $headers[] = "Current Quantity";
      $headers[] = "Sold Quantity";
      $headers[] = "Sold Unit Price";
      $headers[] = "Subtotal";
      $headers[] = "Profit";
    }
    if($report_type=='v'){
      $headers[] = "Vendor Name";
      $headers[] = "Quantity In Stock";
      $headers[] = "Amount To Vendor";
      $headers[] = "Sold Quantity";
      $headers[] = "Subtotal";
      $headers[] = "Profit";
    }
    echo table_row($headers,true);
    return $headers;
  }
  function report_keys($report_type){
    $headers = array();
    if($report_type=='as'){
      $headers[] = "PRODUCT_NAME";
      $headers[] = "VENDOR_NAME";
      $headers[] = "UNIT_COST";
      $headers[] = "CURRENT_QUANTITY";
      $headers[] = "SOLD_QUANTITY";
      $headers[] = "SOLD_UNIT_PRICE";
      $headers[] = "SUBTOTAL";
      $headers[] = "PROFIT";
      $headers[] = "CUSTOMER_NAME";
      $headers[] = "ORDER_DATE";
    }
    if($report_type=='p'){
      $headers[] = "PRODUCT_NAME";
      $headers[] = "VENDOR_NAME";
      $headers[] = "UNIT_COST";
      $headers[] = "CURRENT_QUANTITY";
      $headers[] = "SOLD_QUANTITY";
      $headers[] = "SOLD_UNIT_PRICE";
      $headers[] = "SUBTOTAL";
      $headers[] = "PROFIT";
    }
    if($report_type=='v'){
      $headers[] = "VENDOR_NAME";
      $headers[] = "QUANTITY_IN_STOCK";
      $headers[] = "AMOUNT_TO_VENDOR";
      $headers[] = "SOLD_QUANTITY";
      $headers[] = "SUBTOTAL";
      $headers[] = "PROFIT";
    }

    return $headers;
  }

?>

<html>
  <head>
    <title>View Reports</title>
    <link type='text/css' rel='stylesheet' href='style.css' />
  </head>
  <body>
    <table class='bb1s'>
    <?php echo title($report_period,$report_type);
          $headers = echo_row_headers($report_type);?>
      <?php
      $sql = "call p_REPORT('$report_type', '$report_period')";
      $conn = connect_to_db("2019F_kuleszar");
      $report_type = mysqli_real_escape_string($conn, $report_type);
      $report_period = mysqli_real_escape_string($conn, $report_period);
      $result = $conn->query($sql);

      $p_total = 0;
      $p_profit = 0;
      $v_amtVendor = 0;
      $v_total = 0;
      $v_profit = 0;
      while($row = $result->fetch_assoc()){
        $keys = report_keys($report_type);
        echo_row($row,$keys,$report_type);

        if($report_type == 'p'){
          $p_total += $row['SUBTOTAL'];
          $p_profit += $row['PROFIT'];
        }
        if($report_type == 'v'){
          $v_amtVendor += $row["AMOUNT_TO_VENDOR"];
          $v_total += $row["SUBTOTAL"];
          $v_profit += $row["PROFIT"];
        }

      }

      if($report_type == 'p'){
        echo "<tr class='bb1s'><td class='bb1s'>Total</td><td class='bb1s' colspan=5></td><td class='bb1s'>$p_total</td><td class='bb1s'>$p_profit</td></tr>";
      }
      if($report_type == 'v'){
        echo "<tr class='bb1s'><td class='bb1s'>Total</td><td class='bb1s'></td><td class='bb1s'>$v_amtVendor</td><td class='bb1s'></td><td class='bb1s'>$v_total</td><td class='bb1s'>$v_profit</td></tr>";
      }

      echo "</table>";
      $conn->close();
      ?>
    </table>
  </body>
  <footer>
  </footer>
</html>

<?php } ?>
<?php  } else {
    header("location: phase2_emp_login.php");
  }
?>
