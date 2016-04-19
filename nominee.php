
<?php

  session_start();
  include_once 'dbConnect.php';

  if(!isset($_SESSION['user'])){
    header("Location: index.php");
     
  }
  
  // Get the user's info so we can use it in the page
  $userRow = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']));
  $eeRow = mysql_fetch_array(mysql_query("SELECT * FROM ee_info WHERE eeID=".$_SESSION['user']));
  $nomRow = mysql_fetch_array(mysql_query("SELECT * FROM nomination WHERE eeID=".$_SESSION['user']));
  $orUserRow = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE user_id=".$nomRow['orID']));

  if($userRow['type'] != "Nominee")
  {
    header("Location: index.php");
  }
  
  // Grab info from Session variables
  $eeEmail = $userRow['email'];
  $eeFirstName = $userRow['fname'];
  $eeLastName = $userRow['lname'];
  $eeID = $userRow['user_id'];
  
  if(isset($_POST[ 'btn-addInfo' ]))
  {
    $eePID  = mysql_real_escape_string($_POST['nominee_pid']);
    $eePhone  = mysql_real_escape_string($_POST['nominee_phone']);
    $numGradSemesters  = mysql_real_escape_string($_POST['semesters']);
    $numSemestersGTA  = mysql_real_escape_string($_POST['semestersGTA']);
    $eeGPA  = mysql_real_escape_string($_POST['gpa']);


    if(isset($_POST['Is_PhD'])) {
      $Is_PhD = true;
    }
    else{
      $Is_PhD = false;
    }
    
   if(isset($_POST['USGraduate'])) {
      $isUSGrad = true;
    }
    else{
      $isUSGrad = false;
    } 
    
    if(isset($_POST['passSpeak'])) {
      $passSpeak = true;
    }
    else{
      $passSpeak = false;
    } 
  
    // Get current term
    $current_term_SQL = mysql_query("SELECT s.term FROM sessions as s WHERE id = (SELECT max(id) from sessions)");
    $row_term = mysql_fetch_row($current_term_SQL);
    $current_term = $row_term[0];
    
    // Send ee_info to MySQL
    if(mysql_query("UPDATE ee_info
                    SET 
                      pid = '$eePID',
                      phone = '$eePhone',
                      cs_student = '$Is_PhD',
                      num_sem_grad = '$numGradSemesters',
                      num_sem_gta = '$numSemestersGTA',
                      speak_test = '$passSpeak',
                      grad_gpa = '$eeGPA',
                      usGrad = '$isUSGrad'
                    WHERE eeID = $eeID"))
    {
      ?>
      <script>alert('nominee successfully registered!');</script>
      <?php
      header("Location: nominee.php");
    }
    else
    {
      ?>
      <script>alert('error while setting info...');</script>
      <?php
    }
  }
  
  if (isset($_POST['btn-finished'])) {
    
    if(mysql_query("UPDATE nomination SET complete=1 WHERE eeID=$eeID"))
    {
      ?>
      <script>alert("Updated nomination completion");</script>
      <?php
      // Send post request to send_email.php with the email string
      // Set the email string
      $email = $orUserRow['email'];
      
      $ehtml = "$eeFirstName $eeLastName has finished filling out their personal information.<br/>";
      $ehtml .= "URL      : https://gtass-codecorrupt.c9users.io/nominee_info.php?eeid=".$eeID;
      
      $subj = "Action Requested: Verify Information";
      
      ////////////////////////////////////////
      // Send post request to send_email.php//
      ////////////////////////////////////////
      print("<script>window.onload = function() {document.forms['sendemail'].submit()}</script>");
      ?>
      <script>alert("Sent Email to nominator");</script>
      <?php
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
        <h1>Nominee</h1>
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
      <form method="post" action="nominee.php">
        <table align="center" width="100%" border="0">
          <col width="400">
          <col width="400">
          <tr>
            <td>Nominator First<input type='text' name='nominator_first' value="<?php echo $orUserRow['fname'] ?>"/></td>
            <td>Nominator Last<input type='text' name='nominator_last' value="<?php echo $orUserRow['lname'] ?>"/></td>
          </tr>
          <tr>
            <td>PID<input type='text' name='nominee_pid' value="<?php echo $eeRow['pid'] ?>"/></td>
          </tr>
          <tr>
            <td>Phone Number (ex. 5551234567)<input type="number" name="nominee_phone" placeholder="Phone number of Nominee" value="<?php echo $eeRow['phone'] ?>"/></td>
          </tr>
          <tr>
            <td>
              Are you currently a Ph.D Student in the Department of Computer Science?<br>
              <?php
                if($eeRow['cs_student']==1)
                  print ("<input type='checkbox' name='Is_PhD' value='Is_PhD' checked />");
                else
                  print ("<input type='checkbox' name='Is_PhD' value='Is_PhD' /> ");
              ?>
            </td>
          </tr>
          <tr>
            <td>
              How many semesters have you been a grad?
              <input type="number" name="semesters" placeholder="Semesters as grad student" value="<?php echo $eeRow['num_sem_grad'] ?>"/>
            </td>
          </tr>
          <tr>
            <td>
              Did the nominee graduate from a US institution?
              <?php
                if($eeRow['usGrad']==1)
                  print ("<input type='checkbox' name='USGraduate' value='USGraduate' checked />");
                else
                  print ("<input type='checkbox' name='USGraduate' value='USGraduate'/>");
              ?>
              
            </td>
          </tr>
          <tr>
            <td>
              If not, did the nominee pass the SPEAK test?<br>
              <?php
                if($eeRow['speak_test']==1)
                  print("<input type='checkbox' name='passSpeak' value='passSpeak' checked/>");
                else 
                  print("<input type='checkbox' name='passSpeak' value='passSpeak'/>");
              ?>
            </td>
          </tr>
          <tr>
            <td>
              How many semesters have you been a GTA?
              <input type="number" name="semestersGTA" placeholder="Number of semesters as a GTA" value="<?php echo $eeRow['num_sem_gta'] ?>"/>
            </td>
          </tr>
          <tr>
            <td>GPA<input type="number" step="any" name="gpa" placeholder="Overall graduate GPA" value="<?php echo $eeRow['grad_gpa'] ?>"/></td>
          </tr>
          <tr>
            <td><button type="submit" name="btn-addInfo">Submit Form</button></td>
          </tr>
        </table>
      </form>
      
      <!-- Courses -->
      <?php 
        if(isset($_POST['btn-course']))
        {
          $course  = mysql_real_escape_string($_POST['course']);
          $grade  = mysql_real_escape_string($_POST['grade']);
          mysql_query("INSERT INTO classes_taken VALUES('$eeID','$course','$grade')");
        }
        echo "Entered Classes: <br>";
        $classresults=mysql_query("SELECT c.name, c.grade FROM classes_taken c where c.eeID='$eeID'");
      
        while(  $rowclass=mysql_fetch_row($classresults) )
        {
          print("$rowclass[1] &nbsp;&nbsp;&nbsp;&nbsp;$rowclass[0] <br/>");
        }
      ?>
          
      <form method="post" action = "nominee.php">
        <table align="center" width="40%" border="0">
          <tr>
            <td><input type="text" name="course" placeholder="Course Code" required /></td>
          </tr>
          <tr>
            <td>
              <select name="grade">
                <option value="A">A +/-</option>
                <option value="B">B +/-</option>
                <option value="C">C +/-</option>
                <option value="D">D +/-</option>
                <option value="F">F +/-</option>
              </select>
            </td>
          </tr>
          <tr>
            <td><button type="submit" name="btn-course">Submit Course</button></td>
          </tr>
        </table>
      </form>
      
      <!-- Publications -->
      <?php 
        if(isset($_POST['btn-publication']))
        {
          $publication  = mysql_real_escape_string($_POST['publication']);
          $citation  = mysql_real_escape_string($_POST['date']);
          mysql_query("INSERT INTO publications VALUES('$eeID','$publication','$citation')");
        }
        echo "Entered publications: <br>";
        $pubresults=mysql_query("SELECT pub.date, pub.title FROM publications pub where pub.eeID='$eeID'");
      
        while(  $rowpub=mysql_fetch_row($pubresults) )
        {
          print("Title: $rowpub[1] <br>");
        }
      ?>
          
      <form method="post" action = "nominee.php">
        <table align="center" width="40%" border="0">
          <tr>
            <td><input type="text" name="publication" placeholder="Publication" required /></td>
          </tr>
          <tr>
            <td><input type="date" name="date" placeholder="Date" required /></td>
          </tr>
          <tr>
            <td><button type="submit" name="btn-publication">Submit Publication</button></td>
          </tr>
        </table>
      </form>
      
      <!-- Advisors -->
      <?php 
        if(isset($_POST['btn-advisor']))
        {
          $aFirst  = mysql_real_escape_string($_POST['aFirst']);
          $aLast  = mysql_real_escape_string($_POST['aLast']);
          $aStart = $_POST['aStart'];
          $aEnd = $_POST['aEnd'];

          $aID_sql = mysql_query("SELECT ID FROM advisors WHERE fname = '$aFirst' AND lname = '$aLast'");
          $row_a = mysql_fetch_row($aID_sql);
          $aID = $row_a[0];
          
          if ($aID == "")
          {
            if (mysql_query("INSERT INTO advisors VALUES('','$aFirst','$aLast')"))
            {
              $aID = mysql_insert_id();
              ?>
              <script>alert("Successfully creating advisor")</script>
              <?php
            }
            else
            {
              ?>
              <script>alert("Error creating advisor")</script>
              <?php
            }
          }
          // Add advising record
          if (mysql_query("INSERT INTO advising VALUES('','$eeID','$aID', '$aStart', '$aEnd')"))
          {
            ?>
            <script>alert("Successfully creating advising record")</script>
            <?php
          }
          else
          {
            ?>
            <script>alert("Error creating advising record")</script>
            <?php
          }
        }
        echo "Entered advisors: <br>";
        $advisors=mysql_query("SELECT o.fname, o.lname
                                FROM  advising AS i, advisors AS o
                                WHERE i.eeID = $eeID
                                  AND i.advID = o.ID");
      
        while(  $rowadv = mysql_fetch_row($advisors) )
        {
          print("Advisor: $rowadv[0] $rowadv[1] <br>");
        }
      ?>
          
      <form method="post" action = "nominee.php">
        <table align="center" width="40%" border="0">
          <col width="200">
          <tr>
            <td><input type="text" name="aFirst" placeholder="First Name of PhD advisor" required /></td>
          </tr>
          <tr>
            <td><input type="text" name="aLast" placeholder="Last Name of PhD advisor" required /></td>
          </tr>
          <tr>
            <td>Start<input type="date" name="aStart" placeholder="From" required/></td>
          </tr>
          <tr>
            <td>End<input type="date" name="aEnd" placeholder="To"/></td>
          </tr>
          <tr>
            <td><button type="submit" name="btn-advisor">Submit Advisor</button></td>
          </tr>
        </table>
      </form>
      
      <!-- Submit finished proposal -->
      <form method="post" action = "nominee.php">
        <table>
          <tr>
            <td><button type="submit" name="btn-finished">Finished</button></td>
          </tr>
        </table>
      <form>
    </div>
  </body>
</html>