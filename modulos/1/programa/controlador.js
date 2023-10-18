$(document).ready(function () {
    traerMenus();
    traerComponentes();

    $("#programa").change(function (event) {
        buscarPrograma($("#programa").val());
    });

    $("#btn-grabar-general").click(function (event) {
        //validar campos llenos
        GrabarGeneral();
    });

    $("#btn-eliminar-general").click(function (event) {
        toastr.info(
            'Esta seguro de eliminar la aplicacion?. <button type="button" id="okBtn" onclick=eliminarPrograma($("#programa").val()) class="btn btn-flat btn-danger toastr-action">SI</button>',
            ""
        );
    });

    $("#btn-grabar-permisos").click(function (event) {
        GrabarPermisos();
    });

    $("#btn-grabar-componentes").click(function (event) {
        GrabarComponentes();
    });

    $("#btn-agregar-permiso").click(function (event) {
        agregarPermiso();
    });

    $(".select2").select2();
});

function traerMenus() {
    $.ajax({
        type: "POST",
        url: "rest.php",
        dataType: "json",
        async: true,
        data: { modulo: "menu", metodo: "traerMenu", token: getToken() },
        success: function (data) {
            $("#menu-programa").html('<option value="0">/</option>');
            $.each(data, function (index, val) {
                $("#menu-programa").append(
                    "<option value=" +
                        val.codigo +
                        ">/" +
                        val.nombre +
                        "</option>"
                );
                $.each(val.sub, function (index, val1) {
                    $("#menu-programa").append(
                        "<option value=" +
                            val1.codigo +
                            ">/" +
                            val.nombre +
                            "/" +
                            val1.nombre +
                            "</option>"
                    );
                    $.each(val1.sub, function (index, val2) {
                        $("#menu-programa").append(
                            "<option value=" +
                                val2.codigo +
                                ">/" +
                                val.nombre +
                                "/" +
                                val1.nombre +
                                "/" +
                                val2.nombre +
                                "</option>"
                        );
                        $.each(val2.sub, function (index, val3) {
                            $("#menu-programa").append(
                                "<option value=" +
                                    val3.codigo +
                                    ">/" +
                                    val.nombre +
                                    "/" +
                                    val1.nombre +
                                    "/" +
                                    val2.nombre +
                                    "/" +
                                    val3.nombre +
                                    "</option>"
                            );
                        });
                    });
                });
            });
            $("#menu-programa").trigger("change");
        },
    });
}

function traerComponentes() {
    ajaxRequest({}, "post", "traerComponentes", "programa").done(function (
        response
    ) {
        $("#frm-componentes .card-body").html("");
        $.each(response, function (index, val) {
            $("#frm-componentes .card-body").append(
                '<div class="col-md-6">' +
                    '<div class="checkbox checkbox-css">' +
                    '<input type="checkbox" name="componentes" class="componentes" id="componente_' +
                    index +
                    '" value="' +
                    index +
                    '" />' +
                    '<label for="componente_' +
                    index +
                    '">' +
                    val.descripcion +
                    "</label>" +
                    "</div>" +
                    "</div>"
            );
        });
        $(".checkbox-styled input, .radio-styled input").each(function () {
            if ($(this).next("span").length === 0) {
                $(this).after("<span></span>");
            }
        });
    });
}

function agregarPermiso(permiso, descripcion) {
    permiso = permiso || "A";
    descripcion = descripcion || "Acceso";
    $("#frm-permisos tbody").append(
        "<tr>" +
            "<td><input type='text' class='form-control' name='permiso' value='" +
            permiso +
            "'  size=2></td>" +
            "<td><input type='text' class='form-control' name='descripcion' value='" +
            descripcion +
            "' placeHolder='Descripcion del permiso'></td>" +
            "</tr>"
    );
}

function buscarPrograma(programa) {
    ajaxRequest(
        { programa: programa },
        "post",
        "buscarPrograma",
        "programa"
    ).done(function (response) {
        if (response.existe == "S") {
            $("#programa").addClass("alert-success");
            $("#fieldset-nueva-aplicacion").hide();
        } else {
            $("#programa").removeClass("alert-success");
            $("#fieldset-nueva-aplicacion").show();
        }
        $("#programa").val(response.programa);
        $("#descripcion").val(response.descripcion);
        $("#menu-programa").val(response.menu).trigger("change");
        $("#submenu-programa").val(response.submenu).trigger("change");
        $("#xajaxDefault").val(response.xajaxDefault);
        var autenticado = true;
        if (response.autenticado != "S") autenticado = false;
        $("#autenticado").prop("checked", autenticado);

        $("#frm-permisos tbody").html("");
        $.each(response.permisos, function (codigo, nombre) {
            agregarPermiso(codigo, nombre);
        });

        $(".componentes").prop("checked", false);
        $.each(response.componentes, function (id, nombre) {
            if (nombre != "") {
                $("#componente_" + nombre).prop("checked", true);
            }
        });
    });
}
//GRABA EL PRIMER PERMISO DE ACCESO
function grabarPermisos(nombre, permisos) {
    ajaxRequest(
        { nombre: nombre, permisos: permisos },
        "post",
        "grabarPermisos",
        "rol"
    ).done(function (response) {});
}

function eliminarPrograma(programa) {
    ajaxRequest(
        { programa: programa },
        "post",
        "eliminarPrograma",
        "programa"
    ).done(function (response) {
        toastr.success("Programa " + programa + " eliminado");
        buscarPrograma(programa);
    });
}

function GrabarGeneral() {
    if ($("#frm").valid() || $("#frm-permisos").valid()) {
        var form = Core.FormToJSON("#frm");
        ajaxRequest({ form: form }, "post", "grabarGeneral", "programa").done(
            function (response) {
                if (response.permiso) {
                    var permiso = new Object();
                    permiso["0"] = response.permiso;
                    grabarPermisos("ADMINISTRADOR", permiso);
                }
                toastr.success("Programa " + response.programa + " grabado.");
            }
        );
    } else {
        toastr.warning("El formulario aun contiene errores, verifique");
    }
}

function GrabarPermisos() {
    var form = Core.FormToJSON("#frm-permisos");
    var programa = $("#programa").val();
    ajaxRequest(
        { form: form, programa: programa },
        "post",
        "GrabarPermisos",
        "programa"
    ).done(function (response) {
        toastr.success("Permisos grabados. ( " + response + " )");
    });
}

function GrabarComponentes() {
    var form = Core.FormToJSON("#frm-componentes");
    var programa = $("#programa").val();
    ajaxRequest(
        { form: form, programa: programa },
        "post",
        "GrabarComponentes",
        "programa"
    ).done(function (response) {
        toastr.success("Componentes grabados. ( " + response + " )");
    });
}
