
<?php

  session_start();
  include_once 'dbConnect.php';

  if(!isset($_SESSION['user'])){
    header("Location: index.php");
     
  }
  
  // Get the user's info so we can use it in the page
  $userRow = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']));
  
  if($userRow['type'] != "Nominee")
  {
    header("Location: index.php");
  }
  // Grab info from Session variables
  $eeEmail = $userRow['email'];
  $eeFirstName = $userRow['fname'];
  $eeLastName = $userRow['lname'];
  $eeID = $userRow['user_id'];
  
  if(isset($_POST[ 'btn-addNominee' ]))
  {
  
    $erFirstName = mysql_real_escape_string($_POST['nominator_first']);
    $erLastName = mysql_real_escape_string($_POST['nominator_last']);
    
    
    $eePID  = mysql_real_escape_string($_POST['nominee_pid']);
    $eePhone  = mysql_real_escape_string($_POST['nominee_phone']);
    $numGradSemesters  = mysql_real_escape_string($_POST['semesters']);
    $numSemestersGTA  = mysql_real_escape_string($_POST['semestersGTA']);
    
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
    
    $gradCourse  = mysql_real_escape_string($_POST['gradCourse']);
    $gradCourseGrade  = mysql_real_escape_string($_POST['gradCourseGrade']);
    
    mysql_query("INSERT INTO grad_classes(name) VALUES('$gradCourse')");
    // Get the class id that was just created
     $classID = mysql_insert_id();
    mysql_query("INSERT INTO classes_taken(eeID, classID, Grade) VALUES('$eeID','$classID', '$gradCourseGrade')");
   
    
    $eeGPA  = mysql_real_escape_string($_POST['gpa']);
  
  
    
    // Get current term
    $current_term_SQL = mysql_query("SELECT s.term FROM sessions as s WHERE id = (SELECT max(id) from sessions)");
    $row_term = mysql_fetch_row($current_term_SQL);
    $current_term = $row_term[0];
    
    // Send ee_info to MySQL
    if(mysql_query("INSERT INTO ee_info(eeID, term, pid, phone, cs_student, num_sem_grad, speak_test, num_sem_gta, grad_gpa, usGrad) 
                    VALUES('$eeID', '$current_term', '$eePID','$eePhone','$Is_PhD','$numGradSemesters', '$passSpeak', '$numSemestersGTA', '$eeGPA', '$isUSGrad')"))
    {
      ?>
      <script>alert('nominee successfully registered!');</script>
      <?php
      

      // Send post request to send_email.php with the email string
      // Set the email string
      $ehtml = "$eeFirstName $eeLastName has provided additional information regarding their nomination. \n";
      $ehtml .= "URL      : https://gtass-codecorrupt.c9users.io/nominee_info.php?eeid=$eePID";
      $nom_email_SQL = mysql_query("SELECT u.email FROM users AS u WHERE u.user_id='$eeID'"); 
      $row_email = mysql_fetch_row($nom_email_SQL);
      $nom_email = $row_email[0];
      $subj = "Action Requested: Verify Information";
      
      ////////////////////////////////////////
      // Send post request to send_email.php//
      ////////////////////////////////////////
      print("<script>window.onload = function() {document.forms['sendemail'].submit()}</script>");
      
    }
    else
    {
      ?>
      <script>alert('error while registering...');</script>
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
        <input type="hidden" name="to" value= "<?php echo $nom_email ?>" />
        <input type="hidden" name="subj" value= "<?php echo $subj ?>" />
        <input type="hidden" name="body" value= "<?php echo $ehtml ?>" />
      </form>
      <?php 
      
        //Get current trerm
        $termquery= mysql_query("SELECT sessions.term from sessions WHERE sessions.ID = (SELECT MAX(ID) from sessions s2)");
        $valuet = mysql_fetch_row($termquery);
        $term= $valuet[0];
         
         
        $getnmineeinfo=mysql_query("SELECT e.pid, e.cs_student FROM ee_info e WHERE e.eeID='$eeID' AND e.term='$term'");
        
        $getnominatorinfo=mysql_query("SELECT users.fname,users.lname FROM nomination nom, users WHERE nom.orID=users.user_id AND nom.term='$term'");
         
        $rown=mysql_fetch_row($getnominatorinfo);
        $row=mysql_fetch_row($getnmineeinfo);
      ?>
      <form method="post" action="nominee.php">
        <table align="center" width="100%" border="0">
          <col width="400">
          <col width="400">
          <tr>
            <td><input type='text' name='nominator_first' value="<?php echo $rown[0] ?>" required /></td>
            <td><input type='text' name='nominator_last' value="<?php echo $rown[1] ?>" required /></td>
          </tr>
          <tr>
            <td><input type="text" name="curr_advisor_first" placeholder="First Name of current PhD advisor" required /></td>
            <td><input type="text" name="curr_advisor_last" placeholder="Last Name of current PhD advisor" required /></td>
          </tr>
          <tr>
            <td><input type="text" name="prev_advisor_first" placeholder="First Name of previous PhD advisor" required /></td>
            <td><input type="text" name="prev_advisor_last" placeholder="Last Name of previous PhD advisor" required /></td>
            <td><input type="date" name="prev_advisor_from" placeholder="From" required/></td>
            <td><input type="date" name="prev_advisor_to" placeholder="To" required/></td>
          </tr>
          <tr>
            <td><input type='text' name='nominee_pid' value="<?php echo $row[0] ?>" required /></td>
          </tr>
          <tr>
            <td><input type="number" name="nominee_phone" placeholder="Phone number of Nominee" required /></td>
          </tr>
          <tr>
            <td>
              Is the nominee currently a Ph.D Student in the Department of Computer Science?<br>
              <?php
                if($row[1]==1)
                  print ("<input type='checkbox' name='Is_PhD' value='Is_PhD' checked />");
                else
                  print ("<input type='checkbox' name='Is_PhD' value='Is_PhD' /> ");
              ?>
            </td>
          </tr>
          <tr>
            <td><input type="number" name="semesters" placeholder="Semesters as grad student" required /></td>
          </tr>
          <tr>
            <td>
              Did the nominee graduate from a US institution?<br>
              <input type="checkbox" name="USGraduate" value="USGraduate"/>
            </td>
          </tr>
          <tr>
            <td>
              If not, did the nominee pass the SPEAK test?<br>
              <input type="checkbox" name="passSpeak" value="passSpeak"/>
            </td>
          </tr>
          <tr>
            <td><input type="number" name="semestersGTA" placeholder="Number of semesters (including summer) as a GTA" required /></td>
          </tr>
          <tr>
            <td><input type="text" name="gradCourse" placeholder="Graduate course" required /></td>
            <td><input type="text" name="gradCourseGrade" placeholder="Grade" required /></td>
          </tr>
          <tr>
            <td><input type="number" step="any" name="gpa" placeholder="Overall graduate GPA" required /></td>
          </tr>
          <tr>
            <td><button type="submit" name="btn-addNominee">Submit Form</button></td>
          </tr>
        </table>
      </form>
      <?php 
        if(isset($_POST['btn-publication']))
        {
          $publication  = mysql_real_escape_string($_POST['publication']);
          $citation  = mysql_real_escape_string($_POST['date']);
          mysql_query("INSERT INTO publications VALUES('$eeID','$publication','$citation')");
        }
        echo "Entered publication: <br>";
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
            <td><input type="date" name="aStart" placeholder="From" required/></td>
          </tr>
          <tr>
            <td><input type="date" name="aEnd" placeholder="To"/></td>
          </tr>
          <tr>
            <td><button type="submit" name="btn-advisor">Submit Advisor</button></td>
          </tr>
        </table>
      </form>
    </div>
  </body>
</html>