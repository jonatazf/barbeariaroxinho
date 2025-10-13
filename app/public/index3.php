<?php
// Inicia a sessão para que possamos acessar as variáveis de login em toda a página.
// Deve ser a primeira coisa no arquivo.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Roxinho's Barber</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Barlow Condensed', sans-serif;
      scroll-behavior: smooth;
      background-color: #121212;
      color: #fff;
    }
    .branco {color: #fff}
    .navbar { background-color: #000; }
    .navbar-brand { font-size: 1.8rem; font-weight: bold; color: #d633ff !important; }
    .nav-link { color: #fff !important; margin: 0 10px; }
    .hero { background: url('https://images.unsplash.com/photo-1519415943484-cae71a5fbf4e') center/cover no-repeat; height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; position: relative; }
    .hero::after { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); }
    .hero-content { position: relative; z-index: 1; color: white; }
    .hero h1 { font-size: 4rem; font-weight: bold; color: #e6b800; }
    .hero h2 { color: #d633ffff; font-size: 2rem; }
    .btn-purple { background-color: #d633ff; color: white; padding: 12px 30px; font-size: 1.2rem; border-radius: 30px; transition: 0.3s; }
    .btn-purple:hover { background-color: #b026d1; }
    .btn-outline-purple {  color: #d633ff; background-color: transparent; background-image: none; border-color: #d633ff;}
    .btn-outline-purple:hover { color: #791d90ff; background-color: transparent; background-image: none; border-color: #791d90ff;}
    section { padding: 80px 20px; }
    .section-title { font-size: 2.5rem; color: #d633ff; font-weight: bold; }
    .services .card { background-color: #1f1f2e; color: white; border: none; transition: transform 0.3s ease; text-decoration: none; display: block; }
    .services .card:hover { transform: translateY(-5px); }
    .gallery img { border-radius: 10px; transition: 0.3s; }
    .gallery img:hover { transform: scale(1.05); }
    .map-responsive { overflow: hidden; padding-bottom: 56.25%; position: relative; height: 0; }
    .map-responsive iframe { left: 0; top: 0; height: 100%; width: 100%; position: absolute; }
    footer { background-color: #000; padding: 20px 0; text-align: center; color: #aaa; }
    /* Estilo para os inputs do formulário no modal */
    .form-control { background-color: #2a2a3e; color: #fff; border: 1px solid #d633ff; }
    .form-control:focus { background-color: #2a2a3e; color: #fff; border-color: #e6b800; box-shadow: 0 0 0 0.25rem rgba(230, 184, 0, 0.25); }
    .form-control::placeholder { color: #aaa; }
    .form-control[readonly] { background-color: #333; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#inicio">ROXINHO'S</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml;charset=utf8,%3Csvg viewBox=\'0 0 30 30\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath stroke=\'rgba(255, 255, 255, 0.5)\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' d=\'M4 7h22M4 15h22M4 23h22\'/%3E%3C/svg%3E');"></span>
    </button>
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
            <li class="nav-item"><span class="navbar-text me-3 branco">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</span></li>
            <li class="nav-item"><a href="../controllers/logout.php" class="btn btn-outline-purple">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a href="../views/usuario/login.php" class="btn btn-outline-purple">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<section class="hero d-flex" id="inicio">
  <div class="hero-content container">
    <h2>ROXINHO'S</h2>
    <h1>BARBER</h1>
    <a href="#roxinho" class="btn btn-purple mt-4">SAIBA MAIS</a>
  </div>
</section>

<section id="roxinho">
  <div class="container text-center">
    <h2 class="section-title">Sobre a Barbearia</h2>
    <p class="mt-4">Nossa barbearia é muito legal e divertida. Um espaço moderno e acolhedor, ideal para cuidar do visual com estilo e atitude.</p>
    <h3 class="mt-5 text-warning">Nossa História</h3>
    <p>Começamos com uma ideia simples: transformar o corte de cabelo em uma experiência. Hoje somos referência na região e continuamos crescendo com nossos clientes.</p>
  </div>
</section>

<section id="servicos" class="services bg-dark">
  <div class="container text-center">
    <h2 class="section-title">Serviços</h2>
    <div class="row mt-5">
      <div class="col-md-4 mb-4">
        <a href="#" style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#agendamentoModal" data-corte-id="1" data-corte-nome="Corte Social"><div class="card p-3">
          <h4><i class="bi bi-scissors"></i> Corte Social</h4>
          <p>O corte ideal para você!</p>
          <strong class="text-warning">R$ 24,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href="#" style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#agendamentoModal" data-corte-id="2" data-corte-nome="Navalhado"><div class="card p-3">
          <h4><i class="bi bi-droplet"></i> Navalhado</h4>
          <p>Cabelo na régua total.</p>
          <strong class="text-warning">R$ 29,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href="#" style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#agendamentoModal" data-corte-id="3" data-corte-nome="Barba"><div class="card p-3">
          <h4><i class="bi bi-person-fill"></i> Barba</h4>
          <p>Barba feita com perfeição.</p>
          <strong class="text-warning">R$ 19,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href="#" style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#agendamentoModal" data-corte-id="4" data-corte-nome="Sobrancelha"><div class="card p-3">
          <h4><i class="bi bi-eye"></i> Sobrancelha</h4>
          <p>Definição precisa.</p>
          <strong class="text-warning">R$ 9,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href="#" style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#agendamentoModal" data-corte-id="5" data-corte-nome="Completo (Corte+Barba+Sobrancelha)"><div class="card p-3">
          <h4><i class="bi bi-stars"></i> Completo</h4>
          <p>Corte + barba + sobrancelha.</p>
          <strong class="text-warning">R$ 59,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href="#" style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#agendamentoModal" data-corte-id="6" data-corte-nome="Pintar"><div class="card p-3">
          <h4><i class="bi bi-brush"></i> Pintar</h4>
          <p>Pintura capilar profissional.</p>
          <strong class="text-warning">R$ 29,99</strong>
        </div></a>
      </div>
    </div>
    
  </div>
</section>

<section id="galeria" class="gallery"> <div class="container text-center">
    <h2 class="section-title">Galeria</h2>
    <p class="mb-4">Confira alguns dos nossos trabalhos recentes.</p>
    <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner" role="listbox">
        <div class="carousel-item active"><img src="https://images.unsplash.com/photo-1599338263250-153355a297a9?w=500" class="w-100 d-block" style="height: 60vh; object-fit: cover;" alt="Corte 1"></div>
        <div class="carousel-item"><img src="https://images.unsplash.com/photo-1621605815971-fbc333ab50a0?w=500" class="w-100 d-block" style="height: 60vh; object-fit: cover;" alt="Corte 2"></div>
        <div class="carousel-item"><img src="https://images.unsplash.com/photo-1532710093739-947053e1a00c?w=500" class="w-100 d-block" style="height: 60vh; object-fit: cover;" alt="Corte 3"></div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>
    </div>
  </div>
</section>

<section id="localizacao" class="bg-dark">
  <div class="container text-center">
    <h2 class="section-title">Localização</h2>
    <div class="map-responsive mt-4 mb-3">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3653.2981086036326!2d-46.70278788501968!3d-23.69974208461427!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce52140e7f7e9f%3A0x3e3f4f6b2e3b3d1!2sR.%20Santa%20Z%C3%A9lia%2C%20234%20-%20Jardim%20Santa%20Z%C3%A9lia%2C%20S%C3%A3o%20Paulo%20-%20SP%2C%2004833-110!5e0!3m2!1spt-BR!2sbr!4v1678886543210!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
    <p>R. Santa Zélia, 234 - Jardim Santa Zélia, São Paulo - SP, 04833-110</p>
  </div>
</section>

<div class="modal fade" id="agendamentoModal" tabindex="-1" aria-labelledby="agendamentoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered animate__animated animate__fadeInUp">
    <div class="modal-content" style="background-color: #1f1f2e; color: #fff;">
      <div class="modal-header" style="border-bottom: 1px solid #d633ff;">
        <h5 class="modal-title" id="agendamentoModalLabel">Faça seu Agendamento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="controllers/agendamentoController.php" method="POST" id="formAgendamento">
          <input type="hidden" name="corte_id" id="modalCorteId">
          <div class="mb-3">
            <label for="nomeCliente" class="form-label">Seu Nome</label>
            <input type="text" class="form-control" id="nomeCliente" name="nome_cliente" value="<?php echo isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : ''; ?>" <?php echo isset($_SESSION['usuario_nome']) ? 'readonly' : ''; ?> required>
          </div>
          <div class="mb-3">
            <label for="servicoSelecionado" class="form-label">Serviço Selecionado</label>
            <input type="text" class="form-control" id="servicoSelecionado" name="servico_selecionado" readonly>
          </div>
          <div class="mb-3">
            <label for="dataAgendamento" class="form-label">Escolha a Data</label>
            <input type="date" class="form-control" id="dataAgendamento" name="data_agendamento" required>
          </div>
          <div class="mb-3">
            <label for="horaAgendamento" class="form-label">Escolha o Horário</label>
            <input type="time" class="form-control" id="horaAgendamento" name="hora_agendamento" required>
          </div>
        </form>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #d633ff;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="submit" form="formAgendamento" class="btn btn-purple">Confirmar Agendamento</button>
      </div>
    </div>
  </div>
</div>

<footer>
  <div class="container">
    <p>© 2025 Roxinho's Barber | Todos os direitos reservados</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const agendamentoModal = document.getElementById('agendamentoModal');
  agendamentoModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const corteId = button.getAttribute('data-corte-id');
    const corteNome = button.getAttribute('data-corte-nome');
    const modalCorteIdInput = agendamentoModal.querySelector('#modalCorteId');
    const servicoSelecionadoInput = agendamentoModal.querySelector('#servicoSelecionado');
    
    if (corteId) {
      modalCorteIdInput.value = corteId;
      servicoSelecionadoInput.value = corteNome;
    } else {
      modalCorteIdInput.value = '';
      servicoSelecionadoInput.value = '';
      servicoSelecionadoInput.placeholder = 'Selecione um serviço na página inicial';
    }   
  });
</script>

</body>
</html>