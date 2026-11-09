(function () {
    function iniciarBusca(config) {
        const input  = document.getElementById(config.input);
        const hidden = document.getElementById(config.hidden);
        const lista  = document.getElementById(config.lista);
        if (!input || !hidden || !lista) return;

        let timer = null;
        let controller = null;
        let ativo = -1;

        // Enquanto não escolher da lista, o campo é inválido
        input.setCustomValidity(hidden.value ? '' : config.msgInvalido);

        function fechar() {
            lista.classList.add('d-none');
            input.setAttribute('aria-expanded', 'false');
            ativo = -1;
        }

        function abrir() {
            lista.classList.remove('d-none');
            input.setAttribute('aria-expanded', 'true');
        }

        function marcarAtivo(i) {
            const itens = lista.querySelectorAll('button');
            itens.forEach(el => el.classList.remove('active'));
            if (i >= 0 && i < itens.length) {
                ativo = i;
                itens[i].classList.add('active');
                itens[i].scrollIntoView({ block: 'nearest' });
            }
        }

        function selecionar(item) {
            input.value  = config.rotulo(item);
            hidden.value = item.id;
            if (config.aoSelecionar) config.aoSelecionar(item, hidden);
            hidden.dispatchEvent(new Event('change'));
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            fechar();
        }

        function limparSelecao() {
            hidden.value = '';
            delete hidden.dataset.preco;
            input.setCustomValidity(config.msgInvalido);
            hidden.dispatchEvent(new Event('change'));
        }

        function render(itensEncontrados) {
            lista.innerHTML = '';

            if (itensEncontrados.length === 0) {
                const vazio = document.createElement('div');
                vazio.className = 'list-group-item text-muted small';
                vazio.textContent = config.msgVazio;
                lista.appendChild(vazio);
            }

            itensEncontrados.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.setAttribute('role', 'option');
                btn.textContent = config.rotulo(item);   // textContent evita injeção de HTML
                btn.addEventListener('click', () => selecionar(item));
                btn._item = item;
                lista.appendChild(btn);
            });

            ativo = -1;
            abrir();
        }

        async function buscar(termo) {
            if (controller) controller.abort();
            controller = new AbortController();

            try {
                const resp = await fetch(config.url(termo), {
                    signal: controller.signal
                });
                if (!resp.ok) throw new Error('Falha na busca');
                render(await resp.json());
            } catch (e) {
                if (e.name === 'AbortError') return;
                lista.innerHTML = '';
                const erro = document.createElement('div');
                erro.className = 'list-group-item text-danger small';
                erro.textContent = config.msgErro;
                lista.appendChild(erro);
                abrir();
            }
        }

        // Digitou: invalida a escolha anterior e busca de novo (com debounce)
        input.addEventListener('input', function () {
            limparSelecao();
            clearTimeout(timer);
            timer = setTimeout(() => buscar(input.value.trim()), 300);
        });

        // Ao focar sem item escolhido, já mostra sugestões
        input.addEventListener('focus', function () {
            if (!hidden.value) buscar(input.value.trim());
        });

        // Teclado: setas, Enter e Esc
        input.addEventListener('keydown', function (e) {
            const itens = lista.querySelectorAll('button');
            if (lista.classList.contains('d-none') || itens.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                marcarAtivo(Math.min(ativo + 1, itens.length - 1));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                marcarAtivo(Math.max(ativo - 1, 0));
            } else if (e.key === 'Enter' && ativo >= 0) {
                e.preventDefault();                       // não envia o formulário
                selecionar(itens[ativo]._item);
            } else if (e.key === 'Escape') {
                fechar();
            }
        });

        // Clicou fora: fecha a lista
        document.addEventListener('click', function (e) {
            if (e.target !== input && !lista.contains(e.target)) fechar();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        iniciarBusca({
            input: 'buscaCliente',
            hidden: 'cliente_id',
            lista: 'listaClientes',
            url: termo => `/projeto_faculdade/models/buscar-cliente.php?q=${encodeURIComponent(termo)}`,
            rotulo: c => `${c.nome} · ${c.cpf}`,
            msgInvalido: 'Selecione um cliente da lista.',
            msgVazio: 'Nenhum cliente encontrado.',
            msgErro: 'Erro ao buscar clientes.'
        });

        iniciarBusca({
            input: 'buscaVeiculo',
            hidden: 'veiculo_id',
            lista: 'listaVeiculos',
            url: termo => `/projeto_faculdade/models/buscar-veiculos.php?status=disponivel&q=${encodeURIComponent(termo)}`,
            rotulo: v => `${v.marca} ${v.modelo} · ${v.ano}`,
            aoSelecionar: (v, hidden) => {
                hidden.dataset.preco = v.preco.replace(/\./g, '').replace(',', '.');
            },
            msgInvalido: 'Selecione um veículo da lista.',
            msgVazio: 'Nenhum veículo disponível encontrado.',
            msgErro: 'Erro ao buscar veículos.'
        });
    });
})();
