<?php
  // Start session so we can user the session data
  session_start();
  // Establish a DB connection
  include_once 'dbConnect.php';

 $res = mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']);
  $userRow = mysql_fetch_array($res);
    if($userRow['type'] != 'Nominator')
    {
     header("Location: index.php");
    }

  $eeID = $_GET['eeid'];
 
 

?>



<!-- HTML stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
      Nominee Information
    </title>
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
    <div id="content">
      
      <?php
      $results=mysql_query("SELECT * FROM ee_info e WHERE e.eeID='$eeID'");
      print("<table border='1' style='margin: auto;'>");

  $metadata=mysql_fetch_field($results);
  print("<tr>");
 
  for($i=0;$i<mysql_num_fields($results); $i++)
  {
   $metadata=mysql_fetch_field($results);
      print("<td>");
      printf("%s", $metadata->name);
      print("</td>");
      
  }
    print("</tr>");
while($row = mysql_fetch_row($results))
 {
       print("<tr>");
        foreach( $row as $key => $value)
            print("<td> $value </td>");
       print("</tr>");
 }
  print("</table>");
 
      
      
      ?>
  <form method="post" action="nominee_info.php" >
    <input type = "checkbox" name = "verify" value="verfied"> Verify <br>
    <input type= "submit" name="submit" value="submit">
    
  </form>    
  <?php if(isset($_POST['submit'])):
    
    if(isset($_POST['verify']))
    {
      mysql_query("UPDATE nomination SET complete=2 WHERE eeID=$eeID");
    }
    else {
           mysql_query("UPDATE nomination SET complete=1 WHERE eeID=$eeID");

    }
  
  ?>
   <?php else:
            ?> 
        <?php endif; ?>

    </div>
    
      
  </body>
</html>