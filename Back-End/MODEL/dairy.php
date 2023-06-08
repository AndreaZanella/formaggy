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

    public function getArchiveDairy() // Ottiene tutti i fornitori dall'archivio
    {
        $query = "SELECT id, name, address, telephon_number, email, website, image
                  FROM dairy d
                  WHERE id > 0
                  ORDER BY id"; // Query per selezionare tutti i fornitori ordinati per ID

        $stmt = $this->conn->prepare($query); // Prepara la query
        $stmt->execute(); // Esegue la query

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Restituisce i risultati come un array associativo
    }
    
    public function getDairy($id) // Ottiene i dettagli di un fornitore specifico
    {
        $query = "SELECT id, name, address, telephon_number, email, website, image
                  FROM dairy
                  WHERE id = :id"; // Query per selezionare un fornitore per ID

        $stmt = $this->conn->prepare($query); // Prepara la query
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query

        return $stmt->fetch(PDO::FETCH_ASSOC); // Restituisce i risultati come un array associativo
    }
    
    public function addDairy($name, $address, $telephon_number, $email, $website, $image) // Aggiunge un nuovo fornitore
    {
        $sql = "SELECT d.name, d.address, d.email
                FROM dairy d
                WHERE d.name = :name AND d.address = :address AND d.email = :email"; // Query per verificare l'esistenza di un fornitore

        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':name', $name, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->bindValue(':address', $address, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->bindValue(':email', $email, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query

        if ($stmt->rowCount() == 0) // Controlla se il fornitore esiste già
        {
            $sql = "INSERT INTO dairy (name, address, telephon_number, email, website, image)
                    VALUES (:name, :address, :telephon_number, :email, :website, :image)"; // Query per aggiungere un nuovo fornitore

            $stmt = $this->conn->prepare($sql); // Prepara la query
            $stmt->bindValue(':name', $name, PDO::PARAM_STR); // Assegna il valore del parametro
            $stmt->bindValue(':address', $address, PDO::PARAM_STR); // Assegna il valore del parametro
            $stmt->bindValue(':telephon_number', $telephon_number, PDO::PARAM_STR); // Assegna il valore del parametro
            $stmt->bindValue(':email', $email, PDO::PARAM_STR); // Assegna il valore del parametro
            $stmt->bindValue(':website', $website, PDO::PARAM_STR); // Assegna il valore del parametro
            $stmt->bindValue(':image', $image, PDO::PARAM_STR); // Assegna il valore del parametro
            $stmt->execute(); // Esegue la query

            return ["message" => "Dairy creato con successo"]; // Restituisce un messaggio di successo
        } else {
            return ["message" => "Dairy già esistente"]; // Restituisce un messaggio di errore
        }
    }

    public function modifyDairy($id_dairy, $name, $address, $telephon_number, $email, $website, $image) // Modifica un fornitore esistente
    {
        $sql = "UPDATE dairy
                SET name = :name
                WHERE id = :id_dairy"; // Query per modificare il nome di un fornitore

        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->bindValue(':name', $name, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query
        $cnt = $stmt->rowCount(); // Ottiene il numero di righe modificate

        $sql = "UPDATE dairy
                SET website = :website
                WHERE id = :id_dairy"; // Query per modificare il sito web di un fornitore

        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':website', $website, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query
        $cnt += $stmt->rowCount(); // Aggiorna il numero di righe modificate

        $sql = "UPDATE dairy
                SET address = :address
                WHERE id = :id_dairy"; // Query per modificare l'indirizzo di un fornitore

        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':address', $address, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query
        $cnt += $stmt->rowCount(); // Aggiorna il numero di righe modificate

        $sql = "UPDATE dairy
                SET telephon_number = :telephon_number
                WHERE id = :id_dairy"; // Query per modificare il numero di telefono di un fornitore

        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':telephon_number', $telephon_number, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query
        $cnt += $stmt->rowCount(); // Aggiorna il numero di righe modificate

        $sql = "UPDATE dairy
                SET email = :email
                WHERE id = :id_dairy"; // Query per modificare l'email di un fornitore

        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':email', $email, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query
        $cnt += $stmt->rowCount(); // Aggiorna il numero di righe modificate
        
        $sql = "UPDATE dairy
                SET image = :image
                WHERE id = :id_dairy"; // Query per modificare l'immagine di un fornitore

        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':image', $image, PDO::PARAM_STR); // Assegna il valore del parametro
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query
        $cnt += $stmt->rowCount(); // Aggiorna il numero di righe modificate

        return $cnt; // Restituisce il numero totale di righe modificate
    }

    public function deleteDairy($id_dairy) // Elimina un fornitore
    {
        $sql = "UPDATE supply SET id_dairy = -:id_dairy WHERE id_dairy = :id_dairy"; // Query per aggiornare l'ID del fornitore nelle forniture
        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query

        $sql = "UPDATE dairy SET id = -:id_dairy WHERE id = :id_dairy"; // Query per aggiornare l'ID del fornitore
        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query

        $sql = "SELECT dairy.id
                FROM dairy
                WHERE dairy.id = :id_dairy"; // Query per verificare se il fornitore è stato eliminato correttamente
        $stmt = $this->conn->prepare($sql); // Prepara la query
        $stmt->bindValue(':id_dairy', $id_dairy, PDO::PARAM_INT); // Assegna il valore del parametro
        $stmt->execute(); // Esegue la query

        if ($stmt->rowCount() == 0) {
            return 1; // Restituisce 1 se il fornitore è stato eliminato correttamente
        } else {
            return 0; // Restituisce 0 se il fornitore non è stato eliminato correttamente
        }
    }
}
?>
