$(document).ready(function () {
  listarPedidos();
});

function listarPedidos() {
  ajaxRequest({}, "post", "traerPedidos", "pedidos").done(function (response) {
    $("#tabla-pedidos tbody").html("");
    $.each(response, function (index, val) {
      console.log(val);
      $("#tabla-pedidos tbody").append(
        "<tr>" +
          "<td>" +
          val.id_pedido +
          "</td>" +
          "<td>" +
          val.fecha_pedido +
          "</td>" +
          "<td>" +
          val.tipo_pedido +
          "</td>" +
          "<td>" +
          val.consecutivo +
          "</td>" +
          "</tr>"
      );
    });
  });
}
