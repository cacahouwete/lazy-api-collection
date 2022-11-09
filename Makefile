DOCKER_COMPOSE = docker-compose
DOCKER_RUN     = $(DOCKER_COMPOSE) run --rm -T
RUN_APP        = $(DOCKER_RUN) app
PHP            = $(RUN_APP) php
COMPOSER       = $(RUN_APP) composer
QA             = $(DOCKER_RUN) audit

APP_SRC = src

##
## Project
## -------
##

build: ## Pull and build all services
build: docker-compose.override.yml
	$(DOCKER_COMPOSE) pull --ignore-pull-failures
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1  $(DOCKER_COMPOSE) build --pull

kill: ## Kill and down all containers
kill: docker-compose.override.yml
	$(DOCKER_COMPOSE) kill || true
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

install: ## Install and start the project
install: build
	$(MAKE) vendor

reset: ## Kill and run a fresh install
reset: kill
	$(MAKE) install

clean: ## Kill containers and remove generated files
clean: kill
	rm -rf vendor

thanks:
	$(COMPOSER) thanks

.PHONY: build kill install reset start stop clean thanks

##
## Tests
## -----
##

tests: ## Run all tests
tests: vendor
	$(PHP) -d extension=pcov.so vendor/bin/phpunit --coverage-text=php://stdout

.PHONY: tests

##
## Quality assurance
## -----------------
##

qa: ## Run all QA
qa: cs stan md phpcpd

phploc: ## PHPLoc (https://github.com/sebastianbergmann/phploc)
	$(QA) phploc $(APP_SRC)/

md: ## PHP Mess Detector (https://phpmd.org)
	$(QA) phpmd $(APP_SRC) text .phpmd.xml

phpcpd: ## PHP Copy/Paste Detector (https://github.com/sebastianbergmann/phpcpd)
	$(QA) phpcpd $(APP_SRC)

stan: ## twig code style
	$(QA) php -d memory_limit=50000M /usr/local/src/vendor/bin/phpstan.phar analyse

cs: ## php-cs-fixer (http://cs.sensiolabs.org)
	$(QA) php-cs-fixer fix --dry-run --using-cache=no --verbose --diff

cs-fix: ## apply php-cs-fixer fixes
	$(QA) php-cs-fixer fix

.PHONY: phploc md phpcpd stan cs cs-fix

# rules based on files
docker-compose.override.yml: docker-compose.override.yml.dist
ifeq ($(shell test -f docker-compose.override.yml && echo -n yes),yes)
	@echo "Your docker-compose.override.yml is outdated."
	@while [ -z "$$CONTINUE" ]; do \
		read -r -p "# Do you want to refresh your docker-compose.override.yml ? [y/N] : " CONTINUE; \
	done ; \
	if [ $$CONTINUE = "y" ] || [ $$CONTINUE = "Y" ]; then \
		cp docker-compose.override.yml.dist docker-compose.override.yml ; \
		echo "=> Refresh done" ; \
	fi
else
	cp -n docker-compose.override.yml.dist docker-compose.override.yml
endif

composer.lock: composer.json
	$(COMPOSER) update --no-scripts --no-interaction

vendor: composer.lock
	$(COMPOSER) install

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
