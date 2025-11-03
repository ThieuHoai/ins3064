<?php
session_start();
header("Location: login.php");
/* connect to database check user*/
$con=mysqli_connect('localhost','root', '');
mysqli_select_db($con,'LoginReg');

/* create variables to store data */
$name = $_POST['user'];
$pass = $_POST['password'];
$studentID = $_POST['studentID'];
$dob = $_POST['dob'];
$country = $_POST['country'];

/* select data from DB */
$s = "select * from users where username='$name'";

/* result variable to store data */
$result = mysqli_query($con,$s);

/* check for duplicate names and count records */
$num =mysqli_num_rows($result);
if($num==1){
    echo "Username Exists";
}else{
    $reg = "insert into users(username,password,studentID,dob,country) values ('$name','$pass','$studentID','$dob','$country')";
    mysqli_query($con, $reg);
    echo "registration successful";
}