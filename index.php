<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'app/config/database.php';

$cortesResult = $conn->query("SELECT corte_id, corte_nome, corte_preco, corte_descricao FROM corte ORDER BY corte_id");
if (!$cortesResult) { die("Erro na consulta SQL: " . $conn->error); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roxinho’s Barber | Agende seu Corte</title>
  <link rel="icon" href="app/public/icon.ico" type="image/x-icon" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <style>
    :root {
      --cor-fundo: #121212;
      --cor-fundo-modal: #1f1f2e;
      --cor-texto: #fff;
      --cor-primaria: #a855f7;
      --cor-secundaria: #eab308;
    }
    body {
      font-family: 'Barlow Condensed', sans-serif;
      background-color: var(--cor-fundo);
      color: var(--cor-texto);
      scroll-behavior: smooth;
    }
    .navbar {
      background-color: #000;
      box-shadow: 0 2px 5px rgba(0,0,0,0.8);
    }
    .navbar-brand {
      font-size: 1.8rem;
      color: var(--cor-primaria) !important;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .nav-link {
      color: #fff !important;
      margin: 0 10px;
    }
    .nav-link:hover {
      color: var(--cor-secundaria) !important;
      transition: .3s;
    }
    .hero {
      background: url('https://images.unsplash.com/photo-1519415943484-cae71a5fbf4e') center/cover no-repeat;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      text-align: center;
    }
    .hero::after {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.6);
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    .hero h1 {
      font-size: 4rem;
      font-weight: bold;
      color: var(--cor-secundaria);
    }
    .hero h2 {
      color: var(--cor-primaria);
      font-size: 2rem;
    }
    .btn-purple {
      background-color: var(--cor-primaria);
      color: white;
      border-radius: 30px;
      border: none;
      padding: 12px 25px;
      font-weight: bold;
      transition: .3s;
    }
    .btn-purple:hover { background-color: #9333ea; }
    section {
      padding: 80px 0;
    }
    .section-title {
      font-size: 2.5rem;
      color: var(--cor-primaria);
      font-weight: bold;
    }
    .card {
      background-color: var(--cor-fundo-modal);
      border: none;
      color: white;
      transition: transform 0.3s ease, box-shadow 0.3s;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
    }
    footer {
      background-color: #000;
      color: #aaa;
      text-align: center;
      padding: 20px 0;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">ROXINHO'S</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
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
            <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              Olá, <?php echo htmlspecialchars(explode(' ', $_SESSION['usuario_nome'])[0]); ?>!
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
              <li><a class="dropdown-item" href="app/views/usuario/meus_dados.php"><i class="bi bi-person-fill me-2"></i> Meus Dados</a></li>
              <li><a class="dropdown-item" href="app/views/usuario/meus_agendamentos.php"><i class="bi bi-calendar-check me-2"></i> Meus Agendamentos</a></li>
              <?php if ($_SESSION['usuario_tipo'] == 1): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="app/views/admin/dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i> Painel Admin</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="app/controllers/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a href="app/views/usuario/login.php" class="btn btn-purple ms-3">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main id="inicio" class="hero">
  <div class="hero-content container">
    <h2 class="animate__fadeInDown">ROXINHO'S</h2>
    <h1 class="animate__fadeInUp">BARBER</h1>
    <a href="#servicos" class="btn btn-purple mt-4">AGENDAR HORÁRIO</a>
  </div>
</main>

<section id="roxinho" class="text-center container">
  <h2 class="section-title">Sobre a Barbearia</h2>
  <p class="mt-4">Um espaço moderno, acolhedor e cheio de estilo. O lugar certo para cuidar do seu visual.</p>
</section>

<section id="servicos" class="bg-dark text-center py-5">
  <div class="container">
    <h2 class="section-title mb-5">Nossos Serviços</h2>
    <div class="row justify-content-center">
      <?php while($corte = $cortesResult->fetch_assoc()): ?>
        <div class="col-lg-4 col-md-6 mb-4">
          <a href="#" class="card p-3 text-decoration-none"
             data-bs-toggle="modal"
             data-bs-target="#agendamentoModal"
             data-corte-id="<?php echo $corte['corte_id']; ?>"
             data-corte-nome="<?php echo htmlspecialchars($corte['corte_nome']); ?>">
            <div class="card-body d-flex flex-column justify-content-between">
              <h4><i class="bi bi-scissors"></i> <?php echo htmlspecialchars($corte['corte_nome']); ?></h4>
              <p class="text-white-50"><?php echo htmlspecialchars($corte['corte_descricao']); ?></p>
              <p class="text-warning fw-bold fs-5">R$ <?php echo number_format($corte['corte_preco'], 2, ',', '.'); ?></p>
            </div>
          </a>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<section id="galeria" class="text-center container py-5">
  <h2 class="section-title">Galeria</h2>
  <p>Confira nossos melhores trabalhos</p>
  <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active"><img src="https://images.unsplash.com/photo-1599338263250-153355a297a9?w=800" class="d-block w-100 rounded"></div>
      <div class="carousel-item"><img src="https://images.unsplash.com/photo-1621605815971-fbc333ab50a0?w=800" class="d-block w-100 rounded"></div>
      <div class="carousel-item"><img src="https://images.unsplash.com/photo-1532710093739-947053e1a00c?w=800" class="d-block w-100 rounded"></div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
  </div>
</section>

<section id="localizacao" class="bg-dark text-center py-5">
  <h2 class="section-title">Localização</h2>
  <div class="map-responsive my-4">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18..." width="600" height="450" style="border:0;" allowfullscreen loading="lazy"></iframe>
  </div>
  <p>R. Santa Zélia, 234 - Jardim Santa Zélia, São Paulo - SP</p>
</section>

<footer>
  <p>© <?php echo date("Y"); ?> Roxinho’s Barber. Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="app/public/js/agendamento.js" defer></script>
</body>
</html>
