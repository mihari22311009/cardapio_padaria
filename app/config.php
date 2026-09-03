<?php
$conn = new PDO("mysql:host=mysql;dbname=app", "app", "app");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);