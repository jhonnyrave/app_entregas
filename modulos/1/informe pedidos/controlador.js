 // @autor xxx xxxx (xxxxxxx@ipanu.co)
 // @fecha   
 // @version 

(function(document, window, $, Core) {
    (function() {

        return informe pedidos = {
            //VARIABLES GLOBAL
            Estacion: 0,
            items: [],
            mascosas: "",

            Initialize: function() {
                var self = this;

                 //EJECUTAR METODO 
                self.FuncionEjemplo();

                 //FORMA DE ASIGNAR UN CLICK AUN BOTON
                 $("#myBoton").on("click", function(event) {
                    swal("Ejemplo","evento onclick","error");
                });


            },

            //METODO PARA CONSULTAR USUARIOS DE EJEMPLO
            FuncionEjemplo: function() {
                //PETICION AJAX
                 // {"PARAMETRO":PARAMETRO},    TYPE,           METODO,              MODULO
                ajaxRequest({},                'post',    'PeticionEjemplo',        'inicio').done(function (response){

            $("#tabla-ejemplo tbody").html("");
            $.each(response, function(index, val) {
            
                 $("#tabla-ejemplo tbody").append('<tr>'+
                    '<td>'+val.usuario+'</td>'+
                    '<td>'+val.nombre+'</td>'+
                    '<td>'+val.apellidos+'</td>'+
                    '<td>'+val.correo+'</td>'+
                    '<td><img src="static/images_usuarios/'+val.imagen+'" class="rounded-corner" style="width: 40px;"></td>'+
                    '<td class="text-right">'+
                        '<a href="javascript:void(0)" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Editar usuario" onclick=$("#usuario").val("'+val.usuario+'").change();><i class="fas fa-lg fa-fw m-r-10 fa-pencil-alt"></i></a>'+
                    '</td>'+
                '</tr>');
            });
                });
            },



        }
    })()
    informe pedidos.Initialize()
})(document, window, jQuery, Core)

