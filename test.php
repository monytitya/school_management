<?php
require 'db.php';

$action = $_GET['action'] ?? 'read';

// ─── CREATE ──────────────────────────────
if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO test (firstName, lastName, grade, email, dob, phone, address) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['grade'],
        $_POST['email'],
        $_POST['dob'],
        $_POST['phone'],
        $_POST['address']
    ]);
    header("Location: test.html");
    exit;
}

// ─── READ ────────────────────────────────
if ($action == 'read') {
    $stmt = $pdo->query("SELECT * FROM test");
    echo "<table border='1'>
            <tr><th>Name</th><th>Email</th><th>Grade</th><th>Actions</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>
                <td>{$row['firstName']} {$row['lastName']}</td>
                <td>{$row['email']}</td>
                <td>{$row['grade']}</td>
                <td><a href='test.php?action=delete&email={$row['email']}'>Delete</a></td>
              </tr>";
    }
    echo "</table><br><a href='test.html'>Back to Form</a>";
}

// ─── DELETE ──────────────────────────────
if ($action == 'delete') {
    $email = $_GET['email'];
    $stmt = $pdo->prepare("DELETE FROM test WHERE email = ?");
    $stmt->execute([$email]);
    header("Location: test.php?action=read");
    exit;
}
