var global = {
  session: {},
  loadSession: {},
  preferences: [],
  loadPreferences: function () {
    this.preferences = {
      latitud: 9.935473584881777,
      longitud: -84.1034513712951,
      currency: "₡",
      digits: 2,
      seperator: ",",
      decimalSeparetor: ".",
      timeFormat: "hh:mm A",
      dateFormat: "DD-MM-YYYY",
      dateTimeFormat: "DD-MM-YYYY hh:mm A",
      databaseFormat: "YYYY-MM-DD HH:mm:ss",
    };
  },
  options: {
    method: "POST",
    url: "",
    async: true,
    data: {},
    type: "json",
    headers: {},
    processData: true,
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: true,
    timeout: 0,
    enctype: "application/x-www-form-urlencoded",
  },
  Init: function () {
    var self = this;
    self.options.url = "../configuration/global.php";
    self.loadPreferences();

    Number.prototype.toFixedNoRounding = function (n) {
      const reg = new RegExp("^-?\\d+(?:\\.\\d{0," + n + "})?", "g");
      const a = this.toString().match(reg)[0];
      const dot = a.indexOf(".");
      if (dot === -1) {
        // integer, insert decimal dot and pad up zeros
        return a + "." + "0".repeat(n);
      }
      const b = n - (a.length - dot) + 1;
      return b > 0 ? a + "0".repeat(b) : a;
    };
  },
  Ajax: function (options) {
    var self = this;
    return new Promise(function (resolve, reject) {
      jQuery
        .ajax({
          beforeSend: function () {
            self.loadingBlock("Procesando ...");
          },
          complete: function () {
            self.dismissLoadingBlock("Procesando ...");
          },
          dataFilter: function (data, type) {
            return data;
          },
          method: options.method,
          url: options.url,
          async: options.async,
          data: options.data,
          type: options.type,
          headers: options.headers,
          processData: options.processData,
          contentType: options.contentType,
          cache: options.cache,
          enctype: options.enctype,
        })
        .done(function (response) {
          try {
            const re = JSON.parse(response);
            if (
              ("httpCode" in re && re.httpCode === 403) ||
              ("http_code" in re && re.http_code === 403)
            ) {
              window.location.href = "../../";
            } else {
              resolve(response);
            }
          } catch (ex) {
            console.log(ex);
          }
        })
        .fail(reject);
    });
  },
  loadingBlock: function (msg = "Procesnado ...") {
    jQuery.blockUI({
      baseZ: 2000,
      message:
        '<div class="spinner-border text-info m-1" role="status">\n' +
        '<span class="sr-only"></span>' +
        "</div>" +
        '<br><div class="text-center"><h4>' +
        msg +
        "</h4></div>",
      overlayCSS: {
        backgroundColor: "#FFF",
        opacity: 0.8,
        cursor: "wait",
      },
      css: {
        border: 0,
        padding: 0,
        color: "#333",
        backgroundColor: "transparent",
      },
      onBlock: function () {},
    });
  },
  dismissLoadingBlock: function () {
    jQuery.unblockUI();
  },
};
jQuery(document).ready(function () {
  if (typeof jQuery.fn.datepicker !== "undefined") {
    jQuery.fn.datepicker.defaults.format = "dd-mm-yyyy";
    jQuery.fn.datepicker.defaults.autoclose = true;
  }

  global.Init();
});
