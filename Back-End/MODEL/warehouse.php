<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Warehouse
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }
    
   public function getArchiveWarehouse() //Ritorna tutti i magazzini.
    {
        $query = "SELECT id, address
        from warehouse w";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   public function getWarehouse($id) //ritorna il magazzino richiesto ricevendo in input l'id dell'magazzino stesso
    {
        $sql = "SELECT id, address
        from warehouse w
        where w.id = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
   public function getWarehouseFormaggy($id) //Ritorna tutti i formaggi dentro un magazzino.
    {
        $query = "SELECT f.id, f.name, fw.weight 
                    from warehouse w 
                    inner join formaggyo_warehouse fw on fw.id_warehouse = w.id
                    inner join formaggyo f on fw.id_formaggyo = f.id 
                    where w.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
       
        $stmt->execute();

        return $stmt->fetchall(PDO::FETCH_ASSOC);
    }
    
    public function getFormaggyWarehouse($id_formaggyo)
    {
        $query = "SELECT fw.id_warehouse
        from formaggyo_warehouse fw 
        where fw.id_formaggyo = :id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
       
        $stmt->execute();

        $results = $stmt->fetchall(PDO::FETCH_ASSOC);
        $warehouses = array();

        foreach ($results as $result) {
            $warehouses[] = intval($result['id_warehouse']);
        }
    
        return $warehouses[0];
    }

    //peso totale formaggy e controllo con peso utente se va a buon fine richiamo l'update
    public function modifyFormaggyWeight($id_formaggyo, $id_warehouse, $weight)
    {

            $sql = "SELECT SUM(fw.weight) AS total_weight
                    FROM formaggyo_warehouse fw 
                    WHERE fw.id_formaggyo = :id_formaggyo";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_weight = $result['total_weight'];

                if ($weight > 0 && $total_weight >= $weight) {
                
                    $sqlmodify = "UPDATE formaggyo_warehouse fw 
                                  SET fw.weight = fw.weight - :weight
                                  WHERE fw.id_formaggyo = :id_formaggyo AND fw.id_warehouse = :id_warehouse";
    
                    $stmt = $this->conn->prepare($sqlmodify);
                    $stmt->bindValue(':weight', $weight, PDO::PARAM_STR);
                    $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
                    $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
                    $stmt->execute();
                    $rowCount = $stmt->rowCount();
                    return $rowCount;
                    
                } else {
                    return ["message" => "Il peso richiesto è superiore alle quantità disponibili"];
                }
    }
        public function addWarehouse($address)
    {
        $sql = "SELECT w.id
        FROM warehouse w
        WHERE w.address=:address";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $sql = "INSERT into warehouse (address)
            values(:address)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':address', $address, PDO::PARAM_STR);
            $stmt->execute();
            return ["message" => "Warehouse creato con successo"];
        } else {
            return ["message" => "Warehouse già esistente"];
        }
    }

    public function addMultipleFormaggyo($id_formaggyo, $id_warehouse, $weight)
    {
        $sql = "SELECT fw.id_formaggyo, fw.id_warehouse
        FROM formaggyo_warehouse fw
        WHERE fw.id_formaggyo=:id_formaggyo AND fw.id_warehouse=:id_warehouse";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_STR);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $sql = "INSERT into formaggyo_warehouse (id_formaggyo, id_warehouse,weight)
                values(:id_formaggyo,:id_warehouse,:weight)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
            $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
            $stmt->bindValue(':weight', $weight, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount();
        } else
            return 0;
    }
    public function addSingleFormaggyo($id_formaggyo, $id_warehouse, $weight)
    {
        $sql = "SELECT fw.id_formaggyo, fw.id_warehouse
        FROM formaggyo_warehouse fw
        WHERE fw.id_formaggyo=:id_formaggyo AND fw.id_warehouse=:id_warehouse";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_STR);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $sql = "INSERT into formaggyo_warehouse (id_formaggyo, id_warehouse,weight)
                values(:id_formaggyo,:id_warehouse,:weight)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
            $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
            $stmt->bindValue(':weight', $weight, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount();
        } else
            return 0;
    }
    public function addFormaggyotWarehouse($id_formaggyo, $id_warehouse, $weight)
    {
        if (is_array($id_formaggyo)) {
            $cnt = 0;
            for ($i = 0; $i < count($id_formaggyo); $i++)
                $cnt += $this->addMultipleFormaggyo($id_formaggyo[$i], $id_warehouse, $weight[$i]);
            if ($cnt > 0)
                return ["message" => "formaggyo nella warehouse aggiunto con successo"];
            else
                return ["message" => "tutti i formaggi selezionati sono già presenti nella warehouse"];
        } else {
            if ($this->addSingleFormaggyo($id_formaggyo, $id_warehouse, $weight) == 1)
                return ["message" => "formaggyo nella warehouse aggiunto con successo"];
            else
                return ["message" => "Formaggyo selezionati sono già presenti nella warehouse"];
        }
    }


    public function modifyWarehouse($id_warehouse, $newAddressWarehouse)
    {
        $sql = "UPDATE warehouse
        SET address = :newAddressWarehouse
        WHERE id=:id_warehouse";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':newAddressWarehouse', $newAddressWarehouse, PDO::PARAM_STR);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function deleteWarehouse($id_warehouse)
    {
        $sql = "DELETE from formaggyo_warehouse where id_warehouse =:id_warehouse";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "DELETE from warehouse  where id =:id_warehouse";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
?>
