<?php
require 'db.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['full_name'] ?? $_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $source = sanitizeInput($_POST['source_id']);
    $entry_qualification = sanitizeInput($_POST['entry-qualification'] ?? '');
    $sendoveremail = isset($_POST['sendoveremail']) ? 1 : 0;
    $preferred_program = sanitizeInput($_POST['certification'] ?? '');
    $qualifications = sanitizeInput($_POST['qualifications'] ?? '');
    $company_site = sanitizeInput($_POST['company_site'] ?? '');
    $enrollment = sanitizeInput($_POST['enrollment'] ?? '0');

    $stmt = $conn->prepare("INSERT INTO inquiries (first_name, last_name, email, contact_no, source_id, entry_qualification, sendoveremail, preferred_program, qualifications, company_site, enrollment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $name, $last_name, $email, $phone, $source, $entry_qualification, $sendoveremail, $preferred_program, $qualifications, $company_site, $enrollment);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you! Your submission has been received.'); window.location.href = '/';</script>";
    } else {
        echo "<script>alert('Sorry! There was an error processing your submission.'); window.location.href = '/';</script>";
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
