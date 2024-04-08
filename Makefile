# Executables (local)
DOCKER_COMP = docker compose

# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

link:
	@echo "https://localhost/"
	@echo "http://localhost:8025/"

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

rights: ## Fixing permissions on all files
	sudo chown -R $(USER):$(USER) .

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

admin.crud: ## Create a Symfony admin crud
	$(SYMFONY) make:admin:crud

form: ## Create a Symfony form
	$(SYMFONY) make:form

crons:
	$(SYMFONY) messenger:consume -v scheduler_default

## —— Base de donnée 💽 ————————————————————————————————————————————————————————
entity: ## Create a new entity
	$(SYMFONY) make:entity

migration: ## Create a new migration
	$(SYMFONY) doctrine:migrations:diff

migrate: ## Migrate to the last version
	$(SYMFONY) doctrine:migrations:migrate -n

db.recreate: delImages db.drop db.create migrate fixtures ## Commande pour recréer la BDD de ZERO !
db.init: db.create migrate fixtures ## Commande pour recréer la BDD de ZERO !

db.drop: ## Delete the DB
	$(SYMFONY) doctrine:database:drop -f

db.create: ## Create the DB
	$(SYMFONY) doctrine:database:create

fixtures: ## Load the fixtures (Attention vide la BDD ! )
	$(SYMFONY) doctrine:fixtures:load -n

delImages: ## Delete all the public/images
	rm -f ./public/images/*.jpg

## —— Check ————————————————————————————————————————————————————————————————————
check: ## Launch the code check
	@$(COMPOSER) check

fix: ## Fix the code
	@$(COMPOSER) fix
