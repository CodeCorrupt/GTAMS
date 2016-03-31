<?php
  session_start();
  if(isset($_SESSION['user']) != "")
  {
    header("Location: home.php");
  }
  include_once 'dbConnect.php';
  
  if(isset($_POST[ 'btn-signup' ]))
  {
    $fname = mysql_real_escape_string($_POST['fname']);
    $lname = mysql_real_escape_string($_POST['lname']);
    $email = mysql_real_escape_string($_POST['email']);
    $upass = md5(mysql_real_escape_string($_POST['pass']));
    $type  = mysql_real_escape_string($_POST['type']);
    
    if(mysql_query("INSERT INTO users(fname, lname, email, password, type) VALUES('$fname', '$lname','$email','$upass','$type')"))
    {
      ?>
      <script>alert('successfully registered ');</script>
      <?php
    }
    else
    {
      ?>
      <script>alert('error while registering you...');</script>
      <?php
    }
  }
?>


<!-- HTML Stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>GTAMS - Registration</title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" />
  </head>
  
  <body>
    <center>
      <div id="login-form">
        <form method="post">
          <table align="center" width="30%" border="0">
            <tr>
              <td><input type="text" name="fname" placeholder="First Name" required /></td>
            </tr>
            <tr>
              <td><input type="text" name="lname" placeholder="Last Name" required /></td>
            </tr>
            <tr>
              <td><input type="email" name="email" placeholder="Your Email" required /></td>
            </tr>
            <tr>
              <td><input type="password" name="pass" placeholder="Your Password" required /></td>
            </tr>
            <tr>
              <td>
                <select name="type">
                  <option value="Nominator">Nominator</option>
                  <option value="Nominee">Nominee</option>
                  <option value="Member">GC Member</option>
                  <option value="Admin">System Admin</option>
                </select>
              </td>
            </tr>
            <tr>
              <td><button type="submit" name="btn-signup">Sign Me Up</button></td>
            </tr>
            <tr>
              <td><a href="index.php">Sign In Here</a></td>
            </tr>
          </table>
        </form>
      </div>
    </center>
  </body>
</html>