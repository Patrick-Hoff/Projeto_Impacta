document.addEventListener('DOMContentLoaded', function () {
    const busca = document.getElementById('buscaVeiculoInteresse');
    const lista = document.getElementById('listaVeiculosInteresse');
    const vazio = document.getElementById('listaVeiculosVazia');
    const container = document.getElementById('interessesSelecionados');

    if (!busca || !lista || !container) return;

    // Seleção inicial: lê os hidden que o PHP já colocou na página
    const selecionados = new Set(
        Array.from(container.querySelectorAll('input[name="veiculos_interesse[]"]'))
            .map(function (i) { return parseInt(i.value, 10); })
    );

    // Recria os hidden a partir do conjunto (é isso que vai no POST)
    function sincronizar() {
        container.innerHTML = '';
        selecionados.forEach(function (id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'veiculos_interesse[]';
            input.value = id;
            container.appendChild(input);
        });
    }

    // Marcar/desmarcar atualiza o conjunto
    lista.addEventListener('change', function (e) {
        if (e.target.type !== 'checkbox') return;
        const id = parseInt(e.target.value, 10);
        if (e.target.checked) {
            selecionados.add(id);
        } else {
            selecionados.delete(id);
        }
        sincronizar();
    });

    // Evita HTML injetado por texto vindo do banco
    function escapar(texto) {
        const d = document.createElement('div');
        d.textContent = texto;
        return d.innerHTML;
    }

    // Redesenha a lista com o JSON recebido
    function desenhar(veiculos) {
        lista.innerHTML = '';
        vazio.classList.toggle('d-none', veiculos.length > 0);

        veiculos.forEach(function (v) {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.innerHTML =
                '<div class="d-flex justify-content-between align-items-center">' +
                '<div class="form-check">' +
                '<input class="form-check-input" type="checkbox" value="' + v.id + '" id="veiculo' + v.id + '"' +
                (selecionados.has(v.id) ? ' checked' : '') + '>' +
                '<label class="form-check-label" for="veiculo' + v.id + '">' +
                escapar(v.marca + ' ' + v.modelo) +
                ' <span class="text-muted small">&middot; ' + escapar(String(v.ano)) + '</span>' +
                '</label>' +
                '</div>' +
                '<span class="text-muted small">R$ ' + escapar(v.preco) + '</span>' +
                '</div>';
            lista.appendChild(li);
        });
    }

    // Chama o PHP em segundo plano (a URL da página não muda)
    async function pesquisar() {
        try {
            const url = '../models/buscar-veiculos.php?q=' + encodeURIComponent(busca.value.trim());
            const resp = await fetch(url);
            desenhar(await resp.json());
        } catch (erro) {
            console.error('Erro na busca de veículos:', erro);
        }
    }

    // Espera 300 ms sem digitar antes de buscar
    let timer;
    busca.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(pesquisar, 300);
    });

    // Enter no campo de busca não envia o formulário
    busca.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') e.preventDefault();
    });
});