// script.js - navigation helper. Put in D:\XAMP\htdocs\my_project\login\script.js

function openPage(page) {
  // navigate to a relative page
  window.location.href = page;
}

// optional: make the card clickable when user presses Enter when focused
document.addEventListener('keydown', function(e){
  if(e.key === 'Enter'){
    const firstButton = document.querySelector('.box a button');
    if(firstButton) firstButton.click();
  }
});
