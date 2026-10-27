// Menu mobile toggle
const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
menuToggle.addEventListener('click', () => { 
  sidebar.classList.toggle('is-active'); 
  overlay.classList.toggle('is-active'); 
});
overlay.addEventListener('click', () => { 
  sidebar.classList.remove('is-active'); 
  overlay.classList.remove('is-active'); 
});

// Desbloqueia conteúdo após senha correta
function desbloquearConteudo() {
  const mainContent = document.getElementById('main-content');
  mainContent.style.filter = 'none';
  mainContent.style.pointerEvents = 'auto';
  mainContent.style.userSelect = 'auto';

  const senhaModal = bootstrap.Modal.getInstance(document.getElementById('senhaModal'));
  senhaModal.hide();

  const senhaErroMsg = document.getElementById('senhaErroMsg');
  const senhaAdminInput = document.getElementById('senhaAdmin');
  senhaErroMsg.style.display = 'none';
  senhaAdminInput.value = '';
  senhaAdminInput.classList.remove('is-invalid');

  inicializarPainel();
}



function mostrarErro() {
  const senhaErroMsg = document.getElementById('senhaErroMsg');
  const senhaAdminInput = document.getElementById('senhaAdmin');
  senhaErroMsg.style.display = "block";
  senhaAdminInput.classList.add('is-invalid');
  senhaAdminInput.value = "";
  senhaAdminInput.focus();
}

function validarSenhaAdmin() {
  const btnAutenticar = document.getElementById('btnAutenticar');
  const senhaAdminInput = document.getElementById('senhaAdmin');
  btnAutenticar.disabled = true;
  document.getElementById('senhaErroMsg').style.display = "none";
  senhaAdminInput.classList.remove('is-invalid');

  fetch('controllers/senhaAdminCheck.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'senha=' + encodeURIComponent(senhaAdminInput.value)
  })
  .then(r => r.text())
  .then(resp => {
    btnAutenticar.disabled = false;
    if(resp.trim() === 'ok') {
      desbloquearConteudo();
    } else {
      mostrarErro();
    }
  }).catch(() => {
    btnAutenticar.disabled = false;
    mostrarErro();
  });
}

// Inicializa modal e bind de eventos ao carregar
document.addEventListener('DOMContentLoaded', () => {
  const senhaModal = new bootstrap.Modal(document.getElementById('senhaModal'));
  senhaModal.show();
  document.getElementById('senhaAdmin').focus();

  document.getElementById('btnAutenticar').addEventListener('click', validarSenhaAdmin);
  document.getElementById('senhaAdmin').addEventListener('keydown', e => {
    if(e.key === 'Enter') validarSenhaAdmin();
  });
});

// Filtros tabela
document.addEventListener('DOMContentLoaded', function() {
  const filtroBusca = document.getElementById('filtroBusca');
  const filtroData = document.getElementById('filtroData');
  const limparFiltrosBtn = document.getElementById('limparFiltros');
  const tabela = document.getElementById('tabelaAgendamentos');
  const linhas = tabela.getElementsByTagName('tr');

  function aplicarFiltros() {
    let textoBusca = filtroBusca.value.toLowerCase();
    let dataBusca = filtroData.value;

    for(let i=0; i < linhas.length; i++) {
      let linha = linhas[i];
      let conteudoDaLinha = linha.textContent || linha.innerText;
      let dataDaLinha = linha.getAttribute('data-data');

      const matchTexto = conteudoDaLinha.toLowerCase().indexOf(textoBusca) > -1;
      const matchData = (dataBusca === '' || dataDaLinha === dataBusca);

      linha.style.display = (matchTexto && matchData) ? '' : 'none';
    }
  }

  filtroBusca.addEventListener('keyup', aplicarFiltros);
  filtroData.addEventListener('change', aplicarFiltros);
  limparFiltrosBtn.addEventListener('click', () => {
    filtroBusca.value = '';
    filtroData.value = '';
    aplicarFiltros();
  });
});

// Calendário Admin
const datasComAgendamento = <?php echo json_encode(array_values($datasAgendamento)); ?>; 
const todosAgendamentos = <?php echo json_encode($todos_agendamentos); ?>; 
let diaSelecionado = (new Date()).toISOString().split('T')[0];

