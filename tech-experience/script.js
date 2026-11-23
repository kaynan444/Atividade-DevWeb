document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('form-inscricao');
  var mensagem = document.getElementById('mensagem');
  var contador = document.getElementById('contador');
  var grupoOficinas = document.getElementById('grupo-oficinas');
  var erroOficinas = document.getElementById('erro-oficinas');
  var checkboxes = form.querySelectorAll('input[name="oficinas[]"]');

  var limite = parseInt(mensagem.getAttribute('maxlength'), 10) || 200;

  function atualizarContador() {
    var restantes = limite - mensagem.value.length;
    contador.textContent = 'Restam ' + restantes + ' caractere' + (restantes === 1 ? '' : 's');
  }

  mensagem.addEventListener('input', atualizarContador);
  atualizarContador();

  function mostrarErro(campo, elementoErro, texto) {
    campo.classList.add('is-invalid');
    elementoErro.textContent = texto;
    elementoErro.hidden = false;
  }

  function limparErro(campo, elementoErro) {
    campo.classList.remove('is-invalid');
    elementoErro.textContent = '';
    elementoErro.hidden = true;
  }

  function algumaOficinaMarcada() {
    return Array.prototype.some.call(checkboxes, function (c) { return c.checked; });
  }

  Array.prototype.forEach.call(checkboxes, function (c) {
    c.addEventListener('change', function () {
      if (algumaOficinaMarcada()) {
        limparErro(grupoOficinas, erroOficinas);
      }
    });
  });

  form.addEventListener('submit', function (event) {
    if (!algumaOficinaMarcada()) {
      event.preventDefault();
      mostrarErro(grupoOficinas, erroOficinas, 'Selecione ao menos uma oficina para continuar.');
      checkboxes[0].focus();
      return;
    }
    limparErro(grupoOficinas, erroOficinas);
  });
});
