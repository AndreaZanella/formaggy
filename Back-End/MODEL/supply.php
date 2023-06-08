<?php
// Registra la funzione di caricamento automatico delle classi
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

// Imposta la gestione delle eccezioni personalizzata
set_exception_handler("errorHandler::handleException");
// Imposta la gestione degli errori personalizzata
set_error_handler("errorHandler::handleError");

class Supply
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        // Crea un'istanza di Connect per stabilire la connessione al database
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }
    
    // Ottiene tutti gli ordini dal database
    public function getArchiveSupply()
    {
        $sql = "SELECT s.id, a.username, d.name as dairy_name, s.date_supply, s.total_price, s.status
        FROM supply s
        INNER JOIN dairy d on s.id_dairy = d.id
        INNER JOIN account a on a.id = s.id_account
        WHERE 1 = 1
        ORDER BY s.date_supply desc";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottiene un ordine specifico dal database tramite ID
    public function getSupply($id)
    {
        $sql = "SELECT s.id, a.username, d.name as dairy_name, s.date_supply, s.total_price, s.status
        FROM supply s
        INNER JOIN dairy d on s.id_dairy = d.id
        INNER JOIN account a on a.id = s.id_account
        WHERE s.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Ottiene i prodotti di un ordine specifico dal database tramite ID
    public function getSupplyFormaggy($id)
    {
        $sql = "SELECT f.id, f.name, f.description, sf.weight
        FROM supply s
        INNER JOIN supply_formaggyo sf ON s.id = sf.id_supply
        INNER JOIN formaggyo f on f.id = sf.id_formaggyo
        WHERE s.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Aggiunge un nuovo ordine di fornitura al database
    public function addSupply($id_account, $id_dairy, $id_formaggyo, $total_price, $status, $weight)
    {
        $sql = "INSERT into supply (id_account, id_dairy, date_supply, total_price, status)
                values (:id_account, :id_dairy, now(), :total_price, :status);";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);
        $stmt->bindValue(':total_price', $total_price, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "INSERT into supply_formaggyo (id_supply, id_formaggyo, weight)
              values((select s.id
                from supply s
                order by s.id desc 
                limit 1), :id_formaggyo, :weight)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(':weight', $weight, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    // Elimina un ordine di fornitura dal database tramite ID
    public function deleteSupply($id_supply)
    {
        $sql = "UPDATE supply
            set status = 3
            where id = :id_supply";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_supply', $id_supply, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    } 
    
    // Modifica lo stato di un ordine di fornitura nel database tramite ID
    public function modifyOrderStatusSupply($status, $id_supply)
    {
        $sql = "UPDATE supply
             SET status = :status
             WHERE id = :id_supply";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':id_supply', $id_supply, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
?>
