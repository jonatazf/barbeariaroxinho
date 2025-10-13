<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Roxinho's Barber</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">

  <style>
    .branco{
      color: white;
    }
    body {
      font-family: 'Barlow Condensed', sans-serif;
      scroll-behavior: smooth;
      background-color: #121212;
      color: #fff;
    }
    .navbar {
      background-color: #000;
    }
    .navbar-brand {
      font-size: 1.8rem;
      font-weight: bold;
      color: #d633ff !important;
    }
    .nav-link {
      color: #fff !important;
      margin: 0 10px;
    }
    .hero {
      background: url('https://images.unsplash.com/photo-1519415943484-cae71a5fbf4e') center/cover no-repeat;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
    }
    .hero::after {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.7);
    }
    .hero-content {
      position: relative;
      z-index: 1;
      color: white;
    }
    .hero h1 {
      font-size: 4rem;
      font-weight: bold;
      color: #e6b800;
    }
    .hero h2 {
      color: #d633ff;
      font-size: 2rem;
    }
    .btn-purple {
      background-color: #d633ff;
      color: white;
      padding: 12px 30px;
      font-size: 1.2rem;
      border-radius: 30px;
      transition: 0.3s;
    }
    .btn-purple:hover {
      background-color: #b026d1;
    }
    section {
      padding: 80px 20px;
    }
    .section-title {
      font-size: 2.5rem;
      color: #d633ff;
      font-weight: bold;
    }
    .services .card {
      background-color: #1f1f2e;
      color: white;
      border: none;
      transition: transform 0.3s ease;
    }
    .services .card:hover {
      transform: translateY(-5px);
    }
    .gallery img {
      border-radius: 10px;
      transition: 0.3s;
    }
    .gallery img:hover {
      transform: scale(1.05);
    }
    .map-responsive {
      overflow: hidden;
      padding-bottom: 56.25%;
      position: relative;
      height: 0;
    }
    .map-responsive iframe {
      left: 0;
      top: 0;
      height: 100%;
      width: 100%;
      position: absolute;
    }
    footer {
      background-color: #000;
      padding: 20px 0;
      text-align: center;
      color: #aaa;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<?php
// Inicia a sessão para que possamos acessar as variáveis de login.
// É importante que esta seja a PRIMEIRA coisa no arquivo.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">ROXINHO'S</a>
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

      </ul>

      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
            <a href="#" class="nav-link"><i class="bi bi-instagram fs-4"></i></a>
        </li>
        <li class="nav-item me-2">
            <a href="#" class="nav-link"><i class="bi bi-whatsapp fs-4"></i></a>
        </li>

        <?php
        // A mágica acontece aqui:
        // Se a sessão do usuário existir...
        if (isset($_SESSION['usuario_id'])):
        ?>
            <li class="nav-item">
              <span class="navbar-text me-3 branco">
                Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!
              </span>
            </li>

            <li class="nav-item">
              <a href="../controllers/logout.php" class="btn btn-outline-light">Logout</a>
            </li>

        <?php
        else:
            // Se a sessão NÃO existir, mostramos o botão de login normal.
        ?>
            <li class="nav-item">
              <a href="../views/usuario/login.php" class="btn btn-outline-light">Login</a>
            </li>
        <?php
        endif;
        ?>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero d-flex" id="inicio">
  <div class="hero-content container">
    <h2>ROXINHO'S</h2>
    <h1>BARBER</h1>
    <a href="#roxinho" class="btn btn-purple mt-4">SAIBA MAIS</a>
  </div>
</section>

<!-- SOBRE -->
<section id="roxinho">
  <div class="container text-center">
    <h2 class="section-title">Sobre a Barbearia</h2>
    <p class="mt-4">Nossa barbearia é muito legal e divertida. Um espaço moderno e acolhedor, ideal para cuidar do visual com estilo e atitude.</p>
    <h3 class="mt-5 text-warning">Nossa História</h3>
    <p>Começamos com uma ideia simples: transformar o corte de cabelo em uma experiência. Hoje somos referência na região e continuamos crescendo com nossos clientes.</p>
  </div>
</section>

<!-- SERVIÇOS -->
<section id="servicos" class="services bg-dark">
  <div class="container text-center">
    <h2 class="section-title">Serviços</h2>
    <div class="row mt-5">
      <div class="col-md-4 mb-4">
        <a href="agendamento/index.php?corteId=1" style="text-decoration: none;"><div class="card p-3">
          <h4><i class="bi bi-scissors"></i> Corte Social</h4>
          <p>O corte ideal para você!</p>
          <strong class="text-warning">R$ 24,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href = "agendamento/index.php?corteId=2" style="text-decoration: none;"><div class="card p-3">
          <h4><i class="bi bi-droplet"></i> Navalhado</h4>
          <p>Cabelo na régua total.</p>
          <strong class="text-warning">R$ 29,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href = "agendamento/index.php?corteId=3" style="text-decoration: none;"><div class="card p-3">
          <h4><i class="bi bi-person-fill"></i> Barba</h4>
          <p>Barba feita com perfeição.</p>
          <strong class="text-warning">R$ 19,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href = "agendamento/index.php?corteId=4" style="text-decoration: none;"><div class="card p-3">
          <h4><i class="bi bi-eye"></i> Sobrancelha</h4>
          <p>Definição precisa.</p>
          <strong class="text-warning">R$ 9,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href = "agendamento/index.php?corteId=5" style="text-decoration: none;"><div class="card p-3">
          <h4><i class="bi bi-stars"></i> Completo</h4>
          <p>Corte + barba + sobrancelha.</p>
          <strong class="text-warning">R$ 59,99</strong>
        </div></a>
      </div>
      <div class="col-md-4 mb-4">
        <a href = "agendamento/index.php?corteId=6" style="text-decoration: none;"><div class="card p-3">
          <h4><i class="bi bi-brush"></i> Pintar</h4>
          <p>Pintura capilar profissional.</p>
          <strong class="text-warning">R$ 29,99</strong>
        </div></a>
      </div>
      
      
    </div>
    <a
        name=""
        id=""
        class="btn btn-purple"
        href="agendamento/index.php"
        role="button"
        >AGENDE JA!</a
      >
  </div>
</section>

<!-- GALERIA -->
<section id="galeria" class="gallery bg-secondary">
  <div class="container text-center">
    <h2 class="section-title">Galeria</h2>
    <p class="mb-4">Confira alguns dos nossos trabalhos recentes.</p>
    <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
      <ol class="carousel-indicators">
        <li
          data-bs-target="#carouselId"
          data-bs-slide-to="0"
          class="active"
          aria-current="true"
          aria-label="First slide"
        ></li>
        <li
          data-bs-target="#carouselId"
          data-bs-slide-to="1"
          aria-label="Second slide"
        ></li>
        <li
          data-bs-target="#carouselId"  
          data-bs-slide-to="2"
          aria-label="Third slide"
        ></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="carousel-item active">
          <img
            src="https://lh3.googleusercontent.com/gps-cs-s/AC9h4np8CfDa7A5hlJLcAt3OKb922A7CIXkurBGGMtKlHDKfVC6W2e-BC701syac0p7pXFwjjIhyqyN3MQw_VXSGSf1VliJu-Uvsf6_li5BmYItk3iS-FSdI9u688UbKhNik_q2YI1ox=s680-w680-h510-rw"
            class="w-100 d-block"
            alt="First slide"
            width="50vw"
            height="500vh"
          />

        </div>
        <div class="carousel-item">
          <img
            src="https://lh3.googleusercontent.com/p/AF1QipO4cCvhJSF2S5ZzRTGvflzazuzEALbrZ1sHEbeg=w141-h141-n-k-no-nu"
            class="w-100 d-block"
            alt="Second slide"
            width="50vw"
            height="500vh"
          />

        </div>
        <div class="carousel-item">
          <img
            src="https://lh3.googleusercontent.com/gps-cs-s/AC9h4nq1LcfqMMojqKZSWwMYeM757pqKiAP5c17Ry871GwrWGfSlh8xJ8jHVFO6VpnKlPIf7ZIPtJCrgiA6cga6KizxtTezVVUxIlhMuj2XXShzLRqAUNdrGeQxRANqz6wraikTnhlgQIg=s680-w680-h510-rw"
            class="w-100 d-block"
            alt="Third slide"
            width="50vw"
            height="500vh"
          />
  
        </div>
      </div>
      <button
        class="carousel-control-prev"
        type="button"
        data-bs-target="#carouselId"
        data-bs-slide="prev"
      >
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button
        class="carousel-control-next"
        type="button"
        data-bs-target="#carouselId"
        data-bs-slide="next"
      >
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
    
  
  </div>
</section>

<!-- LOCALIZAÇÃO -->
<section id="localizacao">
  <div class="container text-center">
    <h2 class="section-title">Localização</h2>
    <div class="map-responsive mt-4 mb-3">
      <iframe src="https://www.google.com/maps?q=R.+Santa+Zelia,+234+-+Jardim+Santa+Zelia,+São+Paulo+-+SP,+04833-110&output=embed" frameborder="0"></iframe>
    </div>
    <p>R. Santa Zélia, 234 - Jardim Santa Zélia, São Paulo - SP, 04833-110</p>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="container">
    <p>&copy; 2025 Roxinho's Barber | Todos os direitos reservados</p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
