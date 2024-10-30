var ajaxUrl = initMoovin.ajaxurl;
var page = {
  Init: function () {
    jQuery("#tracking-package").on("click", function () {
      page.searchPackage();
    });
  },
  searchPackage: function () {
    if (jQuery("#cf_package").val() == "") {
      utility.alert(
        "warning",
        "Advertencia",
        "Por favor indique el ID del paquete"
      );
      return false;
    }

    jQuery("#div-tracking").show("slow", function () {
      window.open(
        "https://moovin.me/MoovinWebCliente/src/packageTracking/?idPackage=" +
          jQuery("#cf_package").val()
      );
    });
  },
};

jQuery(document).ready(function () {
  page.Init();
});
