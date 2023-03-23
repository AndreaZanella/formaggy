<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class User
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }

    public function getFormaggy($id) //ritorna il formaggio richiesto ricevendo in input l'id dell'formaggio stesso
    {
        $sql = "SELECT f.id, f.name, f.description, c.name, c2.acronym, s.weight, f.price_kg, f.color, f.smell, f.taste, f.expiry_date, f.kcal, f.fats, f.satured_fats, f.carbohydrates, f.sugars, f.proteins, f.fibers, f.salts
        from formaggyo f
        inner join category c on c.id = f.id_category
        inner join certification c2 on c2.id = f.id_certification
        inner join formaggyo_size fs ON fs.id_formaggyo = f.id
        inner join `size` s on s.id = fs.id_size
        where f.id = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getArchiveFormaggy() //Ritorna tutti i formaggi.
    {
        $query = "SELECT f.id, f.name, f.description, c.name, c2.acronym, s.weight, f.price_kg, f.color, f.smell, f.taste, f.expiry_date, f.kcal, f.fats, f.satured_fats, f.carbohydrates, f.sugars, f.proteins, f.fibers, f.salts
        from formaggyo f
        inner join category c on c.id = f.id_category
        inner join certification c2 on c2.id = f.id_certification
        inner join formaggyo_size fs ON fs.id_formaggyo = f.id
        inner join `size` s on s.id = fs.id_size
        order by f.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
