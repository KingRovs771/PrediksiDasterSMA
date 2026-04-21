<?php

class Users
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getUserByUsername($username)
    {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);
        $result = $this->db->resultSet();
        return !empty($result) ? $result[0] : null;
    }
}

?>