$(document).ready(function() {

	$("#limpiar-formulario").click(function(event) {
		$("#usuario").attr('readonly', false);
		$("#frm")[0].reset();
		$("#usuario").focus();
		$('#frm .help-block').remove();
		$('#frm .form-group').removeClass("has-error");
	});

	$("#grabar-usuario").click(function(event) {
		grabarUsuario(files);
	});

	$("#usuario").change(function(event) {
		buscarUsuario($(this).val());
	});

	$("#listar-activos").click(function(event) {
		listarUsuarios('A');
	});

	$("#listar-inactivos").click(function(event) {
		listarUsuarios('');
	});

	$("#listar-activos").click();
});

function buscarUsuario(usuario){
	ajaxRequest({"usuario":usuario},'post','traerUsuario','usuario').done(function (response){
			if(response.existe=='S'){
				$("#usuario").attr('readonly', 'readonly');
				$("#password1").removeProp("required").removeProp("data-rule-minlength");
				$("#password2").removeProp("required").removeProp("data-rule-minlength");
			}
			$("#nombres").val(response.nombres).change();
			$("#apellidos").val(response.apellidos).change();
			$("#correo").val(response.correo).change();
			var estado;
			if(response.estado=='A') estado=true; else estado=false;
			$("#estado").prop('checked', estado);		
	});
}

function grabarUsuario(files){

	var form = $( "#frm" );
	if(form.valid()){
		if($("#password1").val() != $("#password2").val()){
			toastr.error("Contrase&ntilde;as no concuerda, verifique");		
			return;
		}

		ajaxRequestFile({"frm":JSON.stringify(Core.FormToJSON('#frm')),"file":files},'post','grabarUsuario','usuario').done(function (response){
			if(response.mensaje=='exitoso'){
				toastr.success("Usuario grabado");
				$("#limpiar-formulario").click();
				$("#listar-activos").click();
			}else{
				toastr.error(response.mensaje);				
			}
		});
		
	}else{
		toastr.warning("El formulario aun contiene errores, verifique");
	}
	return;
}

function listarUsuarios(estado){
	ajaxRequest({"estado":estado},'post','traerLista','usuario').done(function (response){
			$("#tabla-listado tbody").html("");
			$.each(response, function(index, val) {
			
				 $("#tabla-listado tbody").append('<tr>'+
					'<td>'+val.usuario+'</td>'+
					'<td>'+val.nombres+'</td>'+
					'<td>'+val.apellidos+'</td>'+
					'<td>'+val.correo+'</td>'+
					'<td><img src="static/images_usuarios/'+val.imagen+'" class="rounded-corner" style="width: 40px;"></td>'+
					'<td class="text-right">'+
						'<a href="javascript:void(0)" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Editar usuario" onclick=$("#usuario").val("'+val.usuario+'").change();><i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i></a>'+
					'</td>'+
				'</tr>');
			});		
	});	
}
