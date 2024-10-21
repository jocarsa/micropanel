<!doctype html>
<html>
    <head>
        <style>
            /* General Styles */
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f4f4f9;
                margin: 0;
                padding: 0;
                color: #333;
            }

            h3 {
                color: #2c3e50;
                margin-bottom: 15px;
            }

            /* Table Styles */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                background-color: #fff;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            th {
                background-color: #2c3e50;
                color: white;
                text-transform: uppercase;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            td a {
                color: #3498db;
                text-decoration: none;
            }

            td a:hover {
                text-decoration: underline;
            }

            /* List of Tables */
            ul {
                padding: 0;
                margin: 20px 0;
                list-style-type: none;
            }

            ul li {
                display: inline-block;
                margin-right: 15px;
            }

            ul li a {
                color: #3498db;
                text-decoration: none;
                padding: 10px 15px;
                background-color: #2c3e50;
                color: white;
                border-radius: 4px;
                transition: background-color 0.3s ease;
            }

            ul li a:hover {
                background-color: #1abc9c;
            }

            /* Form Styles */
            form {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                margin: 0 auto;
            }

            form label {
                font-size: 14px;
                color: #333;
                margin-bottom: 8px;
                display: block;
            }

            form input[type="text"] {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            form input[type="submit"] {
                background-color: #2c3e50;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 4px;
                transition: background-color 0.3s ease;
            }

            form input[type="submit"]:hover {
                background-color: #1abc9c;
            }

            /* Notification Styles */
            .notification {
                padding: 15px;
                margin: 15px 0;
                border: 1px solid transparent;
                border-radius: 4px;
            }

            .notification.success {
                background-color: #dff0d8;
                border-color: #d6e9c6;
                color: #3c763d;
            }

            .notification.error {
                background-color: #f2dede;
                border-color: #ebccd1;
                color: #a94442;
            }

            /* Footer */
            footer {
                text-align: center;
                padding: 15px;
                background-color: #2c3e50;
                color: white;
                margin-top: 30px;
                position: fixed;
                width: 100%;
                bottom: 0;
            }

            
        </style>
    </head>
    <body>
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
    </body>
</html>