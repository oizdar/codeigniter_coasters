COMPOSE_CMD := docker compose

.PHONY: build build-without-cache up down destroy stop restart rebuild rebuild-without-cache ps console

all: console

build-prod:
	cp .env.prod .env
	$(COMPOSE_CMD) build php -f docker-compose.yml

build:
	cp .env.local .env
	$(COMPOSE_CMD) build php

build-without-cache:
	$(COMPOSE_CMD) build php --no-cache

up:
	$(COMPOSE_CMD) up -d

down:
	$(COMPOSE_CMD) down

destroy:
	$(COMPOSE_CMD) down -v

stop:
	$(COMPOSE_CMD) stop

restart: stop up

rebuild: destroy build console

rebuild-without-cache: destroy build-without-cache console

ps:
	$(COMPOSE_CMD) ps

console: up
	$(COMPOSE_CMD) exec php bash

chmods:
	sudo chmod -R 777 writable
