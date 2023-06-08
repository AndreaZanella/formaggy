<?php
// Registra la funzione di caricamento automatico delle classi
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

// Imposta la gestione delle eccezioni personalizzata
set_exception_handler("errorHandler::handleException");
// Imposta la gestione degli errori personalizzata
set_error_handler("errorHandler::handleError");

class Size
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        // Crea un'istanza di Connect per stabilire la connessione al database
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }

    // Ottiene tutte le dimensioni dall'archivio
    public function getArchiveSize()
    {
        $query = "SELECT id, weight
        FROM `size` s";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
