<?php
  // Start session so we can user the session data
  session_start();
  // Establish a DB connection
  include_once 'dbConnect.php';
  
  $eeID = $_GET['eeid'];

  $userRow = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']));
  if($userRow['type'] != 'Nominator')
  {
    header("Location: index.php");
  }
  // Make sure YOU are the right nominator for this nominee
  $nomRow = mysql_fetch_array(mysql_query("SELECT * FROM nomination WHERE eeID = ".$eeID));
  if ($_SESSION['user'] != $nomRow['orID'])
  {
    ?>
    <script>alert("You are not this nominee's nominator!");</script>
    <?php
  }
  
  // Get the user's info so we can use it in the page
  $eeRow = mysql_fetch_array(mysql_query("SELECT * FROM ee_info WHERE eeID=".$eeID));
  
  if (isset($_POST['btn-verify'])) {
    if(mysql_query("UPDATE nomination SET complete = '2' WHERE eeID = '$eeID'"))
    {
      ?>
      <script>alert("Updated nomination completion");</script>
      <?php
      header("Location: nominee_info.php?eeid=$eeID");
    }
    else
    {
      ?>
      <script>alert("Could not update nomination completion");</script>
      <?php
    }
  }
  if (isset($_POST['btn-unverify'])) {
    if(mysql_query("UPDATE nomination SET complete = '1' WHERE eeID = '$eeID'"))
    {
      ?>
      <script>alert("Updated nomination completion");</script>
      <?php
      header("Location: nominee_info.php?eeid=$eeID");
    }
    else
    {
      ?>
      <script>alert("Could not update nomination completion");</script>
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
      Nominee
    </title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" />
  </head>

  <body>
    <div id="header">
      <div id="left">
        <h1>Nominee Info</h1>
      </div>
      <div id="right">
        <div id="content">
          Hi <?php echo $userRow['fname']; ?>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="logout.php?logout">Sign Out</a>
        </div>
      </div>
    </div>
    <div id="content">
      
      <form action="send_email.php" method="post" name="sendemail">
        <input type="hidden" name="from" value="no-reply@gtams.com" />
        <input type="hidden" name="to" value= "<?php echo $email ?>" />
        <input type="hidden" name="subj" value= "<?php echo $subj ?>" />
        <input type="hidden" name="body" value= "<?php echo $ehtml ?>" />
      </form>
      
      <!-- Show user stuff -->
      <table align="center" width="100%" border="0">
        <col width="200">
        <tr>
          <td><h3>Nominee Info</h3></td>
        </tr>
        <tr>
          <td>Nominator<br/><?php echo $userRow['fname'] ?> <?php echo $userRow['lname'] ?></td>
        </tr>
        <tr>
          <td>PID<br/><?php echo $eeRow['pid'] ?></td>
        </tr>
        <tr>
          <td>Phone Number<br/><?php echo $eeRow['phone'] ?></td>
        </tr>
        <tr>
          <td>
            Currently a Ph.D Student in the Department of Computer Science?<br/>
            <?php
              if($eeRow['cs_student']==1)
                print ("yes");
              else
                print ("no");
            ?>
          </td>
        </tr>
        <tr>
          <td>
            Semesters have you been a grad<br/>
            <?php echo $eeRow['num_sem_grad'] ?>
          </td>
        </tr>
        <tr>
          <td>
            Nominee graduate from a US institution?<br/>
            <?php
              if($eeRow['usGrad']==1)
                print ("yes");
              else
                print ("no");
            ?>
            
          </td>
        </tr>
        <tr>
          <td>
            Nominee pass the SPEAK test?<br>
            <?php
              if($eeRow['speak_test']==1)
                print("yes");
              else 
                print("no");
            ?>
          </td>
        </tr>
        <tr>
          <td>
            Semesters have you been a GTA?<br/>
            <?php echo $eeRow['num_sem_gta'] ?>
          </td>
        </tr>
        <tr>
          <td>GPA<br/><?php echo $eeRow['grad_gpa'] ?></td>
        </tr>
        <hr>
        <tr>
          <td><h3>Classes</h3></td>
        </tr>
          <!-- Courses -->
          <?php
            $classes=mysql_query("SELECT c.name, c.grade FROM classes_taken c where c.eeID='$eeID'");
          
            while(  $row=mysql_fetch_row($classes) )
            {
              print("<tr><td>$row[0]</td><td>$row[1]</td></tr>");
            }
          ?>
        <tr>
          <td><h3>Publications</h3></td>
        </tr>
          <!-- Publications -->
          <?php
            $publications=mysql_query("SELECT pub.date, pub.title FROM publications pub where pub.eeID='$eeID'");
          
            while(  $row=mysql_fetch_row($publications) )
            {
              print("<tr><td>$row[1]</td><td>$row[0]</td></tr>");
            }
          ?>
        <tr>
          <td><h3>Advisors</h3></td>
        </tr>
          <!-- Advisors -->
          <?php
            $advisors=mysql_query("SELECT o.fname, o.lname, i.start, i.stop
                                  FROM  advising AS i, advisors AS o
                                  WHERE i.eeID = $eeID
                                    AND i.advID = o.ID");
            while(  $rowadv = mysql_fetch_row($advisors) )
            {
              print("<tr><td>$rowadv[0] $rowadv[1]</td><td>$rowadv[2] ----> $rowadv[3]</td>");
            }
          ?>
      </table>
      <!-- Submit finished proposal -->
      <form method="post">
        <table>
          <tr>
            <?php
              if ($nomRow['complete'] == 0)
              {
                print("<td><h3>Nominee has not yet completed info</h3></td>");
              }
              else if ($nomRow['complete'] == 1)
              {
                print("<td><button type='submit' name='btn-verify'>Verfy</button></td>");
              }
              else if ($nomRow['complete'] == 2)
              {
                print("<td><button type='submit' name='btn-unverify'>Un-Verfy</button></td>");
              }
            ?>
          </tr>
        </table>
      <form>
    </div>
  </body>
</html>