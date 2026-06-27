<?php
$tituloPagina = 'Tipos de atendimento';
require __DIR__ . '/../layouts/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Tipos de atendimento</h1>
        <p class="text-secondary mb-0">Categorias utilizadas nos registros de atendimento.</p>
    </div>
    <button class="btn btn-success" type="button" onclick="novoTipo()">Novo tipo</button>
</div>
<div id="alerta"></div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaTipos">
                <tr>
                    <td colspan="5" class="text-center py-4">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modalTipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="tituloFormulario">Novo tipo</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTipo">
                <div class="modal-body">
                    <input type="hidden" name="id" id="tipoId">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome *</label>
                            <input class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="descricao" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-success" type="submit">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const formTipo = document.getElementById('formTipo');
    const tabelaTipos = document.getElementById('tabelaTipos');
    const campoTipoId = document.getElementById('tipoId');
    const tituloFormulario = document.getElementById('tituloFormulario');

    const tipoModal = () => bootstrap.Modal.getOrCreateInstance(
        document.getElementById('modalTipo')
    );

    function abrirFormulario() {
        tipoModal().show();
    }

    function fecharFormulario() {
        tipoModal().hide();
        formTipo.reset();
        campoTipoId.value = '';
    }

    function novoTipo() {
        formTipo.reset();
        campoTipoId.value = '';
        tituloFormulario.textContent = 'Novo tipo';
        abrirFormulario();
    }
    async function carregarTipos() {
        try {
            const resposta = await AtendeLabApi.get(
                'tipos',
                'listar'
            );
            const tipos = AtendeLabApi.toList(resposta);
            if (tipos.length === 0) {
                tabelaTipos.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            Nenhum tipo cadastrado.
                        </td>
                    </tr>
                    `;
                return;
            }
            tabelaTipos.innerHTML = tipos.map((tipo) => {
                const classeStatus = tipo.status === 'ativo' ?
                    'text-bg-success' :
                    'text-bg-secondary';
                return `
                    <tr>
                        <td>
                            ${AtendeLabApi.escape(tipo.nome)}
                        </td>
                        <td>
                            ${AtendeLabApi.escape(tipo.descricao || '')}
                        </td>
                        <td>
                            <span class="badge ${classeStatus}">
                                ${AtendeLabApi.escape(tipo.status)}
                            </span>
                        </td>
                        <td class="text-end">
                            <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            onclick="editarTipo(${Number(tipo.id)})">
                                Editar
                            </button>
                            <button
                            type="button"
                            class="btn btn-sm btn-outline-danger"
                            onclick="inativarTipo(${Number(tipo.id)})">
                                Inativar
                            </button>
                        </td>
                    </tr>
                    `;
            }).join('');
        } catch (error) {
            AtendeLabApi.showAlert(
                'alerta',
                error.message,
                'danger'
            );
        }
    }
    async function editarTipo(id) {
        try {
            const tipo = AtendeLabApi.toObject(
                await AtendeLabApi.get('tipos', 'buscar', { id })
            );
            formTipo.reset();
            tituloFormulario.textContent = 'Editar tipo';
            campoTipoId.value = String(tipo.id);
            for (const [nomeCampo, valorCampo] of Object.entries(tipo)) {
                if (nomeCampo === 'criado_em' || nomeCampo === 'atualizado_em') continue;
                const campo = formTipo.elements.namedItem(nomeCampo);
                if (campo && 'value' in campo) {
                    campo.value = valorCampo ?? '';
                }
            }
            abrirFormulario();
        } catch (error) {
            AtendeLabApi.showAlert('alerta', error.message, 'danger');
        }
    }
    formTipo.addEventListener('submit', async (event) => {
        event.preventDefault();
        const id = campoTipoId.value;
        const acao = id ?
            'atualizar' :
            'criar';
        const mensagemSucesso = id ?
            'Tipo atualizado com sucesso.' :
            'Tipo cadastrado com sucesso.';
        try {
            await AtendeLabApi.post(
                'tipos',
                acao,
                new FormData(formTipo)
            );
            AtendeLabApi.showAlert(
                'alerta',
                mensagemSucesso,
                'success'
            );
            fecharFormulario();
            await carregarTipos();
        } catch (error) {
            AtendeLabApi.showAlert(
                'alerta',
                error.message,
                'danger'
            );
        }
    });
    async function inativarTipo(id) {
        const confirmou = confirm(
            'Deseja realmente inativar este tipo?'
        );
        if (!confirmou) {
            return;
        }
        try {
            await AtendeLabApi.post(
                'tipos',
                'inativar', {
                    id
                }
            );
            AtendeLabApi.showAlert(
                'alerta',
                'Tipo inativado com sucesso.',
                'success'
            );
            await carregarTipos();
        } catch (error) {
            AtendeLabApi.showAlert(
                'alerta',
                error.message,
                'danger'
            );
        }
    }
    document.addEventListener(
        'DOMContentLoaded',
        carregarTipos
    );
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>