<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Account
{
    private Connect $db;
    private PDO $conn;

    public function __construct()
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }

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
    
    public function getArchiveAccount()
    {
        $sql = "SELECT id, email, username, secret, permits
                FROM account a
                WHERE 1 = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        public function login($email, $password)
    {
        $sql = "SELECT a.id, a.permits
        FROM account a 
        WHERE a.email =:email and a.secret =:password";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        public function addAccount($email, $username, $secret, $permits)
{
    // Controllo se ci sono già altri utenti con la stessa mail
    $sql = "SELECT a.id
    FROM account a
    WHERE a.email = :email";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);

    $stmt->execute();

    // Creo una variabile per contenere l'id dell'utente creato
    $stmt->fetch(PDO::FETCH_ASSOC);
    

    if ($stmt->rowCount() == 0)
    {
        // Aggiungo l'utente nella tabella user
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
public function modifyPassword($id_account,$new_password)
{
    $sql=" UPDATE account  
           SET secret = :new_password
           WHERE id=:id_account";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
    $stmt->bindValue(':new_password', $new_password, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount();

}

public function modifyAccount($id_account,$email,$username)
{
    $sql="UPDATE account
    SET email =:email
    WHERE id=:id_account";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $cnt=$stmt->rowCount();


    $sql="UPDATE account
    SET username =:username
    WHERE id=:id_account";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $cnt+=$stmt->rowCount();

    return $cnt;
}

public function deleteAccount($id_account)
{

    $sql="UPDATE account
    SET permits =-1 
    WHERE id=:id_account";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id_account', $id_account, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1)
    return 1;
    else 
    return 0;
}

public function ActivateUser($id_account,$permits)
{
    $sql="UPDATE account
    SET permits =:permits 
    WHERE id=:id_account";
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
