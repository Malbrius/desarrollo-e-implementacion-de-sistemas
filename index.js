document.getElementById('toggleDarkMode').addEventListener('click', function () {
    document.body.classList.toggle('dark-mode');
    var tables = document.querySelectorAll('.table');
    tables.forEach(function(table) {
        if (document.body.classList.contains('dark-mode')) {
            table.classList.add('table-dark');
        } else {
            table.classList.remove('table-dark');
        }
        table.querySelector('thead').classList.toggle('thead-dark');
    });
});

function listarRegistros() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("resultados").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "index.php?action=listar", true);
    xhttp.send();
}
    
function guardarRegistro() {
    var formulario = document.getElementById("registroForm");
    var datos = new FormData(formulario);

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "index.php", true);
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);
            listarRegistros(); // Actualiza la lista después de guardar
        }
    };
    xhttp.send(datos);
}

function eliminarRegistro(id) { 
    if(confirm("¿Estás seguro de que deseas eliminar este registro?")) {
        var xhttp = new XMLHttpRequest(); 
        xhttp.open("POST", "index.php?action=eliminar", true); 
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); 
        xhttp.onreadystatechange = function() { 
            if (this.readyState == 4 && this.status == 200) { 
                alert(this.responseText); 
                listarRegistros(); // Actualiza la lista después de eliminar 
            } 
        }; 
        xhttp.send("id=" + id);
    } 
}

function editarRegistro(id) {
    var formData = new FormData();
    formData.append('id', id);

    var campo1 = document.querySelector('input[name="campo1"]').value;
    var campo2 = document.querySelector('input[name="campo2"]').value;
    var campo3 = document.querySelector('input[name="campo3"]').value;
    var campo4 = document.querySelector('input[name="campo4"]').value;
    var campo5 = document.querySelector('input[name="campo5"]').value;

    formData.append('campo1', campo1 ? campo1 : '');
    formData.append('campo2', campo2 ? campo2 : '');
    formData.append('campo3', campo3 ? campo3 : '');
    formData.append('campo4', campo4 ? campo4 : '');
    formData.append('campo5', campo5 ? campo5 : '');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?action=editar', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var respuesta = JSON.parse(xhr.responseText);
            if (respuesta.success) {
                // Actualiza los campos del formulario con los datos recibidos
                document.querySelector('input[name="id"]').value = respuesta.id;
                document.querySelector('input[name="campo1"]').value = respuesta.campo1;
                document.querySelector('input[name="campo2"]').value = respuesta.campo2;
                document.querySelector('input[name="campo3"]').value = respuesta.campo3;
                document.querySelector('input[name="campo4"]').value = respuesta.campo4;
                document.querySelector('input[name="campo5"]').value = respuesta.campo5;

                // Borrar el contenido de los campos si la actualización fue exitosa
                if (respuesta.clearFields) {
                    document.querySelector('input[name="campo1"]').value = '';
                    document.querySelector('input[name="campo2"]').value = '';
                    document.querySelector('input[name="campo3"]').value = '';
                    document.querySelector('input[name="campo4"]').value = '';
                    document.querySelector('input[name="campo5"]').value = '';
                }
            } else {
                console.error(respuesta.error);
            }
        }
    };
    xhr.send(formData);
}
