
document.addEventListener('DOMContentLoaded',function(){

    let tableBank = $('#bank-list-table').dataTable( { 
        "aProcessing":true,
        "aServerSide":true,
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
        "ajax":{
            "url": " "+base_url+"/bank/getBanks",
            "dataSrc":""
        },
        "columns":[
            {
                "data":"id",
                "render": function(data, type, row) {
                    return `<span class="text-gradient">#${String(data).padStart(3, '0')}</span>`;
                }
            },
            {"data":"name"},
            {
                "data":"account",
                "render": function(data, type, row) {
                    return `<span class="account-number">${data}</span>`;
                }
            },
            {"data":"enterprise"},
            {"data":"id_bank"},
            {
                "data":"banco",
                "render": function(data, type, row) {
                    return `<span class="bank-prefix">${data}</span>`;
                }
            },
            
            {
                "data": "id",
                "render": function(data, type, row) {
                    let html = '';
                    html += `<div class="d-flex align-items-center justify-content-center">
                                 <a class="btn-action btn-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" href="${base_url}/bank/edit/${data}">
                                    <i class="fas fa-edit"></i>
                                 </a>`
                    if(row.status == 1){
                        html += `<a class="btn-action btn-delete" data-bs-toggle="tooltip" data-bs-placement="top" title="Desactivar" href="javascript:void(0)" onclick="confirmDelete(${data}, 0)">
                                    <i class="fas fa-times"></i>
                                 </a>
                              </div>`
                    }else{
                        html += `<a class="btn-action btn-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Activar" href="javascript:void(0)" onclick="confirmDelete(${data}, 1)">
                                    <i class="fas fa-check"></i>
                                 </a>
                              </div>`
                    }
                                 
                    return html;
                }
            }
        ],
        "bDestroy": true,
        "iDisplayLength": 50,
        "order":[[0,"asc"]]  
    });

    if(document.querySelector("#formNewBank")){
		let formNewBank = document.querySelector("#formNewBank");
		formNewBank.onsubmit = function(e) {
			e.preventDefault();

			let name = document.querySelector('#name').value;
            let account = document.querySelector('#account').value;
            let id_bank = document.querySelector('#id_bank').value;
            let id_enterprise = document.querySelector('#id_enterprise').value;
            let prefix = document.querySelector('#prefix').value;

			if(name == "" || account == "" || id_bank == "" || id_enterprise == "" || prefix == "")
			{
				Swal.fire("Por favor", "Todos los campos son obligatorios.", "error");
				return false;
			}else{
				var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = base_url+'/bank/setBank'; 
				var formData = new FormData(formNewBank);
				request.open("POST",ajaxUrl,true);
				request.send(formData);
				request.onreadystatechange = function(){
					if(request.readyState != 4) return;
					if(request.status == 200){
						var objData = JSON.parse(request.responseText);

						if(objData.status)
						{   
                            Swal.fire('Éxito', objData.message, 'success').then((result) => {
                                if (result.isConfirmed) {
                                    window.location = base_url+'/bank';
                                }
                            });
						}else{
							Swal.fire('Atención',objData.message,'error');
						}
					}else{
						Swal.fire("Atención","Error en el proceso", "error");
					}
					divLoading.style.display = "none";
					return false;
				}
			}
		}
	}

    if(document.querySelector("#formEditBank")){
		let formEditBank = document.querySelector("#formEditBank");
		formEditBank.onsubmit = function(e) {
			e.preventDefault();

			let name = document.querySelector('#name').value;
            let account = document.querySelector('#account').value;
            let id_bank = document.querySelector('#id_bank').value;
            let id_enterprise = document.querySelector('#id_enterprise').value;
            let prefix = document.querySelector('#prefix').value;

			if(name == "" || account == "" || id_bank == "" || id_enterprise == "" || prefix == "")
			{
				Swal.fire("Por favor", "Todos los campos son obligatorios.", "error");
				return false;
			}else{
				var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = base_url+'/bank/updateBank'; 
				var formData = new FormData(formEditBank);
				request.open("POST",ajaxUrl,true);
				request.send(formData);
				request.onreadystatechange = function(){
					if(request.readyState != 4) return;
					if(request.status == 200){
						var objData = JSON.parse(request.responseText);

						if(objData.status)
						{   
                            Swal.fire('Éxito', objData.message, 'success').then((result) => {
                                if (result.isConfirmed) {
                                    window.location = base_url+'/bank';
                                }
                            });
						}else{
							Swal.fire('Atención',objData.message,'error');
						}
					}else{
						Swal.fire("Atención","Error en el proceso", "error");
					}
					divLoading.style.display = "none";
					return false;
				}
			}
		}
	}


},false);

function confirmDelete(id, status) {

    if(status == 0){
        botonText = "Si, desactivar";
        content = "¿Desea desactivar la cuenta bancaria?";
    }else{
        botonText = 'Si, activar';
        content = "¿Desea activar la cuenta bancaria?";
    }
    Swal.fire({
        title: '¡Esta acción no se puede deshacer!',
        text: content,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b8aff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: botonText,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí haces la petición
            fetch(base_url+`/bank/deleteBank/${id}`, {
                method: 'GET', // o 'GET' si es tu caso
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire('Actualizado', data.message, 'success').then((result) => {
                        if (result.isConfirmed) {
                            $('#bank-list-table').DataTable().ajax.reload(); 
                        }
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Ocurrió un error en la solicitud.', 'error');
            });
        }
    });
}