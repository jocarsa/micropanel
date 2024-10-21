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
if (isset($_GET['tabla'])) {
    $tabla = $_GET['tabla'];
    $sql = "SELECT * FROM " . $conn->real_escape_string($tabla);
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr>";
        while ($fieldInfo = $result->fetch_field()) {
            echo "<th>" . $fieldInfo->name . "</th>";
        }
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found in the table.";
    }
}
$conn->close();
?>
