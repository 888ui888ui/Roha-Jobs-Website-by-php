// Load Navbar
fetch("navbar.html")
  .then(response => response.text())
  .then(data => {
      document.getElementById("include-navbar").innerHTML = data;
  });

// Load Footer
fetch("footer.html")
  .then(response => response.text())
  .then(data => {
      document.getElementById("include-footer").innerHTML = data;
  });
