<?php
// Registra la funzione di caricamento automatico delle classi
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

// Imposta la gestione delle eccezioni personalizzata
set_exception_handler("errorHandler::handleException");
// Imposta la gestione degli errori personalizzata
set_error_handler("errorHandler::handleError");

class Ingredient
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        // Crea un'istanza di Connect per stabilire la connessione al database
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }

    // Ottiene tutti gli ingredienti dall'archivio
    public function getArchiveIngredient()
    {
        $query = "SELECT id, name, description, image
        FROM ingredient i
        ORDER BY id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ottiene un ingrediente specifico
    public function getIngredient($id)
    {
        $query = "SELECT id, name, description, image
        FROM ingredient i
        WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Aggiunge un nuovo ingrediente
    public function addIngredient($name, $description, $image)
    {
        // Verifica se l'ingrediente esiste già
        $sql = "SELECT i.id
        FROM ingredient i
        WHERE i.name = :name AND i.description = :description";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Inserisce l'ingrediente nel database
            $sql = "INSERT INTO ingredient (name, description, image)
            VALUES (:name, :description, :image)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':image', $image, PDO::PARAM_STR);
            $stmt->execute();

            return ["message" => "Ingrediente creato con successo"];
        } else {
            return ["message" => "Ingrediente già esistente"];
        }
    }

    // Modifica un ingrediente esistente
    public function modifyIngredient($id_ingredient, $name, $description, $image)
    {
        // Aggiorna la descrizione dell'ingrediente
        $sql = "UPDATE ingredient
        SET description = :description
        WHERE id = :id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();
        $cnt = $stmt->rowCount();

        // Aggiorna il nome dell'ingrediente
        $sql = "UPDATE ingredient
        SET name = :name 
        WHERE id = :id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name , PDO::PARAM_STR);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();
        $cnt += $stmt->rowCount();
        
        // Aggiorna l'immagine dell'ingrediente
        $sql = "UPDATE ingredient
        SET image = :image
        WHERE id = :id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':image', $image, PDO::PARAM_STR);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();
        $cnt += $stmt->rowCount();

        return $cnt;
    }

    // Elimina un ingrediente
    public function deleteIngredient($id_ingredient)
    {
        // Rimuove le associazioni tra l'ingrediente e i formaggi
        $sql = "DELETE FROM formaggyo_ingredient WHERE id_ingredient = :id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();

        // Elimina l'ingrediente dal database
        $sql = "DELETE FROM ingredient WHERE id = :id_ingredient";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_ingredient', $id_ingredient, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
?>
