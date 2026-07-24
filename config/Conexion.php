<?php

class Conexion {

    private static ?Conexion $instancia = null;
    private PDO $pdo;

    /* ── Parámetros de conexión ── */
    private string $host    = 'localhost';
    private string $dbname  = 'itech_contrataciones';
    private string $usuario = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';

    /* ── Constructor privado */
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $this->usuario, $this->password, $opciones);
    }

    /** Retorna la única instancia de la conexión. */
    public static function getInstancia(): Conexion {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    /** Retorna el objeto PDO subyacente. */
    public function getPDO(): PDO {
        return $this->pdo;
    }

    
    public function consulta(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

   
    public function insertar(string $sql, array $params = []): string|false {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->pdo->lastInsertId();
    }

   
    public function ejecutar(string $sql, array $params = []): bool {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
