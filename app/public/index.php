<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once '../config/database.php';

$cortesResult = $conn->query("SELECT corte_id, corte_nome, corte_preco, corte_descricao FROM corte ORDER BY corte_id");
if (!$cortesResult) { die("Erro na consulta SQL: " . $conn->error); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home | Roxinho's Barber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #121212; --cor-fundo-modal: #1f1f2e; --cor-texto: #fff; --cor-primaria: #a855f7; --cor-secundaria: #eab308; }
        body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); scroll-behavior: smooth; }
        .navbar { background-color: #000; } .navbar-brand { font-size: 1.8rem; font-weight: bold; color: var(--cor-primaria) !important; } .nav-link { color: #fff !important; margin: 0 10px; }
        .hero { background: url('https://images.unsplash.com/photo-1519415943484-cae71a5fbf4e') center/cover no-repeat; min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; position: relative; }
        .hero::after { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); }
        .hero-content { position: relative; z-index: 1; } .hero h1 { font-size: 4rem; font-weight: bold; color: var(--cor-secundaria); } .hero h2 { color: var(--cor-primaria); font-size: 2rem; }
        .btn-purple { background-color: var(--cor-primaria); color: white; border-radius: 30px; transition: 0.3s; border: none; padding: 12px 25px; font-weight: bold; }
        .btn-purple:hover { background-color: #9333ea; }
        section { padding: 80px 0; } .section-title { font-size: 2.5rem; color: var(--cor-primaria); font-weight: bold; }
        .services .card { background-color: var(--cor-fundo-modal); color: white; border: none; transition: transform 0.3s ease; text-decoration: none; display: block; }
        .services .card:hover { transform: translateY(-5px); }
        .datepicker-container { background-color: #2a2a3e; padding: 20px; border-radius: 15px; border: 1px solid var(--cor-primaria); }
        .datepicker-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 1.2rem; font-weight: bold; }
        .datepicker-nav { background: none; border: none; color: var(--cor-primaria); font-size: 1.5rem; }
        #diasCalendario { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
        .dia-semana { font-weight: bold; text-align: center; color: var(--cor-secundaria); font-size: 0.8rem; text-transform: uppercase; }
        .dia { text-align: center; line-height: 32px; height: 48px; width: 100%; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; border: 2px solid transparent; background-color: rgba(168, 85, 247, 0.1); }
        .dia:hover:not(.inativo):not(.outro-mes) { background-color: rgba(168, 85, 247, 0.3); border-color: var(--cor-primaria); }
        .dia.outro-mes { cursor: default; background-color: transparent; color: #555; border-color: transparent; }
        .dia.inativo { background-color: rgba(80, 80, 80, 0.2); color: #666; cursor: not-allowed; text-decoration: line-through; border-color: transparent; }
        .dia.selecionado { background-color: var(--cor-primaria); color: #fff; font-weight: bold; border-color: var(--cor-primaria); }
        
        /* CÓDIGO CSS ADICIONADO PARA A BORDA AMARELA */
        .dia.com-agendamento {
            border-color: var(--cor-secundaria); /* Amarelo */
        }

        .botao-horario { background-color: rgba(168, 85, 247, 0.1); color: var(--cor-texto); border: 1px solid var(--cor-primaria); padding: 8px 15px; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; }
        .botao-horario:hover, .botao-horario.selecionado { background-color: var(--cor-primaria); }
        .botao-horario.inativo { opacity: 0.4; cursor: not-allowed; background-color: transparent; border-color: #555; }
        .botao-horario.inativo:hover { background-color: transparent; }
        .botao-horario.ocupado { background-color: transparent; border-color: #dc3545; color: #dc3545; text-decoration: line-through; cursor: not-allowed; }
        .botao-horario.ocupado:hover { background-color: transparent; color: #dc3545; }
        .form-control { background-color: #2a2a3e; color: #fff; border: 1px solid var(--cor-primaria); }
        .form-control[readonly] { background-color: #333; }
        footer { background-color: #000; padding: 20px 0; text-align: center; color: #aaa; }
        .map-responsive { overflow: hidden; padding-bottom: 56.25%; position: relative; height: 0; }
        .map-responsive iframe { left: 0; top: 0; height: 100%; width: 100%; position: absolute; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">ROXINHO'S</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse justify-content-end" id="menu">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="#inicio">Início</a></li>
        <li class="nav-item"><a class="nav-link" href="#roxinho">Roxinho</a></li>
        <li class="nav-item"><a class="nav-link" href="#servicos">Serviços</a></li>
        <li class="nav-item"><a class="nav-link" href="#galeria">Galeria</a></li>
        <li class="nav-item"><a class="nav-link" href="#localizacao">Localização</a></li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-instagram fs-4"></i></a></li>
        <li class="nav-item me-2"><a href="#" class="nav-link"><i class="bi bi-whatsapp fs-4"></i></a></li>
        
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Olá, <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nome'])[0]); ?>!
                </a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="../views/usuario/meus_dados.php"><i class="bi bi-person-fill me-2"></i> Meus Dados</a></li>
                    <li><a class="dropdown-item" href="../views/usuario/meus_agendamentos.php"><i class="bi bi-calendar-check me-2"></i> Meus Agendamentos</a></li>
                    <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 1): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../controllers/admin/dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i> Painel Admin</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../controllers/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item"><a href="../views/usuario/login.php" class="btn btn-purple">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main>
    <section class="hero" id="inicio">
        <div class="hero-content container">
            <h2 class="animate__animated animate__fadeInDown">ROXINHO'S</h2>
            <h1 class="animate__animated animate__fadeInUp">BARBER</h1>
            <a href="#servicos" class="btn btn-purple mt-4">AGENDAR HORÁRIO</a>
        </div>
    </section>
    <section id="roxinho"><div class="container text-center"><h2 class="section-title">Sobre a Barbearia</h2><p class="mt-4">Nossa barbearia é muito legal e divertida. Um espaço moderno e acolhedor, ideal para cuidar do visual com estilo e atitude.</p><h3 class="mt-5 text-warning">Nossa História</h3><p>Começamos com uma ideia simples: transformar o corte de cabelo em uma experiência. Hoje somos referência na região e continuamos crescendo com nossos clientes.</p></div></section>
    <section id="servicos" class="services bg-dark py-5">
      <div class="container text-center">
        <h2 class="section-title">Nossos Serviços</h2>
        <div class="row mt-5 justify-content-center">
            <?php while($corte = $cortesResult->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6 mb-4"><a href="#" class="card h-100" data-bs-toggle="modal" data-bs-target="#agendamentoModal" data-corte-id="<?php echo $corte['corte_id']; ?>" data-corte-nome="<?php echo htmlspecialchars($corte['corte_nome']); ?>"><div class="card-body d-flex flex-column"><h4 class="card-title"><i class="bi bi-scissors"></i> <?php echo htmlspecialchars($corte['corte_nome']); ?></h4><p class="card-text text-white-50"><?php echo htmlspecialchars($corte['corte_descricao']); ?></p><strong class="text-warning mt-auto fs-5">R$ <?php echo number_format($corte['corte_preco'], 2, ',', '.'); ?></strong></div></a></div>
            <?php endwhile; ?>
        </div>
      </div>
    </section>
    <section id="galeria" class="gallery"> <div class="container text-center"><h2 class="section-title">Galeria</h2><p class="mb-4">Confira alguns dos nossos trabalhos recentes.</p><div id="carouselId" class="carousel slide" data-bs-ride="carousel"><div class="carousel-inner" role="listbox"><div class="carousel-item active"><img src="https://images.unsplash.com/photo-1599338263250-153355a297a9?w=500" class="w-100 d-block" style="height: 60vh; object-fit: cover;" alt="Corte 1"></div><div class="carousel-item"><img src="https://images.unsplash.com/photo-1621605815971-fbc333ab50a0?w=500" class="w-100 d-block" style="height: 60vh; object-fit: cover;" alt="Corte 2"></div><div class="carousel-item"><img src="https://images.unsplash.com/photo-1532710093739-947053e1a00c?w=500" class="w-100 d-block" style="height: 60vh; object-fit: cover;" alt="Corte 3"></div></div><button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button><button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button></div></div></section>
    <section id="localizacao" class="bg-dark"><div class="container text-center"><h2 class="section-title">Localização</h2><div class="map-responsive mt-4 mb-3">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3653.2981086036326!2d-46.70278788501968!3d-23.69974208461427!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce52140e7f7e9f%3A0x3e3f4f6b2e3b3d1!2sR.%20Santa%20Z%C3%A9lia%2C%20234%20-%20Jardim%20Santa%20Z%C3%A9lia%2C%20S%C3%A3o%20Paulo%20-%20SP%2C%2004833-110!5e0!3m2!1spt-BR!2sbr!4v1678886543210!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe></div><p>R. Santa Zélia, 234 - Jardim Santa Zélia, São Paulo - SP, 04833-110</p></div></section>
</main>

<div class="modal fade" id="agendamentoModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered animate__animated animate__fadeInUp">
    <div class="modal-content" style="background-color: var(--cor-fundo-modal);">
      <div class="modal-header" style="border-bottom: 1px solid var(--cor-primaria);"><h5 class="modal-title">Faça seu Agendamento</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form action="../controllers/AgendamentoController.php" method="POST" id="formAgendamento">
            <input type="hidden" name="corte_id" id="modalCorteId"><input type="hidden" id="dataAgendamento" name="data_agendamento"><input type="hidden" id="horaAgendamento" name="hora_agendamento">
            <div class="row g-4">
                <div class="col-md-7"><h5 id="servicoSelecionadoTitulo" class="text-warning mb-3"></h5><div class="datepicker-container"><div class="datepicker-header"><button type="button" class="datepicker-nav" id="mesAnterior"><</button><div id="mesAnoTitulo"></div><button type="button" class="datepicker-nav" id="mesSeguinte">></button></div><div id="diasDaSemana" class="d-flex justify-content-around mb-2"></div><div id="diasCalendario"></div></div></div>
                <div class="col-md-5 border-start border-secondary" id="horariosView" style="display: none;"><h5>Horários Disponíveis</h5><p id="dataSelecionadaSubtitulo" class="text-white-50 small"></p><div id="horariosDisponiveis" class="d-grid gap-2"></div></div>
            </div>
        </form>
      </div>
      <div class="modal-footer" style="border-top: 1px solid var(--cor-primaria);"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" form="formAgendamento" class="btn btn-purple" id="btnConfirmar" disabled>Confirmar Agendamento</button></div>
    </div>
  </div>
</div>

<footer><div class="container"><p>© <?php echo date("Y"); ?> Roxinho's Barber</p></div></footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const agendamentoModal = document.getElementById('agendamentoModal'); let dataAtual = new Date();
    agendamentoModal.addEventListener('show.bs.modal', function (event) {
        const isUserLoggedIn = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
        if (!isUserLoggedIn) { event.preventDefault(); window.location.href = '../usuario/login.php?erro=precisa_logar'; return; }
        const button = event.relatedTarget;
        agendamentoModal.querySelector('#modalCorteId').value = button.getAttribute('data-corte-id');
        agendamentoModal.querySelector('#servicoSelecionadoTitulo').textContent = `Serviço: ${button.getAttribute('data-corte-nome')}`;
        dataAtual = new Date(); carregaCalendario(dataAtual.getFullYear(), dataAtual.getMonth() + 1);
    });
    agendamentoModal.addEventListener('hidden.bs.modal', function() { resetModal(); });
    document.getElementById('mesAnterior').addEventListener('click', () => { dataAtual.setMonth(dataAtual.getMonth() - 1); carregaCalendario(dataAtual.getFullYear(), dataAtual.getMonth() + 1); });
    document.getElementById('mesSeguinte').addEventListener('click', () => { dataAtual.setMonth(dataAtual.getMonth() + 1); carregaCalendario(dataAtual.getFullYear(), dataAtual.getMonth() + 1); });

    // AQUI ESTÁ A FUNÇÃO ATUALIZADA
    function carregaCalendario(ano, mes) {
        resetSelecao(); document.getElementById('mesAnoTitulo').textContent = new Date(ano, mes - 1).toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' }).toUpperCase();
        fetch(`../includes/funcoes.php?acao=carregaCalendario&ano=${ano}&mes=${mes}`)
        .then(response => { if (!response.ok) { throw new Error('Erro de rede'); } return response.json(); })
        .then(data => {
            if (data.erro) { throw new Error(data.erro); }
            const diasCalendario = document.getElementById('diasCalendario'); const diasDaSemanaContainer = document.getElementById('diasDaSemana'); diasCalendario.innerHTML = '';
            if(diasDaSemanaContainer.innerHTML === '') { ['Do', 'Se', 'Te', 'Qa', 'Qi', 'Se', 'Sa'].forEach(dia => { diasDaSemanaContainer.innerHTML += `<div class="dia-semana">${dia}</div>`; }); }
            const primeiroDia = new Date(ano, mes - 1, 1).getDay(); const totalDias = new Date(ano, mes, 0).getDate();
            for (let i = 0; i < primeiroDia; i++) { diasCalendario.innerHTML += '<div class="dia outro-mes"></div>'; }
            const hoje = new Date(); hoje.setHours(0,0,0,0);
            for (let dia = 1; dia <= totalDias; dia++) {
                const diaEl = document.createElement('div'); diaEl.className = 'dia'; diaEl.textContent = dia;
                const dataCompleta = `${ano}-${String(mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
                const dataLoop = new Date(ano, mes - 1, dia);

                // Aplica a classe da borda amarela se o dia tiver agendamento
                if (data.comAgendamento && data.comAgendamento.includes(dataCompleta)) {
                    diaEl.classList.add('com-agendamento');
                }

                if (dataLoop < hoje || (data.inativos && data.inativos.includes(dataCompleta))) { 
                    diaEl.classList.add('inativo'); 
                } else { 
                    diaEl.dataset.data = dataCompleta; 
                    diaEl.addEventListener('click', selecionaDia); 
                }
                diasCalendario.appendChild(diaEl);
            }
        }).catch(error => { console.error('Erro ao carregar calendário:', error); document.getElementById('diasCalendario').innerHTML = '<p class="text-danger p-3">Não foi possível carregar o calendário. Verifique o console (F12).</p>'; });
    }

    function selecionaDia(event) {
        document.querySelectorAll('.dia.selecionado').forEach(el => el.classList.remove('selecionado'));
        event.target.classList.add('selecionado');
        const dataSelecionada = event.target.dataset.data;
        document.getElementById('dataAgendamento').value = dataSelecionada;
        document.getElementById('dataSelecionadaSubtitulo').textContent = new Date(dataSelecionada + 'T00:00:00').toLocaleDateString('pt-BR', { dateStyle: 'full' });
        const agora = new Date();
        const hojeString = agora.toISOString().split('T')[0];
        const isHoje = (dataSelecionada === hojeString);
        fetch(`../includes/funcoes.php?acao=buscaHorarios&data=${dataSelecionada}`)
        .then(response => { if (!response.ok) { throw new Error('Erro de rede'); } return response.json(); })
        .then(data => {
            const container = document.getElementById('horariosDisponiveis');
            container.innerHTML = '';
            if(!data.todosOsHorarios || data.todosOsHorarios.length === 0){
                container.innerHTML = '<p class="text-white-50">Não há horários de trabalho definidos para este dia.</p>';
            } else { 
                data.todosOsHorarios.forEach(horario => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'botao-horario w-100';
                    btn.textContent = horario;
                    btn.dataset.hora = horario;
                    const isOcupado = data.horariosOcupados && data.horariosOcupados.includes(horario);
                    let horarioPassou = false;
                    if (isHoje) {
                        const [horas, minutos] = horario.split(':');
                        const horarioDoAgendamento = new Date();
                        horarioDoAgendamento.setHours(horas, minutos, 0, 0);
                        if (horarioDoAgendamento < agora) {
                            horarioPassou = true;
                        }
                    }
                    if (isOcupado) {
                        btn.classList.add('ocupado');
                        btn.disabled = true;
                    } else if (horarioPassou) {
                        btn.classList.add('inativo');
                        btn.disabled = true;
                    } else {
                        btn.addEventListener('click', selecionaHorario);
                    }
                    container.appendChild(btn); 
                }); 
            }
            document.getElementById('horariosView').style.display = 'block';
            document.getElementById('horaAgendamento').value = '';
            document.getElementById('btnConfirmar').disabled = true;
        }).catch(error => {
            console.error('Erro ao buscar horários:', error);
            document.getElementById('horariosDisponiveis').innerHTML = '<p class="text-danger">Erro ao carregar horários.</p>';
        });
    }

    function selecionaHorario(event){ document.querySelectorAll('.botao-horario.selecionado').forEach(el => el.classList.remove('selecionado')); event.target.classList.add('selecionado'); document.getElementById('horaAgendamento').value = event.target.dataset.hora; document.getElementById('btnConfirmar').disabled = false; }
    function resetSelecao(){ document.getElementById('horariosView').style.display = 'none'; document.getElementById('horariosDisponiveis').innerHTML = ''; document.getElementById('btnConfirmar').disabled = true; }
    function resetModal() { resetSelecao(); document.getElementById('formAgendamento').reset(); }
});
</script>

</body>
</html>