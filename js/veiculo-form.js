/**
 * Máscaras e validação do formulário de veículo.
 * Reutilizado em cadastrar-veiculo.php e editar-veiculo.php.
 */
function iniciarFormVeiculo(config) {
    config = config || {};

    var form = document.getElementById('formVeiculo');
    if (!form) return;

    // ---------- Bloqueia teclas inválidas em campo numérico simples (ano) ----------
    var ano = document.getElementById('ano');
    if (ano) {
        ano.addEventListener('keydown', function (e) {
            if (['e', 'E', '+', '-'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }

    // ---------- Máscara genérica: número inteiro com ponto de milhar ----------
    function aplicarMascaraInteiro(displayId, hiddenId) {
        var display = document.getElementById(displayId);
        var hidden = document.getElementById(hiddenId);
        if (!display || !hidden) return;

        function atualizar() {
            var digitos = display.value.replace(/\D/g, '');
            hidden.value = digitos;
            display.value = digitos === '' ? '' : Number(digitos).toLocaleString('pt-BR');
            display.setCustomValidity('');
        }

        display.addEventListener('input', atualizar);
        return { display: display, hidden: hidden, atualizar: atualizar };
    }

    // ---------- Máscara genérica: valor monetário (R$ 0.000,00) ----------
    function aplicarMascaraMoeda(displayId, hiddenId) {
        var display = document.getElementById(displayId);
        var hidden = document.getElementById(hiddenId);
        if (!display || !hidden) return;

        function atualizar() {
            var digitos = display.value.replace(/\D/g, '');

            if (digitos === '') {
                display.value = '';
                hidden.value = '';
                display.setCustomValidity('');
                return;
            }

            var valor = parseInt(digitos, 10) / 100;
            hidden.value = valor.toFixed(2);
            display.value = valor.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            display.setCustomValidity('');
        }

        display.addEventListener('input', atualizar);
        return { display: display, hidden: hidden, atualizar: atualizar };
    }

    var km = aplicarMascaraInteiro('quilometragemDisplay', 'quilometragem');
    var preco = aplicarMascaraMoeda('precoDisplay', 'preco');

    // ---------- Pré-preenche (usado na edição) ----------
    if (km && config.quilometragem !== undefined && config.quilometragem !== null) {
        km.display.value = Number(config.quilometragem).toLocaleString('pt-BR');
        km.hidden.value = config.quilometragem;
    }
    if (preco && config.preco !== undefined && config.preco !== null) {
        preco.display.value = Number(config.preco).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        preco.hidden.value = config.preco;
    }

    // ---------- Validação no submit ----------
    form.addEventListener('submit', function (event) {
        var invalido = !form.checkValidity();

        if (km && km.hidden.value === '') {
            km.display.setCustomValidity('Informe a quilometragem.');
            invalido = true;
        }
        if (preco && preco.hidden.value === '') {
            preco.display.setCustomValidity('Informe o preço.');
            invalido = true;
        }

        if (invalido) {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });
}