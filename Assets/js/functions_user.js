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
				Swal.fire({
				title: "Por favor",
				text: "Todos los campos son obligatorios.",
				icon: "error",
				background: '#19233adb',
				color: '#fff',
				customClass: {
					popup: 'futuristic-popup'
				}
			});
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
                            Swal.fire({
						title: 'Éxito',
						text: objData.msg,
						icon: 'success',
						background: '#19233adb',
						color: '#fff',
						customClass: {
							popup: 'futuristic-popup'
						}
					});
							window.location = base_url + "/home";
						}else{
							Swal.fire({
						title: 'Atención',
						text: objData.msg,
						icon: 'error',
						background: '#19233adb',
						color: '#fff',
						customClass: {
							popup: 'futuristic-popup'
						}
					});
						}
					}else{
						Swal.fire({
					title: "Atención",
					text: "Error en el proceso",
					icon: "error",
					background: '#19233adb',
					color: '#fff',
					customClass: {
						popup: 'futuristic-popup'
					}
				});
					}
					divLoading.style.display = "none";
					return false;
				}
			}
		}
	}

},false);



