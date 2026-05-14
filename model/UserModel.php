<?php
require_once __DIR__ . "/../config/db.php";

function createUser($name, $email, $passwordHash, $role, $address, $phone)
{
    global $conn;

    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $passwordHash = mysqli_real_escape_string($conn, $passwordHash);
    $role = mysqli_real_escape_string($conn, $role);
    $address = mysqli_real_escape_string($conn, $address);
    $phone = mysqli_real_escape_string($conn, $phone);

    $query = "INSERT INTO users (name, email, password_hash, role, address, phone)
              VALUES ('$name', '$email', '$passwordHash', '$role', '$address', '$phone')";

    return mysqli_query($conn, $query);
}