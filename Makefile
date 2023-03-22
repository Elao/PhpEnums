include .make/help.mk
include .make/text.mk

PHP_CS_FIXER_VERSION=v3.13.0

###########
# Install #
###########

## Install - Prepare dev env with Symfony Flex
setup:
	symfony composer global require --no-progress --no-scripts --no-plugins symfony/flex

## Install - Install deps
install: setup
install:
	rm -f symfony composer.lock
	symfony composer config minimum-stability --unset
	symfony composer update --prefer-dist

## Install - Install lowest deps
install.lowest: setup
install.lowest: export SYMFONY_REQUIRE = 5.4.*@dev
install.lowest:
	symfony composer config minimum-stability --unset
	symfony composer update --prefer-lowest

## Install - Install Symfony 5.4 deps
install.54: setup
install.54: export SYMFONY_REQUIRE = 5.4.*@dev
install.54:
	symfony composer config minimum-stability dev
	symfony composer update

## Install - Install Symfony 6.0 deps
install.60: setup
install.60: export SYMFONY_REQUIRE = 6.0.*@dev
install.60:
	symfony composer config minimum-stability dev
	symfony composer update

## Install - Install Symfony 6.1 deps
install.61: setup
install.61: export SYMFONY_REQUIRE = 6.1.*@dev
install.61:
	symfony composer config minimum-stability dev
	symfony composer update

## Install - Install Symfony 6.2 deps
install.62: setup
install.62: export SYMFONY_REQUIRE = 6.2.*@dev
install.62:
	symfony composer config minimum-stability dev
	symfony composer update

## Install - Add Doctrine ODM deps
deps.odm.add:
	symfony composer require --no-update --no-interaction --dev "doctrine/mongodb-odm:^2.3" "doctrine/mongodb-odm-bundle:^4.4.1"
	@$(call log_warning, Run again appropriate install target to update dependencies. Be careful not to commit compose.json changes.)

## Install - Remove back Doctrine ODM deps
deps.odm.rm:
	symfony composer remove --no-update --no-interaction --dev "doctrine/mongodb-odm" "doctrine/mongodb-odm-bundle"
	@$(call log_warning, Run again appropriate install target to update dependencies)

########
# Test #
########
## Tests - Test (TESTDOX=1 for testdox format)
test:
	symfony php vendor/bin/simple-phpunit $(if $(TESTDOX), --testdox --verbose)

## Tests - Test with MySQL server (TESTDOX=1 for testdox format)
test.mysql: export DOCTRINE_DBAL_URL=pdo-mysql://app:password@127.0.0.1:63306/doctrine_tests
test.mysql: docker.start
test.mysql:
	symfony php vendor/bin/simple-phpunit $(if $(TESTDOX), --testdox --verbose)

## Tests - Start Docker services for integration tests
docker.start:
	docker-compose up -d --wait

## Tests - Stop Docker services for integration tests
docker.stop:
	docker-compose kill
	docker-compose rm --force

########
# Lint #
########

## Lint - Lint
lint: lint.php-cs-fixer

## Lint - Fix Lint
lint.fix: lint.php-cs-fixer.fix

## Lint - Update tools
lint.update:
	rm -f php-cs-fixer.phar
	make php-cs-fixer.phar

lint.php-cs-fixer.fix: php-cs-fixer.phar
lint.php-cs-fixer.fix:
	symfony php ./php-cs-fixer.phar fix --no-interaction

lint.php-cs-fixer: php-cs-fixer.phar
lint.php-cs-fixer:
	symfony php ./php-cs-fixer.phar fix --no-interaction --dry-run --diff -vvv

php-cs-fixer.phar:
	wget --no-verbose https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/${PHP_CS_FIXER_VERSION}/php-cs-fixer.phar
	chmod +x php-cs-fixer.phar

lint.php-cs-fixer@integration: php-cs-fixer.phar
lint.php-cs-fixer@integration:
	./php-cs-fixer.phar fix --dry-run --no-interaction --diff

lint.phpstan@integration:
	./vendor/bin/phpstan
