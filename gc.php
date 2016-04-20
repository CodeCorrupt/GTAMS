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
    if($userRow['type'] != "Member")
    {
     header("Location: index.php");
    }
  
  $cterm= mysql_query("SELECT sessions.term from sessions WHERE sessions.ID = (SELECT MAX(ID) from sessions s2)");
  $valuetk = mysql_fetch_row($cterm);
  $current_term= $valuetk[0];
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
      <form method= "post" action="gc.php" > 
      Enter term value: </br>
      <input type = 'text' name = 'term' value="<?php echo $current_term ?>" >
          </br>
        <input type = 'submit' name='getterm' value='See term' > 
        </br>
      </form>
     
      
      
      <form method="post" action ='gc.php'>
      <table border="1" style='width:100%'>
<?php 
extract($_POST);
   error_reporting(E_ALL ^ E_WARNING); 
   if(empty($term) || (strcasecmp($term,"")==0))
    {
      $termquery= mysql_query("SELECT sessions.term from sessions WHERE sessions.ID = (SELECT MAX(ID) from sessions s2)");
      $valuet = mysql_fetch_row($termquery);
       $term= $valuet[0];
       print("Set Default term to current term <br>");
    }

 $gcmembenames= mysql_query("Select users.fname, users.lname FROM users, member_info gc WHERE gc.userID=users.user_id AND gc.term='$term'");
    
   print "<tr>";
  print "<td> Nominator </td> <td> Nominee </td> <td> Rank </td> <td> New </td> ";
  
  while($gcrow=mysql_fetch_row($gcmembenames))
    {
        print("<td> $gcrow[0]  $gcrow[1] </td> <td> Comments </td>" );
    }
    
  print("</tr>");

  $names= mysql_query("SELECT users.lname,users.user_id from users where users.type='Nominator'  ORDER BY users.lname " );
 
 $counter=0;
