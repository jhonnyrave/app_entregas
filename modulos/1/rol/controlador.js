var obj = function () {var o = this;$(document).ready(function () {o.initialize();});}
var rol = obj.prototype;

rol.initialize=function (){
	this.buscarRol(rol);
	this.listarroles(estado);
	this.grabarrol();
	this.listarUsuarios();
	this.listarOpciones();
	this.grabarPermisos(nombre, permisos);
}

$(document).ready(function() {

	$("#limpiar-formulario").click(function(event) {
		$("#rol").attr('readonly', false);
		$("#frm")[0].reset();
		$("#rol").focus();
		$('#frm .help-block').remove();
		$('#frm .form-group').removeClass("has-error");
		$("#usuarios").find('option:selected').removeAttr("selected").change();
		$('#usuarios').select2();
		$("#permisos").find('option:selected').removeAttr("selected").change();
		$('#permisos').select2();		
	});

	$("#listar-activos").click(function(event) {
		rol.listarroles('A');
	});
	$("#listar-inactivos").click(function(event) {
		rol.listarroles('');
	});
	$("#grabar-formulario").click(function(event) {
		rol.grabarrol();
	});
	$("#grabar-permisos").click(function(event) {
		var nombre=$("#rol").val();	
		var permisos=$("#permisos").val();	
		rol.grabarPermisos(nombre,permisos);
	});
	$("#rol").change(function(event) {
		$(this).val($(this).val().toUpperCase().trim());
		rol.buscarRol($(this).val());
	});
	$("#listar-activos").click();
	
	rol.listarUsuarios();
	rol.listarOpciones();
});

rol.buscarRol = function (nombre){

	ajaxRequest({"nombre":nombre},'post','buscarRol','rol').done(function (response){
		
		if(response.existe=='S'){
			$("#rol").attr('readonly', 'readonly');
		}
		$("#usuarios").find('option:selected').removeAttr("selected").change();
		$("#usuarios").val(response.usuarios);	
		$('#usuarios').select2();

		var permisos=[];
		$.each(response.permisos, function(index, val) {
			permisos[index]=val.id_programa+"-"+val.opcion+"-"+val.id_programa_opcion;
		});
		
		//marca como seleccionados los permisos actuales.
		$("#permisos").find('option:selected').removeAttr("selected").change();
		$("#permisos").val(permisos);	
		$('#permisos').select2();

		$("#descripcion").val(response.descripcion).change();
		var estado=true;
		if(response.estado!='A') estado=false;
		$("#estado").prop( "checked", estado );
		
	});
}

rol.listarroles = function (estado){
	ajaxRequest({"estado":estado},'post','traerLista','rol').done(function (response){

		$("#tabla-listado tbody").html("");
			//inserta TR a la tabla de roles
			$.each(response, function(index, val) {
				$("#tabla-listado tbody").append('<tr>'+
					'<td>'+val.nombre+'</td>'+
					'<td>'+val.descripcion+'</td>'+
					'<td class="text-right">'+
					'<a href="javascript:void(0)" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Editar rol" onclick=$("#rol").val("'+val.nombre+'").change();><i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i></a>'+
					'</td>'+
					'</tr>');
			});		
		});	
}

rol.grabarrol = function (){
	var form = $( "#frm" );
	if(form.valid()){
		ajaxRequest({"frm":Core.FormToJSON('#frm')},'post','grabarrol','rol').done(function (response){
			
			if(response.mensaje=='exitoso'){
				toastr.success("rol grabado");
				$("#limpiar-formulario").click();
				$("#listar-activos").click();
			}else{
				toastr.error(response.mensaje);
			}		
		});
	}else{
		toastr.warning("El formulario aun contiene errores, verifique");
	}
	return ;
}

rol.listarUsuarios = function(){
	ajaxRequest({"estado":'A'},'post','traerLista','usuario').done(function (response){
		
		$("#usuarios").html("");
			//agrega usuarios al select
			$.each(response, function(index, val) {
				$("#usuarios").append('<option value="'+val.id_usuario+'"">'+val.nombres+' ('+val.usuario+')</option>');
			});
			$('#usuarios').select2({placeholder: "Selecciona un usuario"});
		});	
}

rol.listarOpciones = function(){
	ajaxRequest({},'post','getOpciones','programa').done(function (response){

		$("#permisos").html("");
		var programa='';
		var HTML='';
		
		$.each(response, function(index, val) {
			if(programa!=val.programa){
				if(index!=0){
					HTML+='</optgroup>';		
				}
				HTML+='<optgroup label="'+val.programa+'">';	
				programa=val.programa;
			}
			HTML+='<option value="'+val.id+'-'+val.opcion+'-'+val.id_programa_opcion+'"">'+val.programa+' -> '+val.nombre+' ('+val.opcion+')</option>';
		});
		HTML+='</optgroup>';
		$("#permisos").append(HTML);
		$('#permisos').select2({
			placeholder: "Selecciona un permiso"
		});
	});	
}

rol.grabarPermisos = function(nombre,permisos){
	if(nombre==''){
		toastr.warning("Debe seleccionar primero un rol de la lista");
	}
	ajaxRequest({"nombre":nombre,"permisos":permisos,"eliminar":'S'},'post','grabarPermisos','rol').done(function (response){

		if(response.mensaje=='exitoso'){
			toastr.success("Permisos grabados al rol "+nombre);
			$("#limpiar-formulario").click();
		}else{
			toastr.error(response.mensaje);
		}
	});
}