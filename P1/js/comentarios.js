
    // Referencias a elementos del DOM
    const mostrarPanel = document.getElementById('mostrarComentarios');
    const cerrarPanel = document.getElementById('cerrarComentarios');
    const enviarFormulario = document.getElementById('formulario');
    const listaComentarios = document.getElementById('listaComentarios');
    const modal = document.getElementById('modalError');
    const mensajeError = document.getElementById('mensajeError');
    const cerrarModal = document.getElementById('cerrarModal');
    const aceptarModal = document.getElementById('botonAceptarModal');
    const comentario = document.getElementById('comentario')
    const panelComentarios = document.getElementById('panelComentarios');
    
    // Lista de palabras prohibidas
    const palabrasProhibidas = [
        'tonto', 'idiota', 'payaso', 'fuck', 'mierda', 
        'feo', 'gilipollas', 'joder', 'horrible', 'capullo'
    ];
    
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
        const esValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; //Cualquier caracter (menos @ y espacio en blanco) + @ + cualquier caracter + . + cualquier caracter
        return esValido.test(email);
    }
    
    // Filtrar palabras prohibidas en tiempo real
    comentario.addEventListener('input', function() {
        let texto = this.value;
        
        palabrasProhibidas.forEach(palabra => {
            // Crear una expresión regular para encontrar la palabra completa
            // con 'i' para que no distinga entre mayúsculas y minúsculas
            const regex = new RegExp(`\\b${palabra}\\b`, 'gi');
            
            // Reemplazar cada letra de la palabra con un asterisco
            texto = texto.replace(regex, match => '*'.repeat(match.length));
        });
        
        this.value = texto;
    });
    
    // Enviar comentario
    enviarFormulario.addEventListener('submit', (e)=> {
        e.preventDefault(); // Evitar que se recargue la página y se pierda el comentario
        
        const nombre = document.getElementById('nombre').value;
        const email = document.getElementById('email').value;
        const comentarioForm = comentario.value;
        
        // Validación de campos
        if (!nombre || !email || !comentarioForm) {
            mostrarError('Por favor, completa todos los campos.');
            return;
        }
        
        // Validación de email
        if (!validarEmail(email)) {
            mostrarError('Por favor, introduce un correo válido.');
            return;
        }
        
        // Crear y añadir nuevo comentario
        agregarComentario(nombre, comentarioForm);
        
        // Limpiar formulario
        enviarFormulario.reset();
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
    function agregarComentario(autor, texto) {
        // Obtener fecha y hora actual
        const ahora = new Date();
        const fechaHora = `${ahora.getDate()}/${ahora.getMonth()+1}/${ahora.getFullYear()} ${ahora.getHours()}:${ahora.getMinutes().toString().padStart(2, '0')}`;
        
        // Crear estructura del comentario
        const nuevoComentario = document.createElement('div');
        nuevoComentario.className = 'comentarios';
        nuevoComentario.innerHTML = `
            <div class="comentario-header">
                <span class="comentario-autor">${autor}</span>
                <span class="comentario-fecha">${fechaHora}</span>
            </div>
            <p class="comentario-texto">${texto}</p>
        `;
        
        // Añadir al principio de la lista
        listaComentarios.insertBefore(nuevoComentario, listaComentarios.lastChild);
    }