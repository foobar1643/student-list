<?php

include("../app/init.php");

use Pimple\Container;
use \App\Model\Student;
use \App\Helper\RegistrationHelper;
use \App\Exception\FileOperationException;

function outputHelpMessage() {
    print("This is a command line PHP script, it requires an option -c in order to run." . PHP_EOL . PHP_EOL
    . "Usage: " . $argv[0] . " -c <option>" . PHP_EOL
    . "<option> can be any number that is bigger than zero." . PHP_EOL . PHP_EOL
    . "Additionally, with the -h option you can show this help." . PHP_EOL);
}

function getRandomString($min, $max) {
    $letters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    for($i = 0; $i < mt_rand($min, $max); $i++) {
        $string .= $letters[mt_rand(0, count($letters) - 1)];
    }
    return $string;
}

function checkStringCallback($string) {
    if(preg_match("/^[А-ЯЁA-Z][-а-яёa-zА-ЯЁA-Z\\s]{1,20}$/u", $string)) {
        return true;
    }
    return false;
}

function readNamesFile($filename) {
    $fileRaw = file($filename);
    if($fileRaw == false) {
        throw new FileOperationException("Can't read file " . $filename . "." . PHP_EOL);
    }
    $fileTrimmed = array_map("trim", $fileRaw);
    $fileFiltered = array_filter($fileTrimmed, "checkStringCallback");
    return $fileFiltered;
}

function selectRandomElement($array) {
    return $array[mt_rand(0, count($array) - 1)];
}

$options = getopt("c:h");

if(!isset($options["c"]) || $options["c"] <= 0 || isset($options["h"])) {
    die(outputHelpMessage());
}

$names = readNamesFile("data/names.txt");
$surnames = readNamesFile("data/surnames.txt");

$dataGateway = $container["dataGateway"];
for($i = 0; $i < $options["c"]; $i++) {
    $student = new Student();
    $student->name = selectRandomElement($names);
    $student->surname =selectRandomElement($surnames);
    $student->gender = Student::GENDER_MALE; // Sexism?
    $student->sgroup = getRandomString(3, 5);
    $student->email = getRandomString(5, 13) . "@example.com";
    $student->byear = "19" . mt_rand(0,9) . mt_rand(0,9);
    $student->status = Student::STATUS_RESIDENT;
    $student->rating = mt_rand(0, RegistrationHelper::MAX_RATING);
    $dataGateway->addStudent($student);
}

print("Database was successfully filled for " . $options["c"] . " entries." . PHP_EOL);