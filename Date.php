<?php
require_once 'vendor\autoload.php';

$faker = Faker\Factory::create();

$db= new PDO('mysql:host=localhost; dbname=summer_camp')