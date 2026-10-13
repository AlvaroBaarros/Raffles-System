<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:A80030,100:1a1a2e&height=200&section=header&text=Sistema%20de%20Rifas&fontSize=48&fontColor=ffffff&animation=fadeIn&fontAlignY=38&desc=by%20debian&descAlignY=58&descSize=20" width="100%" alt="Sistema de Rifas by debian"/>

🇧🇷 **Português** · [🇺🇸 English](README.en.md)

### 🎟️ Plataforma completa para criar, vender e sortear rifas online

Venda de cotas com **Pix automático**, painel administrativo, programa de **afiliados**, ranking de compradores e notificações por **WhatsApp**, tudo em um só sistema.

<br/>

<img src="https://img.shields.io/badge/PHP-7.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 7.4"/>
<img src="https://img.shields.io/badge/Laravel-5.8-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 5.8"/>
<img src="https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
<img src="https://img.shields.io/badge/Bootstrap-4-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap"/>
<img src="https://img.shields.io/badge/Pix-Pagamentos-32BCAD?style=for-the-badge&logo=pix&logoColor=white" alt="Pix"/>

</div>

---

## ✨ Funcionalidades

<table>
<tr>
<td width="50%" valign="top">

### 🛒 Para o comprador
- 🔢 Escolha manual ou **números aleatórios**
- ⚡ Checkout rápido com **Pix copia e cola e QR Code**
- ✅ Confirmação automática do pagamento
- 🔎 Consulta de reservas pelo telefone
- 🏆 Página de **ganhadores** e regulamento
- 📱 Layout responsivo, pensado para celular

</td>
<td width="50%" valign="top">

### 🧑‍💼 Para o administrador
- 🎁 Criação, edição e **duplicação de sorteios**
- 📅 Agendamento e previsão de sorteio
- 🏅 **Ranking** de compradores e definição de ganhadores
- 📊 Resumos de **lucro**, rifas ativas e pagamentos pendentes
- 🧾 Relatórios de compra em **PDF**
- 🎯 Integração com **Pixel** para campanhas

</td>
</tr>
<tr>
<td width="50%" valign="top">

### 🤝 Programa de afiliados
- 🔗 Link exclusivo por afiliado em cada rifa
- 💰 Comissão percentual configurável
- 🏦 Solicitação e confirmação de **saques**
- 📋 Painel próprio para o afiliado

</td>
<td width="50%" valign="top">

### 🔔 Automação
- 💬 Mensagens automáticas por **WhatsApp**
- ⏱️ Verificação de pagamentos **a cada minuto**
- ♻️ Liberação automática de reservas expiradas
- 🎁 Promoções por quantidade de cotas

</td>
</tr>
</table>

---

## 💳 Gateways de pagamento

<div align="center">

| Gateway | Método |
|:---:|:---:|
| **Mercado Pago** | Pix |
| **Asaas** | Pix |
| **Paggue** | Pix |

</div>

---

## 🛠️ Tecnologias

<div align="center">

<img src="https://skillicons.dev/icons?i=php,laravel,mysql,js,jquery,bootstrap,html,css&theme=dark" alt="Tecnologias"/>

</div>

---

## 🚀 Como rodar localmente

> **Requisitos:** PHP 7.2.5 a 7.4 · MySQL ou MariaDB · Composer

```bash
# 1. Clone o repositório
git clone https://github.com/AlvaroBaarros/Ssitema-Rifa---by---Debian.git
cd Ssitema-Rifa---by---Debian

# 2. Instale as dependências
composer install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Crie o banco e rode as migrations
php artisan migrate --seed

# 5. Suba o servidor
php artisan serve
```

Acesse **http://localhost:8000** 🎉

### ⏰ Tarefas agendadas

Para a confirmação automática de pagamentos e a liberação de reservas, adicione ao cron do servidor:

```bash
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

| Comando | Frequência | O que faz |
|---|:---:|---|
| `pix:check-payment` | a cada minuto | Confirma pagamentos Pix pendentes |
| `reservas:expiradas` | a cada minuto | Libera números de reservas não pagas |

---

## 📁 Estrutura

```
📦 rifas
 ┣ 📂 app
 ┃ ┣ 📂 Console/Commands   → tarefas agendadas (pagamentos, reservas)
 ┃ ┣ 📂 Http/Controllers   → checkout, sorteios, afiliados, painel
 ┃ ┣ 📂 Models             → produtos, participantes, afiliados
 ┃ ┗ 📂 Services           → integração com gateways de pagamento
 ┣ 📂 database/migrations  → estrutura do banco
 ┣ 📂 resources/views      → telas (Blade)
 ┗ 📂 routes               → rotas web
```

---

<div align="center">

## 📬 Contato

Desenvolvido com 💙 por **debian**

<a href="https://github.com/AlvaroBaarros">
  <img src="https://img.shields.io/badge/GitHub-AlvaroBaarros-181717?style=for-the-badge&logo=github&logoColor=white" alt="GitHub AlvaroBaarros"/>
</a>

<br/><br/>

⭐ Se este projeto te ajudou, deixe uma estrela!

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:1a1a2e,100:A80030&height=120&section=footer" width="100%" alt=""/>

</div>
