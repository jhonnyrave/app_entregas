let objentregas = function () {
  var o = this;
  $(document).ready(function () {
    o.initialize();
  });
};
let entregas = objentregas.prototype;

entregas.initialize = function () {
  this.funcionJS(parametro);
};

$(document).ready(function () {
  entregas.traerTransportadora();
  $("#consultar").on("click", function (e) {
    let tipo = $("#transportadora").val();
    if (tipo == "") {
      toastr.error("Por favor selecciona la transportadora");
      return;
    }

    let estado = $("#estado").val();
    entregas.traerDataTransportadores(tipo, estado);
  });

  $("#btnGrabarq").on("click", function (e) {
    let fecha = $("#fecha_entrega").val();
    if (fecha == "") {
      toastr.error("La Fecha de entrega  se encuentra vacia");
      return;
    }

    let estado = $("#estado_entrega").val();
    if (estado == "") {
      toastr.error("Por favor un estado de entrega");
      return;
    }

    let id_entrega = $("#entrega").val();
    let observaciones = $("#observaciones").val();

    //verificar si estan los archivos
    let imagen = $("#img_entrega")[0].files.length;
    if (imagen === 0) {
      toastr.error("No tenemos nada para cargar");
      return false;
    }

    entregas.grabar_ch(id_entrega, estado, fecha, observaciones);
  });

  $("#tipo_q").on("change", function (event) {
    id = document.getElementById("img_qv");
    if ($(this).val() == "VD") {
      id.type = "text";
    } else {
      id.type = "file";
    }
  });

  $("#tipo_ch").on("change", function (event) {
    id = document.getElementById("img_ch");
    if ($(this).val() == "CAT") {
      id.type = "text";
    } else {
      id.type = "file";
    }
  });

  $(document).on("change", 'input[type="file"]', function () {
    if (this.files[0].name) {
      var fileName = this.files[0].name;
      var fileSize = this.files[0].size;

      if (fileSize > 5000000) {
        toastr.error("El archivo no debe superar los 5MB");
        this.value = "";
        this.files[0].name = "";
      } else {
        // recuperamos la extensión del archivo
        var ext = fileName.split(".").pop();

        // Convertimos en minúscula porque
        // la extensión del archivo puede estar en mayúscula
        ext = ext.toLowerCase();
        switch (ext) {
          case "jpg":
          case "jpeg":
          case "png":
          case "pdf":
          case "gif":
          case "webp":
            if (this.files && this.files[0]) {
              var idimg = "#imgs_qv";
              if (
                this.files[0].name.match(
                  /\.(jpg|JPG|JPEG|jpeg|png|gif|GIF|PNG|JPG)$/
                )
              ) {
                var reader = new FileReader();
                reader.onload = function (e) {
                  $(idimg).show().attr("src", e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
              } else if (
                this.files &&
                this.files[0] &&
                this.files[0].name.match(/\.(PDF|pdf)$/)
              ) {
                $(idimg)
                  .show()
                  .attr("src", "static/images/acrobat_pdf_icon_large.png");
              }
            }
            break;
          default:
            toastr.error("El archivo no tiene la extensión adecuada");
            this.value = ""; // reset del valor
        }
      }
    }
  });
});

entregas.traerTransportadora = function () {
  let usuario = $.parseJSON(localStorage.getItem("usuario"));
  let self = this;

  ajaxRequest(
    { nit: usuario.nit },
    "post",
    "traerTransportadora",
    "entregas"
  ).done(function (response) {
    console.log(response);
    $.each(response, function (index, val) {
      $("#transportadora").append(
        '<option value="' +
          val.id_transportadora +
          '">' +
          val.nombre_transportadora +
          "</option>"
      );
    });
  });
};

entregas.grabar_ch = function (id_entrega, estado, fecha, observaciones) {
  var form = new FormData($("#formPedido")[0]);
  form.append("token", getToken());
  form.append("modulo", "entregas");
  form.append("metodo", "actualizardatos");
  form.append("id_entrega", id_entrega);
  form.append("estado", estado);
  form.append("fecha", fecha);
  form.append("observaciones", observaciones);
  $.ajax({
    type: "POST",
    url: "rest.php",
    dataType: "JSON",
    async: true,
    cache: false,
    contentType: false,
    processData: false,
    data: form,
    success: function (data) {
      console.log(data);
      if (data.mensaje == "exitoso") {
        toastr.success("Datos actualizados");
        let tipo = $("#transportadora").val();
        let estado = $("#estado").val();
        entregas.traerDataTransportadores(tipo, estado);
        $("#info-pedido").modal("hide");
      } else {
        toastr.error(data.mensaje);
      }
    },
  });
};

entregas.traerInfoData = function (id_entrega) {
  ajaxRequest(
    { id_entrega: id_entrega },
    "post",
    "traerInfoData",
    "entregas"
  ).done(function (response) {
    console.log(response);
    $("#entrega").val(response[0].id_entrega).change();
    $("#cliente").val(response[0].nombre_cliente).change();
    $("#estado_entrega").val(response[0].estado_entrega).change();
    $("#fecha_entrega").val(response[0].fecha_entrega).change();
    $("#info-pedido").modal("show");
  });
};

entregas.traerDataTransportadores = function (tipo, estado) {
  ajaxRequest(
    { tipo: tipo, estado: estado },
    "post",
    "traerDataTransportadores",
    "entregas"
  ).done(function (response) {
    $("#tabla-entregas tbody").html("");
    $.each(response, function (index, val) {
      console.log(val);
      $("#tabla-entregas tbody").append(
        "<tr>" +
          "<td>" +
          val.id_entrega +
          "</td>" +
          "<td>" +
          val.id_pedido +
          "</td>" +
          "<td>" +
          val.nombre_cliente +
          "</td>" +
          "<td>" +
          val.direccion +
          "</td>" +
          "<td>" +
          val.fecha_despacho +
          "</td>" +
          "<td>" +
          val.numero_guia +
          "</td>" +
          "<td>" +
          val.nombre_transportadora +
          "</td>" +
          "<td>" +
          val.estado_entrega +
          "</td>" +
          "<td>" +
          val.fecha_entrega +
          "</td>" +
          "<td><img src='static/images_guias/" +
          val.prueba_entrega +
          "'  style='width: 40px;'></td>" +
          "<td><a style='cursor:pointer;' data-toggle='lightbox' onClick='javascript:entregas.traerInfoData(" +
          val.id_entrega +
          ")' class='lightbox'><u>Ver Detalle</u></a> '" +
          "</td>" +
          "</tr>"
      );
    });
  });
};
