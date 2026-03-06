# Tallstarter - A Laravel Livewire Starter Kit

This Starter kit contains my starting point when developing a new Laravel project. Its based on the official Livewire Starter kit, and includes the following features:
- ✅ **User Management**, 
- ✅ **Role Management**,
- ✅ **Permissions Management**,
- ✅ **Two-Factor Authentication (2FA)**
- ✅ **Teams** - Collaborative team management (configurable)
- ✅ **Social Login** (Google, Facebook, Twitter/X)
- ✅ **Localization** options
- ✅ Separate **Dashboard for Super Admins**
- ✅ Updated for Laravel 12.0 **and** Livewire 3.0


### Admin dashboard view:
![alt text](docs/backend.png "Backend View")
### Supporting multiple languages:
![alt text](docs/locale.png "Localization View")


## TALL stack
It uses the TALL stack, which stands for:
-   [Tailwind CSS](https://tailwindcss.com)
-   [Alpine.js](https://alpinejs.dev)
-   [Laravel](https://laravel.com)
-   [Laravel Livewire](https://livewire.laravel.com) using the components.

## Further it includes:
Among other things, it also includes:
-   [Flux UI](https://fluxui.dev) for flexible UI components (free version)
-   [Laravel Pint](https://github.com/laravel/pint) for code style fixes
-   [PestPHP](https://pestphp.com) for testing
-   [missing-livewire-assertions](https://github.com/christophrumpel/missing-livewire-assertions) for extra testing of Livewire components by [Christoph Rumpel](https://github.com/christophrumpel)
-   [LivewireAlerts](https://github.com/jantinnerezo/livewire-alert) for SweetAlerts
-   [Spatie Roles & Permissions](https://spatie.be/docs/laravel-permission/v5/introduction) for user roles and permissions
-   [Google2FA](https://github.com/antonioribeiro/google2fa) for Two-Factor Authentication (TOTP)
-   [Strict Eloquent Models](https://planetscale.com/blog/laravels-safety-mechanisms) for safety
-   [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) for debugging
-   [Laravel IDE helper](https://github.com/barryvdh/laravel-ide-helper) for IDE support

## Upcoming features
I'm considering adding the following features, depending on my clients' most common requirements:
-   [Wire Elements / Modals](https://github.com/wire-elements/modal) for modals (still deciding - for now I'm using Flux UI for this)
-   [Laravel Cashier](https://laravel.com/docs/10.x/billing) for Stripe integration

# Installation

```bash
git clone 
```

You could also just use this repository as a starting point for your own project by clicking use template. If installing manually, these are the steps to install:

## 1. Install dependencies

```bash
composer install
npm install
npm run build # or npm run dev
```

## 2. Configure environment

Setup your `.env` file and run the migrations.

```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

## 3. Migration

```bash
php artisan migrate
```

## 4. Seeding

```bash
php artisan db:seed
```

## 5. Creating the first Super Admin user

```bash
php artisan app:create-super-admin
```

## 6. Set default timezone if different from UTC

```php
// config/app.php
return [
    // ...

    'timezone' => 'Europe/Copenhagen' // Default: UTC

    // ...
];
```