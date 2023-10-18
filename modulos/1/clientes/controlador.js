$(document).ready(function () {
  listarClientes();
});

function listarClientes() {
  ajaxRequest({}, "post", "traerClientes", "clientes").done(function (
    response
  ) {
    $("#tabla-clientes tbody").html("");
    $.each(response, function (index, val) {
      console.log(val);
      $("#tabla-clientes tbody").append(
        "<tr>" +
          "<td>" +
          val.id_cliente +
          "</td>" +
          "<td>" +
          val.nombre_cliente +
          "</td>" +
          "<td>" +
          val.direccion +
          "</td>" +
          "<td>" +
          val.informacion_contacto +
          "</td>" +
          "</tr>"
      );
    });
  });
}
