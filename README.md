# 💈 Roxinho's Barber - Sistema de Gestão e Agendamento

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![React Native](https://img.shields.io/badge/React_Native-20232A?style=for-the-badge&logo=react&logoColor=61DAFB)

Este projeto consiste no desenvolvimento de um sistema web e um aplicativo mobile para a **Roxinho's Barber**, uma barbearia de pequeno porte localizada em São Paulo. O foco é a modernização, automatização dos processos de gestão e melhora na experiência do cliente.

---

## 🎯 Objetivo do Projeto

Após 15 anos de atuação, o proprietário, Wlademir Roberto Bernardo ("Roxinho"), enfrenta desafios típicos da gestão manual. Nosso objetivo principal é introduzir a tecnologia para otimizar a rotina do estabelecimento, permitindo que o profissional se dedique integralmente à sua paixão: o corte de cabelo.

---

## 🛑 O Problema vs. 💡 A Solução

| Desafios da Gestão Manual (Antes) | Solução Digital Implementada (Depois) |
| :--- | :--- |
| **Comunicação Interrompida:** O proprietário parava constantemente para agendar clientes via WhatsApp. | **Agendamento Autônomo:** Clientes marcam seus próprios horários pelo sistema web de forma rápida. |
| **Agendamento Desorganizado:** Marcações em papel geravam erros, duplicidades e dificultavam encaixes. | **Agenda Digital:** Organização automatizada com estimativa de tempo médio para cada serviço. |
| **Falta de Controle Financeiro:** Dificuldade em visualizar a lucratividade, entradas e saídas. | **Dashboard Administrativo:** Acompanhamento financeiro detalhado e gestão inteligente de estoque. |
| **Divulgação Limitada:** Dependência exclusiva do "boca a boca". | **Presença Digital:** Sistema online que facilita a captação de novos clientes e fortalece a marca. |

---

## 💻 Funcionalidades Principais

### Para o Cliente (Interface Web)
*   Cadastro e login seguro.
*   Visualização de horários disponíveis em tempo real.
*   Agendamento e cancelamento de serviços de forma autônoma.

### Para o Administrador (Painel Web & Mobile)
*   **Gestão de Agenda:** Visão completa dos agendamentos do dia/semana.
*   **Controle de Estoque:** Histórico de movimentações e alertas de reposição de produtos.
*   **Gestão Financeira:** Registro de entradas e saídas do caixa.

---

## 🧪 Tecnologias Utilizadas

| Categoria | Tecnologia | Aplicação no Projeto |
| :--- | :--- | :--- |
| **Frontend Web** | HTML5, CSS3, JavaScript | Estruturação, estilização e interatividade das telas de agendamento e painel. |
| **Backend** | PHP | Regras de negócio, rotinas de autenticação, validação e integração com o banco. |
| **Banco de Dados** | MySQL | Armazenamento relacional seguro de usuários, agendamentos, estoque e finanças. |
| **Mobile** | React Native | Desenvolvimento do aplicativo proprietário para Android, consumindo a mesma base de dados. |

---

## 🚀 Como Executar o Projeto

*(Dica: Substitua as instruções abaixo pelos passos exatos do seu projeto)*

### Pré-requisitos
*   [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou similar (para rodar o servidor Apache e o MySQL).
*   [Node.js](https://nodejs.org/en/) (caso utilize pacotes NPM ou para rodar o app React Native).

### Passo a Passo (Web)
1. Clone este repositório: `git clone https://github.com/SeuUsuario/roxinhos-barber.git`
2. Mova a pasta do projeto para o diretório `htdocs` do seu XAMPP.
3. Inicie os serviços **Apache** e **MySQL** no painel do XAMPP.
4. Acesse `http://localhost/phpmyadmin` e crie um banco de dados chamado `roxinho_db`.
5. Importe o arquivo `database.sql` (localizado na pasta `/sql`) para o banco criado.
6. Acesse o sistema pelo navegador em: `http://localhost/roxinhos-barber`.

---

## 🤝 Desenvolvedores

Projeto de Conclusão de Curso (TCC) desenvolvido pelos alunos do curso Técnico em Desenvolvimento de Sistemas da **ETEC Jardim Ângela** (São Paulo):

*   **Guilherme Carvalho Costa**
*   **Jonatas Soares Ferreira**
*   **Lucas Rolim de Amorim**
*   **Marcos Gomes dos Santos**
*   **Vinicius Fernandes Oliveira Silva**
