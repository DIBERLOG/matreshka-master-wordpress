# Развёртывание

## Локальная разработка

- использовать `docker-compose.yml`
- запускать `scripts/bootstrap.ps1`

## Production

1. Развернуть обычный WordPress-хостинг.
2. Перенести:
   - `wp-content/themes/matreshka-master`
   - `wp-content/plugins/matreshka-master-core`
3. Установить обязательные плагины:
   - WooCommerce
   - multilingual plugin
   - SEO plugin
   - SMTP plugin
   - платёжный модуль
4. Подключить реальные ключи и webhook-URL.
5. Заменить placeholder-контент на production материалы.

## Что проверить перед публикацией

- переводы RU / EN / ZH
- рабочий checkout
- почта форм
- Telegram / Bitrix / WhatsApp / MAX интеграции
- legal pages
- реальные контакты
- favicon, OG и SEO-метаданные
