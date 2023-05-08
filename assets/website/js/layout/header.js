headerWrapper = document.querySelector('.header-wrapper');
header = document.querySelector('.header');
menuToggle = document.querySelector('.mobile-menu-toggle');

menuToggle.addEventListener('click', (event) => {
  header.classList.toggle('show');
});

document.addEventListener('scroll', (event) => {
  const headerPosition = headerWrapper.getBoundingClientRect();
  if (headerPosition.y <= 0) {
    header.classList.add('fixed');
  } else {
    header.classList.remove('fixed');
  }
});
