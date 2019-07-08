$(function () {
  var success_fun = function (data) {
    console.log("IRMA authentication successful, submitting form");
    $("#jwt_result").attr("value", data);
    $("form#irma_result_form").submit();
  };
  var cancel_fun = function (data) {
    console.log("Authentication cancelled!");
    $("#irma_msg").html('<div class="alert alert-warning" role="alert">IRMA authentication cancelled.</div>');
  };

  var error_fun = function (data) {
    console.log("Authentication failed!");
    console.log("Error data:", data);
    $("#irma_msg").html('<div class="alert alert-danger" role="alert">The IRMA authentication has failed.</div>');
  };

  $("#irma_btn").on("click", function () {
    console.log("Button clicked");
    $.getJSON('get_irma_session.php?AuthState=' + authStateId, function (data) {
      var irma_session = data.sessionPtr;
      var irma_token = data.token;
      var promise = irma.handleSession(irma_session);
      promise.then(function (data) {
        if (data === 'DONE') {
          $.get(irma_api_server + '/session/' + irma_token + '/getproof', function (result_jwt) {
            success_fun(result_jwt);
          });
        } else {
          cancel_fun(data);
        }
      });
      promise.catch(error_fun);
    });
  });
});
