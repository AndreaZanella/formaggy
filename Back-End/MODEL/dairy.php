<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Dairy
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }


    public function getArchiveDairy() //Ritorna tutti i fornitori.
    {
        $query = "SELECT id, name, address, telephon_number, email, website, image
        from dairy d
        where id > 0
        order by id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDairy($id)
    {
        $query = "SELECT id, name, address, telephon_number, email, website, image
        FROM dairy
        WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        public function addDairy($name,$address,$telephon_number,$email,$website,$image)
    {
        $sql="SELECT d.name, d.address, d.email
        FROM dairy d
        WHERE d.name=:name AND d.address=:address AND d.email=:email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->rowCount() == 0)
        {
            $sql = "INSERT into dairy (name, address, telephon_number, email, website, image)
            values(:name,:address,:telephon_number,:email,:website,:image)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':telephon_number', $telephon_number, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':website', $website, PDO::PARAM_STR);
        $stmt->bindValue(':image', $image, PDO::PARAM_STR);
        $stmt->execute();

        return ["message" => "Dairy creato con successo"];

        } 
        else
            return ["message" => "Dairy già esistente"];

    }

    public function modifyDairy($id_dairy,$name,$address,$telephon_number,$email,$website,$image)
    {
        $sql="UPDATE dairy
              SET name = :name
              where id=:id_dairy";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();
        $cnt=$stmt->rowCount();

        $sql="UPDATE dairy
        SET website = :website
        where id=:id_dairy";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':website', $website, PDO::PARAM_STR);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();
        $cnt+=$stmt->rowCount();


        $sql="UPDATE dairy
        SET address = :address
        where id=:id_dairy";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();
        $cnt+=$stmt->rowCount();


        $sql="UPDATE dairy
        SET telephon_number = :telephon_number 
        where id=:id_dairy";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':telephon_number', $telephon_number , PDO::PARAM_STR);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();
        $cnt+=$stmt->rowCount();


        $sql="UPDATE dairy
        SET email  = :email 
        where id=:id_dairy";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email , PDO::PARAM_STR);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();
        $cnt+=$stmt->rowCount();
        
        $sql="UPDATE dairy
        SET image = :image
        where id=:id_dairy";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':image', $image, PDO::PARAM_STR);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();
        $cnt+=$stmt->rowCount();

        return $cnt;

    }

    public function deleteDairy($id_dairy)
    {
        /*$sql="UPDATE supply_formaggyo SET id_dairy = -:id_dairy WHERE id_supply=(
        SELECT supply_formaggyo.id_supply
        FROM supply
        INNER JOIN supply_formaggyo ON supply_formaggyo.id_supply =supply.id
        WHERE supply.id_dairy = :id_dairy)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();*/

        $sql="UPDATE supply SET id_dairy = -:id_dairy WHERE id_dairy =:id_dairy";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();

        $sql="UPDATE dairy SET id = -:id_dairy WHERE id=:id_dairy";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();

        $sql="SELECT dairy.id
        FROM dairy
        WHERE dairy.id=:id_dairy";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() == 0)
        return 1;
        else 
        return 0;
    }
}
?>
