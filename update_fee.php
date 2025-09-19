<?php
//
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "edumart";
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

if (!empty($_POST['fee_id']) && !empty($_POST['student_id'])) {
    $fee_id = intval($_POST['fee_id']);
    $student_id = intval($_POST['student_id']);

    $sql = "UPDATE fee SET student_id = $student_id WHERE fee_id = $fee_id";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Updated fee_id $fee_id with student_id $student_id";
    } else {
        echo "❌ Update failed: " . $conn->error;
    }
} else {
    echo "❌ Missing parameters.";
}

$conn->close();
?>
