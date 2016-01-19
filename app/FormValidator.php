<?php

class FormValidator {

  public function validate_student(Student $student) {
    $errors = null;
    if(!preg_match("/[А-ЯЁA-Z]{1}[а-яёa-z]{1,15}/u", $student->name)) $errors['name'] = true;
    if(!preg_match("/[А-ЯЁA-Z]{1}[а-яёa-z]{1,15}/u", $student->surname)) $errors['surname'] = true;
    if($student->gender != "m" && $student->gender != "f") $errors['gender'] = true;
    if(!filter_var($student->email, FILTER_VALIDATE_EMAIL)) $errors['email'] = true;
    if(!preg_match("/[А-ЯЁа-яёa-zA-Z0-9]{1,6}/u", $student->group)) $errors['group'] = true;
    if(!preg_match("/[19][0-9]{2}/", $student->byear)) $errors['byear'] = true;
    if($student->status != "mov" && $student->status != "sty") $errors['status'] = true;
    if($student->rating < 0 || $student->rating > 100) $errors['rating'] = true;
    return $errors;
  }

  public function process_field($field) {
    return htmlspecialchars(trim(strval($field)));
  }

  public function generate_token() {
    $source = str_split('abcdefghijklmnopqrstuvwxyz'
      .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
      .'0123456789');
    for($i = 0; $i < 40; $i++) $result .= $source[rand(0, count($source))];
    return $result;
  }

  public function store_token($token, $id, $pdo) {
    $query = $pdo->prepare("INSERT INTO tokens(token, student_id) VALUES (:token_bind, :student_id_bind)");
    $query->bindValue(':token_bind', $token, PDO::PARAM_STR);
    $query->bindValue(':student_id_bind', $id, PDO::PARAM_STR);
    try {
      $query->execute();
    } catch(PDOException $e) {
      print("Storing token failed. " . $e->getMessage());
      exit();
    }
  }

  public function select_token($token, $pdo) {
    $query = $pdo->prepare("SELECT student_id FROM tokens WHERE token = :token_bind");
    $query->bindValue(':token_bind', $token, PDO::PARAM_STR);
    try {
      $query->execute();
      $result = $query->fetch();
    } catch(PDOException $e) {
      print("Selecting token failed. " . $e->getMessage());
      exit();
    }
    return $result;
  }
}
