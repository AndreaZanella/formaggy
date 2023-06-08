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
        $query = "SELECT id, name, description, image
        from ingredient i
        ORDER BY id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
        public function getIngredient($id) //Ritorna tutti gli ingredienti.
    {
        $query = "SELECT id, name, description, image
        from ingredient i
        where id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id",$id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        public function addIngredient($name, $description, $image)
    {
        $sql = "SELECT i.id
        FROM ingredient i
        WHERE i.name = :name AND i.description=:description";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->execute();


       if($stmt->rowCount()==0)
       {
        $sql = "INSERT  into ingredient (name, description, image)
        values (:name,:description,:image)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':image', $image, PDO::PARAM_STR);
        $stmt->execute();
        return ["message" => "Ingrediente creato con successo"];

       } else 
       {
        return ["message" => "Ingrediente già esistente"];
       } 
    
    }

    public function modifyIngredient($id_ingredient, $name, $description, $image)
    {
        $sql="UPDATE ingredient
        SET description = :description
        WHERE id=:id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();
        $cnt=$stmt->rowCount();

        $sql="UPDATE ingredient
        SET name = :name 
        WHERE id=:id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name , PDO::PARAM_STR);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();
        $cnt+=$stmt->rowCount();
        
        $sql="UPDATE ingredient
        SET image = :image
        WHERE id=:id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':image', $image, PDO::PARAM_STR);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();
        $cnt+=$stmt->rowCount();

        return $cnt;
    }

    public function deleteIngredient($id_ingredient)
    {
        $sql="DELETE from formaggyo_ingredient where id_ingredient =:id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();

        $sql="DELETE from ingredient where id =:id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();

    }
}
?>
