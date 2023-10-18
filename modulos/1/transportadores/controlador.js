$(document).ready(function () {
  listarTransportadores();
});

function listarTransportadores() {
  ajaxRequest({}, "post", "traerTransportadores", "transportadores").done(
    function (response) {
      $("#tabla-transportadores tbody").html("");
      $.each(response, function (index, val) {
        console.log(val);
        $("#tabla-transportadores tbody").append(
          "<tr>" +
            "<td>" +
            val.id_transportadora +
            "</td>" +
            "<td>" +
            val.nombre_transportadora +
            "</td>" +
            "<td>" +
            val.informacion_contacto +
            "</td>" +
            "</tr>"
        );
      });
    }
  );
}
