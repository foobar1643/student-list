<?php

include("../src/Bootstrap.php");

use Students\Entity\Student;
use Students\Validation\StudentValidator;

function outputHelpMessage(array $argv)
{
    print("This is a command line PHP script, it requires an option -c in order to run." . PHP_EOL . PHP_EOL
    . "Usage: {$argv[0]} -c <option>" . PHP_EOL
    . "<option> can be any number that is bigger than zero." . PHP_EOL . PHP_EOL
    . "Additionally, with the -h option you can show this help." . PHP_EOL);
}

function getRandomString($min, $max)
{
    $string = "";
    $letters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    for($i = 0; $i < mt_rand($min, $max); $i++) {
        $string .= $letters[mt_rand(0, count($letters) - 1)];
    }
    return $string;
}

function checkStringCallback($string)
{
    return (preg_match("/^[А-ЯЁA-Z][-а-яёa-zА-ЯЁA-Z\\s]{1,20}$/u", $string));
}

function readNamesFile($filename)
{
    $fileRaw = file($filename);
    if($fileRaw == false) {
        throw new \Exception("Can't read file {$filename}." . PHP_EOL);
    }
    $fileTrimmed = array_map("trim", $fileRaw);
    $fileFiltered = array_filter($fileTrimmed, "checkStringCallback");
    return $fileFiltered;
}

function selectRandomElement($array)
{
    return $array[mt_rand(0, count($array) - 1)];
}

$options = getopt("c:h");

if(!isset($options["c"]) || $options["c"] <= 0 || isset($options["h"])) {
    die(outputHelpMessage($argv));
}

$names = readNamesFile("./resource/names.txt");
$surnames = readNamesFile("./resource/surnames.txt");

for($i = 0; $i < $options["c"]; $i++) {
    $student = new Student();
    $student->setFirstName(selectRandomElement($names));
    $student->setLastName(selectRandomElement($surnames));
    $student->setGender(Student::GENDER_MALE);
    $student->setGroup(getRandomString(3, 5));
    $student->setEmail(sprintf("%s@example.com", getRandomString(5, 13)));
    $student->setBirthYear(sprintf("19%d%d", mt_rand(0,9), mt_rand(0,9)));
    $student->setStatus(Student::STATUS_RESIDENT);
    $student->setRating(mt_rand(0, StudentValidator::STUDENT_MAX_RATING));
    $container["studentGateway"]->addStudent($student);
}

print("Database was successfully filled for {$options['c']} entries." . PHP_EOL);