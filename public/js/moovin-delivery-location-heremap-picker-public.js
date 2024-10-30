(function () {
  "use strict";
  var herekey = moovinhere.herekey;

  let enabled_section;
  var map, marker;
  var sg_address = {};
  var behavior;
  var platform;
  var section;
  // temp storage of new address data
  jQuery(document).ready(() => {
    enabled_section = jQuery("#sg_del_add_status").val();

    setInterval(function () {
      jQuery(".sg_del_add_hidden_fields").hide();
    }, 1000);

    // popup open
    jQuery(".sg-del-add-add-new-opt").on(
      "click",
      sgitsdlmp_address_create_popup_window_open
    );

    // popup close when click outside
    jQuery(".sg-del-add-overlay").on(
      "click",
      sgitsdlmp_address_create_popup_window_close
    );

    // popup close when click close button
    jQuery(".sg-popup-close-button").on("click", function () {
      sgitsdlmp_address_create_popup_window_close(
        jQuery(this).attr("data-type")
      );
    });

    // address type if change to other specify input shown else set input value
    jQuery('.sg-address-type input[name="sg_address_type"]').on(
      "change",
      (e) => {
        if (e.target.id.includes("other")) {
          sgitsdlmp_toggle_address_type();
        } else {
          jQuery('input[name="sg_new_address_type"]').val(e.target.value);
        }
      }
    );

    // when cancel specify other type of address
    jQuery(".sg-del-add-type-other").on("click", () => {
      sgitsdlmp_toggle_address_type();
    });

    // prevent closing popup when clicked in the form
    jQuery("#sg_delivery_address_billing_popup_panel").on("click", (e) => {
      e.stopImmediatePropagation();
    });
    jQuery("#sg_delivery_address_shipping_popup_panel").on("click", (e) => {
      e.stopImmediatePropagation();
    });

    // trigger form submit
    jQuery(".sg-submit-btn").on("click", (e) => {
      e.stopImmediatePropagation();
      sgitsdlmp_add_new_sg_address(
        e.target.id.includes("billing") ? "billing" : "shipping"
      );
    });

    var req_input_list = [...jQuery(".sg-field input")].filter((field) =>
      jQuery(field).attr("required")
    );
    for (const input of req_input_list) {
      jQuery(input).on("input", function () {
        if (this.value.split(" ").join("").length > 0) {
          if (jQuery(this).parents(".sg-field").hasClass("has-error")) {
            jQuery(this).parents(".sg-field").removeClass("has-error");
          }
        } else {
          jQuery(this).parents(".sg-field").addClass("has-error");
        }
      });
    }

    // remove address
    jQuery(document).on("click", ".sg-remove-button", sgitsdlmp_remove_address);

    // input focus event of popup forms
    jQuery(".sg-field input").on("focus", (e) => {
      jQuery(".sg-field label[for=" + e.target.id + "]").addClass("active");
    });

    // input blur event of popup forms
    jQuery(".sg-field input").on("blur", (e) => {
      if (e.target.value === "") {
        jQuery(".sg-field label[for=" + e.target.id + "]").removeClass(
          "active"
        );
      }
    });
    // address card options menu open
    jQuery(document).on("click", ".sg-menu-option", (event) => {
      if (
        jQuery(event.target)
          .parents(".sg-dropdown")
          .children(".sg-dropdown-list")
          .hasClass("active")
      ) {
        jQuery(".sg-dropdown-list").removeClass("active");
      } else {
        jQuery(".sg-dropdown-list").removeClass("active");
        jQuery(event.target)
          .parents(".sg-dropdown")
          .children(".sg-dropdown-list")
          .addClass("active");
      }
    });
    // address card options menu close
    document.addEventListener("click", (event) => {
      if (
        !jQuery(event.target).hasClass("sg-menu-option") &&
        jQuery(".sg-dropdown-list").hasClass("active")
      ) {
        jQuery(".sg-dropdown-list").removeClass("active");
      }
    });
    jQuery(".sg-del-add-selected-address").on("click", function () {
      jQuery(this)
        .parents(".sg-del-add-container-outer")
        .find(".sg-del-address")
        .slideToggle();
    });
  });

  // select another address from stored address list
  jQuery(document).on(
    "change",
    ".addresses-section.sg-del-address-list input.sg-del-add-select",
    (e) => {
      if (jQuery(e.target).parents(".single-address").hasClass("removed")) {
        sgitsdlmp_clear_addresses();
      } else {
        sgitsdlmp_get_selected_address(
          e.target.value,
          jQuery(e.target).attr("data-type")
        );
      }
    }
  );

  jQuery(document).on(
    "click",
    ".addresses-section.sg-del-address-list .available-address .action-container .sg-del-add-select-button",
    (e) => {
      if (
        jQuery("input#" + jQuery(e.target).attr("for")).prop("checked") === true
      ) {
        sgitsdlmp_get_selected_address(
          jQuery("input#" + jQuery(e.target).attr("for"))[0].value,
          jQuery("input#" + jQuery(e.target).attr("for")).attr("data-type")
        );
      }
    }
  );

  function sgitsdlmp_get_selected_address(address_id, section) {
    if (!section) {
      section = "";
    }

    jQuery.ajax({
      type: "post",
      url: document.getElementById("sg_del_add_ajax_url").value,
      data: {
        action: "moovin_address_get",
        id: address_id,
        section: section,
      },
      success: function (response) {
        if (response !== "") {
          if (section === "") {
            if (enabled_section === "enable_for_both") {
              sgitsdlmp_update_address_fields(response, "billing");
              sgitsdlmp_update_address_fields(response, "shipping");
            } else if (enabled_section === "enable_for_shipping") {
              sgitsdlmp_update_address_fields(response, "shipping");
            } else {
              sgitsdlmp_update_address_fields(response, "billing");
            }
          } else {
            sgitsdlmp_update_address_fields(response, section);
          }
        }
      },
    });
  }

  // clear address type field
  function sgitsdlmp_toggle_address_type() {
    jQuery('input[name="sg_new_address_type"]').val("");
    jQuery("input.sg_del_address_type").prop("checked", false);
    jQuery(".sg-address-type .sg-address-inner").toggleClass("show");
  }

  // popup open
  function sgitsdlmp_address_create_popup_window_open() {
    section = this.id.includes("billing") ? "billing" : "shipping";

    jQuery("#sg_delivery_address_" + section + "_new_address").on(
      "change",
      function () {
        geocodeName(
          jQuery("#sg_delivery_address_" + section + "_new_address").val(),
          platform
        );
      }
    );

    jQuery("#sg_delivery_address_" + section + "_popup_window").addClass(
      "show"
    );

    jQuery("#sg_delivery_address_" + section + "_popup_window").addClass(
      "show"
    );
    jQuery("#sg_delivery_address_" + section + "_popup_inner").addClass("show");
    jQuery("#sg_delivery_address_" + section + "_popup_window").css(
      "opacity",
      "1"
    );
    jQuery("body").addClass("has-popup");
    // clear input fields
    for (const input of jQuery(
      "#sg_delivery_address_" + section + "_create_form input"
    )) {
      if (
        !input.id.includes("map") &&
        jQuery(input).attr("data-clear") === "true"
      ) {
        if (input.type === "radio") {
          jQuery(input).prop("checked", false);
        } else {
          input.value = "";
        }
        jQuery(".sg-field label[for=" + input.id + "]").removeClass("active");
      }
    }
    // map initialise
    sgitsdlmp_locator_map_init(section, []);
  }

  // close popup
  function sgitsdlmp_address_create_popup_window_close(add_section) {
    let section = "";
    if (this !== undefined) {
      section = this.id.includes("billing") ? "billing" : "shipping";
    } else if (section !== undefined) {
      section = add_section;
    }
    jQuery("#sg_delivery_address_" + section + "_popup_inner").removeClass(
      "show"
    );
    jQuery("body").removeClass("has-popup");
    jQuery("#sg_delivery_address_" + section + "_popup_window").css(
      "opacity",
      "0"
    );
    jQuery(".sg-del-add-" + section + "-container")[0].scrollIntoView();
    setTimeout(() => {
      jQuery("#sg_delivery_address_" + section + "_popup_window").removeClass(
        "show"
      );
    }, 350);
  }

  function sgitsdlmp_add_new_sg_address(section) {
    var req_input_list = [
      ...jQuery("#sg_delivery_address_" + section + "_create_form input"),
    ];
    if (jQuery("#sg_del_add_unnamed_error_status").val() !== "no") {
      req_input_list = req_input_list.filter(
        (data) =>
          jQuery(data).attr("required") &&
          (jQuery(data).val().toLowerCase().includes("unnamed") ||
            jQuery(data).val() === "")
      );
    } else {
      req_input_list = req_input_list.filter(
        (data) => jQuery(data).attr("required") && jQuery(data).val() === ""
      );
    }
    if (req_input_list.length === 0) {
      let address_list_el = jQuery(".sg-del-address-list");
      sg_address.address_2 = document.getElementById(
        "sg_delivery_address_" + section + "_new_area"
      ).value;
      sg_address.door = document.getElementById(
        "sg_delivery_address_" + section + "_new_flat_no"
      ).value;
      sg_address.landmark = document.getElementById(
        "sg_delivery_address_" + section + "_new_landmark"
      ).value;
      sg_address.address_type = document.getElementById(
        "sg_delivery_address_" + section + "_new_address_type"
      ).value;
      if (address_list_el.find(".available-address").length > 0) {
        sg_address.id =
          parseFloat(
            address_list_el.find(
              '.available-address input[name="selected_' +
                section +
                '_deliver_address"]'
            )[0].value
          ) + 1;
      } else {
        sg_address.id = address_list_el.find(".available-address").length;
      }
      sg_address.default = document.getElementById(
        "sg_delivery_address_" + section + "_new_as_default"
      ).checked;

      global.options.url = document.getElementById("sg_del_add_ajax_url").value;
      global.options.data = {
        action: "moovin_address_insert",
        address: sg_address,
        section: section,
      };
      var response = global.Ajax(global.options);
      response
        .then(function (result) {
          if (jQuery("#sg_moovin_outzone").val() == "0") {
            try {
              var data = JSON.parse(result);
              utility.alert("error", "Ubicación no valida", data.msg);
            } catch (err) {
              jQuery(
                "#sg_delivery_address_" +
                  section +
                  "_list .sg-del-address-list-inner"
              ).prepend(result);
              sgitsdlmp_update_address_fields(sg_address, section);
              sgitsdlmp_address_create_popup_window_close(section);
            }
          } else {
            jQuery(
              "#sg_delivery_address_" +
                section +
                "_list .sg-del-address-list-inner"
            ).prepend(result);
            sgitsdlmp_update_address_fields(sg_address, section);
            sgitsdlmp_address_create_popup_window_close(section);
          }
        })
        .catch(function (error) {
          utility.alert(
            "error",
            "Ha ocurrido un error, si el problema persiste contacte con el administrador",
            error.message
          );
        });
    } else {
      for (const field of req_input_list) {
        if (
          jQuery(field).attr("required") &&
          (jQuery(field).val().toLowerCase().includes("unnamed") ||
            jQuery(field).val() === "")
        ) {
          jQuery(field).parents(".sg-field").addClass("has-error");

          if (
            field.id ===
            "sg_delivery_address_" + section + "_new_address_type"
          ) {
            jQuery(field)
              .siblings("span.sg-error")
              .html(jQuery("#sg_del_add_title_error").val());
          } else if (
            field.id ===
            "sg_delivery_address_" + section + "_new_address"
          ) {
            jQuery(field)
              .siblings("span.sg-error")
              .html(jQuery("#sg_del_add_unnamed_error").val());
          }
        }
      }
    }
  }

  function sgitsdlmp_remove_address() {
    const section = this.id.includes("billing") ? "billing" : "shipping";
    const remove_el = jQuery(this).parents(".available-address");
    const address_id = parseFloat(
      remove_el
        .find('input[name="selected_' + section + '_deliver_address"]')
        .val()
    );
    if (
      remove_el.find(
        'input[name="selected_' + section + '_deliver_address"]'
      )[0].checked === true
    ) {
      utility.alert(
        "warning",
        "Advertencia",
        "No puedes eliminar la ubicación por defecto"
      );
    } else {
      utility.confirm2(
        "warning",
        "Confirmar",
        "Estas seguro que deseas eliminar la dirección?",
        "Confirmar",
        function (confirm) {
          if (confirm.value) {
            global.options.url = document.getElementById(
              "sg_del_add_ajax_url"
            ).value;
            global.options.data = {
              action: "moovin_address_remove",
              id: address_id,
            };
            var response = global.Ajax(global.options);
            response
              .then(function (result) {
                jQuery(remove_el.remove());
              })
              .catch(function (error) {
                utility.alert(
                  "error",
                  "Ha ocurrido un error, si el problema persiste contacte con el administrador",
                  error.message
                );
              });
          }
        }
      );
    }
  }

  function sgitsdlmp_clear_addresses() {
    utility.confirm2(
      "warning",
      "Confirmar",
      "Estas seguro que deseas eliminar todas las dirección registradas?",
      "Confirmar",
      function (confirm) {
        console.log(confirm.value);
        if (confirm.value) {
          global.options.url = document.getElementById(
            "sg_del_add_ajax_url"
          ).value;
          global.options.data = {
            action: "moovin_address_clear",
          };
          var response = global.Ajax(global.options);
          response
            .then(function (response) {
              jQuery(".available-address").remove();
            })
            .catch(function (error) {
              utility.alert(
                "error",
                "Ha ocurrido un error, si el problema persiste contacte con el administrador",
                error.message
              );
            });
        }
      }
    );
  }

  // update field content
  function sgitsdlmp_update_address_fields(address, section) {
    let inputs = document.querySelectorAll("input");
    const fields = [
      "address_1",
      "address_2",
      "city",
      "state",
      "postcode",
      "door",
      "landmark",
      "address_type",
      "address_latitude",
      "address_longitude",
      "country",
    ];
    for (const input of inputs) {
      if (input.id.includes(section) && !input.id.includes("sg")) {
        for (const field of fields) {
          if (input.id.includes(field)) {
            if (typeof address[field] !== "string") {
              if (address[field]) {
                if (!field.includes("country")) {
                  input.value = address[field].long_name;
                } else {
                  input.value = address[field].short_name;
                }
              } else {
                if (address.position) {
                  if (field.includes("latitude")) {
                    input.value = address.position.lat;
                  } else if (field.includes("longitude")) {
                    input.value = address.position.lng;
                  }
                } else {
                }
              }
            } else {
              input.value = address[field];
            }
          }
        }
      }
    }
    jQuery("body").trigger("update_checkout");
    const selected_address = jQuery(
      ".sg-del-add-" + section + "-container"
    ).find(".sg-del-add-selected-address");
    if (address.address_type !== "") {
      jQuery(selected_address)
        .find(".sg-del-add-type")
        .html(address.address_type);
    } else {
      jQuery(selected_address)
        .find(".sg-del-add-type")
        .html(jQuery("#sg_del_add_default_title").val());
    }
    jQuery(selected_address)
      .find(".sg-del-add-description")
      .html(
        (address.door !== "" ? address.door + ", " : "") +
          address.formatted_address
      );
    if (address.address_2 !== "") {
      jQuery(selected_address).find(".sg-del-add-area").show();
      jQuery(selected_address)
        .find(".sg-del-add-area span")
        .html(address.address_2);
    } else {
      jQuery(selected_address).find(".sg-del-add-area").hide();
    }
    if (address.landmark !== "") {
      jQuery(selected_address).find(".sg-del-add-landmark").show();
      jQuery(selected_address)
        .find(".sg-del-add-landmark span")
        .html(address.landmark);
    } else {
      jQuery(selected_address).find(".sg-del-add-landmark").hide();
    }
    jQuery(".sg-del-add-" + section + "-container")
      .find(".sg-del-address")
      .slideToggle();
  }

  function sgitsdlmp_locator_map_init(section, points) {
    var map_id = document.getElementById(
      "sg_delivery_address_" + section + "_picker_map"
    );

    jQuery(map_id).html("");

    let position = {
      lat: parseFloat(
        document.getElementById("sg_del_add_map_default_lat").value
      ),
      lng: parseFloat(
        document.getElementById("sg_del_add_map_default_lng").value
      ),
    };

    platform = new H.service.Platform({
      apikey: herekey,
    });

    var defaultLayers = platform.createDefaultLayers();

    map = new H.Map(map_id, defaultLayers.vector.normal.map, {
      center: position,
      zoom: 15,
      pixelRatio: window.devicePixelRatio || 1,
    });

    behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

    if (
      document.getElementById("sg_del_add_auto_detect_user_location").value ===
      "yes"
    ) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (event) => {
            reverseGeocode(
              event.coords.latitude,
              event.coords.longitude,
              platform
            );
          },
          (error) => {
            console.log(error.message);
          }
        );
      }
    }
  }

  function geocodeName(name, platform) {
    var geocoder = platform.getSearchService(),
      geocodingParameters = {
        q: name + " CR ",
      };
    geocoder.geocode(geocodingParameters, onSuccess, onError);
  }

  function reverseGeocode(lat, lng, platform) {
    var geocoder = platform.getSearchService(),
      reverseGeocodingParameters = {
        at: lat + "," + lng,
        limit: "1",
      };
    geocoder.reverseGeocode(reverseGeocodingParameters, onSuccess, onError);
  }

  function onSuccess(result) {
    var locations = result.items;
    console.log(locations);

    sgitsdlmp_get_address(locations, section);
  }

  function sgitsdlmp_get_address(locations, section) {
    if (locations.length == 0) {
      if (
        jQuery("#sg_delivery_address_" + section + "_new_address")
          .parents(".sg-field")
          .hasClass("has-error")
      ) {
        jQuery("#sg_delivery_address_" + section + "_new_address")
          .parents(".sg-field")
          .removeClass("has-error");
        jQuery("#sg_delivery_address_" + section + "_new_address")
          .siblings("span.sg-error")
          .html("");
      }

      return;
    }

    var location = locations[0];
    var position = location.position;

    let address_items = {};
    let field;

    address_items.formatted_address = location.title;

    address_items.postcode = {
      short_name: location.address.postalCode,
      long_name: location.address.district,
    };
    address_items.state = {
      short_name: location.address.state,
      long_name: location.address.state,
    };
    address_items.city = {
      short_name: location.address.county,
      long_name: location.address.county,
    };
    address_items.country = {
      short_name: location.address.countryCode,
      long_name: location.address.countryName,
    };

    address_items.address_1 = {
      short_name: location.address.label,
      long_name: location.address.label,
    };

    address_items.position = {
      lat: location.position.lat,
      lng: location.position.lng,
    };

    sg_address = address_items;

    if (location.address.countryCode == "CRI") {
      jQuery("#shipping_country").val("CR");
    }

    document.getElementById(
      "sg_delivery_address_" + section + "_new_address"
    ).value = location.title;
    document.getElementById(
      "sg_delivery_address_" + section + "_new_lat"
    ).value = location.position.lat;

    document.getElementById(
      "sg_delivery_address_" + section + "_new_lng"
    ).value = location.position.lng;

    addDraggableMarker(
      map,
      document.getElementById("sg_delivery_address_" + section + "_new_lat")
        .value,
      document.getElementById("sg_delivery_address_" + section + "_new_lng")
        .value,
      behavior
    );

    document
      .querySelectorAll(
        'label[for="sg_delivery_address_' + section + '_new_address"]'
      )[0]
      .classList.add("active");
  }

  function onError(error) {
    alert("Can't reach the remote server");
  }

  function addDraggableMarker(map, lat, lng, behavior) {
    map.removeObjects(map.getObjects());

    var marker = new H.map.Marker({ lat: lat, lng: lng }, { volatility: true });
    // Ensure that the marker can receive drag events
    marker.draggable = true;
    map.addObject(marker);
    map.setCenter({ lat: lat, lng: lng });

    // disable the default draggability of the underlying map
    // and calculate the offset between mouse and target's position
    // when starting to drag a marker object:
    map.addEventListener(
      "dragstart",
      function (ev) {
        var target = ev.target,
          pointer = ev.currentPointer;
        if (target instanceof H.map.Marker) {
          var targetPosition = map.geoToScreen(target.getGeometry());
          target["offset"] = new H.math.Point(
            pointer.viewportX - targetPosition.x,
            pointer.viewportY - targetPosition.y
          );

          //Request location via lat or lng
          //console.log(target);
          behavior.disable();
        }
      },
      false
    );

    // re-enable the default draggability of the underlying map
    // when dragging has completed
    map.addEventListener(
      "dragend",
      function (ev) {
        var target = ev.target;
        if (target instanceof H.map.Marker) {
          behavior.enable();
          console.log(target);
          reverseGeocode(target.b.lat, target.b.lng, platform);
        }
      },
      false
    );

    // Listen to the drag event and move the position of the marker
    // as necessary
    map.addEventListener(
      "drag",
      function (ev) {
        var target = ev.target,
          pointer = ev.currentPointer;
        if (target instanceof H.map.Marker) {
          target.setGeometry(
            map.screenToGeo(
              pointer.viewportX - target["offset"].x,
              pointer.viewportY - target["offset"].y
            )
          );
        }
      },
      false
    );
  }
})(jQuery);
