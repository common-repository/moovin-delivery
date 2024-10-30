var utility = {
  getYear: function () {
    return document.write(new Date().getFullYear());
  },
  alert: function (type, ttile, message) {
    Swal.fire(
      {
        title: ttile,
        text: message,
        type: type,
        confirmButtonColor: "#073546",
      },
      function (response) {}
    );
  },
  alert2: function (type, ttile, message) {
    Swal.fire(
      {
        title: ttile,
        html: message,
        type: type,
        confirmButtonColor: "#073546",
      },
      function (response) {}
    );
  },
  alert3: function (type, ttile, message, callback) {
    Swal.fire(
      {
        title: ttile,
        html: message,
        type: type,
        confirmButtonColor: "#073546",
      },
      function (response) {
        callback(response);
      }
    );
  },
  confirm: function (type, ttile, message, callback) {
    Swal.fire({
      title: ttile,
      text: message,
      type: type,
      showCancelButton: !0,
      confirmButtonText: "Eliminar",
      cancelButtonText: "Cancelar",
      confirmButtonClass: "btn btn-danger mt-2",
      cancelButtonClass: "btn btn-default ml-2 mt-2",
    }).then(function (response) {
      callback(response);
    });
  },
  confirm2: function (type, ttile, message, buttonText, callback) {
    Swal.fire({
      title: ttile,
      text: message,
      type: type,
      showCancelButton: !0,
      confirmButtonText: buttonText,
      cancelButtonText: "Cancelar",
      confirmButtonClass: "btn btn-danger mt-2",
      cancelButtonClass: "btn btn-default ml-2 mt-2",
    }).then(function (response) {
      callback(response);
    });
  },
  cutDecimals: function (number) {
    var regex = "/^\\d+(?:\\.\\d{0, 5})?/";
    return Number(number.toString().match());
  },
  options: function (key, value, data, first = null, extra) {
    var options = "";
    if (first !== null) {
      options = '<option value="">' + first + "</option>";
    }
    data.forEach(function (row) {
      var v =
        typeof extra === "undefined" || extra === null || extra === ""
          ? ""
          : row[extra];
      options +=
        '<option value="' +
        row[key] +
        '" data-extra="' +
        v +
        '">' +
        row[value] +
        "</option>";
    });
    return options;
  },
  validateEmail: function (email) {
    var re =
      /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  },
  splitAtFirstSpace: function (str) {
    if (!str) return [];
    var i = str.indexOf(" ");
    if (i > 0) {
      return [str.substring(0, i), str.substring(i + 1)];
    } else return [str];
  },
  getParameterByName: function (name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return "";
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  },
};
