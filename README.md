# Sistema de Rifas — by debian

Plataforma de rifas online feita em Laravel 5.8, com painel administrativo, checkout via Pix, área de afiliados e mensagens automáticas por WhatsApp.

## Requisitos

- PHP 7.2.5 a 7.4
- MySQL ou MariaDB
- Composer

## Rodando localmente

```bash
composer install
cp .env.example .env   # ajuste banco de dados e chaves
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Acesse http://localhost:8000.

## Contato

Desenvolvido por **debian** — [github.com/AlvaroBaarros](https://github.com/AlvaroBaarros)
