<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class User
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }

    public function getWarehouse($id) //ritorna il magazzino richiesto ricevendo in input l'id dell'magazzino stesso
    {
        $sql = "SELECT w.id, f.name, fw.weight
        from warehouse w
        inner join formaggyo_warehouse fw on fw.id_warehouse = w.id
        inner join formaggyo f on fw.id_formaggyo = f.id
        where w.id = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getArchiveWarehouse() //Ritorna tutti i magazzini.
    {
        $query = "SELECT id, address
        from warehouse w";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
