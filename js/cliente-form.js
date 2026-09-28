function iniciarFormCliente() {
    var form = document.getElementById('formCliente');

    // ---------- Máscara de CPF ----------
    var cpfDisplay = document.getElementById('cpfDisplay');
    var cpfHidden = document.getElementById('cpf');

    cpfDisplay.addEventListener('input', function () {
        var digitos = cpfDisplay.value.replace(/\D/g, '').slice(0, 11);
        cpfHidden.value = digitos; // valor limpo pro PHP/MySQL

        var formatado = digitos
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');

        cpfDisplay.value = formatado;
    });

    // ---------- Máscara de telefone ----------
    var telefoneDisplay = document.getElementById('telefoneDisplay');
    var telefoneHidden = document.getElementById('telefone');

    telefoneDisplay.addEventListener('input', function () {
        var digitos = telefoneDisplay.value.replace(/\D/g, '').slice(0, 11);
        telefoneHidden.value = digitos; // valor limpo pro PHP/MySQL

        var formatado = digitos;
        if (digitos.length > 10) {
            formatado = digitos.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        } else if (digitos.length > 5) {
            formatado = digitos.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (digitos.length > 2) {
            formatado = digitos.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        } else if (digitos.length > 0) {
            formatado = digitos.replace(/(\d{0,2})/, '($1');
        }

        telefoneDisplay.value = formatado.trim();
    });

    // ---------- Formata valores já preenchidos (página de editar) ----------
    if (telefoneDisplay.value) telefoneDisplay.dispatchEvent(new Event('input'));
    if (cpfDisplay.value) cpfDisplay.dispatchEvent(new Event('input'));


    // ---------- Filtro da lista de veículos de interesse ----------
    var buscaVeiculo = document.getElementById('buscaVeiculoInteresse');
    var itensVeiculo = document.querySelectorAll('#listaVeiculosInteresse li');
    var listaVazia = document.getElementById('listaVeiculosVazia');

    buscaVeiculo.addEventListener('input', function () {
        var termo = buscaVeiculo.value.trim().toLowerCase();
        var algumVisivel = false;

        itensVeiculo.forEach(function (item) {
            var visivel = item.dataset.busca.includes(termo);
            item.classList.toggle('d-none', !visivel);
            if (visivel) algumVisivel = true;
        });

        listaVazia.classList.toggle('d-none', algumVisivel);
    });

    // ---------- Validação geral no submit ----------
    form.addEventListener('submit', function (event) {
        var camposInvalidos = !form.checkValidity();
        var cpfInvalido = cpfHidden.value.length !== 11;
        var telefoneInvalido = telefoneHidden.value.length < 10;

        if (camposInvalidos || cpfInvalido || telefoneInvalido) {
            event.preventDefault();
            event.stopPropagation();

            if (cpfInvalido) cpfDisplay.setCustomValidity('Informe um CPF válido.');
            if (telefoneInvalido) telefoneDisplay.setCustomValidity('Informe um telefone válido.');
        }

        form.classList.add('was-validated');
    });

    cpfDisplay.addEventListener('input', function () { cpfDisplay.setCustomValidity(''); });
    telefoneDisplay.addEventListener('input', function () { telefoneDisplay.setCustomValidity(''); });
}