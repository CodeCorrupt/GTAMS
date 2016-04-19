<?php
  session_start();
  include_once 'dbConnect.php';

  if(!isset($_SESSION['user']))
  {
    header("Location: index.php");
  }
  $userRow = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']));
  $type = $userRow['type'];
  switch ($type) {
    case 'Admin':
        header("Location: sys_admin.php");
        break;
    case 'Member':
        header("Location: gc.php");
        break;
    case 'Nominee':
        header("Location: nominee.php");
        break;
    case 'Nominator':
        header("Location: nominator.php");
        break;
}
?>

<!-- HTML stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Welcome - <?php print($userRow['fname']); ?></title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" />
  </head>

  <body>
    <div id="header">
      <div id="left">
        <h1>Home</h1>
      </div>
      <div id="right">
        <div id="content">
          Hi <?php echo $userRow['fname']; ?>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="logout.php?logout">Sign Out</a>
        </div>
      </div>
    </div>
  </body>
</html>