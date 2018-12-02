<html>
  <head>
    <title>Using PHP and Bing Maps REST Services APIs</title>
  </head>
  <body>
    <form action="BingMaps_REST_LocationsSample.php" method="post">
      Bing Maps Key: <input type="text" name="key"
     value="<?php echo (isset($_POST['key'])?$_POST['key']:'') ?>"><br>
        Street Address: <input type="text" name="address"
      value="<?php echo (isset($_POST['address'])?$_POST['address']:'') ?>"><br>
          City: <input type="text" name="city"
      value="<?php echo (isset($_POST['city'])?$_POST['city']:'') ?>"><br>
            State: <input type="text" name="state"
       value="<?php echo (isset($_POST['state'])?$_POST['state']:'') ?>"><br>
              Zip Code: <input type="text" name="zipcode"
      value="<?php echo (isset($_POST['zipcode'])?$_POST['zipcode']:'') ?>"><br>
                <input type="submit" value="Submit">
    </form>
    <?php
  // Code goes here
    ?>
  </body>
</html>