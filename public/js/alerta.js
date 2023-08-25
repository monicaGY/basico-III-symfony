document.addEventListener("DOMContentLoaded",setup);

function setup(){

    document.querySelectorAll(".btnEliminar").forEach( boton => {
        boton.addEventListener("click", e => {
            document.querySelector("#alertDelete").classList.remove('d-none')
            let categoria = e.target.getAttribute("data-producto")
            document.querySelector("#btnRespuestaEliminar").setAttribute("href",`/doctrine/categoria/eliminar/${categoria}`)

            document.querySelector("#btnRespuestaCancelar").addEventListener('click', e => {
                document.querySelector("#alertDelete").classList.add('d-none')
            })    
        })
    } )
    
    
}


function cerrarSesion(ruta){
    swal({
      title: "¿Desea cerrar sesión?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: "Sí",
      cancelButtonText: "Cancelar",
    })
    .then(resultado => {
      if (resultado.value) {
        window.location = ruta;
      }
    });
  }