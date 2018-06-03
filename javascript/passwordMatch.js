window.onload = function () {
	document.getElementById("password_reg").onchange = validatePassword;
	document.getElementById("password_reg_confirm").onchange = validatePassword;
}
function validatePassword(){
var pass2=document.getElementById("password_reg").value;
var pass1=document.getElementById("password_reg_confirm").value;
if(pass1!=pass2)
	document.getElementById("password_reg_confirm").setCustomValidity("Hesla nesouhlasí");
else
	document.getElementById("password_reg_confirm").setCustomValidity('');	 
//empty string means no validation error
}