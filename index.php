<?php
// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "edumart";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// 1️⃣ Get all fee records where student_id is NULL, empty, or 0
$fee_sql = "SELECT * FROM fee WHERE (student_id IS NULL OR student_id = '' OR student_id = 0)";
$fee_result = $conn->query($fee_sql);

$records = [];
if ($fee_result->num_rows > 0) {
    while ($fee = $fee_result->fetch_assoc()) {
        $fee_class = $conn->real_escape_string($fee['class']);
        $fee_roll  = $conn->real_escape_string($fee['roll']);

        // 2️⃣ Match with student table
        $stu_sql = "SELECT student_id, firstname, lastname, roll_no, class
                    FROM student
                    WHERE class = '$fee_class' AND roll_no = '$fee_roll'";
        $stu_result = $conn->query($stu_sql);

        if ($stu_result->num_rows > 0) {
            $student = $stu_result->fetch_assoc();

            // Save fee + student info for JS
            $records[] = [
                'fee_id' => $fee['fee_id'],
                'fee_name' => $fee['name'],
                'fee_roll' => $fee['roll'],
                'fee_class'=> $fee['class'],
                'student_id' => $student['student_id'],
                'stu_name' => $student['firstname'] . ' ' . $student['lastname'],
                'stu_roll' => $student['roll_no'],
                'stu_class'=> $student['class']
            ];
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Fee Student ID</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<script>
$(document).ready(function(){
    let records = <?php echo json_encode($records); ?>;

    function processRecord(i) {
        if (i >= records.length) {
            alert("✅ Process finished.");
            return;
        }

        let r = records[i];
        let msg = "Match found!\n\n" +
                  "➡ Fee Table:\n" +
                  "   Name: " + r.fee_name + "\n" +
                  "   Roll No: " + r.fee_roll + "\n" +
                  "   Class: " + r.fee_class + "\n\n" +
                  "➡ Student Table:\n" +
                  "   Name: " + r.stu_name + "\n" +
                  "   Roll No: " + r.stu_roll + "\n" +
                  "   Class: " + r.stu_class + "\n\n" +
                  "Do you want to update fee_id " + r.fee_id + 
                  " with student_id " + r.student_id + "?";

        if (confirm(msg)) {
            $.post("update_fee.php", { fee_id: r.fee_id, student_id: r.student_id }, function(resp){
                alert(resp);
                processRecord(i+1);
            });
        } else {
            processRecord(i+1);
        }
    }

    if (records.length > 0) {
        processRecord(0);
    } else {
        alert("No fee records to update.");
    }
});
</script>

</body>
</html>
