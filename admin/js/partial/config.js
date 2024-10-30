var ajaxUrl = initMoovin.ajaxurl;
var statusEnv = null;
var config = null;
var googlemaps = null;
let mapzone;
var zones = null;
var page = {
  goOption: function (option) {
    switch (option) {
      case 1:
        jQuery("#bind-status-click").trigger("click");
        break;
      case 2:
        jQuery("#bind-keys-click").trigger("click");
        break;
      case 3:
        jQuery("#bind-config-click").trigger("click");
        break;
    }
  },
  Init: function () {
    [].slice
      .call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      .map(function (e) {
        return new bootstrap.Tooltip(e);
      });

    jQuery(".info-dialog").bind("click", function () {
      utility.alert(
        "info",
        jQuery(this).data("title"),
        jQuery(this).data("body")
      );
    });

    jQuery("#save-status").bind("click", function () {
      page.changeStatus();
    });

    jQuery("#save-sandbox").bind("click", function () {
      page.saveCredentialSandBox();
    });

    jQuery("#save-prod").bind("click", function () {
      page.saveCredentialProd();
    });

    jQuery("#save-google").bind("click", function () {
      page.saveGoogle();
    });

    jQuery("#save-here").bind("click", function () {
      page.saveHere();
    });

    jQuery("#save-default-location").bind("click", function () {
      page.saveDefaultLocation();
    });

    jQuery("#save-package").bind("click", function () {
      page.savePackageDefault();
    });

    jQuery("#bind-status").bind("click", function () {
      jQuery("#notificaciones-status").html("");
      page.loadStatus();
    });

    jQuery("#bind-keys").bind("click", function () {
      jQuery("#notificaciones-status").html("");
      page.loadKeysInfo();
    });

    jQuery("#save-contact").bind("click", function () {
      jQuery("#notificaciones-status").html("");
      page.saveContact();
    });

    jQuery("#save-task").bind("click", function () {
      jQuery("#notificaciones-status").html("");
      page.saveTask();
    });

    jQuery("#save-general").bind("click", function () {
      jQuery("#notificaciones-status").html("");
      page.saveGeneral();
    });

    jQuery("#bind-config").bind("click", function () {
      jQuery("#notificaciones-status").html("");
      if (self.googlemaps) {
        page.loadConfigurations();
      } else {
        utility.alert(
          "error",
          "Mapa no disponible",
          "No se ha configurado el key de google maps, configurelo primero para configurar la ubicación de recolección"
        );
      }
    });

    jQuery("#bind-zone").bind("click", function () {
      jQuery("#notificaciones-status").html("");
      page.loadZoneDefault();
    });

    page.loadStatus();
  },
  loadZoneDefault: function () {
    if (self.zones != null) {
      page.initZoneMap(self.zones.points);
      return;
    }

    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_load_zones",
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        console.log(data);

        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          self.zones = data;
          page.initZoneMap(self.zones.points);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  initZoneMap: function (points) {
    self.mapzone = new google.maps.Map(document.getElementById("mapzone"), {
      zoom: 13,
      center: { lat: 24.886, lng: -70.268 },
      mapTypeId: "roadmap",
    });

    points.forEach(function (zone) {
      console.log(zone.zoneData);
      const moovinZone = new google.maps.Polygon({
        paths: zone.zoneData,
        strokeColor: "#898989",
        strokeOpacity: 0.8,
        strokeWeight: 3,
        fillColor: "#898989",
        fillOpacity: 0.35,
      });
      moovinZone.setMap(self.mapzone);
    });

    // Construct the polygon.
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0; i < points.length; i++) {
      var boundZones = points[i];
      boundZones.zoneData.forEach(function (data) {
        bounds.extend(data);
      });
    }
    self.mapzone.fitBounds(bounds);
  },
  saveTask: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_task",
      document: jQuery("#cf_id_photo").prop("checked") ? "1" : "0",
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  saveGeneral: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_general",
      autocollect: jQuery("#cf_collect_auto").prop("checked") ? "1" : "0",
      expressservices: jQuery("#cf_express_services").prop("checked")
        ? "1"
        : "0",
      routeservices: jQuery("#cf_route_services").prop("checked") ? "1" : "0",
      emailnotification: jQuery("#cf_email_notification").prop("checked")
        ? "1"
        : "0",
      inithour: jQuery("#cf_hour_init").val(),
      finalhour: jQuery("#cf_hour_final").val(),
      extend: jQuery("#cf_extend_mode").prop("checked") ? "extend" : "simple",
      outzone: jQuery("#cf_out_zone").prop("checked") ? "1" : "0",
      woocommerce: jQuery("#cf_woocommerce_zone").prop("checked") ? "1" : "0",
      payments: jQuery("#cf_payment_confirm_method").val(),
      expressname: jQuery("#cf_express_name").val(),
      routesname: jQuery("#cf_route_name").val(),
      amountpromo: jQuery("#cf_amount_promo").val(),
      statuspromo: jQuery("#cf_status_promo").prop("checked") ? "1" : "0",
      statuspromoruta: jQuery("#cf_status_promo_ruta").prop("checked")
        ? "1"
        : "0",
      paymentsmoney: jQuery("#cf_payment_money_in_site").val(),
      paymentscard: jQuery("#cf_payment_card_in_site").val(),
      round: jQuery("#cf_amount_round").prop("checked") ? "1" : "0",
      amountadd: jQuery("#cf_amount_add").val(),
      tcauto: jQuery("#cf_tc_auto").prop("checked") ? "1" : "0",
      tcvalue: jQuery("#cf_tc_value").val(),
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  saveContact: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_contact",
      name: jQuery("#cf_name_contact").val(),
      phone: jQuery("#cf_phone_contact").val(),
      email: jQuery("#cf_email_contact").val(),
      notes: jQuery("#cf_note_contact").val(),
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  savePackageDefault: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_default_package",
      size: jQuery("#pkg_size").val(),
      weight: jQuery("#pkg_weight").val(),
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  saveDefaultLocation: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_default_location",
      lat: jQuery("#cf_lat").val(),
      lng: jQuery("#cf_lng").val(),
      name: jQuery("#cf_address").val(),
      fulfillment: jQuery("#cf_fullfillment").prop("checked") ? "1" : "0",
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  loadConfigurations: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_load_configurations",
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          if (jQuery(".payment-select2").data("select2")) {
            jQuery(".payment-select2").select2("destroy");
          }

          jQuery("#cf_payment_money_in_site").empty();
          jQuery("#cf_payment_card_in_site").empty();
          jQuery("#cf_payment_confirm_method").empty();

          jQuery.each(data.payments, function (key, entry) {
            //Confirmation method
            var found =
              data.paymentcodes.find((metodo) => metodo === entry.code) != null
                ? true
                : false;

            var option = jQuery("<option></option>")
              .attr("value", entry.code)
              .attr("selected", found)
              .text(entry.name);

            jQuery("#cf_payment_confirm_method").append(option);

            //Money method

            var foundMoney =
              data.paymentMoney.find((metodo) => metodo === entry.code) != null
                ? true
                : false;

            var option = jQuery("<option></option>")
              .attr("value", entry.code)
              .attr("selected", foundMoney)
              .text(entry.name);

            jQuery("#cf_payment_money_in_site").append(option);

            //Card method

            var foundCard =
              data.paymentCard.find((metodo) => metodo === entry.code) != null
                ? true
                : false;

            var option = jQuery("<option></option>")
              .attr("value", entry.code)
              .attr("selected", foundCard)
              .text(entry.name);

            jQuery("#cf_payment_card_in_site").append(option);
          });

          jQuery("#cf_express_name").val(data.expressname);
          jQuery("#cf_route_name").val(data.routesname);
          jQuery("#cf_status_promo").prop("checked", data.statusPromo == "1");
          jQuery("#cf_status_promo_ruta").prop(
            "checked",
            data.statusPromoRuta == "1"
          );

          jQuery("#cf_amount_promo").val(data.amountPromo);

          jQuery("#cf_amount_add").val(data.amountadd);
          jQuery("#cf_amount_round").prop("checked", data.round == "1");

          jQuery(".payment-select2").select2({ width: "100%" });

          jQuery("#latitud").val(data.location.lat);
          jQuery("#longitud").val(data.location.lng);

          jQuery("#pkg_size").val(data.package.size);
          jQuery("#pkg_weight").val(data.package.weight);

          jQuery("#cf_name_contact").val(data.contact.name);
          jQuery("#cf_phone_contact").val(data.contact.value);
          jQuery("#cf_note_contact").val(data.contact.value1);
          jQuery("#cf_email_contact").val(data.contact.value2);

          jQuery("#cf_collect_auto").prop("checked", data.collectAuto == "1");
          jQuery("#cf_id_photo").prop("checked", data.documentTask == "1");
          jQuery("#cf_fullfillment").prop("checked", data.fulfillment == "1");

          jQuery("#cf_express_services").prop("checked", data.express == "1");
          jQuery("#cf_route_services").prop("checked", data.routes == "1");

          jQuery("#cf_email_notification").prop(
            "checked",
            data.emailnotification == "1"
          );

          jQuery("#cf_hour_init").val(data.initdate);
          jQuery("#cf_hour_final").val(data.finaldate);

          jQuery("#cf_tc_value").val(data.tcvalue);
          jQuery("#cf_tc_auto").prop("checked", data.tcauto == "1");

          jQuery("#cf_extend_mode").prop("checked", data.mode == "extend");
          jQuery("#cf_out_zone").prop("checked", data.outzone == "1");
          jQuery("#cf_woocommerce_zone").prop(
            "checked",
            data.woocommerce == "1"
          );

          jQuery("#mappicker").locationpicker({
            location: {
              latitude: data.location.lat,
              longitude: data.location.lng,
            },
            radius: 0,
            inputBinding: {
              latitudeInput: jQuery("#cf_lat"),
              longitudeInput: jQuery("#cf_lng"),
              locationNameInput: jQuery("#cf_address"),
            },
            markerInCenter: true,
            enableAutocomplete: true,
          });
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  loadKeysInfo: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_load_all_credentials",
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          config = data;
          jQuery("#cf_username_sandbox").val(data.sandbox.username);
          jQuery("#cf_password_sandbox").val(data.sandbox.password);
          jQuery("#status-sandbox").val("");

          jQuery("#cf_username_prod").val(data.prod.username);
          jQuery("#cf_password_prod").val(data.prod.password);
          jQuery("#status-prod").val("");

          jQuery("#cf_google_maps").val(data.googlemaps.key);
          jQuery("#cf_zoom_google_maps").val(data.googlemaps.zoom);

          jQuery("#cf_google_location_map").prop(
            "checked",
            data.googlemaps.location == "1"
          );
          jQuery("#cf_google_status").prop(
            "checked",
            data.googlemaps.status == "1"
          );

          jQuery("#cf_key_here").val(data.heremaps.key);
          jQuery("#cf_zoom_here").val(data.heremaps.zoom);
          jQuery("#cf_here_location_map").prop(
            "checked",
            data.heremaps.location == "1"
          );
          jQuery("#cf_here_status").prop(
            "checked",
            data.heremaps.status == "1"
          );
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  saveCredentialSandBox: function () {
    if (jQuery("#cf_username_sandbox").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique el usuario para el ambiente Sandbox"
      );
      return false;
    }

    if (jQuery("#cf_password_sandbox").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique la contraseña para el ambiente Sandbox"
      );
      return false;
    }

    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_credential_sandbox",
      username: jQuery("#cf_username_sandbox").val(),
      password: jQuery("#cf_password_sandbox").val(),
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          jQuery("#status-sandbox").html("Conectado");
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  saveCredentialProd: function () {
    if (jQuery("#cf_username_prod").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique el usuario para el ambiente producción"
      );
      return false;
    }

    if (jQuery("#cf_password_prod").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique la contraseña para el ambiente producción"
      );
      return false;
    }

    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_credential_prod",
      username: jQuery("#cf_username_prod").val(),
      password: jQuery("#cf_password_prod").val(),
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          jQuery("#status-prod").html("Conectado");
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  saveGoogle: function () {
    if (jQuery("#cf_google_maps").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique el key de mapas de Google"
      );
      return false;
    }

    if (jQuery("#cf_zoom_google_maps").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique el zoom para el mapa"
      );
      return false;
    }

    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_google_maps",
      googlekey: jQuery("#cf_google_maps").val(),
      zoom: jQuery("#cf_zoom_google_maps").val(),
      location: jQuery("#cf_google_location_map").prop("checked") ? "1" : "0",
      status: jQuery("#cf_google_status").prop("checked") ? "1" : "0",
    };

    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  saveHere: function () {
    if (jQuery("#cf_key_here").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique el key de mapas de Here"
      );
      return false;
    }

    if (jQuery("#cf_zoom_here").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por indique el zoom para el mapa"
      );
      return false;
    }

    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_save_here_maps",
      herekey: jQuery("#cf_key_here").val(),
      zoom: jQuery("#cf_zoom_here").val(),
      location: jQuery("#cf_here_location_map").prop("checked") ? "1" : "0",
      status: jQuery("#cf_here_status").prop("checked") ? "1" : "0",
    };

    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          utility.alert("success", "Acción Completada", data.message);
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  loadStatus: function () {
    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_load_status",
    };
    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        self.statusEnv = data;
        if (data.error) {
          utility.alert("error", "Ha ocurrido un Error", data.message);
        } else {
          jQuery("#notificaciones-status").html("");
          jQuery("#complete-install").html("");

          if (!data.sandbox) {
            jQuery("#notificaciones-status").append(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                "Las credenciales de pruebas no se encuentran configuradas!" +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                "</div>"
            );
          }

          if (!data.prod) {
            jQuery("#notificaciones-status").append(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                "Las credenciales de produccion no se encuentran configuradas!" +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                "</div>"
            );
          }

          if (!data.googlemaps) {
            jQuery("#notificaciones-status").append(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                "No se encuentra configurado aun ningún mapa!" +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                "</div>"
            );
          }

          if (!data.pckgsize) {
            jQuery("#notificaciones-status").append(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                "El tamaño del paquete por defecto no se encuentra configurado!" +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                "</div>"
            );
          }

          if (!data.pckgweight) {
            jQuery("#notificaciones-status").append(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                "El peso del paquete por defecto no se encuentra configurado!" +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                "</div>"
            );
          }

          if (!data.location) {
            jQuery("#notificaciones-status").append(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                "La ubicación por defecto no se encuentra configurada!" +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                "</div>"
            );
          }

          if (!data.collect_inside) {
            jQuery("#notificaciones-status").append(
              '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                "La ubicación de recolección se sale de nuestra area de cobertura!" +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                "</div>"
            );
          }

          jQuery("#complete-install").html(data.complete_install);

          self.googlemaps = data.googlemaps;
          jQuery("#sp-country").val(data.country);

          if (data.env == "SANDBOX") {
            jQuery("#sp-env").val(data.env);
            if (
              data.sandbox &&
              data.googlemaps &&
              data.pckgsize &&
              data.pckgweight &&
              data.location
            ) {
              jQuery("#status-env").prop("checked", data.status_env == "1");
              jQuery("#status-env").prop("checked", data.status_env == "1");
            } else {
              jQuery("#status-env").prop("checked", false);
              jQuery("#msg-status").html(
                "Para activar debes de completar todas las configuraciones necesarias!"
              );
            }
          }

          if (data.env == "PROD") {
            jQuery("#sp-env").val(data.env);
            if (
              data.prod &&
              data.googlemaps &&
              data.pckgsize &&
              data.pckgweight &&
              data.location
            ) {
              jQuery("#status-env").prop("checked", data.status_env == "1");
            } else {
              jQuery("#status-env").prop("checked", false);
              jQuery("#msg-status").html(
                "Para activar debes de completar todas las configuraciones necesarias!"
              );
            }
          }
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
  changeStatus: function () {
    if (jQuery("#sp-env").val() === "") {
      utility.alert(
        "error",
        "Error",
        "Por favor seleccione un ambiente a conectar"
      );
      return;
    }

    if (
      jQuery("#sp-env").val() == "SANDBOX" &&
      jQuery("#status-env").prop("checked")
    ) {
      if (
        !self.statusEnv.sandbox ||
        !self.statusEnv.googlemaps ||
        !self.statusEnv.pckgsize ||
        !self.statusEnv.pckgweight ||
        !self.statusEnv.location
      ) {
        utility.alert(
          "error",
          "Error",
          "Para activar el plugin en (Pruebas) debes completar todas las configuraciones primero"
        );
        return;
      }
    }

    if (
      jQuery("#sp-env").val() == "PROD" &&
      jQuery("#status-env").prop("checked")
    ) {
      if (
        !self.statusEnv.prod ||
        !self.statusEnv.googlemaps ||
        !self.statusEnv.pckgsize ||
        !self.statusEnv.pckgweight ||
        !self.statusEnv.location
      ) {
        utility.alert(
          "error",
          "Error",
          "Para activar el plugin en (Producción) debes completar todas las configuraciones primero"
        );
        return;
      }
    }

    global.options.url = ajaxUrl;
    global.options.data = {
      action: "moovin_lib_handler",
      option: "op_change_status",
      env: jQuery("#sp-env").val(),
      country: jQuery("#sp-country").val(),
      status_env: jQuery("#status-env").prop("checked") == true ? "1" : "0",
    };

    var response = global.Ajax(global.options);
    response
      .then(function (response) {
        var data = JSON.parse(response);
        if (data.error) {
          utility.alert("error", "Error", data.message);
        } else {
          jQuery("#lbl-status").addClass("text-success");
          jQuery("#lbl-status").html("Activo");
          utility.alert("success", "Acción Completada", data.message);
          page.loadStatus();
        }
        global.options.data = {};
      })
      .catch(function (error) {
        utility.alert(
          "error",
          "Ha ocurrido un error, si el problema persiste contacte con el administrador",
          error.message
        );
        global.options.data = {};
      });
  },
};

jQuery(document).ready(function () {
  page.Init();
});
