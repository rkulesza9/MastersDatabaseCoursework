<?php include 'dbconfig.php'; ?>
<html>
<header>
  <title>Customer Home Page</title>
  <style>
    table {
      border: 1px black solid;
    }
    .warning {
      color:red;
    }
  </style>
</header>
<body>
<?php
  if(customer_logged_in()){
    $ip = $_SERVER['REMOTE_ADDR'];
    if(substr($ip,0,3) == '10.' || substr($ip,0,8) == '131.125.'){
      $from_kean = "You are from Kean Unviersity.";
    } else {
      $from_kean = "You are NOT from Kean University.";
    }

    //get data from DB
    $conn = connect_to_db("2019F_kuleszar");
    $query = "select first_name, last_name, address, city, zipcode from CUSTOMER where login_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s",$_COOKIE['user_id']);
    $stmt->bind_result($first_name, $last_name, $address, $city, $zipcode);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    $conn->close();

  ?>

  <table>
    <tr><td>Welcome customer:<strong><?php echo $first_name." ".$last_name ?></strong></td></tr>
    <tr><td><?php echo $address.", ".$city.", ".$zipcode ?></td></tr>
    <tr><td><?php echo "Your IP: ".$ip; ?></td></tr>
    <tr><td><?php echo $from_kean; ?></td></tr>
    <tr><td><a href='phase2_cus_login.php?logout=true'>Customer logout</a></td></tr>
    <tr><td><a href='phase2_cus_update.php'>Update my data</a></td></tr>
    <tr><td></td></tr>
    <tr><td><a href='phase2.php'>project home page</a></td></tr>
    <tr><td><a href='search_product.php'>Search and Order</a></td></tr>
    <tr><td><a href='customer_order_history.php'>View Order History</a></td></tr>
  </table>
  <br><form action='search_product.php' method='GET'>
    <table>
      <tr><td colspan=2>Search Product (* for all):</tr>
      <tr><td><input type='text' name='search_value' /></td><td><input type='submit' name='search' /></tr>
    </table>
  </form>
  <?php
    //get image, description, url from db  by calling get_advertisement_from_last_keyword()
    //id, category, image, description, url
    $login = $_COOKIE['user_id'];
    $conn = connect_to_db("2019F_kuleszar");
    $query = "call get_advertisement_from_last_keyword((select customer_id from CUSTOMER where login_id='".mysqli_escape_string($conn,$login)."'))";
    $result = $conn->query($query);

    $row = $result->fetch_assoc();
    $id = $row['id'];
    $image = $row['image'];
    $category = $row['category'];
    $description = $row['description'];
    $url = $row['url'];

    //convert mysql blob to data-url
    $type = "image/png"; //or the actual mime type of the file
    $base64blob = base64_encode($image); //encode to base64
    $datauri = "data:$type;base64,$base64blob";
    echo "<p><a href='$url'>"
          ."<img src='$datauri' />"
          ."</a><br>"
          ."$description</p>";
    $conn->close();

    //display image with src=data url wrapped in a with href=link

    //write the description beneath the image
  ?>

  <a href='phase2_cus_login.php'>Customer Home Page</a><br>
  <a href='phase2.php'>Project Home Page</a>;
  <?php } else { ?>
    <form action='phase2_cus_login.php' method='post'>
      <span class='warning'><?php echo $warning; ?></span>
      <table>
        <tr><th colspan=2>Customer Login</th></tr>
        <tr><td>Login ID:</td><td><input type='text' name='cus_login' /></td></tr>
        <tr><td>Password:</td><td><input type='password' name='cus_password' /></td></tr>
        <tr><td><input type='submit' value='Login' name='cus_submit' /></td></tr>
      </table>
    </form>
  <?php } ?>
</body>
<footer>
</footer>
</html>
