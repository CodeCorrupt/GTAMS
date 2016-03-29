<?php
  session_start();
  include_once 'dbConnect.php';

  if(!isset($_SESSION['user']))
  {
    header("Location: index.php");
  }
  $res = mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']);
  $userRow = mysql_fetch_array($res);
?>

<!-- HTML stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Welcome - <?php echo $userRow['username']; ?></title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" />
  </head>

  <body>
    <div id="header">
      <div id="left">
        <h1>Home</h1>
      </div>
      <div id="right">
        <div id="content">
          hi <?php echo $userRow['username']; ?>&nbsp;<a href="logout.php?logout">Sign Out</a>
        </div>
      </div>
    </div>
  </body>
</html>