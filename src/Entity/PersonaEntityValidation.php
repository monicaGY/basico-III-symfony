<?php  
namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
class PersonaEntityValidation
{
    #[Assert\NotBlank(message: 'El campo nombre es obligatorio')]
    protected $nombre;
    #[Assert\NotBlank(message: 'El campo correo es obligatorio')]

    protected $correo;
    protected $telefono;
    protected $pais;

    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function getCorreo(){
        return $this->correo;
    }

    public function setCorreo($correo){
        $this->correo = $correo;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    public function setTelefono($telefono){
        $this->telefono = $telefono;
    }

    public function getPais(){
        return $this->pais;
    }

    public function setPais($pais){
        $this->pais = $pais;
    }
    
}
?>