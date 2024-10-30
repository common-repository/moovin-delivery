var ajaxUrl = initMoovin.ajaxurl;
var tableDetail = null;
var map;
var formatter = new Intl.NumberFormat("en-US", {
  minimumFractionDigits: 2,
});
var page = {
  Init: function () {
    post = { action: "moovin_lib_handler", option: "op_datatable_order" };

    jQuery("#modal-dialog").on("shown.bs.modal", function () {
      google.maps.event.trigger(map, "resize");
    });

    jQuery("#close-modal").on("click", function () {
      jQuery("#modal-dialog").modal("toggle");
    });

    jQuery("#close-modal-qr").on("click", function () {
      jQuery("#modal-dialog-qr").modal("toggle");
    });

    jQuery("#close-modal-products").on("click", function () {
      jQuery("#modal-dialog-products").modal("toggle");
    });

    jQuery("#print-qr").on("click", function () {
      var mywindow = window.open("", "PRINT", "height=400,width=600");

      mywindow.document.write(document.getElementById("div-print-ticket").innerHTML);
      mywindow.document.close(); // necessary for IE >= 10
      myDelay = setInterval(checkReadyState, 100);

      function checkReadyState() {
        if (mywindow.document.readyState == "complete") {
          clearInterval(myDelay);
          mywindow.focus(); // necessary for IE >= 10

          mywindow.print();
          mywindow.close();
        }
      }
    });

    tableDetail = jQuery("#datatable-orders")
      .DataTable({
        language: {
          paging: false,
          lengthChange: false,
          sProcessing: "Procesando...",
          sLengthMenu: "Mostrar _MENU_ ordenes",
          sZeroRecords: "No se encontraron resultados",
          sEmptyTable: "Ningún dato disponible en esta tabla",
          sInfo: "Mostrando ordenes del _START_ al _END_ de un total de _TOTAL_ ",
          sInfoEmpty: "Mostrando ordenes de 0 al 0 de un total de 0",
          sInfoFiltered: "(filtrado de un total de _MAX_ ordenes)",
          sInfoPostFix: "",
          sSearch: "Buscar: ",
          sUrl: "",
          sInfoThousands: ",",
          sLoadingRecords: "Cargando...",
          processing: "Cargando...",
          oPaginate: {
            sFirst: "Primero",
            sLast: "Último",
            sNext: "Siguiente",
            sPrevious: "Anterior",
          },
          oAria: {
            sSortAscending: ": Activar para ordenar la columna de manera ascendente",
            sSortDescending: ": Activar para ordenar la columna de manera descendente",
          },
        },
        order: [[0, "desc"]],
        processing: true,
        select: {
          style: "multi",
          selector: "td:first-child",
        },
        //"serverSide": false,
        footerCallback: function (row, data, start, end, display) {},
        ajax: {
          url: ajaxUrl,
          type: "POST",
          data: post,
        },
        columns: [
          { data: "order_id", visible: true },
          { data: "date_order_created", visible: true },
          {
            data: "shipping_method",
            visible: true,
            render: function (data, type, row, meta) {
              var shipping = data.split("#");

              if (shipping[0] == "route") {
                return '<span class="badge rounded-pill bg-success">Envio en Ruta</span> ';
              } else if (shipping[0] == "Ondemand") {
                return '<span class="badge rounded-pill bg-info">Envio Express</span> ';
              } else {
                return '<span class="badge rounded-pill bg-danger">Tipo de envio no indentificado</span> ';
              }
            },
          },
          {
            data: "total_sales",
            visible: true,
            render: function (data, type, row, meta) {
              return row.currency + " " + formatter.format(data);
            },
          },
          {
            data: "shipping_total",
            visible: true,
            render: function (data, type, row, meta) {
              return row.currency + " " + formatter.format(data);
            },
          },
          { data: "contact_delivery", visible: true },
          { data: "phone_delivery", visible: true },
          { data: "email_account", visible: true },
          {
            data: null,
            render: function (data, type, row, meta) {
              if (row.fulfillment == "1") {
                return '<span class="badge rounded-pill bg-primary">Fulfillment</span> ';
              } else {
                return '<span class="text-center"><button type="button" class="btn btn-info show-collect">Ver Ubicación</button></span>';
              }
            },
          },
          {
            data: null,
            render: function (data, type, row, meta) {
              return '<span class="text-center"><button type="button" class="btn btn-info show-delivery">Ver Ubicación</button></span>';
            },
          },
          { data: "num_items_sold", visible: true },
          {
            data: "status_order_delivery_moovin",
            visible: true,
            render: function (data, type, row, meta) {
              if (row.status_order_delivery_moovin == "CREATED") {
                return '<span class="badge rounded-pill bg-warning">Orden Creada</span>';
              } else if (row.status_order_delivery_moovin == "READY") {
                return '<span class="badge rounded-pill bg-success">Orden Lista</span>';
              } else if (row.status_order_delivery_moovin == "CANCEL") {
                return '<span class="badge rounded-pill bg-danger">Orden Cancelada</span>';
              } else if (row.status_order_delivery_moovin == "CONFIRMATION") {
                return '<span class="badge rounded-pill bg-danger">Orden requiere confirmación de pago</span>';
              } else {
                // row.status_order_delivery_moovin == "PENDING" ||
                // row.status_order_delivery_moovin == "ERROR"
                return '<span class="badge rounded-pill bg-danger">Acción Requerida</span> ';
              }
            },
          },
          { data: "date_order_ready", visible: true },
          { data: "date_update_moovin", visible: true },
          {
            data: null,
            render: function (data, type, row, meta) {
              return '<span class="text-center"><button type="button" class="btn btn-primary qr-ticket">Imprimir Etiqueta</button></span>';
            },
          },
          {
            data: null,
            render: function (data, type, row, meta) {
              return '<span class="text-center"><button type="button" class="btn btn-info products">Ver productos</button></span>';
            },
          },
          {
            data: null,
            render: function (data, type, row, meta) {
              if (row.status_order_delivery_moovin == "CREATED") {
                if (row.fulfillment == "0") {
                  return (
                    '<span class="text-center"><button type="button" class="btn btn-success request-collect">Solicitar Recolección</button></span>' +
                    '<br><br><span class="text-center"><button type="button" class="btn btn-danger cancel-collect">Cancelar Recolección</button></span>'
                  );
                } else {
                  return '<span class="text-center"><button type="button" class="btn btn-danger cancel-collect">Cancelar Recolección</button></span>';
                }
              } else if (row.status_order_delivery_moovin == "READY") {
                return '<span class="text-center"><button type="button" class="btn btn-success status-ready">Ver estado</button></span>';
              } else if (row.status_order_delivery_moovin == "CANCEL") {
                return '<span class="badge rounded-pill bg-danger">Orden Cancelada</span>';
              } else if (row.status_order_delivery_moovin == "CONFIRMATION") {
                return (
                  '<span class="text-center"><button type="button" class="btn btn-warning confirm-order">Confirmar Orden</button></span><br>' +
                  '<span class="badge rounded-pill bg-danger">Orden requiere confirmación de pago</span>'
                );
              } else {
                return (
                  '<span class="text-center"><button type="button" class="btn btn-warning create-order">Crear Orden</button></span><br>' +
                  '<span class="badge rounded-pill bg-danger">Acción requerida debe crear la orden</span>'
                );
              }
            },
          },
        ],
      })
      .on("select", function (e, dt, type, indexes) {})
      .on("deselect", function (e, dt, type, indexes) {})
      .on("click", ".products", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        page.loadProducts(info);
      })
      .on("click", ".qr-ticket", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();

        if (info.status_order_delivery_moovin == "CREATED" || info.status_order_delivery_moovin == "READY") {
          jQuery("#r_package").html("ID Paquete:" + info.id_package_moovin);
          jQuery("#r_name").html(info.contact_delivery);
          jQuery("#r_phone").html(info.phone_delivery);
          jQuery("#r_address").html(info.location_alias_delivery);
          jQuery("#r_notes").html(info.notes_delivery);

          jQuery("#qr-preview").prop("src", "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" + info.qr_code);
          jQuery("#modal-dialog-qr").modal("toggle");
        } else if (info.status_order_delivery_moovin == "CONFIRMATION") {
          utility.alert(
            "error",
            "Error",
            "La impresión de etiqueta no se encuentra disponible, por favor confirme la orden para continuar"
          );
        } else if (info.status_order_delivery_moovin == "CANCEL") {
          utility.alert("error", "Error", "La impresión de etiqueta no se encuentra disponible, la orden fue cancelada");
        } else {
          utility.alert("error", "Error", "La impresión de etiqueta no se encuentra disponible, por favor cree la orden para continuar");
        }
      })
      .on("click", ".show-collect", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        jQuery("#title-location").html("Punto de Recolección");

        page.initializeMap(info.latitude_collect, info.longitude_collect);
        jQuery("#cf_notes").val(info.notes_collect);
        jQuery("#cf_address").val(info.location_alias_collect);
        jQuery("#modal-dialog").modal("toggle");
      })
      .on("click", ".show-delivery", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        jQuery("#title-location").html("Punto de entrega");

        page.initializeMap(info.latitude_delivery, info.longitude_delivery);
        jQuery("#cf_notes").val(info.notes_delivery);
        jQuery("#cf_address").val(info.location_alias_delivery);
        jQuery("#modal-dialog").modal("toggle");
      })
      .on("click", ".request-collect", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        page.requestCollect(info);
      })
      .on("click", ".cancel-collect", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        page.cancelCollect(info);
      })
      .on("click", ".status-ready", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        page.checkStatus(info);
      })
      .on("click", ".create-order", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        page.confirmCreateOrderError(info);
      })
      .on("click", ".confirm-order", function () {
        var tr = jQuery(this).closest("tr");
        info = tableDetail.row(tr).data();
        page.confirmCreateOrder(info);
      });
  },
  confirmCreateOrder(row) {
    utility.confirm2(
      "info",
      "Confirmación",
      "Confirma que el paquete ya se encuentra pagado y desea crear la orden de recolección?",
      function (confirm) {
        if (confirm.value) {
          global.options.url = ajaxUrl;
          global.options.data = {
            action: "moovin_lib_handler",
            option: "op_order_confirmation",
            id_order: row.order_id,
          };
          var response = global.Ajax(global.options);
          response
            .then(function (response) {
              console.log(response);
              var data = JSON.parse(response);
              if (data.error) {
                utility.alert("error", "Error", data.message);
              } else {
                jQuery("#datatable-orders").DataTable().ajax.reload();
                utility.alert("success", "Acción Completada", data.message);
              }
              global.options.data = {};
            })
            .catch(function (error) {
              utility.alert("error", "Ha ocurrido un error, si el problema persiste contacte con el administrador", error.message);
              global.options.data = {};
            });
        }
      },
      "Confirmar Pago"
    );
  },
  confirmCreateOrderError(row) {
    utility.confirm2(
      "info",
      "Confirmación",
      "Confirma desea crear la orden de recolección?",
      function (confirm) {
        if (confirm.value) {
          global.options.url = ajaxUrl;
          global.options.data = {
            action: "moovin_lib_handler",
            option: "op_order_create",
            id_order: row.order_id,
          };
          var response = global.Ajax(global.options);
          response
            .then(function (response) {
              console.log(response);
              var data = JSON.parse(response);
              if (data.error) {
                utility.alert("error", "Error", data.message);
              } else {
                jQuery("#datatable-orders").DataTable().ajax.reload();
                utility.alert("success", "Acción Completada", data.message);
              }
              global.options.data = {};
            })
            .catch(function (error) {
              utility.alert("error", "Ha ocurrido un error, si el problema persiste contacte con el administrador", error.message);
              global.options.data = {};
            });
        }
      },
      "Crear Orden"
    );
  },
  checkStatus: function (row) {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_check_status",
      id_order: row.order_id,
      id_package: row.id_package_moovin,
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        console.log(response);
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          jQuery("#datatable-orders").DataTable().ajax.reload();
          utility.alert("success", data.estado, data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert("error", "Ha ocurrido un error, si el problema persiste contacte con el administrador", error.message);
        global.options.data = {};
      });
  },
  cancelCollect: function (row) {
    console.log(row);
    utility.confirm2(
      "info",
      "Confirmación",
      "Confirma que desea cancelar la order de recolección?",
      function (confirm) {
        if (confirm.value) {
          global.options.url = ajaxUrl;
          global.options.data = {
            action: "moovin_lib_handler",
            option: "op_order_cancel",
            id_order: row.order_id,
            id_package: row.id_package_moovin,
          };
          var response = global.Ajax(global.options);
          response
            .then(function (response) {
              console.log(response);
              var data = JSON.parse(response);
              if (data.error) {
                utility.alert("error", "Error", data.message);
              } else {
                jQuery("#datatable-orders").DataTable().ajax.reload();
                utility.alert("success", "Acción Completada", data.message);
              }
              global.options.data = {};
            })
            .catch(function (error) {
              utility.alert("error", "Ha ocurrido un error, si el problema persiste contacte con el administrador", error.message);
              global.options.data = {};
            });
        }
      },
      "Cancelar Recolección"
    );
  },
  requestCollect: function (row) {
    console.log(row);
    utility.confirm2(
      "info",
      "Confirmación",
      "Confirma que el paquete ya se encuentra listo para ser recogido por un Moover (mensajero)?",
      function (confirm) {
        if (confirm.value) {
          global.options.url = ajaxUrl;
          global.options.data = {
            action: "moovin_lib_handler",
            option: "op_order_ready",
            id_order: row.order_id,
            id_package: row.id_package_moovin,
            email: row.email_account,
          };
          var response = global.Ajax(global.options);
          response
            .then(function (response) {
              console.log(response);
              var data = JSON.parse(response);
              if (data.error) {
                utility.alert("error", "Error", data.message);
              } else {
                jQuery("#datatable-orders").DataTable().ajax.reload();
                utility.alert("success", "Acción Completada", data.message);
              }
              global.options.data = {};
            })
            .catch(function (error) {
              utility.alert("error", "Ha ocurrido un error, si el problema persiste contacte con el administrador", error.message);
              global.options.data = {};
            });
        }
      },
      "Solicitar Recolección"
    );
  },
  loadProducts: function (row) {
    if (jQuery.fn.DataTable.isDataTable("#datatable-products")) {
      jQuery("#datatable-products").DataTable().destroy();
    }

    post = {
      action: "moovin_lib_handler",
      option: "op_datatable_products",
      order_id: row.order_id,
    };

    jQuery("#datatable-products").DataTable({
      language: {
        paging: false,
        lengthChange: false,
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ productos",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "Ningún dato disponible en esta tabla",
        sInfo: "Mostrando productos del _START_ al _END_ de un total de _TOTAL_ ",
        sInfoEmpty: "Mostrando productos de 0 al 0 de un total de 0",
        sInfoFiltered: "(filtrado de un total de _MAX_ productos)",
        sInfoPostFix: "",
        sSearch: "Buscar: ",
        sUrl: "",
        sInfoThousands: ",",
        sLoadingRecords: "Cargando...",
        processing: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
        oAria: {
          sSortAscending: ": Activar para ordenar la columna de manera ascendente",
          sSortDescending: ": Activar para ordenar la columna de manera descendente",
        },
      },
      order: [[2, "desc"]],
      processing: true,
      select: {
        style: "multi",
        selector: "td:first-child",
      },
      //"serverSide": false,
      footerCallback: function (row, data, start, end, display) {},
      ajax: {
        url: ajaxUrl,
        type: "POST",
        data: post,
      },
      columns: [
        { data: "code_product", visible: true },
        { data: "name_product", visible: true },
        {
          data: "quantity",
          visible: true,
          render: function (data, type, row, meta) {
            return '<span class="badge bg-info">' + data + "</span>";
          },
        },
        {
          data: "price",
          visible: true,
          render: function (data, type, row, meta) {
            return "₡ " + formatter.format(data);
          },
        },
      ],
    });

    jQuery("#modal-dialog-products").modal("toggle");
  },
  initializeMap: function (lat, lng) {
    var mapOptions = {
      center: new google.maps.LatLng(lat, lng),
      zoom: 15,
      mapTypeId: google.maps.MapTypeId.terrain,
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(lat, lng),
    });
    marker.setMap(map);
  },
};

jQuery(document).ready(function () {
  page.Init();
});
