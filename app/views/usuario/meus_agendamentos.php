<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. BLOQUEIO DE SEGURANÇA: Redireciona se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?erro=precisa_logar");
    exit();
}

// 2. CONEXÃO E BUSCA DOS DADOS
require_once '../../config/database.php';

$usuario_id = $_SESSION['usuario_id'];

// Prepara a consulta para buscar os agendamentos do usuário, juntando com o nome e preço do corte
$stmt = $conn->prepare("
    SELECT 
        ag.agen_id,
        ag.agen_data_a,
        ag.agen_hora_a,
        ct.corte_nome,
        ct.corte_preco
    FROM 
        agendamento AS ag
    JOIN 
        corte AS ct ON ag.corte_id = ct.corte_id
    WHERE 
        ag.usuario_id = ?
    ORDER BY 
        ag.agen_data_a DESC, ag.agen_hora_a DESC
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

// 3. SEPARA OS AGENDAMENTOS EM FUTUROS E PASSADOS
date_default_timezone_set('America/Sao_Paulo');
$agora = new DateTime();
$proximos_agendamentos = [];
$historico_agendamentos = [];

while ($agendamento = $resultado->fetch_assoc()) {
    $dataHoraAgendamento = new DateTime($agendamento['agen_data_a'] . ' ' . $agendamento['agen_hora_a']);
    if ($dataHoraAgendamento >= $agora) {
        $proximos_agendamentos[] = $agendamento;
    } else {
        $historico_agendamentos[] = $agendamento;
    }
}
// Reverte a ordem dos próximos agendamentos para mostrar do mais próximo para o mais distante
$proximos_agendamentos = array_reverse($proximos_agendamentos);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Meus Agendamentos | Roxinho's Barber</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../public/icon.ico" type="image/x-icon" />
    <style>
        :root { --cor-fundo: #121212; --cor-fundo-card: #1f1f2e; --cor-texto: #fff; --cor-primaria: #a855f7; --cor-secundaria: #eab308; }
        body { font-family: 'Barlow Condensed', sans-serif; background-color: var(--cor-fundo); color: var(--cor-texto); }
        .navbar { background-color: #000; } 
        .navbar-brand { font-size: 1.8rem; font-weight: bold; color: var(--cor-primaria) !important; } 
        .nav-link { color: #fff !important; margin: 0 10px; }
        .btn-purple { background-color: var(--cor-primaria); color: white; border-radius: 30px; transition: 0.3s; border: none; padding: 12px 25px; font-weight: bold; }
        .btn-purple:hover { background-color: #9333ea; }
        .section-title { font-size: 2.5rem; color: var(--cor-primaria); font-weight: bold; }
        
        .card-agendamento {
            background-color: var(--cor-fundo-card);
            border: 1px solid #444;
            border-left: 5px solid var(--cor-primaria);
            border-radius: 10px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .card-agendamento.passado {
            border-left-color: #6c757d; /* Cinza */
            opacity: 0.7;
        }
        .card-agendamento:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }
        .card-header { background-color: transparent; border-bottom: 1px solid #444; }
        .data-hora { font-size: 1.5rem; font-weight: bold; }
        .corte-nome { font-size: 1.3rem; color: var(--cor-secundaria); }
        .corte-preco { font-size: 1.2rem; }
        
        footer { background-color: #000; padding: 20px 0; text-align: center; color: #aaa; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../../../index.php">ROXINHO'S</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse justify-content-end" id="menu">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="../../../index.php#inicio">Início</a></li>
        <li class="nav-item"><a class="nav-link" href="../../../index.php#roxinho">Roxinho</a></li>
        <li class="nav-item"><a class="nav-link" href="../../../index.php#servicos">Serviços</a></li>
        <li class="nav-item"><a class="nav-link" href="../../../index.php#galeria">Galeria</a></li>
        <li class="nav-item"><a class="nav-link" href="../../../index.php#localizacao">Localização</a></li>
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
                    <li><a class="dropdown-item" href="meus_dados.php"><i class="bi bi-person-fill me-2"></i> Meus Dados</a></li>
                    <li><a class="dropdown-item" href="meus_agendamentos.php"><i class="bi bi-calendar-check me-2"></i> Meus Agendamentos</a></li>
                    <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 1): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../admin/dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i> Painel Admin</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../../controllers/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item"><a href="login.php" class="btn btn-purple">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="container" style="padding-top: 120px; padding-bottom: 80px;">
    <h2 class="section-title text-center mb-5">Meus Agendamentos</h2>

    <h3 class="text-warning mb-4"><i class="bi bi-clock-history me-2"></i>Próximos Agendamentos</h3>
    <?php if (empty($proximos_agendamentos)): ?>
        <div class="alert alert-secondary text-center" style="background-color: var(--cor-fundo-card);">
            Você não possui nenhum agendamento futuro.
            <a href="../../../index.php#servicos" class="btn btn-purple btn-sm mt-2">Agendar um Horário</a>
        </div>
    <?php else: ?>
        <?php foreach ($proximos_agendamentos as $agendamento): ?>
            <div class="card card-agendamento">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div>
                            <div class="data-hora text-white"><?php echo date('d/m/Y', strtotime($agendamento['agen_data_a'])); ?> às <?php echo htmlspecialchars($agendamento['agen_hora_a']); ?></div>
                            <div class="corte-nome"><?php echo htmlspecialchars($agendamento['corte_nome']); ?></div>
                        </div>
                        <div class="text-md-end mt-3 mt-md-0">
                            <div class="corte-preco text-warning">R$ <?php echo number_format($agendamento['corte_preco'], 2, ',', '.'); ?></div>
                            <a href="../../controllers/cancelar_agendamento.php?id=<?php echo $agendamento['agen_id']; ?>" 
                               class="btn btn-outline-danger btn-sm mt-2"
                               onclick="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                                <i class="bi bi-x-circle me-1"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h3 class="text-warning mt-5 mb-4"><i class="bi bi-check-circle me-2"></i>Histórico</h3>
    <?php if (empty($historico_agendamentos)): ?>
        <div class="alert alert-secondary text-center" style="background-color: var(--cor-fundo-card);">
            Seu histórico de agendamentos está vazio.
        </div>
    <?php else: ?>
        <?php foreach ($historico_agendamentos as $agendamento): ?>
            <div class="card card-agendamento passado">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div>
                            <div class="data-hora text-white"><?php echo date('d/m/Y', strtotime($agendamento['agen_data_a'])); ?> às <?php echo htmlspecialchars($agendamento['agen_hora_a']); ?></div>
                            <div class="corte-nome"><?php echo htmlspecialchars($agendamento['corte_nome']); ?></div>
                        </div>
                        <div class="text-md-end mt-3 mt-md-0">
                            <div class="corte-preco text-warning">R$ <?php echo number_format($agendamento['corte_preco'], 2, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<footer class="mt-5">
    <div class="container"><p>© <?php echo date("Y"); ?> Roxinho's Barber</p></div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>