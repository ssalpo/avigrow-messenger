# Переменные
PHP_CONTAINER = php-fpm

# Основные команды
up: ## Запускает контейнеры
	docker-compose up -d

down: ## Останавливает и удаляет контейнеры
	docker-compose down

logs: ## Показывает логи контейнеров
	docker-compose logs -f

artisan: ## Выполняет команду artisan (например, make artisan cmd="migrate")
	docker-compose exec $(PHP_CONTAINER) php artisan $(cmd)

migrate: ## Выполняет миграции
	make artisan cmd="migrate"

seed: ## Выполняет seeders
	make artisan cmd="db:seed"

fresh: ## Удаляет все таблицы и запускает миграции заново
	make artisan cmd="migrate:fresh --seed"

tinker: ## Запускает tinker
	make artisan cmd="tinker"

clear: ## Очищает кэш Laravel
    make artisan cmd="config:clear"
    make artisan cmd="cache:clear"
    make artisan cmd="view:clear"
    make artisan cmd="route:clear"

composer-install: ## Устанавливает зависимости через Composer
	docker-compose exec $(PHP_CONTAINER) composer install

composer-update: ## Обновляет зависимости через Composer
	docker-compose exec $(PHP_CONTAINER) composer update

test: ## Запускает тесты
	make artisan cmd="test"

bash: ## Открывает bash в PHP контейнере
	docker-compose exec $(PHP_CONTAINER) bash

help: ## Показывает доступные команды
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
