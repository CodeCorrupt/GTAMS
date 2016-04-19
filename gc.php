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
  if($userRow['type'] != "Member")
  {
    header("Location: index.php");
  }
  $current_term = mysql_fetch_row(  mysql_query("SELECT sessions.term from sessions WHERE sessions.ID = (SELECT MAX(ID) from sessions s2)"))[0];
  
  if(!isset($_POST['btn-getterm']) || $_POST['term'] == "")
  {
    $term = $current_term;
  }
  else
  {
    $term = $_POST['term'];
  }
  
  if(isset($_POST['btn-submitScore']))
  {
    $nID = $_POST['nomID'];
    $mID = $_SESSION['user'];
    $score = $_POST['score'];
    
    if(mysql_query("INSERT INTO member_scores (nomID, memberID, score) 
                    VALUES ('$nID', '$mID' , '$score') 
                    ON DUPLICATE KEY 
                      UPDATE score = '$score'"))
    {
      ?>
      <script>alert("Updated scores sucessfully");</script>
      <?php
      header("location: gc.php");
    }
    else
    {
      ?>
      <script>alert("Error updating score!");</script>
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
     GC Interface
    </title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" />
  </head>

  <body>
    <div id="header">
      <div id="left">
        <h1>Member</h1>
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
      <form method= "post"> 
        Enter term value: </br>
        <input type = 'text' name = 'term' value="<?php echo $term ?>"/>
        <input type = 'submit' name='btn-getterm' value='See term'/>
      </form>
      <table border="1">
        <?php
          $memberNames=mysql_query("Select u.lname FROM users u, member_info m WHERE m.userID = u.user_id AND m.term='$term' ORDER BY m.userID");
          print("<tr>");
          print("<td>Nominator</td><td>Nominee</td><td>Rank</td><td>New Student</td>");
          while($row = mysql_fetch_row($memberNames))
          {
            print("<td>$row[0]'s Score</td>" );
          }
          print("<td>Average Score</td>");
          print("</tr>");
          
          $nomInfo = mysql_query("SELECT n.ID, ou.lname, eu.lname, n.eeRanking, (e.num_sem_grad = 0)
                                  FROM nomination n
                                  INNER JOIN users ou ON ou.user_id = n.orID
                                  INNER JOIN users eu ON eu.user_id = n.eeID
                                  INNER JOIN ee_info e ON n.eeID = e.eeID
                                  WHERE n.complete = '2'
                                    AND n.term = '$term'
                                  ORDER BY ou.lname, n.eeRanking");
          while($nomInfoRow = mysql_fetch_row($nomInfo))
          {
            print("<tr>");
            print("<td>$nomInfoRow[1]</td><td>$nomInfoRow[2]</td><td>$nomInfoRow[3]</td><td>$nomInfoRow[4]</td>");
            $memScore = mysql_query("SELECT mss.score, mids.userID
                                     FROM (SELECT mi.userID
                                           FROM member_info mi
                                           WHERE mi.term = '$term') AS mids
                                     LEFT JOIN (SELECT ms.memberID, ms.score
                                                FROM member_scores ms
                                                WHERE ms.nomID = '$nomInfoRow[0]') AS mss ON mids.userID = mss.memberID
                                     ORDER BY mids.userID");
            $sum = 0;
            $count = 0;
            while ($memScoreRow = mysql_fetch_row($memScore))
            {
              $sum += $memScoreRow[0];
              $count++;
              if ($_SESSION['user'] == $memScoreRow[1])
              {
                print("<td>");
                print("  <form method='post'>");
                print("    <input type='number' value='$memScoreRow[0]' name='score' required/>");
                print("    <input type='hidden' value='$nomInfoRow[0]' name='nomID'/>");
                print("    <input type='submit' value='Submit' name='btn-submitScore'/>");
                print("  </form>");
                print("</td>");
              }
              else
              {
                print("<td>$memScoreRow[0]</td>");
              }
            }
            if ($count == 0)
            {
              print("<td>0</td>");
            }
            else
            {
              $avg = $sum/$count;
              print("<td>$avg</td>");
            }
            print("</tr>");
          }
        ?>
      </table>
    </div>
  </body>
</html>