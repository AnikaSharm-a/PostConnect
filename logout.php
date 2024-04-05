<?php

require "functions.php";

session_unset();
session_regenerate_id();
session_destroy();
header("Location: login.php");
die;
