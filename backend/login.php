<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) {
        header('Location: ../login.html?error=missing');
        exit;
    }
    $usersFile = __DIR__ . '/users.json';
    $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header('Location: ../index.php');
            exit;
        }
    }
    header('Location: ../login.html?error=invalid');
    exit;
}
header('Location: ../login.html');
exit; 