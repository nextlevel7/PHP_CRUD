<?php
include "connection.php";  // Include the database connection

// Initialize errors array
$errors = [];

// Handle delete action
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] === "delete" && isset($_GET["id"])) {
    $companyId = $_GET["id"];

    // Prepare and execute SQL statement to delete the company
    $stmt = $conn->prepare("DELETE FROM company WHERE id = ?");
    $stmt->bind_param("i", $companyId);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER["PHP_SELF"]); // Redirect after successful deletion
        exit();
    } else {
        echo "Error deleting company: " . $stmt->error;
    }

    $stmt->close();
}

// Handle form submission for create and edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $companyName = $_POST["companyName"];
    $address = $_POST["address"];
    $companyId = $_POST["companyId"];

    // Perform validation
    if (empty($companyName)) {
        $errors["companyName"] = "Company name is required.";
    }
    // Add more validation as needed

    if (empty($errors)) {
        if ($companyId) {
            // Update existing company
            $stmt = $conn->prepare("UPDATE company SET companyName = ?, address = ? WHERE id = ?");
            $stmt->bind_param("ssi", $companyName, $address, $companyId);
        } else {
            // Create new company
            $stmt = $conn->prepare("INSERT INTO company (companyName, address) VALUES (?, ?)");
            $stmt->bind_param("ss", $companyName, $address);
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

// Fetch company from the database
$company = [];
$result = $conn->query("SELECT id, companyName, address FROM company");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $company[] = $row;
    }
    $result->free();
} else {
    echo "Error fetching company: " . $conn->error;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Company Management</title>
    <style>
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
        form input[type="text"] {
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

/* Styling for Delete button */
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
        <h2>Company Management</h2>
        <h3>Create/Edit Company</h3>
        <form method="POST" action="">
            <?php
            $editCompany = null;
            if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] === "edit" && isset($_GET["id"])) {
                $companyId = $_GET["id"];
                $editCompany = $conn->query("SELECT id, companyName, address FROM company WHERE id = $companyId")->fetch_assoc();
            }
            ?>
            <input type="hidden" name="companyId" value="<?php echo $editCompany ? $editCompany["id"] : ""; ?>">
            <label>Company Name:</label>
            <input type="text" name="companyName" value="<?php echo $editCompany ? $editCompany["companyName"] : ""; ?>">
            <?php if (!empty($errors["companyName"])) { echo "<span class='error'>".$errors["companyName"]."</span>"; } ?>
            <label>Address:</label>
            <input type="text" name="address" value="<?php echo $editCompany ? $editCompany["address"] : ""; ?>">
            <input type="submit" value="Save">
        </form>
    </div>

    <div class="container">
        <h3>Companies List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Company Name</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($company as $company) { ?>
                <tr>
                    <td><?php echo $company["id"]; ?></td>
                    <td><?php echo $company["companyName"]; ?></td>
                    <td><?php echo $company["address"]; ?></td>
                    <td>
                    <a href="?action=edit&id=<?php echo $company["id"]; ?>" class="edit-button">Edit</a>
                    <a href="?action=delete&id=<?php echo $company["id"]; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this company?')">Delete</a>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
