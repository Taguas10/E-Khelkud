<?php
$host ='localhost';
$username ='root';
$password ='';
$database ='ekhelkud_database';

$conn = new mysqli($host,$username,$password,$database);

if($conn->connect_error){
    die("Database Connection failed" .$conn->connect_error);
}