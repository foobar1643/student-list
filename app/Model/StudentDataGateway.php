<?php

class StudentDataGateway {

  private $db_pdo;

  public function __construct(PDO $pdo) {
    $this->db_pdo = $pdo;
  }

  public function add_student(Student $student) {
    $query = $this->db_pdo->prepare("INSERT INTO student_list(name, surname, gender, sgroup, email, byear, status, rating)
        VALUES (:name_bind, :surname_bind, :gender_bind, :sgroup_bind, :email_bind, :byear_bind, :status_bind, :rating_bind) RETURNING id");
    $query->bindValue(':name_bind', $student->name, PDO::PARAM_STR);
    $query->bindValue(':surname_bind', $student->surname, PDO::PARAM_STR);
    $query->bindValue(':gender_bind', $student->gender, PDO::PARAM_STR);
    $query->bindValue(':sgroup_bind', $student->group, PDO::PARAM_STR);
    $query->bindValue(':email_bind', $student->email, PDO::PARAM_STR);
    $query->bindValue(':byear_bind', $student->byear, PDO::PARAM_STR);
    $query->bindValue(':status_bind', $student->status, PDO::PARAM_STR);
    $query->bindValue(':rating_bind', $student->rating, PDO::PARAM_STR);
    try {
      $query->execute();
    } catch(PDOException $e) {
      print("Adding failed. " . $e->getMessage());
      exit();
    }
    $student_id = $query->fetch();
    return $student_id['id'];
  }

  public function update_student(Student $student) {
    $query = $this->db_pdo->prepare("UPDATE student_list SET name = :name_bind, surname = :surname_bind,
        gender = :gender_bind, sgroup = :sgroup_bind, email = :email_bind, byear = :byear_bind, status = :status_bind,
        rating = :rating_bind WHERE id = :id_bind");
    $query->bindValue(':id_bind', $student->id, PDO::PARAM_INT);
    $query->bindValue(':name_bind', $student->name, PDO::PARAM_STR);
    $query->bindValue(':surname_bind', $student->surname, PDO::PARAM_STR);
    $query->bindValue(':gender_bind', $student->gender, PDO::PARAM_STR);
    $query->bindValue(':sgroup_bind', $student->group, PDO::PARAM_STR);
    $query->bindValue(':email_bind', $student->email, PDO::PARAM_STR);
    $query->bindValue(':byear_bind', $student->byear, PDO::PARAM_STR);
    $query->bindValue(':status_bind', $student->status, PDO::PARAM_STR);
    $query->bindValue(':rating_bind', $student->rating, PDO::PARAM_INT);
    try {
      $query->execute();
    } catch(PDOException $e) {
      print("Updating failed. " . $e->getMessage());
      exit();
    }
  }

  public function get_total_students($search_pattern) {
    $query = $this->db_pdo->prepare("SELECT COUNT(*) FROM student_list WHERE CONCAT(name, ' ', surname) LIKE :search_bind");
    $query->bindValue(':search_bind', "%" . $search_pattern . "%", PDO::PARAM_STR);
    try {
      $query->execute();
    } catch(PDOException $e) {
      print("Can't count students. " . $e->getMessage());
      exit();
    }
    return $query->fetch();
  }

  public function select_students($offset, $sorting_pattern, $sorting_type, $search_pattern) {
    $student = array();
    $query = $this->db_pdo->prepare("SELECT * FROM student_list WHERE CONCAT(name, ' ', surname, ' ', sgroup, ' ', rating) LIKE :search_bind ORDER BY $sorting_pattern $sorting_type LIMIT 15 OFFSET :offset_bind");
    $query->bindValue(':offset_bind', $offset, PDO::PARAM_INT);
    $query->bindValue(':search_bind', "%" . $search_pattern . "%", PDO::PARAM_STR);
    try {
      $query->execute();
    } catch(PDOException $e) {
      print("Can't select students. " . $e->getMessage());
      exit();
    }
    $rows = $query->fetchAll();
    for($i = 0; $i < count($rows); $i++) {
      $student[$i] = new Student();
      $student[$i]->id = $rows[$i]["id"];
      $student[$i]->name = $rows[$i]["name"];
      $student[$i]->surname = $rows[$i]["surname"];
      $student[$i]->gender = $rows[$i]["gender"];
      $student[$i]->group = $rows[$i]["sgroup"];
      $student[$i]->email = $rows[$i]["email"];
      $student[$i]->byear = $rows[$i]["byear"];
      $student[$i]->status = $rows[$i]["status"];
      $student[$i]->rating = $rows[$i]["rating"];
    }
    return $student;
  }

  public function fetch_student($id) {
    $student = new Student();
    $query = $this->db_pdo->prepare("SELECT * FROM student_list WHERE id = :id_bind");
    $query->bindValue(":id_bind", $id, PDO::PARAM_INT);
    try {
      $query->execute();
    } catch(PDOException $e) {
      print("Selecting failed. " . $e->getMessage());
      exit();
    }
    $row = $query->fetch();
    $student->id = $row["id"];
    $student->name = $row["name"];
    $student->surname = $row["surname"];
    $student->gender = $row["gender"];
    $student->group = $row["sgroup"];
    $student->email = $row["email"];
    $student->byear = $row["byear"];
    $student->status = $row["status"];
    $student->rating = $row["rating"];
    return $student;
  }
}
