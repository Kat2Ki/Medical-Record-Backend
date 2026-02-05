document.getElementById('Login').addEventListener('click', function () {
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();

  if (username !== "" && password !== "") {
    window.location.href = 'CEP2.html?username=' + encodeURIComponent(username);
  } else {
    alert("Please enter a Username and Password.");
  }
});
