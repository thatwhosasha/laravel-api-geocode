# laravel-api-geocode
Приложение для получения информации о районе города, станции метро, улице и дому по адресу, который введет пользователь в текстовом поле
### Быстрый старт

#### Клонировать и перейти в папку:
`git clone https://github.com/thatwhosasha/laravel-api-geocode.git`

`cd laravel-api-geocode`
#### Переменные окружения:

* В файлах `.env` и `docker_sandbox/.env` уже все настроено кроме ключа
  для пакета «API Геокодера».
* Получение ключа:
    1. Перейти по ссылке [API Геокодера](https://yandex.ru/maps-api/docs/geocoder-api/index.html)
    2. Перейти в кабинет управления API и авторизоваться
    3. Выбрать **API Геокодера** и получить ключ.
    4. Далее перейти в файл .env и ввести ключ
     ```
     YANDEX_GEOCODER_API_KEY=
     ```


### Настройка докер окружения
`docker-compose up -d` - установить контейнеры

`docker-compose exec php-fpm bash ` - войти в php контейнер

`php artisan migration` - запустить миграцию

`docker-compose down -v` - удалить контейнеры

`docker-compose start` - запустить контейнеры

`docker-compose stop` - остановить контейнеры


### Как пользоваться приложением
1. Перейдите по адресу `http://localhost:93/`
2. Попробуйте найти адрес в пределах Москвы
    
    Например: "Москва, ул. Покровка, д. 29 — Басманный район, метро Курская".
3. Выведется информация о городе, станции метро, улице и доме




## Задача #1. MySql
Для всех артикулов выведите поставщика или поставщиков (артикулы и поставшики
на выводе могут повторяться) с самой высокой ценой.

```sql
SELECT s.article, s.dealer, s.price
FROM shop s
JOIN (
    SELECT article, MAX(price) AS max_price
    FROM shop
    GROUP BY article
) t ON s.article = t.article AND s.price = t.max_price
ORDER BY s.article ASC;
```
