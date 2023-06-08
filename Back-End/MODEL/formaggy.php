<?php

spl_autoload_register(function ($class) {
    require __DIR__ . "/../COMMON/$class.php";
});

set_exception_handler("errorHandler::handleException");
set_error_handler("errorHandler::handleError");

class Formaggy
{
    private PDO $conn;
    private Connect $db;

    public function __construct()
    {
        $this->db = new Connect;
        $this->conn = $this->db->getConnection();
    }
    
    public function getArchiveFormaggy() //Ritorna tutti i formaggi.
    {
        $query = "SELECT f.id, f.name, f.description, f.price_kg, c.name as category, c2.acronym as certification, f.color, f.smell, f.taste, f.expiry_date, f.kcal, f.fats, f.satured_fats, f.carbohydrates, f.sugars, f.proteins, f.fibers, f.salts, f.image
        from formaggyo f
        inner join category c on c.id = f.id_category
        inner join certification c2 on c2.id = f.id_certification
        where f.id > 0
        order by f.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFormaggy($id) //ritorna il formaggio richiesto ricevendo in input l'id dell'formaggio stesso
    {
        $sql = "SELECT f.id, f.name, f.description, f.price_kg, c.name as category, c2.acronym as certification, f.color, f.smell, f.taste, f.expiry_date, f.kcal, f.fats, f.satured_fats, f.carbohydrates, f.sugars, f.proteins, f.fibers, f.salts, f.image
        from formaggyo f
        inner join category c on c.id = f.id_category
        inner join certification c2 on c2.id = f.id_certification
        where f.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getFormaggyIngredients($id) //Ritorna gli ingredienti del formaggio
    {
        $sql = "SELECT i.id, i.name, i.description 
        FROM formaggyo f
        INNER JOIN formaggyo_ingredient fi ON f.id = fi.id_formaggyo
        INNER JOIN ingredient i ON fi.id_ingredient = i.id
        WHERE f.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getFormaggySizes($id) //ritorna le taglie del formaggio richiesto
    {
        $sql = "SELECT s.id, s.weight 
                from formaggyo f 
                inner join formaggyo_size fs on fs.id_formaggyo =f.id 
                inner join `size` s on s.id =fs.id_size 
                where f.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchall(PDO::FETCH_ASSOC);
    }
        public function createFormaggyo($name,$description,$price_kg,$id_category,$id_certification,$color,$smell,$taste,$expiry_date,$kcal,$fats,$satured_fats,$carbohydrates,$sugars,$proteins,$fibers,$salts,$id_ingredient,$id_size,$image) //Inserisce un nuovo prodotto.
    {
        $query = "INSERT INTO formaggyo (name,description,price_kg,id_category,id_certification,color,smell,taste,expiry_date,kcal,fats,satured_fats,carbohydrates,sugars,proteins,fibers,salts,image)
         VALUES(:name,:description,:price_kg,:id_category,:id_certification,:color,:smell,:taste,:expiry_date,:kcal,:fats,:satured_fats,:carbohydrates,:sugars,:proteins,:fibers,:salts,:image);";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":name",$name,PDO::PARAM_STR);
        $stmt->bindValue(":description",$description,PDO::PARAM_STR);
        $stmt->bindValue(":price_kg",$price_kg,PDO::PARAM_STR);
        $stmt->bindValue(":id_category",$id_category,PDO::PARAM_INT);
        $stmt->bindValue(":id_certification",$id_certification,PDO::PARAM_INT);
        $stmt->bindValue(":color",$color,PDO::PARAM_STR);
        $stmt->bindValue(":smell",$smell,PDO::PARAM_STR);
        $stmt->bindValue(":taste",$taste,PDO::PARAM_STR);
        $stmt->bindValue(":expiry_date",$expiry_date,PDO::PARAM_STR);
        $stmt->bindValue(":kcal",$kcal,PDO::PARAM_INT);
        $stmt->bindValue(":fats",$fats,PDO::PARAM_STR);
        $stmt->bindValue(":satured_fats",$satured_fats,PDO::PARAM_STR);
        $stmt->bindValue(":carbohydrates",$carbohydrates,PDO::PARAM_STR);
        $stmt->bindValue(":sugars",$sugars,PDO::PARAM_STR);
        $stmt->bindValue(":proteins",$proteins,PDO::PARAM_STR);
        $stmt->bindValue(":fibers",$fibers,PDO::PARAM_STR);
        $stmt->bindValue(":salts",$salts,PDO::PARAM_STR);
        $stmt->bindValue(":image",$image,PDO::PARAM_STR);

        $stmt->execute();
        for($i=0;$i<(count($id_ingredient));$i++)
        {
           $this->addIngredientId($id_ingredient[$i]);
        }

        for($i=0;$i<(count($id_size));$i++)
        {
           $this->addSizeId($id_size[$i]);
        }

        // Query sul prodotto appena creato
        $query = "SELECT f.id
                  FROM formaggyo f
                  order by f.id desc 
                  limit 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function addIngredientId($id_ingredient)
    {
            $query = "INSERT INTO formaggyo_ingredient (id_formaggyo, id_ingredient)
            values ((select f.id
            from formaggyo f
            order by f.id desc 
            limit 1),:id_ingredient)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id_ingredient", $id_ingredient, PDO::PARAM_INT);    
            $stmt->execute();
    }
    
        public function addSizeId($id_size)
    {
            $query = "INSERT INTO formaggyo_size(id_formaggyo,id_size)
                VALUES ((select f.id
            from formaggyo f
            order by f.id desc 
            limit 1),:id_size)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id_size", $id_size, PDO::PARAM_INT);    
            $stmt->execute();
    }

    public function modifyFormaggyo($id_formaggyo,$name,$description,$price_kg,$id_category,$id_certification,$color,$smell,$taste,$expiry,$kcal,$fats,$satured_fats,$carbohydrates,$sugars,$proteins,$fibers,$salts,$image)
    {
        $query = "UPDATE formaggyo 
        set name= :name
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set description= :description
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":description", $description, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set price_kg= :price_kg
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":price_kg", $price_kg, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set id_category= :id_category
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":id_category", $id_category, PDO::PARAM_INT);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set id_certification= :id_certification
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":id_certification", $id_certification, PDO::PARAM_INT);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set color= :color
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":color", $color, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set smell= :smell
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":smell", $smell, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set taste= :taste
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":taste", $taste, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set expiry_date= :expiry
        where id=:id_formaggyo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":expiry", $expiry, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set kcal= :kcal
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":kcal", $kcal, PDO::PARAM_INT);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set fats= :fats
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":fats", $fats, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set satured_fats= :satured_fats
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":satured_fats", $satured_fats, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set carbohydrates= :carbohydrates
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":carbohydrates", $carbohydrates, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set sugars= :sugars
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":sugars", $sugars, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set proteins= :proteins
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":proteins", $proteins, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set fibers= :fibers
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":fibers", $fibers, PDO::PARAM_STR);
        $stmt->execute();

        $query = "UPDATE formaggyo 
        set salts= :salts
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":salts", $salts, PDO::PARAM_STR);
        $stmt->execute();
        
        $query = "UPDATE formaggyo 
        set image= :image
        where id=:id_formaggyo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->bindValue(":image", $image, PDO::PARAM_STR);
        $stmt->execute();

        $query = "SELECT f.id
        FROM formaggyo f
        order by f.id desc 
        limit 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function deleteFormaggyo($id_formaggyo)
    {
        $sql=" Delete from formaggyo_ingredient
        WHERE id_formaggyo =:id_formaggyo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->execute();

        $sql=" Delete from formaggyo_warehouse WHERE id_formaggyo =:id_formaggyo ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->execute();

        $sql=" UPDATE supply_formaggyo SET id_formaggyo = -:id_formaggyo WHERE id_formaggyo =:id_formaggyo ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->execute();

        $sql=" Delete from formaggyo_size WHERE id_formaggyo =:id_formaggyo ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->execute();

        $sql=" UPDATE order_formaggyo SET id_formaggyo = -:id_formaggyo WHERE id_formaggyo =:id_formaggyo ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->execute();

        $sql=" UPDATE formaggyo SET id = -:id_formaggyo WHERE id =:id_formaggyo ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_formaggyo", $id_formaggyo, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
?>
