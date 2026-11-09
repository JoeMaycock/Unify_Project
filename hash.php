<?php

function passwordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function passwordVerify($password, $hash) {
    return password_verify($password, $hash);
}

?>