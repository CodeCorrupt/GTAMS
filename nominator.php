<!--
  TODO:
    Notify the the user if past nomination deadline
    Impliment php to submit form to DB
    Fix CSS
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
  $orID = $userRow['user_id'];
  
  /****************************************************
  *This below is used to work with forms and DB calls *
  ****************************************************/
  if(isset($_POST[ 'submit' ])) //If you use a form below then this will handle the post
  {
    $eeFirstName = mysql_real_escape_string($_POST['Nominee_firstName']);
    $eeLastName = mysql_real_escape_string($_POST['Nominee_lastName']);
    $ee_rank = mysql_real_escape_string($_POST['Nominee_rank']);
    $ee_PID = mysql_real_escape_string($_POST['Nominee_PID']);
    $ee_email = mysql_real_escape_string($_POST['Nominee_email']);
    $unencrypt_pass = "password";
    $ee_password = md5($unencrypt_pass);
    
     // See if Nominee is a PhD student, if not set value to false
    if(isset($_POST['Is_PhD'])) {
      $Is_PhD = true;
    }
    else{
      $Is_PhD = false;
    }
    
      // See if Nominee is newly admitted, if not set value to false
    if(isset($_POST['Is_newPhD'])) {
      $Is_newPhD = true;
    }
    else{
      $Is_newPhD = false;
    }
    
    // Get current term
    $current_term_SQL = mysql_query("SELECT s.term FROM sessions as s WHERE id = (SELECT max(id) from sessions)");
    $row_term = mysql_fetch_row($current_term_SQL);
    $current_term = $row_term[0];
    
    // Insert into ee_info and nomination tables
    if(mysql_query("INSERT INTO users (email, password, fname, lname, type)
                    VALUES ('$ee_email', '$ee_password', '$eeFirstName', '$eeLastName', 'Nominee')"))
    {
      ?>
      <script>alert('Successfully made user!');</script>
      <?php
      $ee_id = mysql_insert_id();
      if(mysql_query("INSERT INTO ee_info(eeID, term, pid, cs_student)
                      VALUES('$ee_id', '$current_term','$ee_PID','$Is_PhD')"))
      {
        ?>
        <script>alert('Successfully added ee_info!');</script>
        <?php
        $orID = $_SESSION['user'];
        if(mysql_query("INSERT INTO nomination(term,eeID,orID,eeRanking)
                        VALUES('$current_term','$ee_id', '$orID', '$ee_rank')"))
        {
          ?>
          <script>alert('Successfully submitted nomination!');</script>
          <?php
        
          // Send post request to send_email.php with the email string
          // Set the email string
          $ehtml = "Congratulations, $eeFirstName $eeLastName, you have been nominated for a Graduate Teaching Assistantship for the upcoming year. Please use the link below to provide additional information regarding your nomination.<br/>";
          $ehtml .= "URL      : https://gtass-codecorrupt.c9users.io/nominee.php<br/>";
          $ehtml .= "Email    : $ee_email<br/>";
          $ehtml .= "Password : $unencrypt_pass";
          
          $subj = "Congratulations! You have been selected for a GTA";
          
          // Send post request to send_email.php//
          print("<script>window.onload = function() {document.forms['sendemail'].submit()}</script>");
        }
        else
        {
          ?>
          <script>alert('Error while making nomination...');</script>
          <?php
        }
      }
      else
      {
        ?>
        <script>alert('Error while making ee_info...');</script>
        <?php
      }
    }
    else
    {
      ?>
      <script>alert('Error while creating user');</script>
      <?php
    }
  }
?>

<!-- HTML stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>GTAMS - Nominator</title>
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
      
      <form action="send_email.php" method="post" name="sendemail">
      <input type="hidden" name="from" value="no-reply@gtams.com" />
      <input type="hidden" name="to" value= "<?php echo $ee_email ?>" />
      <input type="hidden" name="subj" value= "<?php echo $subj ?>" />
      <input type="hidden" name="body" value= "<?php echo $ehtml ?>" />
      </form>
      
      
      <form method='post'>
        <table align="center" width="30%" border="0">
          <tr>
            <td>
              First Name of Nominee:<br>
              <input type = 'text' name='Nominee_firstName'>
            </td>
          </tr>
          <tr>
            <td>
              Last Name of Nominee:<br>
              <input type = 'text' name='Nominee_lastName'>
            </td>
          </tr>
          <tr>
            <td>
              Rank of Nominee:<br>
              <input type = 'number' name='Nominee_rank'>
            </td>
          </tr>
          <tr>
            <td>
              Nominee PID:<br>
              <input type = 'number' name='Nominee_PID'>
            </td>
          </tr>
          <tr>
            <td>
              Nominee Email:<br>
              <input type= 'text' name='Nominee_email'>
            </td>
          </tr>
          <tr>
            <td>
              Is the Nominee currently a Ph.D Student in the Department of Computer Science?<br>
              <input type="checkbox" name="Is_PhD" value="Is_PhD">
            </td>
          </tr>
          <tr>
            <td>
              Is the nominee a newly admitted Ph.D. Student?<br>
              <input type="checkbox" name='Is_newPhD' value='Is_newPhD'>
            </td>
          </tr>
          <tr>
            <td>
              <input type='submit' value='Submit' name='submit' />
            </td>
          </tr>
        </table>
      </form>
    </div>
  </body>
</html>