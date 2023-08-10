<?php 
namespace App\Utilidades;
class Saludo{

    //Utilidades::saludo('Juan')
    //metodo estático
    public static function saludar($nombre){
        return "Hola,  {$nombre}";

    }

    //metodo instanancia
    public function saludar2($nombre){
        return "Hola,  {$nombre}";

    }
}
?>