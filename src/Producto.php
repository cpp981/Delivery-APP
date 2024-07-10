<?php
require 'Conexion.php';
class Producto extends Conexion{
    private $id;
    private $nombre;
    private $nombre_corto;
    private $pvp;
    private $familia;
    private $descripcion;

    

    public function __construct(){
        parent::__construct();
    }
    //Recupera los productos de la tabla productos.
    public function recuperarProductos(){
        $consulta = "SELECT id, nombre, pvp FROM productos ORDER BY nombre";
        $con = $this->crearConexion()->prepare( $consulta );
        try{
            $con->execute();
        }catch(PDOException $ex){
            die("Error al recuperar los productos: ".$ex->getMessage());
        }
        return $con;
    }

    /**
     * @return mixed
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param mixed $id
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    

    /**
     * @return mixed
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @param mixed $nombre
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }
    

    /**
     * @return mixed
     */ 
    public function getNombre_corto()
    {
        return $this->nombre_corto;
    }

    /**
     * Set the value of nombre_corto
     *
     * @param mixed $nombre_corto
     */ 
    public function setNombre_corto($nombre_corto)
    {
        $this->nombre_corto = $nombre_corto;

        return $this;
    }
    

    /**
     * @return mixed
     */ 
    public function getPvp()
    {
        return $this->pvp;
    }

    /**
     * Set the value of pvp
     *
     * @param mixed $pvp
     */ 
    public function setPvp($pvp)
    {
        $this->pvp = $pvp;

        return $this;
    }
    

    /**
     * @return mixed
     */ 
    public function getFamilia()
    {
        return $this->familia;
    }

    /**
     * Set the value of familia
     *
     * @param mixed $familia
     */ 
    public function setFamilia($familia)
    {
        $this->familia = $familia;

        return $this;
    }
    

    /**
     * @return mixed
     */ 
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     *
     * @param mixed $descripcion
     */ 
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}