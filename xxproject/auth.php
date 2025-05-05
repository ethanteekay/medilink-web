<?php
session_start();
require_once 'db_connect.php';

function login($email, $password, $user_type) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND user_type = ?");
    $stmt->execute([$email, $user_type]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function signup($name, $email, $password, $user_type) {
    global $pdo;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $email, $hashed_password, $user_type]);
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function getCurrentUser() {
    return isLoggedIn() ? $_SESSION['user'] : null;
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>