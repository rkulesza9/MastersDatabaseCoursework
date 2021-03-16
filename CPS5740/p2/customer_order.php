<?php include 'dbconfig.php'; ?>

<?php if(customer_logged_in()) { ?>

<html>
<head>
  <title>Customer Order</title>
  <link rel='stylesheet' type='text/css' href='style.css' />
</head>
<body>
  <?php
    $success_flag = false;
    $success = "";
    $warning = "";
    $login_id = $_COOKIE['user_id'];
    $place_order = $_GET['place_order'];
    $product_ids = $_GET['product_id'];
    $product_names = $_GET['product_name'];
    $order_quantities = $_GET['order_quantity'];
    $current_quantities = $_GET['current_quantity'];

    if(isset($place_order)){
      $order_id = -1;
      for($x = 0; $x < count($product_ids); $x++){
        $insert = true;

        /* empty */
        if($order_quantities[$x] == ''){
          $insert = false;
        }

        /* negative numbers */
        if($order_quantities[$x] < 0){
          $insert = false;
          $warning .= "Negative numbers are not allowed. Order did not go through for ".$product_names[$x]."<br>";
        }

        /* order_Quantity <= current_quantity */
        if($order_quantities[$x] > $current_quantities[$x]){
          $insert = false;
          $warning .= "Not enough quantity for product ".$product_names[$x]."<br>";
        }

        if($insert){
          $conn = connect_to_db("2019F_kuleszar");
          $login_id = mysqli_escape_string($conn,$login_id);
          $product_id = mysqli_escape_string($conn,$product_ids[$x]);
          $order_quantity = mysqli_escape_string($conn,$order_quantities[$x]);
          $query = "call p_CUSTOMER_ORDER((select customer_id from CUSTOMER where login_id='$login_id'), $product_id, $order_quantity, $order_id)";
          $result = $conn->query($query);

          if(is_bool($result)){
            $warning .= "There was a problem completing the order for ".$product_names[$x].". Please Try Again.<br>";
          }else{
            while($row = $result->fetch_assoc()){
              $order_id = $row['order_id'];
            }
            $success_flag = true;
            //$success .= "The product ".$product_names[$x]." was successfully ordered with order_id $order_id!<br>";
          }
        }
      }
    }
  ?>
  <span class='warning'><?php echo $warning; ?></span>
  <span class='success'><?php echo $success; ?></span>

  <?php
    if($success_flag) {
      $total = 0;

      echo "<h1>Your Order List: </h1>";
      echo "<table class='bb1s'>";
      echo "<tr clas='bb1s'><th class='bb1s'>Product Name</th><th class='bb1s'>Unit Price</th><th class='bb1s'>Quantity</th><th class='bb1s'>Subtotal</th></tr>";
      for($x=0;$x < count($product_ids); $x++){
        $product_name = $product_names[$x];
        $order_quantity = $order_quantities[$x];
        if($order_quantity <= 0) continue;
        $unit_price = $_GET['unit_prices'][$x];
        echo "<script>alert('$order_quantity');</script>";
        $subtotal = $unit_price*$order_quantity;
        $total += $subtotal;
        echo "<tr class='bb1s'>";
        echo "<td class='bb1s'>$product_name</td>";
        echo "<td class='bb1s'>$unit_price</td>";
        echo "<td class='bb1s'>$order_quantity</td>";
        echo "<td class='bb1s'>$subtotal</td>";
        echo "</tr>";
      }
      echo "<tr><td colspan=3 class='bb1s'>Total</td><td>$total</td></tr>";
      echo "</table>";
    }
  ?>
  <br><a href='customer_check_p2.php'>Customer Home Page</a>
  <br><a href='phase2.php'>Project Home Page</a>
</body>
<footer>
</footer>
</html>

<?php } else { header("location: phase2_cus_login.php"); } ?>
