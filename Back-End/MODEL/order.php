<?php
// Registra la funzione di caricamento automatico delle classi
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

// Imposta la gestione delle eccezioni personalizzata
set_exception_handler("errorHandler::handleException");
// Imposta la gestione degli errori personalizzata
set_error_handler("errorHandler::handleError");

class Order
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        // Crea un'istanza di Connect per stabilire la connessione al database
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }
    
    // Ottiene tutti gli ordini dall'archivio
    public function getArchiveOrder()
    {
        $query = "SELECT o.id, a.username, o.address, o.date_order, o.total_price, o.status
        FROM `order` o
        INNER JOIN account a ON o.id_account = a.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottiene un ordine specifico
    public function getOrder($id)
    {
        $sql = "SELECT o.id, a.username , o.address, o.date_order, o.total_price, o.status
        FROM `order` o
        INNER JOIN account a ON a.id = o.id_account 
        WHERE o.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Ottiene i prodotti di un ordine specifico
    public function getOrderFormaggy($id)
    {
        $sql = "SELECT f.id, f.name, f.description, of2.weight
        FROM `order` o
        INNER JOIN order_formaggyo of2 ON of2.id_order = o.id
        INNER JOIN formaggyo f ON f.id = of2.id_formaggyo 
        WHERE o.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Modifica lo stato di un ordine
    public function modifyOrderStatus($status, $id_order)
    {
        $sql = "UPDATE `order`
             SET status = :status
             WHERE id = :id_order";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':id_order', $id_order, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    // Elimina un ordine
    public function deleteOrder($id_order)
    {
        $sql = "UPDATE `order`
            SET status = 3
            WHERE id = :id_order";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_order', $id_order, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
?>