function montarCalendarioAdmin(year, month) {
  const container = document.getElementById('calendar-container');
  container.innerHTML = '';
  const dataHoje = new Date(year, month-1);
  const meses = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
  ];
  const primeiroDiaSemana = new Date(year, month - 1, 1).getDay();
  const totalDias = new Date(year, month, 0).getDate();

  let html = '<div class="d-flex justify-content-between align-items-center mb-2">';
  html += `<button class="btn btn-sm btn-outline-secondary" id="calPrev">&lt;</button>`;
  html += `<strong style="font-size:1.3rem;">${meses[month-1]} / ${year}</strong>`;
  html += `<button class="btn btn-sm btn-outline-secondary" id="calNext">&gt;</button>`;
  html += '</div>';
  html += '<div class="d-grid" style="grid-template-columns: repeat(7,1fr);gap:2px;">';
  ['D','S','T','Q','Q','S','S'].forEach(d => html += `<div class="fw-bold text-warning small text-center">${d}</div>`);
  
  for(let i=0; i<primeiroDiaSemana; i++) html += '<div>&nbsp;</div>';
  for(let d=1; d<=totalDias; d++) {
    const diaStr = `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    let classe = 'btn btn-light btn-sm w-100 mb-1';
    if(datasComAgendamento.includes(diaStr)) classe += ' border border-warning bg-gradient';
    if(diaStr === diaSelecionado) classe += ' bg-primary text-white';
    html += `<button type="button" class="${classe}" style="min-height:34px" data-dia="${diaStr}">${d}</button>`;
  }
  html += '</div>';
  container.innerHTML = html;

  document.getElementById('calPrev').onclick = () => trocaMes(-1);
  document.getElementById('calNext').onclick = () => trocaMes(1);

  document.querySelectorAll('#calendar-container button[data-dia]').forEach(btn => {
    btn.onclick = function() {
      diaSelecionado = this.dataset.dia;
      montarCalendarioAdmin(year, month);
      mostrarAgendamentosDoDia(diaSelecionado);
    }
  });
}

let calendarioData = new Date();
function trocaMes(inc) {
  calendarioData.setMonth(calendarioData.getMonth() + inc);
  montarCalendarioAdmin(calendarioData.getFullYear(), calendarioData.getMonth() + 1);
}

function mostrarAgendamentosDoDia(dia) {
  const lista = todosAgendamentos.filter(ag => ag.agen_data_a === dia);
  let html = '';
  const dataStrFormatada = new Date(dia).toLocaleDateString('pt-BR', { year: 'numeric', month: 'long', day: 'numeric' });
  document.getElementById('tituloDestaqueDia').textContent = 'Agendamentos para ' + dataStrFormatada;
  if(lista.length === 0) {
    html = '<div class="alert alert-secondary">Nenhum agendamento neste dia.</div>';
  } else {
    html = "<ul class='list-group'>";
    lista.forEach(ag => {
      html += `<li class='list-group-item bg-dark text-white'>
        <b>${ag.corte_nome}</b> (${ag.agen_hora_a.substr(0,5)})<br>
        Cliente: ${ag.usuario_nome} <span class='badge bg-secondary ms-2'>${ag.agen_status}</span>
      </li>`;
    });
    html += '</ul>';
  }
  document.getElementById('destaqueDia').innerHTML = html;
}

function inicializarPainel() {
  calendarioData = new Date();
  diaSelecionado = calendarioData.toISOString().split('T')[0];
  montarCalendarioAdmin(calendarioData.getFullYear(), calendarioData.getMonth()+1);
  mostrarAgendamentosDoDia(diaSelecionado);

  // Desbloquear filtro e tabela
  const mainContent = document.getElementById('main-content');
  mainContent.style.filter = 'none';
  mainContent.style.pointerEvents = 'auto';
  mainContent.style.userSelect = 'auto';
}

// Modal senha admin
const senhaModal = new bootstrap.Modal(document.getElementById('senhaModal'));

document.addEventListener('DOMContentLoaded', () => {
  senhaModal.show();
  const senhaAdminInput = document.getElementById('senhaAdmin');
  const btnAutenticar = document.getElementById('btnAutenticar');
  const senhaErroMsg = document.getElementById('senhaErroMsg');

  function mostrarErro() {
    senhaErroMsg.style.display = 'block';
    senhaAdminInput.classList.add('is-invalid');
    senhaAdminInput.value = '';
    senhaAdminInput.focus();
  }

  function validarSenhaAdmin() {
    btnAutenticar.disabled = true;
    senhaErroMsg.style.display = 'none';
    senhaAdminInput.classList.remove('is-invalid');
    fetch('controllers/senhaAdminCheck.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'senha=' + encodeURIComponent(senhaAdminInput.value)
    })
    .then(r => r.text())
    .then(resp => {
      btnAutenticar.disabled = false;
      if (resp.trim() === 'ok') {
        senhaModal.hide();
        inicializarPainel();
      } else {
        mostrarErro();
      }
    })
    .catch(() => {
      btnAutenticar.disabled = false;
      mostrarErro();
    });
  }

  btnAutenticar.addEventListener('click', validarSenhaAdmin);

  senhaAdminInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      validarSenhaAdmin();
    }
  });
});