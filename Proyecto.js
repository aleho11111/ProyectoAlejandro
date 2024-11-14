
function validarInicioSesion() {
    const email = document.getElementById('usuario').value.trim();
    const password = document.getElementById('contrasena').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email || !emailRegex.test(email)) {
        alert("Por favor ingrese un correo válido.");
        return false;
    }

    if (!password) {
        alert("Por favor ingrese su contraseña.");
        return false;
    }

    window.location.href = "pagina_principal.html";
    return false;
}

function validarRegistro() {
    const nombre = document.getElementById('nombre').value.trim();
    const id = document.getElementById('id').value.trim();
    const usuario = document.getElementById('usuario').value.trim();
    const correo = document.getElementById('correo').value.trim();
    const contrasena = document.getElementById('contrasena').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!nombre) {
        alert("Por favor ingrese su nombre.");
        return false;
    }
    if (!id) {
        alert("Por favor ingrese su identificación o NIT.");
        return false;
    }
    if (!usuario) {
        alert("Por favor ingrese el tipo de usuario.");
        return false;
    }
    if (!correo || !emailRegex.test(correo)) {
        alert("Por favor ingrese un correo electrónico válido.");
        return false;
    }
    if (contrasena.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres.");
        return false;
    }

    alert("Registro exitoso");
    return true;
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById('preview');
        preview.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function validarFormulario() {
    const habilidades = document.getElementById("habilidades").value.trim();
    const contacto = document.getElementById("contacto").value.trim();
    const horario = document.getElementById("horario").value.trim();
    const foto = document.getElementById("foto").files;

    if (!habilidades || !contacto || !horario || foto.length === 0) {
        alert("Por favor completa todos los campos, incluida la foto.");
        return false;
    }
    return true;
}