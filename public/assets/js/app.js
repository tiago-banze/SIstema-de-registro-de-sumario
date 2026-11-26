(function () {
  // Menu lateral
  document.querySelectorAll('[data-menu]').forEach(function (el) {
    el.addEventListener('click', function () { document.body.classList.toggle('menu-aberto'); });
  });
  // Confirmação antes de apagar ou remover
  document.querySelectorAll('form[data-confirmar]').forEach(function (f) {
    f.addEventListener('submit', function (ev) {
      if (!window.confirm(f.getAttribute('data-confirmar'))) { ev.preventDefault(); }
    });
  });
  // Marcar todos os estudantes de uma vez
  document.querySelectorAll('[data-marcar]').forEach(function (b) {
    b.addEventListener('click', function () {
      var valor = b.getAttribute('data-marcar');
      document.querySelectorAll('.seg input[value="' + valor + '"]').forEach(function (r) { r.checked = true; });
    });
  });
  // Mensagens de sucesso desaparecem sozinhas
  document.querySelectorAll('.alerta-sucesso').forEach(function (a) {
    setTimeout(function () { a.style.display = 'none'; }, 6000);
  });
})();