while($row = mysql_fetch_row($names))
{
  
    print("<tr>");
    print("<td> $row[0] </td>");
   
    $Nominees= mysql_query("SELECT users.lname, nom.eeRanking,nom.eeID FROM users,nomination nom  WHERE nom.orID='$row[1]' AND nom.eeID=users.user_id AND nom.complete='2'AND nom.term='$term' ORDER BY nom.eeRanking ");
               // $temp=0;
  
    while( $rowj = mysql_fetch_row($Nominees))
    { 
                $t = 0;
                
        print("<tr>");
          print("<td> </td>");
                  
                  //need to put pop info here possible way to do it probably a bad way
        $Nominationinfo=mysql_query("SELECT * from ee_info e  WHERE e.term='$term'");
        print("<td> <a  onclick='getinfo()'>$rowj[0] </a>");
         $printnomineeinfo="<table border='1' style='margin: auto;'>";
         
         $printnomineeinfo .=  "<tr>";
         
         for($in=0;$in<mysql_num_fields($Nominationinfo); $in++)
         {
         $metadatan=mysql_fetch_field($Nominationinfo);
            $printnomineeinfo .=  "<td>";
            $temps= $metadatan->name;
            $printnomineeinfo .= $temps;
            $printnomineeinfo .= "</td>";
         }
         
         $printnomineeinfo .= "</tr>";
        while( $rown=mysql_fetch_row($Nominationinfo))
         {
          $printnomineeinfo .= "<tr>";
          foreach( $rown as $keyn => $valuen)
             $printnomineeinfo .= "<td> $valuen </td>";
          $printnomineeinfo .= "</tr>";
         }
        $printnomineeinfo .= "</table>";
                
                     
                     //// JS portion
                     
        print("<script>
              function getinfo() {
              alert('$printnomineeinfo'); 
                                 }
                             </script>");
                     
               
        print("<td> $rowj[1] </td> ");
//$nomquery=mysql_query("Select nom.ID FROM nomination nom WHERE nom.eeID='$rowj[2]'");
//$rownom=mysql_fetch_row($nomquery);
  // $gcsquery2= mysql_query("Select gc.userID FROM users, member_info gc WHERE gc.userID=users.user_id AND gc.term='$term'");
    //$rowgc=mysql_fetch_row($gcsquery2);
   //mysql_query("INSERT INTO member_scores VALUES('$rownom[0]','$rowgc[0]','0','comment') ");                      
                
        $isnewquery=mysql_query ("Select e.num_sem_grad from ee_info e WHERE e.eeID='$rowj[2]' AND e.term='$term'");
        $gcsquery= mysql_query("Select users.lname, gc.score,gc.commment,gc.memberID,gc.eeID FROM users, member_scores gc, nomination nom  WHERE gc.memberID=users.user_id AND gc.eeID='$rowj[2]' AND  gc.nomID=nom.ID AND nom.term='$term' ");
        $therow=mysql_fetch_row($isnewquery);
    

        if($therow[0] == 0)
            print("<td> New </td>");
        else 
            print("<td> Existing </td>");
             
                    
        $t++;
              
               
                //get gc info here
        $averagescore=0; $numgcmembers=0;
        
         $rowk = mysql_fetch_row($gcsquery);
        
          
            $score= "score". $counter;
            $comment= "comment". $counter;
             $isgcname=0;
             foreach($rowk as $kek => $valuek)
                {
                    $canedit=false;
                    if($isgcname==0)
                      {
                    //print("<td> $valuek </td>");
                         if(strcasecmp($valuek, $userRow['lname'])==0 && $term==$current_term)
                            $canedit=true;
                      }
                      else if($isgcname==1) 
                      {
                        if($canedit)
                            print("<td> <input type='number' name='$score' min='0' max='10' value= '$valuek'> </td>");
                        else
                            print("<td> $valuek </td>");
                        $averagescore+=$valuek;
                        $numgcmembers++;
                      }
                      else if($isgcname==2)
                      {
                        if($canedit)  
                            print("<td> <input type = 'text' name='$comment' value='$valuek'> </td>"); 
                        else 
                            print("<td> $valuek </td> ");
                      }
                    $isgcname++;
                   
                }
                 $counter++;
        
            
        $averagescore/= $numgcmembers;
        print("<td> $averagescore</td> ");
        print("</tr>");
        
           
    }
print("</tr>");
    
}
    
?>
     </table>
     <input type='submit' value='Submit scores' name='submit' /> 
     <input type='hidden' value='5' name="insert" />
     </form>
 <!-- insert the new scores -->
 
<?php if(isset( $_POST['submit'] )):
    $countert=0;
    // Variable Variables teststing.
  //$test= '$score'. "3";
  //$testt="test";
 //print("${$testt}");
    while($row = mysql_fetch_row($names))
    {  
             while( $rowj = mysql_fetch_row($Nominees))
                {
                      $scoret= '$score'. $countert;
                      $commentt= '$comment'. $countert;
                      $gcsquery3= mysql_query("Select gc.memberID,gc.eeID FROM users, member_scores, nomination nom gc WHERE gc.memberID=users.user_id AND gc.eeID='$rowj[2]' AND nom.eeID=gc.eeID AND non.term='$term' ");
                      $rowgc2=mysql_fetch_row($gcsquery3);
                    mysql_query("UPDATE member_scores SET score='${$scoret}', comment='${$commentt}') WHERE gc.memberID='$rowgc2[0]' AND gc.eeID='$rowgc2[1]' ");
                   
                
                }   
    }    


?>
   <?php else:
            ?> 
        <?php endif; ?>


     
     
     
     <!-- show the incomplete forms -->
 <?php
  $incomplete = mysql_query("Select users.lname,nom.complete FROM users, nomination nom  Where users.user_id = nom.eeID AND nom.complete=1 OR nom.complete=0  AND nom.term='$term' ");
   
  while($row = mysql_fetch_row($incomplete))
    {
     
       print("$row[0] : ");
       
       if($row[1] == 0)
            print("Nominee has not completed form");
       else 
            print("Nominee has not been verfied");

      
    }
 ?>
  </div> 
  </body> 
  </html>