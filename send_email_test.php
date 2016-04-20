<!-- HTML Stuff! -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Email Test</title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" />
  </head>
  
  <body>
    <center>
      <div id="login-form">
        <form action="send_email.php" method="post">
          <table align="center" width="30%" border="0">
            <tr>
              <td><input type="email" name="from" placeholder="From" required /></td>
            </tr>
            <tr>
              <td><input type="email" name="to" placeholder="To" required /></td>
            </tr>
            <tr>
              <td><input type="text" name="subj" placeholder="Subject" required /></td>
            </tr>
            <tr>
              <td><input type="box" name="body" placeholder="Body" required /></td>
            </tr>
            <tr>
              <td><button type="submit" name="btn-send">Send Email</button></td>
            </tr>
          </table>
        </form>
      </div>
    </center>
  </body>
</html>