<?php

namespace App;

use PDO;

class CRUD extends DB
{
    // ? Select 
    public function select(String $table, $rows = '*', $join = null, $where = null, $order = null, $limit = null)
    {
        if (!$this->tableExist($table)) {
            return "$table table not exist";
        }

        $sql = "SELECT $rows FROM $table ";

        if ($join) $sql .= "JOIN $join ";
        if ($where) $sql .= "WHERE $where ";
        if ($order) $sql .= "ORDER BY $order ";
        if ($limit) {
            $page = $_GET['page'] ?? 1;
            $start = ($page - 1) * $limit;
            $sql .= "LIMIT $start, $limit ";
        }

        $conn = parent::getConnection();
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    private function tableExist($table)
    {
        $conn = parent::getConnection();
        $stmt = $conn->prepare("SHOW TABLES LIKE :table");
        $stmt->execute([":table" => $table]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
