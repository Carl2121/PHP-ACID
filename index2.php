<?php
include "db_conn.php";

if (isset($_POST['submit'])) {
    // Collect data
    $s_number = $_POST['s_number'];
    $s_fn = $_POST['s_fn'];
    $s_mn = $_POST['s_mn'];
    $s_ln = $_POST['s_ln'];
    $s_gender = $_POST['s_gender'];
    $s_bday = $_POST['s_birthday'];

    $s_contact = $_POST['s_contact'];
    $s_street = $_POST['s_street'];
    $s_town = $_POST['s_town'];
    $s_province = $_POST['s_province'];
    $s_zipcode = $_POST['s_zipcode'];

    // Start transaction
    $conn->begin_transaction();
    try {
        // Insert into students table
        $stmt1 = $conn->prepare("INSERT INTO students (student_number, first_name, middle_name, last_name, gender, birthday) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("ssssss", $s_number, $s_fn, $s_mn, $s_ln, $s_gender, $s_bday);
        $stmt1->execute();

        // Get the last inserted ID
        $student_id = $conn->insert_id;

        // Insert into student_details table
        $stmt2 = $conn->prepare("INSERT INTO student_details (student_id, contact_number, street, town_city, province, zip_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("isssss", $student_id, $s_contact, $s_street, $s_town, $s_province, $s_zipcode);
        $stmt2->execute();

        // Commit transaction
        $conn->commit();
        echo "New student and details added successfully.";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Transaction failed: " . $e->getMessage();
    }
}

// Update functionality
if (isset($_POST['update'])) {
    $old_id = $_POST['old_id'];
    $new_id = $_POST['new_id'];

    // Start a transaction
    $conn->begin_transaction();
    try {
        // Update `students` table
        $stmt3 = $conn->prepare("UPDATE students SET id = ? WHERE id = ?");
        $stmt3->bind_param("ss", $new_id, $old_id);
        $stmt3->execute();

        // Update `student_details` table
        $stmt4 = $conn->prepare("UPDATE student_details SET student_id = ? WHERE student_id = ?");
        $stmt4->bind_param("ss", $new_id, $old_id);
        $stmt4->execute();

        // Commit the transaction
        $conn->commit();
        echo "Student ID updated successfully.";
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo "Update failed: " . $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Student Records</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Add Student Record</h1>
    <form action="" method="post">
        <label>Student Number:</label> <input type="text" name="s_number" required><br><br>
        <label>First Name:</label><input type="text" name="s_fn" required><br><br>
        <label>Middle Name:</label><input type="text" name="s_mn"><br><br>
        <label>Last Name:</label><input type="text" name="s_ln" required><br><br>
        <label>Gender:</label><input type="text" name="s_gender" required><br><br>
        <label>Birthday:</label><input type="date" name="s_birthday" required><br><br>
        <label>Contact Number:</label><input type="text" name="s_contact" required><br><br>
        <label>Street Name:</label><input type="text" name="s_street"><br><br>
        <label>Town Name:</label><input type="text" name="s_town"><br><br>
        <label>Province Name:</label><input type="text" name="s_province"><br><br>
        <label>Zip Code:</label><input type="text" name="s_zipcode"><br><br>
        <button type="submit" name="submit">Submit</button>
    </form>

    <h1>Update Student ID</h1>
    <form action="" method="post">
        <label>Old Student ID:</label> <input type="text" name="old_id" required><br><br>
        <label>New Student ID:</label> <input type="text" name="new_id" required><br><br>
        <button type="submit" name="update">Update</button>
    </form>
</body>
</html>
