<?php
  include 'connect.php';
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    $sql = "INSERT INTO user (fullname, email, password, phone) VALUES ('$name', '$email', '$password', '$phone')";
    //instance of exception handling
    if ($conn->query($sql) === TRUE) {
        header('Location: ../Dashboard.html');
    } else {
        // header('Location: ../register.html');
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
?>
