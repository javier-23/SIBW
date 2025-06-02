
$(document).ready(function() {

    const resultadosContainer = $('#resultados-busqueda');

    $('input[name="buscar"]').on('keyup', function() { 
        const busqueda = $(this).val().trim(); // trim elimina espacios al inicio y al final
        if(busqueda !== '')  
            hacerPeticionAjax(busqueda);
        else
            resultadosContainer.removeClass('active'); // Oculta los resultados si el input está vacío
    });

    function hacerPeticionAjax(busqueda) {
        
        $.ajax({
            data: {buscar: busqueda},
            dataType: 'json',
            url: '../P3/busqueda.php',
            type: 'get',
            success: function(respuesta) {
                mostrarResultados(respuesta);
            }
        });
    }

    function mostrarResultados(peliculas) {
        resultadosContainer.empty();

        if (peliculas) {
            peliculas.forEach(function(pelicula) {
                const item = $('<div class="resultado-item"></div>');
                
                item.append(`<img class="miniatura-busqueda" src="../${pelicula.imagen}">`);
                item.append(`<span class="titulo-pelicula">${pelicula.titulo}</span>`);
                
                item.on('click', function() {
                    window.location.href = `pelicula.php?id=${pelicula.id}`;
                });
                
                resultadosContainer.append(item);
            });
        resultadosContainer.addClass('active');
        }
    }
});