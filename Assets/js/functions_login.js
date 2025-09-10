
var divLoading = document.querySelector("#divLoading");
document.addEventListener('DOMContentLoaded', function(){
	
	if(document.querySelector("#formLogin")){
		let formLogin = document.querySelector("#formLogin");
		formLogin.onsubmit = function(e) {
			e.preventDefault();

			let strUsername = document.querySelector('#txtUsername').value;
			let strPassword = document.querySelector('#txtPassword').value;

			if(strUsername == "" || strPassword == "")
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
				var ajaxUrl = base_url+'/Login/loginUser'; 
				var formData = new FormData(formLogin);
				request.open("POST",ajaxUrl,true);
				request.send(formData);
				request.onreadystatechange = function(){
					if(request.readyState != 4) return;
					if(request.status == 200){
						var objData = JSON.parse(request.responseText);
						if(objData.status)
						{	
							window.location = base_url+'/home';
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

							document.querySelector('#txtPassword').value = "";
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

}, false);