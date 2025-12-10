document.documentElement.style.display = 'none';
// localStorage.setItem('petname', 'Bark Twain');
localStorage.setItem('username', 'admin');

function checkIfAdmin() {
  const username = localStorage.getItem('username');
  
  if (username !== 'admin') {
    alert('Access Denied');
    window.location.href = 'index.html';
  } else {
    // Show page only if admin
    document.documentElement.style.display = '';
  }
}

checkIfAdmin();