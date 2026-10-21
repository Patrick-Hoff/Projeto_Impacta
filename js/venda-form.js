/**
 * Máscara de valor, data padrão e validação do formulário de venda.
 * Reutilizado em cadastrar-venda.php e editar-venda.php.
 */
function iniciarFormVenda(config) {
    config = config || {};

    var form = document.getElementById('formVenda');
    if (!form) return;

    var veiculo = document.getElementById('veiculo_id');
    var dataVenda = document.getElementById('dataVenda');
    var valorDisplay = document.getElementById('valorDisplay');
    var valorHidden = document.getElementById('valor');
    var precoTabela = document.getElementById('precoTabela');

    function formatarMoeda(numero) {
        return numero.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ---------- Data: hoje (horário do navegador) como padrão e como limite ----------
    var agora = new Date();
    var hoje = agora.getFullYear() + '-' +
        String(agora.getMonth() + 1).padStart(2, '0') + '-' +
        String(agora.getDate()).padStart(2, '0');

    dataVenda.max = hoje;
    if (!dataVenda.value) dataVenda.value = hoje;

    // ---------- Máscara de valor monetário (R$ 0.000,00) ----------
    // Na edição o valor já vem preenchido e não deve ser sobrescrito pelo preço do veículo.
    var valorEditadoManual = config.valor !== undefined && config.valor !== null;

    function definirValor(numero) {
        valorHidden.value = numero.toFixed(2);
        valorDisplay.value = formatarMoeda(numero);
        valorDisplay.setCustomValidity('');
    }

    valorDisplay.addEventListener('input', function () {
        var digitos = valorDisplay.value.replace(/\D/g, '');

        if (digitos === '') {
            valorDisplay.value = '';
            valorHidden.value = '';
            valorEditadoManual = false;
        } else {
            definirValor(parseInt(digitos, 10) / 100);
            valorEditadoManual = true;
        }
        valorDisplay.setCustomValidity('');
    });

    if (valorEditadoManual) {
        definirValor(Number(config.valor));
    }

    // ---------- Ao escolher o veículo, sugere o preço dele como valor da venda ----------
    function aoTrocarVeiculo() {
        var opcao = veiculo.options[veiculo.selectedIndex];
        var preco = opcao && opcao.dataset.preco ? Number(opcao.dataset.preco) : null;

        if (precoTabela) {
            precoTabela.textContent = preco !== null
                ? 'Preço de tabela: R$ ' + formatarMoeda(preco)
                : '';
        }

        if (preco !== null && !valorEditadoManual) {
            definirValor(preco);
        }
    }

    veiculo.addEventListener('change', aoTrocarVeiculo);
    if (veiculo.value) aoTrocarVeiculo();

    // ---------- Validação no submit ----------
    form.addEventListener('submit', function (event) {
        var invalido = !form.checkValidity();

        if (valorHidden.value === '' || Number(valorHidden.value) <= 0) {
            valorDisplay.setCustomValidity('Informe o valor da venda.');
            invalido = true;
        }

        if (invalido) {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });
}