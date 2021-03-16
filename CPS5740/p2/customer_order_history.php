<?php include 'dbconfig.php'; ?>

<?php if(customer_logged_in()){ ?>

<html>
<head>
  <title>Customer Order History</title>
  <link type='text/css' rel='stylesheet' href='style.css' />
  <style>
  table, tr, th, td {
    border: 1px solid black;
  }
  </style>
</head>
<body>
  <h1>Customer Order History:</h1>

  <?php
    $login_id = $_COOKIE['user_id'];
    $query = "SELECT O.order_id as 'order_id' , O.date as 'order_date', P.NAME as 'product_name', PO.quantity as 'order_quantity', P.sell_price as 'subtotal' ".
	             "FROM `ORDER` O, PRODUCT_ORDER PO, PRODUCT P ".
               "WHERE PO.product_id = P.product_id and ".
		              "O.order_id = PO.order_id and ".
                  "O.customer_id = (SELECT customer_id from CUSTOMER where login_id=?) ".
               "ORDER BY O.order_id";

   $conn = connect_to_db("2019F_kuleszar");
   $stmt = $conn->prepare($query);
   $stmt->bind_param("s",$login_id);
   $stmt->bind_result($order_id, $order_date, $product_name, $order_quantity, $sell_price);
   $stmt->execute();
   $stmt->store_result();

   $total = 0;
   $subtotal = 0;
   $last_order_id = -1;

   if($stmt->num_rows() == 0){
     echo "<p>You don't have any order history.</p>";
   } else {
     while($stmt->fetch()){
       if($last_order_id <> $order_id){
         if($last_order_id <> -1){
           echo "<tr><td></td><td>Order Paid:</td><td colspan=3 style='text-align:right;'>$subtotal</td></tr>";
           echo "</table><br>";
         }
         echo "<table>";
         echo "<tr><th>Order ID</th><th>Product Name</th><th>Order Quantity</th><th>Unit Price</th><th>Subtotal</th><th>Order Date</th></tr>";
         $last_order_id = $order_id;
         $subtotal = 0;
       }
        echo "<tr>";
        echo "<td>$order_id</td>";
        echo "<td>$product_name</td>";
        echo "<td>$order_quantity</td>";
        echo "<td>$sell_price</td>";
        echo "<td style='text-align:right;'>".($sell_price*$order_quantity)."</td>";
        echo "<td>$order_date</td>";
        echo "</tr>";

        $subtotal += $order_quantity*$sell_price;
        $total += $order_quantity*$sell_price;
     }
     echo "<tr><td></td><td>Order Paid:</td><td colspan=3 style='text-align:right;'>$subtotal</td></tr>";
     echo "</table><br>";
     echo "<table>";
     echo "<tr><td>Total Paid:</td><td>$total</td></tr>";
     echo "</table>";
   }
  ?>

  <a href='phase2_cus_login.php'>Customer Home Page</a><br>
  <a href='phase2.php'>Project Home Page</a>;
</body>
<footer>
</footer>
</html>


<?php } else { header("location: phase2_cus_login.php"); } ?>
