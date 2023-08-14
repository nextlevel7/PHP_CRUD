<?php
include "connection.php";  // Include the database connection

// Initialize errors array
$errors = [];

// Handle delete action
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] === "delete" && isset($_GET["id"])) {
    $employeeId = $_GET["id"];

    // Prepare and execute SQL statement to delete the employee
    $stmt = $conn->prepare("DELETE FROM employee WHERE id = ?");
    $stmt->bind_param("i", $employeeId);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER["PHP_SELF"]); // Redirect after successful deletion
        exit();
    } else {
        echo "Error deleting employee: " . $stmt->error;
    }

    $stmt->close();
}

// Handle form submission for create and edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Name = $_POST["Name"];
    $Salary = $_POST["Salary"];
    $DateOfBirth = $_POST["DateOfBirth"];
    $CompanyId = $_POST["CompanyId"];
    $employeeId = $_POST["employeeId"];

    // Perform validation
    if (empty($Name)) {
        $errors["Name"] = "Name is required.";
    }
    // Add more validation as needed

    if (empty($errors)) {
        if ($employeeId) {
            // Update existing employee
            $stmt = $conn->prepare("UPDATE employee SET Name = ?, Salary = ?, DateOfBirth = ?, CompanyId = ? WHERE id = ?");
            $stmt->bind_param("sdsii", $Name, $Salary, $DateOfBirth, $CompanyId, $employeeId);
        } else {
            // Create new employee
            $stmt = $conn->prepare("INSERT INTO employee (Name, Salary, DateOfBirth, CompanyId) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdsi", $Name, $Salary, $DateOfBirth, $CompanyId);
        }

        if ($stmt->execute()) {
            header("Location: ".$_SERVER["PHP_SELF"]); // Redirect after successful operation
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch employee data from the database
$employees = [];
$result = $conn->query("SELECT id, Name, Salary, DateOfBirth, CompanyId FROM employee");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    $result->free();
} else {
    echo "Error fetching employee data: " . $conn->error;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Management</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
        }
        h3 {
            margin-top: 20px;
            color: #333;
        }
        form label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        form input[type="text"], form input[type="number"], form input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        .edit-button {
            background-color: blue;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        .edit-button:hover {
            background-color: darkblue;
        }
        .delete-button {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        .delete-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Employee Management</h2>
        <h3>Create/Edit Employee</h3>
        <form method="POST" action="">
            <!-- Add the form fields for employee data here -->
            <input type="hidden" Name="employeeId" value="">
            <label>Name:</label>
            <input type="text" Name="Name" value="">
            <span class="error"></span>
            <label>Salary:</label>
            <input type="number" Name="Salary" value="">
            <span class="error"></span>
            <label>Date of Birth:</label>
            <input type="date" Name="DateOfBirth" value="">
            <label>Company ID:</label>
            <input type="number" Name="CompanyId" value="">
            <input type="submit" value="Save">
        </form>
    </div>

    <div class="container">
        <h3>Employees List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Salary</th>
                <th>Date of Birth</th>
                <th>Company ID</th>
                <th>Actions</th>
            </tr>
            <!-- Loop through employees array and display data -->
            <?php foreach ($employees as $employee) { ?>
                <tr>
                    <td><?php echo $employee["id"]; ?></td>
                    <td><?php echo $employee["Name"]; ?></td>
                    <td><?php echo $employee["Salary"]; ?></td>
                    <td><?php echo $employee["DateOfBirth"]; ?></td>
                    <td><?php echo $employee["CompanyId"]; ?></td>
                    <td>
                        <a href="?action=edit&id=<?php echo $employee["id"]; ?>" class="edit-button">Edit</a>
                        <a href="?action=delete&id=<?php echo $employee["id"]; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

