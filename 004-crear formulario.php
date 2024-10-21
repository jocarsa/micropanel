
<?php
$host = 'localhost';  
$user = 'negocio';      
$password = 'negocio';      
$dbname = 'negocio';  
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If a delete request is made
if (isset($_GET['delete_id']) && isset($_GET['tabla'])) {
    $tabla = $_GET['tabla'];
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    
    // Assuming primary key is named 'Identificador'. You may need to modify this for specific table primary key names.
    $sql = "DELETE FROM " . $conn->real_escape_string($tabla) . " WHERE Identificador = '$delete_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully.";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// If a table is selected, handle form submission for inserting a new row
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert'])) {
    $tabla = $_GET['tabla'];
    $fields = [];
    $values = [];
    
    foreach ($_POST as $key => $value) {
        if ($key != 'insert') { // Skip the insert button
            $fields[] = $conn->real_escape_string($key);
            $values[] = "'" . $conn->real_escape_string($value) . "'";
        }
    }
    
    $sql = "INSERT INTO " . $conn->real_escape_string($tabla) . " (" . implode(",", $fields) . ") VALUES (" . implode(",", $values) . ")";
    
    if ($conn->query($sql) === TRUE) {
        echo "New record inserted successfully.";
    } else {
        echo "Error inserting record: " . $conn->error;
    }
}

// Display tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li><a href='?tabla=" . $row[0] . "'>" . $row[0] . "</a></li>";  
    }
    echo "</ul>";
} 

// If a table is selected, display its contents
if (isset($_GET['tabla'])) {
    $tabla = $_GET['tabla'];
    $sql = "SELECT * FROM " . $conn->real_escape_string($tabla);
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr>";
        
        // Fetch and display field names (headers)
        $fields = [];
        while ($fieldInfo = $result->fetch_field()) {
            echo "<th>" . $fieldInfo->name . "</th>";
            $fields[] = $fieldInfo->name; // Store field names for later use in form
        }
        echo "<th>Action</th>"; // Additional column for the delete button
        echo "</tr>";
        
        // Fetch and display row data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "<td><a href='?tabla=" . $tabla . "&delete_id=" . $row['Identificador'] . "' onclick=\"return confirm('Are you sure you want to delete this row?');\">Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found in the table.";
    }

    // Display form for inserting new rows
    echo "<h3>Insert a new row into the table: " . htmlspecialchars($tabla) . "</h3>";
    echo "<form method='POST'>";
    
    foreach ($fields as $field) {
        echo "<label for='" . htmlspecialchars($field) . "'>" . htmlspecialchars($field) . ":</label>";
        echo "<input type='text' name='" . htmlspecialchars($field) . "'><br>";
    }
    
    echo "<input type='submit' name='insert' value='Insert'>";
    echo "</form>";
}

$conn->close();
?>
