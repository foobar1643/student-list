<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Database
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Database;

use Students\Entity\Student;

/**
 * Provides a simple interface that works with a 'students' table in the database.
 */
class StudentDataGateway
{
    /** @var PDO $pdo PDO object. */
    private $pdo;

    /**
     * Constructor.
     *
     * @param PDO $pdo PDO object.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Adds a student to the database and returns the ID of the added student.
     *
     * @param Student $student A File entity to be added.
     *
     * @return int
     */
    public function addStudent(Student $student)
    {
        $query = $this->pdo->prepare("INSERT INTO students(name, surname, gender, sgroup, email, byear, status, rating, token) "
            ."VALUES (:name_bind, :surname_bind, :gender_bind, :sgroup_bind, :email_bind, :byear_bind, :status_bind, :rating_bind, :token_bind) RETURNING id");
        $query->bindValue(':name_bind', $student->getName(), \PDO::PARAM_STR);
        $query->bindValue(':surname_bind', $student->getSurname(), \PDO::PARAM_STR);
        $query->bindValue(':gender_bind', $student->getGender(), \PDO::PARAM_STR);
        $query->bindValue(':sgroup_bind', $student->getGroup(), \PDO::PARAM_STR);
        $query->bindValue(':email_bind', $student->getEmail(), \PDO::PARAM_STR);
        $query->bindValue(':byear_bind', $student->getBirthYear(), \PDO::PARAM_INT);
        $query->bindValue(':status_bind', $student->getStatus(), \PDO::PARAM_STR);
        $query->bindValue(':rating_bind', $student->getRating(), \PDO::PARAM_INT);
        $query->bindValue(':token_bind', $student->getToken(), \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchColumn();
    }

    /**
     * Updates student in the database.
     *
     * @param Student $student A File entity to update.
     *
     * @return void
     */
    public function updateStudent(Student $student)
    {
        $query = $this->pdo->prepare("UPDATE students SET name = :name_bind, surname = :surname_bind, "
            ."gender = :gender_bind, sgroup = :sgroup_bind, email = :email_bind, byear = :byear_bind, status = :status_bind, "
            ."rating = :rating_bind WHERE token = :token_bind");
        $query->bindValue(':token_bind', $student->getToken(), \PDO::PARAM_STR);
        $query->bindValue(':name_bind', $student->getName(), \PDO::PARAM_STR);
        $query->bindValue(':surname_bind', $student->getSurname(), \PDO::PARAM_STR);
        $query->bindValue(':gender_bind', $student->getGender(), \PDO::PARAM_STR);
        $query->bindValue(':sgroup_bind', $student->getGroup(), \PDO::PARAM_STR);
        $query->bindValue(':email_bind', $student->getEmail(), \PDO::PARAM_STR);
        $query->bindValue(':byear_bind', $student->getBirthYear(), \PDO::PARAM_INT);
        $query->bindValue(':status_bind', $student->getStatus(), \PDO::PARAM_STR);
        $query->bindValue(':rating_bind', $student->getRating(), \PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * Returns a number of total students in the database.
     *
     * @param string $searchPattern Search pattern to use.
     *
     * @return int
     */
    public function getTotalStudents($searchPattern = null)
    {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM students WHERE CONCAT(name, ' ', surname, ' ', sgroup) LIKE :search_bind");
        $query->bindValue(':search_bind', "%" . $searchPattern . "%", \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchColumn();
    }

    /**
     * Searches students in the database.
     *
     * @param string $searchPattern Search pattern to use.
     * @param int $offset Offset in the database.
     * @param int $limit Limit in the database.
     * @param string $sortingPattern Sorting pattern.
     * @param string $sortingType Sorting type.
     *
     * @return array
     */
    public function searchStudents($searchPattern, $offset, $limit, $sortingPattern, $sortingType)
    {
        $query = $this->pdo->prepare("SELECT * FROM students WHERE CONCAT(name, ' ', surname, ' ', sgroup)"
        . " ILIKE :search_bind ORDER BY ". pg_escape_string($sortingPattern) ." ". pg_escape_string($sortingType) ." LIMIT :limit OFFSET :offset");
        $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $query->bindValue(':search_bind', "%{$searchPattern}%", \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, "Students\Entity\Student");
    }

    /**
     * Selects a single student from the database.
     *
     * @param string $token Student auth token.
     *
     * @return Student
     */
    public function selectStudent($token)
    {
        $student = new Student();
        $query = $this->pdo->prepare("SELECT * FROM students WHERE token = :token_bind");
        $query->bindValue("token_bind", $token, \PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, "Students\Entity\Student");
        return $query->fetch();
    }

    /**
     * Checks if given email exists in the database.
     *
     * @param string $email An email to check.
     * @param string $token Student auth token.
     *
     * @return int
     */
    public function checkEmail($email, $token)
    {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM students WHERE email = lower(:email_bind) AND token != :token_bind");
        $query->bindValue(":email_bind", $email, \PDO::PARAM_STR);
        $query->bindValue(":token_bind", strval($token), \PDO::PARAM_STR);
        $query->execute();
        return $query->fetchColumn();
    }
}