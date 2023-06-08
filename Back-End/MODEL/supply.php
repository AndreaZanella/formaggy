<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Supply
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }
    
    public function getArchiveSupply() //Ritorna tutti gli ordini
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

    public function getSupply($id) //Ritorna l'ordine richiesto fatto al fornitore ricevendo in input l'id
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
    
    public function getSupplyFormaggy($id) //Ritorna i prodotti di un ordine
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
        public function addSupply($id_account,$id_dairy,$id_formaggyo,$total_price,$status,$weight)
    {
            $sql = "INSERT into supply (id_account, id_dairy, date_supply, total_price,status)
                values (:id_account,:id_dairy,now(),:total_price,:status);";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
            $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT);
            $stmt->bindValue(':total_price', $total_price, PDO::PARAM_INT);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->execute();


        $sql="INSERT into supply_formaggyo (id_supply,id_formaggyo,weight)
              values((select s.id
                from supply s
                order by s.id desc 
                limit 1),:id_formaggyo,:weight)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
            $stmt->bindValue(':weight', $weight, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->rowCount();
        }

    public function deleteSupply($id_supply)
    {
        $sql="UPDATE supply
            set status = 3
            where id=:id_supply";
         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(':id_supply', $id_supply, PDO::PARAM_INT);         
         $stmt->execute();
         return $stmt->rowCount();
         
    } 
    
     public function modifyOrderStatusSupply($status,$id_supply)
    {
        $sql="UPDATE supply
             SET status = :status
             WHERE id=:id_supply";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':status', $status , PDO::PARAM_INT);
        $stmt->bindValue(':id_supply', $id_supply, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
?>
