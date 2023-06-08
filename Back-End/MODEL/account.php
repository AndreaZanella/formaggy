<?php

// Registra la funzione di autoload, che viene chiamata quando viene utilizzata una classe non definita nel codice corrente.
spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

// Imposta la funzione per gestire le eccezioni non catturate.
set_exception_handler("errorHandler::handleException");

// Imposta la funzione per gestire gli errori.
set_error_handler("errorHandler::handleError");

// Definizione della classe Account
class Account
{
    private Connect $db;
    private PDO $conn;

    public function __construct()
    {
        // Crea un'istanza della classe Connect per connettersi al database.
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }

    // Ottiene le informazioni di un account dato un ID.
    public function getAccount($id)
    {
        $sql = "SELECT id, email, username, secret, permits
                FROM account a 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); 

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Ottiene tutte le informazioni degli account presenti nell'archivio.
    public function getArchiveAccount()
    {
        $sql = "SELECT id, email, username, secret, permits
                FROM account a
                WHERE 1 = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Funzione per effettuare il login di un utente.
    public function login($email, $password)
    {
        $sql = "SELECT a.id, a.permits
        FROM account a 
        WHERE a.email = :email and a.secret = :password";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Aggiunge un nuovo account al database.
    public function addAccount($email, $username, $secret, $permits)
    {
        $sql = "SELECT a.id
        FROM account a
        WHERE a.email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->execute();
        $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stmt->rowCount() == 0)
        {
            $sql = "INSERT INTO account 
            (email, username, secret, permits)
            VALUES (:email, :username, :secret, :permits)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':secret', $secret, PDO::PARAM_STR);
            $stmt->bindValue(':permits', $permits, PDO::PARAM_INT);
            
            $stmt->execute();
            return ["message" => "Utente creato con successo"];
        }
        else
        {
            return ["message" => "Utente già esistente"];
        }
    }
    
    // Modifica la password di un account dato l'ID dell'account.
    public function modifyPassword($id_account, $new_password)
    {
        $sql = "UPDATE account  
                SET secret = :new_password
                WHERE id = :id_account";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
        $stmt->bindValue(':new_password', $new_password, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount();
    }
    
    // Modifica l'email e il nome utente di un account dato l'ID dell'account.
    public function modifyAccount($id_account, $email, $username)
    {
        $sql = "UPDATE account
                SET email = :email
                WHERE id = :id_account";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $cnt = $stmt->rowCount();

        $sql = "UPDATE account
                SET username = :username
                WHERE id = :id_account";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $cnt += $stmt->rowCount();

        return $cnt;
    }
    
    // Elimina un account dato l'ID dell'account.
    public function deleteAccount($id_account)
    {
        $sql = "UPDATE account
                SET permits = -1 
                WHERE id = :id_account";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1)
            return 1;
        else 
            return 0;
    }
    
    // Attiva o disattiva un utente impostando il valore dei permessi.
    public function ActivateUser($id_account, $permits)
    {
        $sql = "UPDATE account
                SET permits = :permits 
                WHERE id = :id_account";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
        $stmt->bindValue(':permits', $permits, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1)
            return 1;
        else 
            return 0;   
    }
}
?>
