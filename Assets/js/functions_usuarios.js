// Variable global para el DataTable
let tableUsuarios;

document.addEventListener('DOMContentLoaded', function () {
    
    // Inicializar DataTable para usuarios si existe
    if (document.getElementById('usuarios-table')) {
        tableUsuarios = $('#usuarios-table').DataTable({
            "aProcessing": true,
            "aServerSide": false,
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible en esta tabla",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "infoThousands": ",",
                "loadingRecords": "Cargando...",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": ">",
                    "previous": "<"
                },
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros"
            },
            "ajax": {
                "url": base_url + "/usuario/getUsuarios",
                "dataSrc": ""
            },
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "username" },
                { "data": "rol_name" },
                { "data": "enterprise_name" },
                { "data": "type_text" },
                { "data": "delete_mov_text" },
                { "data": "status_text" },
                { "data": "options", "orderable": false, "searchable": false }
            ],
            "bDestroy": true,
            "iDisplayLength": 25,
            "order": [[1, "asc"]]
        });

        // Función para recargar tabla
        function reloadUsuariosTable() {
            if (tableUsuarios) {
                const btnReload = document.getElementById('btnReloadTable');
                if (btnReload) {
                    const originalHTML = btnReload.innerHTML;
                    btnReload.innerHTML = '<span class="btn-glow"></span><i class="fas fa-spinner fa-spin me-2"></i>Recargando...';
                    btnReload.disabled = true;
                    
                    tableUsuarios.ajax.reload(function() {
                        btnReload.innerHTML = originalHTML;
                        btnReload.disabled = false;
                    }, false);
                }
            }
        }

        // Event listener para el botón de recarga
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'btnReloadTable') {
                reloadUsuariosTable();
            }
        });
    }

    // Formulario de nuevo usuario
    if (document.querySelector("#formNewUsuario")) {
        let formNewUsuario = document.querySelector("#formNewUsuario");
        formNewUsuario.onsubmit = function (e) {
            e.preventDefault();

            let name = document.querySelector('#name').value;
            let username = document.querySelector('#username').value;
            let password = document.querySelector('#password').value;
            let id_rol = document.querySelector('#id_rol').value;
            let id_enterprise = document.querySelector('#id_enterprise').value;
            let type = document.querySelector('#type').value;
            let delete_mov = document.querySelector('#delete_mov').checked ? 1 : 0;

            if (name == "" || username == "" || password == "" || id_rol == "" || id_enterprise == "" || type == "") {
                Swal.fire("Por favor", "Todos los campos son obligatorios.", "error");
                return false;
            }

            let formData = new FormData(formNewUsuario);
            formData.set('delete_mov', delete_mov);

            fetch(base_url + '/usuario/setUsuario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: data.msg,
                        icon: "success",
                        confirmButtonText: "Aceptar"
                    }).then(() => {
                        window.location.href = base_url + '/usuario/usuarios';
                    });
                } else {
                    Swal.fire("Error", data.msg, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire("Error", "Hubo un problema al procesar la solicitud", "error");
            });
        }
    }

    // Formulario de editar usuario
    if (document.querySelector("#formEditUsuario")) {
        let formEditUsuario = document.querySelector("#formEditUsuario");
        formEditUsuario.onsubmit = function (e) {
            e.preventDefault();

            let id = document.querySelector('#id').value;
            let name = document.querySelector('#name').value;
            let username = document.querySelector('#username').value;
            let id_rol = document.querySelector('#id_rol').value;
            let id_enterprise = document.querySelector('#id_enterprise').value;
            let type = document.querySelector('#type').value;
            let delete_mov = document.querySelector('#delete_mov').checked ? 1 : 0;

            if (id == "" || name == "" || username == "" || id_rol == "" || id_enterprise == "" || type == "") {
                Swal.fire("Por favor", "Todos los campos son obligatorios.", "error");
                return false;
            }

            let formData = new FormData(formEditUsuario);
            formData.set('delete_mov', delete_mov);

            fetch(base_url + '/usuario/updateUsuarioAdmin', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: data.msg,
                        icon: "success",
                        confirmButtonText: "Aceptar"
                    }).then(() => {
                        window.location.href = base_url + '/usuario/usuarios';
                    });
                } else {
                    Swal.fire("Error", data.msg, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire("Error", "Hubo un problema al procesar la solicitud", "error");
            });
        }
    }
});

// Función para editar usuario
function editUsuario(id) {
    window.location.href = base_url + '/usuario/editUsuario?id=' + id;
}

// Función para eliminar usuario
function deleteUsuario(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción desactivará el usuario y no podrá acceder al sistema",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', id);

            fetch(base_url + '/usuario/delUsuario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        title: "¡Desactivado!",
                        text: data.msg,
                        icon: "success",
                        confirmButtonText: "Aceptar"
                    }).then(() => {
                        // Recargar solo el DataTable
                        if (tableUsuarios) {
                            tableUsuarios.ajax.reload(null, false);
                        }
                    });
                } else {
                    Swal.fire("Error", data.msg, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire("Error", "Hubo un problema al procesar la solicitud", "error");
            });
        }
    });
}

// Función para activar usuario
function activateUsuario(id) {
    Swal.fire({
        title: '¿Activar usuario?',
        text: "El usuario podrá acceder nuevamente al sistema",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', id);

            fetch(base_url + '/usuario/activateUsuario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        title: "¡Activado!",
                        text: data.msg,
                        icon: "success",
                        confirmButtonText: "Aceptar"
                    }).then(() => {
                        // Recargar solo el DataTable
                        if (tableUsuarios) {
                            tableUsuarios.ajax.reload(null, false);
                        }
                    });
                } else {
                    Swal.fire("Error", data.msg, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire("Error", "Hubo un problema al procesar la solicitud", "error");
            });
        }
    });
}
