(function () {
    const URL_BUSCA = '/projeto_faculdade/models/buscar-cliente.php';
    const MSG_INVALIDO = 'Selecione um cliente da lista.';

    function iniciarBuscaCliente() {
        const input  = document.getElementById('buscaCliente');
        const hidden = document.getElementById('cliente_id');
        const lista  = document.getElementById('listaClientes');
        if (!input || !hidden || !lista) return;

        let timer = null;
        let controller = null;
        let ativo = -1;

        // Enquanto não escolher da lista, o campo é inválido
        input.setCustomValidity(MSG_INVALIDO);

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

        function selecionar(c) {
            input.value  = `${c.nome} · ${c.cpf}`;
            hidden.value = c.id;
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            fechar();
        }

        function limparSelecao() {
            hidden.value = '';
            input.setCustomValidity(MSG_INVALIDO);
        }

        function render(clientes) {
            lista.innerHTML = '';

            if (clientes.length === 0) {
                const vazio = document.createElement('div');
                vazio.className = 'list-group-item text-muted small';
                vazio.textContent = 'Nenhum cliente encontrado.';
                lista.appendChild(vazio);
            }

            clientes.forEach(c => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.setAttribute('role', 'option');
                btn.textContent = `${c.nome} · ${c.cpf}`;   // textContent evita injeção de HTML
                btn.addEventListener('click', () => selecionar(c));
                btn._cliente = c;
                lista.appendChild(btn);
            });

            ativo = -1;
            abrir();
        }

        async function buscar(termo) {
            if (controller) controller.abort();
            controller = new AbortController();

            try {
                const resp = await fetch(`${URL_BUSCA}?q=${encodeURIComponent(termo)}`, {
                    signal: controller.signal
                });
                if (!resp.ok) throw new Error('Falha na busca');
                render(await resp.json());
            } catch (e) {
                if (e.name === 'AbortError') return;
                lista.innerHTML = '<div class="list-group-item text-danger small">Erro ao buscar clientes.</div>';
                abrir();
            }
        }

        // Digitou: invalida a escolha anterior e busca de novo (com debounce)
        input.addEventListener('input', function () {
            limparSelecao();
            clearTimeout(timer);
            timer = setTimeout(() => buscar(input.value.trim()), 300);
        });

        // Ao focar sem cliente escolhido, já mostra sugestões
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
                selecionar(itens[ativo]._cliente);
            } else if (e.key === 'Escape') {
                fechar();
            }
        });

        // Clicou fora: fecha a lista
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#buscaCliente, #listaClientes')) fechar();
        });
    }

    document.addEventListener('DOMContentLoaded', iniciarBuscaCliente);
})();