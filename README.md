git clone git@github.com:melnikovav/adtracker.git adtracker <br>
cd adtracker <br>
composer install <br>
cp .env.example .env <br>
vi .env <br>
php artisan migrate <br>
php -S localhost:8000 -t public <br>
<br>
GET  /api/ad<br>
POST /api/ad<br>
POST /api/ad/:id (PUT не отправляет файл multipart form data)<br>