COMPOSE_CMD := $(shell if docker compose version >/dev/null 2>&1; then echo "docker compose"; elif docker-compose version >/dev/null 2>&1; then echo "docker-compose"; fi)
COMPOSE_DEV := $(COMPOSE_CMD)
COMPOSE_PROD := $(COMPOSE_CMD)

.PHONY: install up down build ps prod-install prod-up prod-down prod-build prod-ps prod-composer-install prod-cache check-compose

check-compose:
	@if [ -z "$(COMPOSE_CMD)" ]; then echo "Erro: docker compose/docker-compose nao encontrado no host"; exit 1; fi

install:
	make up

up: check-compose
	$(COMPOSE_DEV) up -d
	docker ps

down: check-compose
	$(COMPOSE_DEV) down

build: check-compose
	$(COMPOSE_DEV) up -d --build --remove-orphans --force-recreate

ps: check-compose
	$(COMPOSE_DEV) ps

prod-install:
	$(MAKE) prod-up
	$(MAKE) prod-composer-install
	$(MAKE) prod-cache

prod-up: check-compose
	$(COMPOSE_PROD) up -d

prod-down: check-compose
	$(COMPOSE_PROD) down

prod-build: check-compose
	$(COMPOSE_PROD) up -d --build --remove-orphans --force-recreate

prod-ps: check-compose
	$(COMPOSE_PROD) ps

prod-composer-install: check-compose
	$(COMPOSE_PROD) exec -T app-clinic composer install --ignore-platform-reqs

prod-cache: check-compose
	$(COMPOSE_PROD) exec -T app-clinic php artisan optimize
	$(COMPOSE_PROD) exec -T app-clinic php artisan view:cache

prod-migrate: check-compose
	$(COMPOSE_PROD) exec -T app-clinic php artisan migrate --force

prod-composer-update: check-compose
	$(COMPOSE_PROD) exec -T app-clinic composer update --ignore-platform-reqs

prod-restart: check-compose
	$(COMPOSE_PROD) restart
	make prod-cache
restart-app: check-compose
	$(COMPOSE_DEV) restart app
#yes | php artisan make:entity
