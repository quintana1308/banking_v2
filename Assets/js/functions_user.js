document.addEventListener('DOMContentLoaded',function(){

	if(document.querySelector("#formEditUser")){
		let formEditUser = document.querySelector("#formEditUser");
		formEditUser.onsubmit = function(e) {
			e.preventDefault();
 
			let name = document.querySelector('#name').value;
            let username = document.querySelector('#username').value;
            let enterprise = document.querySelector('#enterprise').value;

			if(name == "" || username == "" || enterprise == "")
			{
				Swal.fire("Por favor", "Todos los campos son obligatorios.", "error");
				return false;
			}else{
				var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = base_url+'/usuario/updateUsuario'; 
				var formData = new FormData(formEditUser);
				request.open("POST",ajaxUrl,true);
				request.send(formData);
				request.onreadystatechange = function(){
					if(request.readyState != 4) return;
					if(request.status == 200){
						var objData = JSON.parse(request.responseText);

						if(objData.status)
						{   
                            Swal.fire('Éxito',objData.msg,'success');
							window.location = base_url + "/home";
						}else{
							Swal.fire('Atención',objData.msg,'error');
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



