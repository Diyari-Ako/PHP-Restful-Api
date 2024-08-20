<?php

//This file will run evry week by Task Scheduler.

require "bootstrap.php";

$refreshToken = new Model\RefreshToken($database);
$refreshToken->deleteExpired();

