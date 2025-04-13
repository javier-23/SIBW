document.addEventListener('DOMContentLoaded', () => {
    // Referencias a elementos del DOM
    const mostrarPanel = document.getElementById('mostrarComentarios'); // Botón para mostrar el panel de comentarios
    const cerrarPanel = document.getElementById('cerrarComentarios');
    const enviarFormulario = document.getElementById('formulario');
    const listaComentarios = document.getElementById('listaComentarios');
    const modal = document.getElementById('modalError');
    const mensajeError = document.getElementById('mensajeError');
    const cerrarModal = document.getElementById('cerrarModal');
    const aceptarModal = document.getElementById('botonAceptarModal');
    const comentarioForm = document.getElementById('comentario')
    const panelComentarios = document.getElementById('panelComentarios');

    const textoComentarios = document.getElementById('texto_comentarios');
    const palabrasProhibidas = JSON.parse(textoComentarios.getAttribute('data-palabrasProhibidas'));

    // Mostrar panel de comentarios
    mostrarPanel.addEventListener('click', () => {
        panelComentarios.classList.add('abierto');
    });

    // Cerrar panel de comentarios
    cerrarPanel.addEventListener('click', () => {
        panelComentarios.classList.remove('abierto');
    });
    
    // Validar correo
    function validarEmail(email) {
        const esValido = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; 
        return esValido.test(email);
    }
    
    // Filtrar palabras prohibidas en tiempo real
    comentarioForm.addEventListener('input', function() {
        let texto = this.value;
        
        palabrasProhibidas.forEach(palabra => {
            // Expresión regular para encontrar la palabra completa con 'i' para que no distinga entre mayúsculas y minúsculas
            const aux = new RegExp(`${palabra}`, 'i');
            
            // Reemplazar cada letra de la palabra con un asterisco
            texto = texto.replace(aux, match => '*'.repeat(match.length));
        });
        
        this.value = texto;
    });
    
    // Enviar comentario
    enviarFormulario.addEventListener('submit', (e)=> {
        e.preventDefault(); // Evitar que se recargue la página y se pierda el comentario
        
        const ahora = new Date();
        // Fecha y hora con el formato de la base de datos YYYY-MM-DD HH:MM
        const fechaHora_bd = `${ahora.getFullYear()}-${(ahora.getMonth()+1).toString().padStart(2,'0')}-${ahora.getDate().toString().padStart(2,'0')} ${ahora.getHours().toString().padStart(2,'0')}:${ahora.getMinutes().toString().padStart(2, '0')}`;
        // Fecha y hora con el formato DD-MM-YYYY HH:MM
        const fechaHora = `${ahora.getDate().toString().padStart(2,'0')}-${(ahora.getMonth()+1).toString().padStart(2,'0')}-${ahora.getFullYear()} ${ahora.getHours().toString().padStart(2,'0')}:${ahora.getMinutes().toString().padStart(2, '0')}`;

        const datos = {
            idPelicula: parseInt(new URLSearchParams(window.location.search).get('id')), // Obtener id de la película desde la URL
            nombre: document.getElementById('nombre').value,
            email: document.getElementById('email').value,
            comentario: comentarioForm.value.trim(), // Eliminar espacios en blanco al principio y al final
            fecha_hora: fechaHora_bd
        }

        // Validación de campos
        if (!datos.nombre || !datos.email || !datos.comentario) {
            mostrarError('Por favor, completa todos los campos.');
            return;
        }
        
        // Validación de email
        if (!validarEmail(datos.email)) {
            mostrarError('Por favor, introduce un correo válido.');
            return;
        }

        fetch("../P3/pelicula.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        })
        .then(res => {
            return res.json(); // Convertir la respuesta a JSON
        })
        .then(data => {
            if (data.exito) {
                agregarComentario(datos.nombre, datos.comentario, fechaHora);
                enviarFormulario.reset();
            } else {
                mostrarError(data.error || 'Hubo un problema al enviar el comentario.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Hubo un problema al enviar el comentario.');
        });
        
    });
    
    // Mostrar modal de error
    function mostrarError(mensaje){
        mensajeError.textContent = mensaje;
        modal.style.display = 'block';
    }
    
    // Cerrar modal con X
    cerrarModal.addEventListener('click', ()=> {
        modal.style.display = 'none';
    });
    
    // Cerrar modal con botón Aceptar
    aceptarModal.addEventListener('click', ()=> {
        modal.style.display = 'none';
    });
    
    // Función para agregar un nuevo comentario
    function agregarComentario(autor, texto, fecha_hora) {
        
        // Crear estructura del comentario
        const nuevoComentario = document.createElement('div');
        nuevoComentario.className = 'comentarios';
        nuevoComentario.innerHTML = `
            <div class="comentario-header">
                <span class="comentario-autor">${autor}</span>
                <span class="comentario-fecha">${fecha_hora}</span>
            </div>
            <p class="comentario-texto">${texto}</p>
        `;
        
        // Añadir al final de la lista
        listaComentarios.appendChild(nuevoComentario);
    }
});