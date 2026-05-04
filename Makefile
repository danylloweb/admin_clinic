COMPOSE_DEV := docker-compose
COMPOSE_PROD := docker-compose -f docker-compose-prod.yml

.PHONY: install up down build ps prod-install prod-up prod-down prod-build prod-ps prod-composer-install

install:
	make up

up:
	$(COMPOSE_DEV) up -d
	docker ps

down:
	$(COMPOSE_DEV) down

build:
	$(COMPOSE_DEV) up -d --build --remove-orphans --force-recreate

ps:
	$(COMPOSE_DEV) ps

prod-install:
	$(MAKE) prod-up
	$(MAKE) prod-composer-install

prod-up:
	$(COMPOSE_PROD) up -d

prod-down:
	$(COMPOSE_PROD) down

prod-build:
	$(COMPOSE_PROD) up -d --build --remove-orphans --force-recreate

prod-ps:
	$(COMPOSE_PROD) ps

prod-composer-install:
	$(COMPOSE_PROD) exec -T app-clinic composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

