let navBar=document.getElementById("myNavBar")
let hamburgerMenu= document.getElementsByClassName("hamburger")[0];
let navItems = document.querySelectorAll(".navItem");

// Hamburger Menu  open and close
function hamburgerSwitch() {
    if (navBar.className === "navBar") {
      navBar.className += " responsive";
      hamburgerMenu.innerHTML="X"
    } else {
      navBar.className = "navBar";
      hamburgerMenu.innerHTML=" <i class='fa fa-bars'></i>"
    }
  }

// Close hamburger menu when cliked on nav items
  navItems.forEach(navItem => {
    navItem.addEventListener('click', () => {
        if (navBar.classList.contains("responsive")) {
            navBar.classList.remove("responsive");
            hamburgerMenu.innerHTML = " <i class='fa fa-bars'></i>";
        }
    });
});
