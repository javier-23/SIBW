document.addEventListener('DOMContentLoaded', () => {
    // Referencias a elementos del DOM
    const mostrarPanel = document.getElementById('mostrarComentarios'); // Botón para mostrar el panel de comentarios
    const cerrarPanel = document.getElementById('cerrarComentarios');
    const listaComentarios = document.getElementById('listaComentarios');
    const modal = document.getElementById('modalError');
    const mensajeError = document.getElementById('mensajeError');
    const cerrarModal = document.getElementById('cerrarModal');
    const aceptarModal = document.getElementById('botonAceptarModal');
    const panelComentarios = document.getElementById('panelComentarios');

    // Referencias a elementos del formulario que pueden no existir si no estas logueado
    const enviarFormulario = document.getElementById('formulario');
    const comentarioForm = document.getElementById('comentario');
    const textoComentarios = document.getElementById('texto_comentarios');
    
    let palabrasProhibidas = [];
    if(textoComentarios)
        palabrasProhibidas = JSON.parse(textoComentarios.getAttribute('data-palabrasProhibidas'));

    // Mostrar panel de comentarios
    if (mostrarPanel) {
        mostrarPanel.addEventListener('click', () => {
            panelComentarios.classList.add('abierto');
        });
    }

    // Cerrar panel de comentarios
    if (cerrarPanel) {
        cerrarPanel.addEventListener('click', () => {
            panelComentarios.classList.remove('abierto');
        });
    }
    
    // Validar correo
    function validarEmail(email) {
        const esValido = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; 
        return esValido.test(email);
    }
    
    // Filtrar palabras prohibidas en tiempo real
    if (comentarioForm) {
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
    }
    
    // Enviar comentario
    if (enviarFormulario) {
        enviarFormulario.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Resto del código para enviar comentarios...
            const ahora = new Date();
            const fechaHora_bd = `${ahora.getFullYear()}-${(ahora.getMonth()+1).toString().padStart(2,'0')}-${ahora.getDate().toString().padStart(2,'0')} ${ahora.getHours().toString().padStart(2,'0')}:${ahora.getMinutes().toString().padStart(2, '0')}`;
            const fechaHora = `${ahora.getDate().toString().padStart(2,'0')}-${(ahora.getMonth()+1).toString().padStart(2,'0')}-${ahora.getFullYear()} ${ahora.getHours().toString().padStart(2,'0')}:${ahora.getMinutes().toString().padStart(2, '0')}`;
            const nombreUsuario = enviarFormulario.getAttribute('data-usuario-nombre');

            const datos = {
                idPelicula: parseInt(new URLSearchParams(window.location.search).get('id')),
                nombre: nombreUsuario,
                comentario: comentarioForm?.value?.trim() || '',
                fecha_hora: fechaHora_bd
            }

            // Validaciones y envío de datos...
            if (!datos.comentario) {
                mostrarError('Por favor, completa todos los campos.');
                return;
            }

            fetch("../P3/pelicula.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            })
            .then(res => res.json())
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
    }
    
    // Mostrar modal de error
    function mostrarError(mensaje){
        mensajeError.textContent = mensaje;
        modal.style.display = 'block';
    }

    // Cerrar modal con X
    if (cerrarModal) {
        cerrarModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }
    
    // Cerrar modal con botón Aceptar
    if (aceptarModal) {
        aceptarModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }
    
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

    // ================ FUNCIONALIDADES DE MODERACIÓN ================
    // Solo inicializar estas funciones si el usuario es moderador o superusuario
        
        // Manejar botones de editar
        document.querySelectorAll('.edit-comentario-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentBox = this.closest('.comentarios');
                
                // Ocultar texto y mostrar formulario de edición
                commentBox.querySelector('.comentario-texto').style.display = 'none';
                commentBox.querySelector('.edit-comment-form').style.display = 'block';
            });
        });
    
    
        // Manejar cancelación de edición
        document.querySelectorAll('.cancel-edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentForm = this.closest('.edit-comment-form');
                const commentBox = this.closest('.comentarios');
                
                // Mostrar texto y ocultar formulario
                commentBox.querySelector('.comentario-texto').style.display = 'block';
                commentForm.style.display = 'none';
            });
        });
        
        // Manejar guardado de edición
        document.querySelectorAll('.save-edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentForm = this.closest('.edit-comment-form');
                const commentId = commentForm.getAttribute('data-comment-id');
                const newText = commentForm.querySelector('.edit-textarea').value;
                
                // Enviar datos al servidor
                fetch('../P3/moderacion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'edit',
                        comment_id: commentId,
                        nuevo_texto: newText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar el texto visible
                        const commentBox = this.closest('.comentarios'); // Obtener el contenedor del comentario
                        const commentText = commentBox.querySelector('.comentario-texto'); // Obtener el elemento de texto del comentario
                        commentText.textContent = newText;
                        commentText.style.display = 'block';
                        commentForm.style.display = 'none';

                        // Añadir indicador de editado si no existe
                        const editComment = commentBox.querySelector('.comentario-header');
                        if (!editComment.querySelector('.editado-tag')) {
                            const editadoTag = document.createElement('span');
                            editadoTag.className = 'editado-tag';
                            editadoTag.textContent = '(editado)';
                            editComment.appendChild(editadoTag);
                        }
                        
                    } else {
                        mostrarError('Error: ' + (data.error || 'No se pudo actualizar el comentario'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('Ha ocurrido un error al procesar la solicitud');
                });
            });
        });
        
        // Manejar eliminación de comentarios
        document.querySelectorAll('.delete-comentario-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('¿Estás seguro de que deseas eliminar este comentario?')) {
                    const commentId = this.getAttribute('data-comment-id');
                    const commentBox = this.closest('.comentarios');
                    
                    fetch('../P3/moderacion.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'delete',
                            comment_id: commentId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Eliminar el comentario del DOM
                            commentBox.remove();
                        } else {
                            mostrarError('Error: ' + (data.error || 'No se pudo eliminar el comentario'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarError('Ha ocurrido un error al procesar la solicitud');
                    });
                }
            });
        });
});