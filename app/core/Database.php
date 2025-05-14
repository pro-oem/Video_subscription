<?php
class Database {
    private static $instance = null;
    private $pdo;
    private $inTransaction = false;
    
    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            // For SELECT queries
            if (stripos($sql, 'SELECT') === 0) {
                $result = $stmt->fetchAll();
                // If it's a COUNT query returning a single value
                if (count($result) === 1 && count((array)$result[0]) === 1) {
                    return $result[0];
                }
                return $result;
            }
            
            // For INSERT queries, return last insert ID
            if (stripos($sql, 'INSERT') === 0) {
                return $this->pdo->lastInsertId();
            }
            
            // For UPDATE/DELETE queries, return affected rows
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    public function beginTransaction() {
        if (!$this->inTransaction) {
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
        }
    }
    
    public function commit() {
        if ($this->inTransaction) {
            $this->pdo->commit();
            $this->inTransaction = false;
        }
    }
    
    public function rollback() {
        if ($this->inTransaction) {
            $this->pdo->rollBack();
            $this->inTransaction = false;
        }
    }
    
    private function handleError($e) {
        // Log the error
        error_log("Database error: " . $e->getMessage());
        
        // Rollback if in transaction
        if ($this->inTransaction) {
            $this->rollback();
        }
        
        // Determine user-friendly error message
        $errorMessage = "A database error occurred.";
        
        if (stripos($e->getMessage(), 'duplicate') !== false) {
            $errorMessage = "This record already exists.";
        } elseif (stripos($e->getMessage(), 'foreign key') !== false) {
            $errorMessage = "This operation would violate data integrity.";
        } elseif (stripos($e->getMessage(), 'not null') !== false) {
            $errorMessage = "Required fields cannot be empty.";
        }
        
        throw new Exception($errorMessage);
    }
    
    public function insertMultiple($table, $columns, $values) {
        try {
            $placeholders = rtrim(str_repeat('(?' . str_repeat(',?', count($columns) - 1) . '),', count($values)), ',');
            $columns = implode(',', $columns);
            
            $sql = "INSERT INTO {$table} ({$columns}) VALUES {$placeholders}";
            
            // Flatten the values array
            $flatValues = [];
            foreach ($values as $row) {
                foreach ($row as $value) {
                    $flatValues[] = $value;
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($flatValues);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    public function update($table, $data, $where) {
        try {
            $setClauses = [];
            $params = [];
            
            foreach ($data as $column => $value) {
                $setClauses[] = "{$column} = ?";
                $params[] = $value;
            }
            
            $whereClauses = [];
            foreach ($where as $column => $value) {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }
            
            $sql = sprintf(
                "UPDATE %s SET %s WHERE %s",
                $table,
                implode(', ', $setClauses),
                implode(' AND ', $whereClauses)
            );
            
            return $this->query($sql, $params);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    public function delete($table, $where) {
        try {
            $whereClauses = [];
            $params = [];
            
            foreach ($where as $column => $value) {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }
            
            $sql = sprintf(
                "DELETE FROM %s WHERE %s",
                $table,
                implode(' AND ', $whereClauses)
            );
            
            return $this->query($sql, $params);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    public function get($table, $where = [], $columns = ['*'], $orderBy = null, $limit = null) {
        try {
            $sql = sprintf("SELECT %s FROM %s", implode(',', $columns), $table);
            $params = [];
            
            if (!empty($where)) {
                $whereClauses = [];
                foreach ($where as $column => $value) {
                    $whereClauses[] = "{$column} = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }
            
            if ($orderBy) {
                $sql .= " ORDER BY " . $orderBy;
            }
            
            if ($limit) {
                $sql .= " LIMIT " . (int)$limit;
            }
            
            return $this->query($sql, $params);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    public function exists($table, $where) {
        try {
            $whereClauses = [];
            $params = [];
            
            foreach ($where as $column => $value) {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }
            
            $sql = sprintf(
                "SELECT EXISTS(SELECT 1 FROM %s WHERE %s) as exists_flag",
                $table,
                implode(' AND ', $whereClauses)
            );
            
            $result = $this->query($sql, $params);
            return (bool)$result->exists_flag;
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
}