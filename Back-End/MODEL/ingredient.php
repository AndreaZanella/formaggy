<?php
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Ingredient
{
    private PDO $conn;
    private Connect $db;

    public function __construct() //Si connette al DB.
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }

    public function getArchiveIngredient() //Ritorna tutti gli ingredienti.
    {
        $query = "SELECT id, name, description
        from ingredient i";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
        public function getIngredient($id) //Ritorna tutti gli ingredienti.
    {
        $query = "SELECT id, name, description
        from ingredient i
        where id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id",$id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
