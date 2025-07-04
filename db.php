<?php
class Database {
    private $host = 'localhost';
    private $db = 'chroma_spark';    // Database name
    private $user = 'root';          // Database username
    private $pass = '';              // Database password
    private $conn;

    public function __construct() {
        // Create the database connection
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to execute SELECT queries
    public function select($query, $params = []) {
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if ($stmt === false) {
            die("Prepared statement failed: " . $this->conn->error);
        }

        // Bind parameters if any
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                $types .= $this->getParamType($param);
            }
            $stmt->bind_param($types, ...$params);
        }

        // Execute the query
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        // Close the statement
        $stmt->close();
        
        return $result;
    }

    // Method to execute INSERT/UPDATE/DELETE queries
    public function execute($query, $params = []) {
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        if ($stmt === false) {
            die("Prepared statement failed: " . $this->conn->error);
        }

        // Bind parameters if any
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                $types .= $this->getParamType($param);
            }
            $stmt->bind_param($types, ...$params);
        }

        // Execute the query
        $stmt->execute();
        
        // Check if the query was successful
        if ($stmt->affected_rows > 0) {
            return true;
        }

        // Close the statement
        $stmt->close();
        
        return false;
    }

    // Helper function to determine the type of each parameter
    private function getParamType($param) {
        if (is_int($param)) {
            return 'i';  // integer
        } elseif (is_double($param)) {
            return 'd';  // double
        } elseif (is_string($param)) {
            return 's';  // string
        } else {
            return 'b';  // blob (for binary data)
        }
    }

    // Destructor to close the connection
    public function __destruct() {
        $this->conn->close();
    }
}
?>
