<?php
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Category
{
    private Connect $db; // Oggetto Connect per la connessione al database
    private PDO $conn; // Oggetto PDO per l'esecuzione delle query

    public function __construct() // Costruttore della classe Category
    {
        $this->db = new Connect; // Creazione di un'istanza dell'oggetto Connect
        $this->conn = $this->db->getConnection(); // Connessione al database attraverso il metodo getConnection()
    }

    public function getArchiveCategory() // Metodo per ottenere tutte le categorie dall'archivio
    {
        $query = "SELECT id, name
        from category c"; // Query per selezionare l'id e il nome delle categorie dalla tabella "category"

        $stmt = $this->conn->prepare($query); // Preparazione della query
        $stmt->execute(); // Esecuzione della query

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Restituzione dei risultati della query come array associativo
    }
}
?>
