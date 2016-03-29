<?php
session_start();
if(!mysql_connect("localhost","codecorrupt",""))
{
     die('oops connection problem ! --> '.mysql_error());
}
else if(!mysql_select_db("gtams"))
{
     die('oops database selection problem ! --> '.mysql_error());
}
else{
    error_log("DB Connected successfully", 0);
}
?>