<?php
$host = 'localhost';  
$user = 'negocio';      
$password = 'negocio';      
$dbname = 'negocio';  
$conn = new mysqli($host, $user, $password, $dbname);
$sql = "SHOW TABLES";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li><a href='?tabla=" . $row[0] . "'>" . $row[0] . "</a></li>";  
    }
    echo "</ul>";
} 
$conn->close();
?>
