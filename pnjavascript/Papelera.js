//Declaraciones
var contenido_textarea = "" ;
var num_caracteres_permitidos = 500;
//Funciones

//Funcion para no permitir más de 500 caracteres en la descripcion
function valida_longitud(){ 
	
	txtDescripcion = document.getElementById("txtDescripcion");
	num_caracteres = txtDescripcion.value.length;

  if (num_caracteres > num_caracteres_permitidos){ 
  	txtDescripcion.value = contenido_textarea;
  }else{ 
  	contenido_textarea = txtDescripcion.value;
  } 

}

//Funcion para generar los botones de imagen
function crear_sub(obj) {
  valor = document.FichaNuevoMapa.TieneSub.options[document.FichaNuevoMapa.TieneSub.selectedIndex].value;
  capa=document.getElementById('capa_sub');
  capa.innerHTML="";

  if (valor = "Sí"){
	capa.innerHTML+="Imagen Sub:&nbsp;<input name=\"ImageSub\" type=\"file\" id=\"ImageSub\" size=\"50\" maxlength=\"255\">(Solo JPG permitido)";
  }
	
}

function fltAdmin(){
	var tipoMenu,url,indice;
	indice = document.frmFiltro.cmbFiltro.selectedIndex;
	tipoMenu = document.frmFiltro.cmbFiltro.options[indice].value;
	url = 'index.php?module=Mapas&type=admin&func=view&est=' + tipoMenu;
	//Redireccionar
	document.frmFiltro.action = url;
	document.frmFiltro.submit();
}