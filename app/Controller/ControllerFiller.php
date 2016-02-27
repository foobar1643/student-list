<?php

namespace App\Controller;

use Pimple\Container;
use \App\Model\Student;
use \App\Helper\FormHelper;
use \App\Exception\FileOperationException;

class ControllerFiller {

    private $container;

    public function __construct(Container $c) {
        $this->container = $c;
    }

    public function run() {
        $config = $this->container["config"];
        if($config->getValue("app", "enableFiller") == false) {
            header("Location: index.php");
            die();
        }
        if($_POST) {
            $count = isset($_POST['count_field']) ? strval($_POST['count_field']) : '';
            if($count < 100 && $count > 0) {
                $data = $this->readNames();
                $letters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
                $dataGateway = $this->container["dataGateway"];
                for($i = 0; $i < $count; $i++) {
                    $student = new Student();
                    $student->name = $data["names"][mt_rand(0, count($data["names"])-1)];
                    $student->surname = $data["surnames"][mt_rand(0, count($data["surnames"])-1)];
                    $student->gender = Student::GENDER_MALE; // Sexism?
                    for($x = 0; $x < mt_rand(3, 4); $x++) $student->sgroup .= $letters[mt_rand(0, count($letters)-1)];
                    for($x = 0; $x < mt_rand(3, 15); $x++) $student->email .= $letters[mt_rand(0, count($letters)-1)];
                    $student->email .= "@gmail.com";
                    $student->byear = "19" . mt_rand(0,9) . mt_rand(0,9);
                    $student->status = Student::STATUS_RESIDENT;
                    $student->rating = mt_rand(0,FormHelper::MAX_RATING);
                    $dataGateway->add_student($student);
                }
                $success = $_POST['count_field'];
            } else {
                $error = true;
            }
        }
        $pageTitle = "Заполнитель базы данных";
        $navTitle = "filler";
        include("../templates/filler.html");
    }

    private function readNames() {
        $names = fopen("../data/names.txt", "r");
        $surnames = fopen("../data/surnames.txt", "r");
        $names_array = array("names" => array(), "surnames" => array());
        if($names && $surnames) {
            while(($line = fgets($names)) !== false) array_push($names_array["names"], trim($line));
            while(($line = fgets($surnames)) !== false) array_push($names_array["surnames"], trim($line));
        } else {
            throw new FileOperationException("Can't open data file in read mode.");
        }
        return $names_array;
    }
}