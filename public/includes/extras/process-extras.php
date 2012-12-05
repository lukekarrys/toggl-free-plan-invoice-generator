<?php
  include_once('../config/setup.php');
  include_once('../config/setup-private.php');
  
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['REQUEST'] == 'update') {  
    $col = $_POST['COLUMN'];
    $new = $_POST['NEW_DATA'];
    $autonumber = $_POST['AUTONUMBER'];
    try {
        $database = new PDO(DB_CONN, DB_USER, DB_PASS);
        $query = "UPDATE extra_items SET ".$col." = '".$new."' WHERE AUTONUMBER='".$autonumber."'";
        $execute = $database->query($query);
        $database = null;
    } catch (PDOException $e) {
        print "<p>Error!: " . $e->getMessage() . "</p>";
        die();
    }
  } elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['REQUEST'] == 'add') {  
    try {
        $database = new PDO(DB_CONN, DB_USER, DB_PASS);
        $query = "INSERT INTO extra_items (CLIENT,PROJECT,DESCRIPTION,TOTAL) VALUES (".$_GET['CLIENT'].",".$_GET['PROJECT'].",'',0)";
        $execute = $database->query($query);
        $query = "SELECT * FROM extra_items ORDER BY AUTONUMBER DESC LIMIT 1";
        $execute = $database->query($query);
        foreach ($execute as $row) {
          $autonumber = $row['AUTONUMBER'];
          echo json_encode(compact("autonumber"));
        }
        $database = null;
    } catch (PDOException $e) {
        print "<p>Error!: " . $e->getMessage() . "</p>";
        die();
    }
  } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['REQUEST'] == 'delete') {
    $autonumber = $_POST['AUTONUMBER'];  
    try {
        $database = new PDO(DB_CONN, DB_USER, DB_PASS);
        $query = "DELETE FROM extra_items WHERE AUTONUMBER='".$autonumber."'";
        $execute = $database->query($query);
        $database = null;
    } catch (PDOException $e) {
        print "<p>Error!: " . $e->getMessage() . "</p>";
        die();
    }
  }