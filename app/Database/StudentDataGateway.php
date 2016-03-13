<?php

namespace App\Database;

use \App\Model\Student;

class StudentDataGateway {

    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function addStudent(Student $student) {
        $query = $this->pdo->prepare("INSERT INTO students(name, surname, gender, sgroup, email, byear, status, rating, token) "
            ."VALUES (:name_bind, :surname_bind, :gender_bind, :sgroup_bind, :email_bind, :byear_bind, :status_bind, :rating_bind, :token_bind) RETURNING id");
        $query->bindValue(':name_bind', $student->name, \PDO::PARAM_STR);
        $query->bindValue(':surname_bind', $student->surname, \PDO::PARAM_STR);
        $query->bindValue(':gender_bind', $student->gender, \PDO::PARAM_STR);
        $query->bindValue(':sgroup_bind', $student->sgroup, \PDO::PARAM_STR);
        $query->bindValue(':email_bind', $student->email, \PDO::PARAM_STR);
        $query->bindValue(':byear_bind', $student->byear, \PDO::PARAM_INT);
        $query->bindValue(':status_bind', $student->status, \PDO::PARAM_STR);
        $query->bindValue(':rating_bind', $student->rating, \PDO::PARAM_INT);
        $query->bindValue(':token_bind', $student->token, \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchColumn();
    }

    public function updateStudent(Student $student) {
        $query = $this->pdo->prepare("UPDATE students SET name = :name_bind, surname = :surname_bind, "
            ."gender = :gender_bind, sgroup = :sgroup_bind, email = :email_bind, byear = :byear_bind, status = :status_bind, "
            ."rating = :rating_bind WHERE token = :token_bind");
        $query->bindValue(':token_bind', $student->token, \PDO::PARAM_STR);
        $query->bindValue(':name_bind', $student->name, \PDO::PARAM_STR);
        $query->bindValue(':surname_bind', $student->surname, \PDO::PARAM_STR);
        $query->bindValue(':gender_bind', $student->gender, \PDO::PARAM_STR);
        $query->bindValue(':sgroup_bind', $student->sgroup, \PDO::PARAM_STR);
        $query->bindValue(':email_bind', $student->email, \PDO::PARAM_STR);
        $query->bindValue(':byear_bind', $student->byear, \PDO::PARAM_INT);
        $query->bindValue(':status_bind', $student->status, \PDO::PARAM_STR);
        $query->bindValue(':rating_bind', $student->rating, \PDO::PARAM_INT);
        $query->execute();
    }

    public function getTotalStudents($searchPattern) {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM students WHERE CONCAT(name, ' ', surname, ' ', sgroup) LIKE :search_bind");
        $query->bindValue(':search_bind', "%" . $searchPattern . "%", \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchColumn();;
    }

    public function selectStudents($offset, $sortingPattern, $sortingType, $searchPattern, $limit) {
        $query = $this->pdo->prepare("SELECT * FROM students WHERE CONCAT(name, ' ', surname, ' ', sgroup)"
        . " ILIKE :search_bind ORDER BY $sortingPattern $sortingType LIMIT $limit OFFSET :offset_bind");
        $query->bindValue(':offset_bind', $offset, \PDO::PARAM_INT);
        $query->bindValue(':search_bind', "%" . $searchPattern . "%", \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, "\App\Model\Student");
    }

    public function selectStudent($token) {
        $student = new Student();
        $query = $this->pdo->prepare("SELECT * FROM students WHERE token = :token_bind");
        $query->bindValue("token_bind", $token, \PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, "\App\Model\Student");
        return $query->fetch();
    }

    public function checkEmail($email, $token) {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM students WHERE email = lower(:email_bind) AND token != :token_bind");
        $query->bindValue(":email_bind", $email, \PDO::PARAM_STR);
        $query->bindValue(":token_bind", strval($token), \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchColumn();
    }
}
