
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
				Swal.fire("Por favor", "Escribe usuario y contraseña.", "error");
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
							Swal.fire('Atención',objData.msg,'error');
							document.querySelector('#txtPassword').value = "";
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

}, false);