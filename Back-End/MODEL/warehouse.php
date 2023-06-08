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
    
    /**
     * Ritorna tutti i magazzini presenti nel database.
     * @return array Array associativo contenente gli id e gli indirizzi dei magazzini.
     */
    public function getArchiveWarehouse()
    {
        $query = "SELECT id, address
        FROM warehouse w";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ritorna le informazioni di un magazzino specificato tramite ID.
     * @param int $id L'ID del magazzino.
     * @return array Array associativo contenente l'ID e l'indirizzo del magazzino.
     */
    public function getWarehouse($id)
    {
        $sql = "SELECT id, address
        FROM warehouse w
        WHERE w.id = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ritorna i formaggi presenti in un magazzino specificato tramite ID.
     * @param int $id L'ID del magazzino.
     * @return array Array associativo contenente l'ID, il nome e il peso dei formaggi presenti nel magazzino.
     */
    public function getWarehouseFormaggy($id)
    {
        $query = "SELECT f.id, f.name, fw.weight 
        FROM warehouse w 
        INNER JOIN formaggyo_warehouse fw ON fw.id_warehouse = w.id
        INNER JOIN formaggyo f ON fw.id_formaggyo = f.id 
        WHERE w.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
       
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Aggiunge un nuovo magazzino al database.
     * @param string $address L'indirizzo del magazzino da aggiungere.
     * @return array Array contenente un messaggio che indica se il magazzino è stato creato con successo o se esiste già.
     */
    public function addWarehouse($address)
    {
        $sql = "SELECT w.id
        FROM warehouse w
        WHERE w.address=:address";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $sql = "INSERT INTO warehouse (address)
            VALUES (:address)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':address', $address, PDO::PARAM_STR);
            $stmt->execute();
            return ["message" => "Warehouse creato con successo"];
        } else {
            return ["message" => "Warehouse già esistente"];
        }
    }

    /**
     * Aggiunge un formaggio specifico a un magazzino specifico nel database.
     * @param int $id_formaggyo L'ID del formaggio da aggiungere.
     * @param int $id_warehouse L'ID del magazzino in cui aggiungere il formaggio.
     * @param int $weight Il peso del formaggio da aggiungere.
     * @return int Il numero di righe inserite nel database (1 se l'inserimento è riuscito, altrimenti 0).
     */
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
            $sql = "INSERT INTO formaggyo_warehouse (id_formaggyo, id_warehouse,weight)
                VALUES (:id_formaggyo,:id_warehouse,:weight)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
            $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
            $stmt->bindValue(':weight', $weight, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount();
        } else
            return 0;
    }
    
    /**
     * Aggiunge più formaggi a un magazzino specifico nel database.
     * @param array $id_formaggyo Un array contenente gli ID dei formaggi da aggiungere.
     * @param int $id_warehouse L'ID del magazzino in cui aggiungere i formaggi.
     * @param array $weight Un array contenente i pesi dei formaggi corrispondenti.
     * @return array Array contenente un messaggio che indica se i formaggi sono stati aggiunti con successo o se sono già presenti.
     */
    public function addFormaggyoWarehouse($id_formaggyo, $id_warehouse, $weight)
    {
        if (is_array($id_formaggyo)) {
            $cnt = 0;
            for ($i = 0; $i < count($id_formaggyo); $i++)
                $cnt += $this->addSingleFormaggyo($id_formaggyo[$i], $id_warehouse, $weight[$i]);
            if ($cnt > 0)
                return ["message" => "Formaggi nella warehouse aggiunti con successo"];
            else
                return ["message" => "Tutti i formaggi selezionati sono già presenti nella warehouse"];
        } else {
            if ($this->addSingleFormaggyo($id_formaggyo, $id_warehouse, $weight) == 1)
                return ["message" => "Formaggio nella warehouse aggiunto con successo"];
            else
                return ["message" => "Formaggio selezionato già presente nella warehouse"];
        }
    }

    /**
     * Modifica l'indirizzo di un magazzino nel database.
     * @param int $id_warehouse L'ID del magazzino da modificare.
     * @param string $newAddressWarehouse Il nuovo indirizzo del magazzino.
     * @return int Il numero di righe modificate nel database.
     */
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

    /**
     * Elimina un magazzino dal database.
     * @param int $id_warehouse L'ID del magazzino da eliminare.
     * @return int Il numero di righe eliminate nel database.
     */
    public function deleteWarehouse($id_warehouse)
    {
        $sql = "DELETE FROM formaggyo_warehouse WHERE id_warehouse =:id_warehouse";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "DELETE FROM warehouse WHERE id =:id_warehouse";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Elimina un formaggio da un magazzino nel database.
     * @param int|array $id_formaggyo L'ID o un array di ID dei formaggi da eliminare.
     * @param int $id_warehouse L'ID del magazzino da cui eliminare i formaggi.
     * @return int Il numero di righe eliminate nel database.
     */
    public function deleteFormaggyWarehouse($id_formaggyo,$id_warehouse)
    {

        if(is_array($id_formaggyo))
        {
            $cnt=0;
            for ($i = 0; $i < count($id_formaggyo); $i++)
            {
                $sql = "DELETE FROM formaggyo_warehouse WHERE id_formaggyo=:id_formaggyo AND id_warehouse =:id_warehouse";

                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id_formaggyo', $id_formaggyo[$i], PDO::PARAM_INT);
                $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
                $stmt->execute();
                $cnt+=$stmt->rowCount();
            }
            return $cnt;
        } else {
            $sql = "DELETE FROM formaggyo_warehouse WHERE id_formaggyo=:id_formaggyo AND id_warehouse =:id_warehouse";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
            $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        }
        

    }

    /**
     * Modifica il peso di un formaggio in un magazzino nel database.
     * @param int $id_formaggyo L'ID del formaggio da modificare.
     * @param int $id_warehouse L'ID del magazzino in cui modificare il peso.
     * @param int $newWeight Il nuovo peso del formaggio.
     * @return int Il numero di righe modificate nel database.
     */
    public function modifyFormaggyoWarehouse($id_formaggyo,$id_warehouse,$newWeight)
    {
        $sql = "UPDATE formaggyo_warehouse
        SET weight = :newWeight
        WHERE id_formaggyo=:id_formaggyo AND id_warehouse=:id_warehouse";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':newWeight', $newWeight, PDO::PARAM_INT);
        $stmt->bindValue(':id_formaggyo', $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(':id_warehouse', $id_warehouse, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}

?>
