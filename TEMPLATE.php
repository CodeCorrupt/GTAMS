<!-- 
  This is a template to use when creating your content.
  Please use this as your base so that all of our pages have similar layouts.
-->

<?php
  // Start session so we can user the session data
  session_start();
  // Establish a DB connection
  include_once 'dbConnect.php';
  
  // If there is no session then the user is not logged in and redirect to index
  if(!isset($_SESSION['user']))
  {
    header("Location: index.php");
  }
  // Get the user's info so we can use it in the page
  $res = mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']);
  $userRow = mysql_fetch_array($res);
  
  /****************************************************
  *This below is used to work with forms and DB calls *
  ****************************************************/
  if(isset($_POST[ 'btn-<<<BUTTON NAME>>>' ])) //If you use a form below then this will handle the post
  {
    $item1 = mysql_real_escape_string($_POST['item1']);
    $item2 = mysql_real_escape_string($_POST['item2']);

    // EXAMPLE QUERY
      //if(mysql_query("INSERT INTO users(username,email,password) VALUES('$uname','$email','$upass')"))
    if(mysql_query("<<<YOUR QUERY>>>"))
    {
      ?>
      <script>alert('success');</script>
      <?php
    }
    else
    {
      ?>
      <script>alert('error while executing query...');</script>
      <?php
    }
  }
?>

<!-- HTML stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
      <!-- PUT YOUR PAGE TITLE HERE -->
    </title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" />
  </head>

  <body>
    <div id="header">
      <div id="left">
        <h1><!--PAGE TITLE --></h1>
      </div>
      <div id="right">
        <div id="content">
          hi <?php echo $userRow['username']; ?>&nbsp;<a href="logout.php?logout">Sign Out</a>
        </div>
      </div>
    </div>
    <div id="content">
      <!-- PUT YOUR SHIT HERE -->
      <!-- 
      HERE IS AN EXAMPLE FORM
      <form method="post">
        <table align="center" width="30%" border="0">
          <tr>
            <td><input type="text" name="uname" placeholder="User Name" required /></td>
          </tr>
          <tr>
            <td><input type="email" name="email" placeholder="Your Email" required /></td>
          </tr>
          <tr>
            <td><input type="password" name="pass" placeholder="Your Password" required /></td>
          </tr>
          <tr>
            <td><button type="submit" name="btn-signup">Sign Me Up</button></td>
          </tr>
          <tr>
            <td><a href="index.php">Sign In Here</a></td>
          </tr>
        </table>
      </form>
      -->
    </div>
  </body>
</html>