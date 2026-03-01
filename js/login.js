// registro funciona una vez se pulsa el boton registrar
function registro()
{
    //recogemos los datos del formulario para crear la cuenta
    let correo = document.getElementById('email').value 
    let contraseña = document.getElementById('pass').value
    let repeticion_contraseña = document.getElementById('rep-pass').value

    //validacion basica correo
    if (!correo.includes("@"))
    {
        alert("No se ha introducido un correo correcto")
        return
    } //validacion contraseña y su repeticion
    else if (repeticion_contraseña != contraseña)
    {
        alert("La repeticion de la contraseña no coincide")
        return
    }
    
}

let boton = document.getElementById("submit");
boton.addEventListener("click", registro);
