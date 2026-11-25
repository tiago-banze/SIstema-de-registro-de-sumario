(function () {
  // Menu lateral no telemóvel
  document.querySelectorAll('[data-menu]').forEach(function (el) {
    el.addEventListener('click', function () { document.body.classList.toggle('menu-aberto'); });
  });
  // Confirmação antes de apagar
  document.querySelectorAll('form[data-confirmar]').forEach(function (f) {
    f.addEventListener('submit', function (ev) {
      if (!window.confirm(f.getAttribute('data-confirmar'))) { ev.preventDefault(); }
    });
  });
  // Mensagens de sucesso desaparecem sozinhas
  document.querySelectorAll('.alerta-sucesso').forEach(function (a) {
    setTimeout(function () { a.style.display = 'none'; }, 5000);
  });
})();
