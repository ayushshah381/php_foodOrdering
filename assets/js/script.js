var userLoggedIn;
var timer;

function logout() {
  $.post("includes/handlers/ajax/logout.php", function() {
    location.reload();
  });
}

function openPage(url) {
  if (timer != null) {
    clearTimeout(timer);
  }

  if (url.indexOf("?") == -1) {
    url = url + "?";
  }

  var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
  $("#mainContent").load(encodedUrl);
  $("body").scrollTop(0);
  history.pushState(null, null, url);
}
