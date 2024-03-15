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

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach
	@$(SYMFONY) asset-map:compile

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

restart: down up ## Restart containers



logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) bash

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit --no-coverage $(c)

ptest: ## Start tests with paratest, pass the parameter "c=" to add options to paratest
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/paratest --no-coverage $(c)

infection: ## Start infection
	@$(DOCKER_COMP) infection --threads=max

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Assets ————————————————————————————————————————————————————————————————————
asset: asset.install asset.compile

asset.install:
	@$(SYMFONY) assets:install

asset.compile:
	@$(SYMFONY) asset-map:compile

## —— Sass 👓 ———————————————————————————————————————————————————————————————————
sass: asset.install sass.build asset.compile

sass.build:
	@$(SYMFONY) sass:build

sass.watch:
	@$(SYMFONY) sass:build --watch

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf


## —— Tools  ——————————————————————————————————————————————————————————————————
cs-fix: 
	docker run --rm -v $(PWD):/code ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.3 fix src
phive: ## Run phive, pass the parameter "c=" to run a given command, example: make phive c='infection'
	@$(eval c ?=)
	docker run -it --rm -v $(PWD):/repo phario/phive:0.15.2 install --copy $(c)
drivers: ## For symfony/panther
	@$(PHP_CONT) vendor/bin/bdi detect drivers