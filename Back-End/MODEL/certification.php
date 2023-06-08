<?php
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Certification
{
    private PDO $conn;
    private Connect $db;

    public function __construct() // Costruttore della classe
    {
        $this->db = new Connect; // Crea un'istanza dell'oggetto Connect
        $this->conn = $this->db->getConnection(); // Ottiene la connessione al database
    }

    public function getArchiveCertification() // Ottiene tutte le certificazioni dall'archivio
    {
        $query = "SELECT id, acronym, name
                  FROM certification c"; // Query per selezionare le certificazioni

        $stmt = $this->conn->prepare($query); // Prepara la query
        $stmt->execute(); // Esegue la query

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Restituisce i risultati come un array associativo
    }
}
?>
