<?php

namespace App\Database;
use \App\Model\Student;

class StudentDataGateway {

  private $db_pdo;

  public function __construct(\PDO $pdo) {
    $this->db_pdo = $pdo;
  }

  public function add_student(Student $student) {
    $query = $this->db_pdo->prepare("INSERT INTO students(name, surname, gender, sgroup, email, byear, status, rating, token)
        VALUES (:name_bind, :surname_bind, :gender_bind, :sgroup_bind, :email_bind, :byear_bind, :status_bind, :rating_bind, :token_bind) RETURNING id");
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
    $result = $query->fetchColumn();
    return $result;
  }

  public function update_student(Student $student) {
    $query = $this->db_pdo->prepare("UPDATE students SET name = :name_bind, surname = :surname_bind,
        gender = :gender_bind, sgroup = :sgroup_bind, email = :email_bind, byear = :byear_bind, status = :status_bind,
        rating = :rating_bind WHERE id = :id_bind");
    $query->bindValue(':id_bind', $student->id, \PDO::PARAM_INT);
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

  public function get_total_students($search_pattern) {
    $query = $this->db_pdo->prepare("SELECT COUNT(*) FROM students WHERE CONCAT(name, ' ', surname, ' ', sgroup) LIKE :search_bind");
    $query->bindValue(':search_bind', "%" . $search_pattern . "%", \PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetchColumn();
    return $result;
  }

  public function select_students($offset, $sorting_pattern, $sorting_type, $search_pattern, $limit) {
    $query = $this->db_pdo->prepare("SELECT * FROM students WHERE CONCAT(name, ' ', surname, ' ', sgroup)"
    . " LIKE :search_bind ORDER BY $sorting_pattern $sorting_type LIMIT $limit OFFSET :offset_bind");
    //SELECT * FROM students WHERE CONCAT(name, ' ', surname, ' ', sgroup, ' ', rating) LIKE :search_bind ORDER BY $sorting_pattern $sorting_type LIMIT 15 OFFSET :offset_bind
    $query->bindValue(':offset_bind', $offset, \PDO::PARAM_INT);
    $query->bindValue(':search_bind', "%" . $search_pattern . "%", \PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetchAll(\PDO::FETCH_CLASS, "\App\Model\Student");
    return $rows;
  }

    public function selectToken($token) {
        $student = new Student();
        $query = $this->db_pdo->prepare("SELECT * FROM students WHERE token = :token_bind");
        $query->bindValue("token_bind", $token, \PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, "\App\Model\Student");
        $student = $query->fetch();
        return $student;
    }

    public function checkEmail($email, $id) {
        $query = $this->db_pdo->prepare("SELECT COUNT(*) FROM students WHERE email = :email_bind AND NOT EXISTS (SELECT * FROM students WHERE id = :id_bind AND email = :email_bind)");
        $query->bindValue(":email_bind", $email, \PDO::PARAM_STR);
        $query->bindValue(":id_bind", $id, \PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchColumn();
        return $result;
    }

  public function fetch_student($id) {
    $student = new Student();
    $query = $this->db_pdo->prepare("SELECT * FROM students WHERE id = :id_bind");
    $query->bindValue(":id_bind", $id, \PDO::PARAM_INT);
    $query->execute();
    $query->setFetchMode(\PDO::FETCH_CLASS, "\App\Model\Student");
    $result = $query->fetch();
    return $result;
  }
}
