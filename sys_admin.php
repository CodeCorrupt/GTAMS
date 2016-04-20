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
  $userRow = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE user_id=".$_SESSION['user']));
  
  // Makes sure only Sys Admins can see the page
  if($userRow['type'] != "Admin") {
    header("Location: index.php");
  }
  
  /****************************************************
  *This below is used to work with forms and DB calls *
  ****************************************************/
  if(isset($_POST[ 'btn-setMembers' ])) //If you use a form below then this will handle the post
  {
    //Extract the elements of the GC Member post request
    $memberTerm = mysql_real_escape_string($_POST['memberTerm']);
    $fname = mysql_real_escape_string($_POST['firstname']);
    $lname = mysql_real_escape_string($_POST['lastname']);
    $email = mysql_real_escape_string($_POST['emailaddress']);
    $upass_unencrypt = mysql_real_escape_string($_POST['password']);
    $upass = md5($upass_unencrypt);
    $type  = mysql_real_escape_string('member');
    
    // See if Member is chair, if not set value to false
    if(isset($_POST['isChair'])) {
      $isChair = true;
    }
    else{
      $isChair = false;
    }

        // Send GC Member info to user table
    if(mysql_query("INSERT INTO users(fname, lname, email, password, type) VALUES('$fname', '$lname','$email','$upass','$type')"))
    {
      ?>
      <script>alert('successfully created user!');</script>
      <?php
      
      // Get the user id that was jsut created
      $userid = mysql_insert_id();
      // Send GC Member info to member_info table
      if(mysql_query("INSERT INTO member_info(userID, term, chair) VALUES('$userid', '$memberTerm','$isChair')"))
      {
        ?>
        <script>alert('successfully added member info!');</script>
        <?php
        // Send post request to send_email.php with the email string
        // Set the email string
        $ehtml = "$fname $lname,\n We are pleased to inform you that you have been selected to be a GC Member, Please log in at the following URL with the provided information<br/>";
        $ehtml .= "URL      : https://gtass-codecorrupt.c9users.io/<br/>";
        $ehtml .= "Email    : $email<br/>";
        $ehtml .= "Password : $upass_unencrypt";
        
        $subj = "Welcom new GC Member!";
        // Send post request to send_email.php//
        print("<script>window.onload = function() {document.forms['sendemail'].submit()}</script>");
      }
      else
      {
        ?>
        <script>alert('error while adding member info');</script>
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
  
  // Extract the elements of the deadline post request  
  if(isset($_POST[ 'btn-setDeadlines' ]))
  {
    // Extract the elements of the post request
    $deadlineTerm = mysql_real_escape_string($_POST['deadlineTerm']);
    $initiate = mysql_real_escape_string($_POST['initiate']);
    $respond = mysql_real_escape_string($_POST['respond']);
    $verify = mysql_real_escape_string($_POST['verify']);

    // Send deadline info to MySQL
    if(mysql_query("INSERT INTO sessions(term, initDeadline, respondDeadline, verifyDeadline) VALUES('$deadlineTerm','$initiate', '$respond','$verify')"))
    {
      ?>
      <script>alert('successfully added deadlines!');</script>
      <?php
    }
    else
    {
      ?>
      <script>alert('error while adding deadlines...');</script>
      <?php
    }
  }
  $current_term_SQL = mysql_query("SELECT s.term FROM sessions as s WHERE id = (SELECT max(id) from sessions)");
  $row_term = mysql_fetch_row($current_term_SQL);
  $current_term = $row_term[0];
?>
<!-- HTML stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    System Administrator
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
<form action="send_email.php" method="post" name="sendemail">
  <input type="hidden" name="from" value="no-reply@gtams.com" />
  <input type="hidden" name="to" value= "<?php echo $email ?>" />
  <input type="hidden" name="subj" value= "<?php echo $subj ?>" />
  <input type="hidden" name="body" value= "<?php echo $ehtml ?>" />
</form>

<br>
<h1> Set up a new nomination session: </h1>

    <form method="post">
    <table align="center" width="50%" border="0">
      <th>Session Identifier:</th>
     <br>
    <tr>
      <td>
        <input type="text" name="deadlineTerm" placeholder="Session ID" />
      </td>
      </tr>
    <th>Deadline to initiate nomination (faculty member):</th>
    <tr>
      <td>
        <input type="date" name="initiate" />
      </td>
    </tr>
    <th>Deadline to respond to nomination (nominee):</th>
    <tr>
      <td>
        <input type="date" name="respond" />
      </td>
    </tr>
    <th>Deadline to verify nominee information and complete nomination (nominator):</th>
    <tr>
      <td>
        <input type="date" name="verify" />
      </td>
    </tr>
    <tr>
      <td>
        <button type="submit" name="btn-setDeadlines">Create new session</button>
      </td>
    </tr>
  </table>
</form>

<br><h1> Add GC Members to nomination session: </h1>

<form method="post">
  <table align="center" width="70%" border="0">
    <th>Session Identifier:</th>
     <br>
    <tr>
      <td>
        <input type="text" name="memberTerm" placeholder="Session ID" value="<?php echo $current_term ?>" />
      </td>
      </tr>
    <th>For each GC Member: </th>
    <br>
    <tr>
      <td>
        <input type="text" name="firstname" placeholder="First Name" />
      </td>
       <td>
        <input type="text" name="lastname" placeholder="Last Name" />
      </td>
      <td>
        <input type="email" name="emailaddress" placeholder="Email" />
      </td>
      <td>
        <input type="password" name="password" placeholder="Password" />
      </td>
      </tr>
      <tr>
      <td>
        Is this GC member the chair?
        <input type="checkbox" name="isChair" value="isChair">
      </td>
    </tr>
     <tr>
      <td>
        <button type="submit" name="btn-setMembers">Create new user</button>
      </td>
    </tr>
    </table>
    </form>
</body>

</html>