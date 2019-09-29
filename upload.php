<?php

if (isset($_POST) && isset($_FILES)) {
    if (isset($_FILES['file-category'])) {
        move_uploaded_file($_FILES['file-category']['tmp_name'], "files/groups.csv");
    }
    if (isset($_FILES['file-products'])) {
        move_uploaded_file($_FILES['file-products']['tmp_name'], "files/products.csv");
    }
}