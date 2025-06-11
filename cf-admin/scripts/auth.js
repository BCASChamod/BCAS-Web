document.getElementById('loginForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;

  const res = await fetch('../api/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ username, password })
  });

  const data = await res.json();

  if (data.success) {
    window.location.href = "../index.php";
  } else {
    const errBox = document.getElementById('error');
    errBox.innerText = data.message;
    errBox.style.display = "block";
  }
});

function resetpassword() {
  alert("Reset password logic here...");
}
