include .make/help.mk
include .make/text.mk
include .make/try.mk

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
	rm -f composer.lock
	symfony composer config minimum-stability --unset
	symfony composer update --prefer-dist --ignore-platform-req=ext-mongodb

## Install - Install lowest deps
install.lowest: setup
install.lowest: export SYMFONY_REQUIRE = 5.4.*@dev
install.lowest:
	symfony composer config minimum-stability --unset
	symfony composer update --prefer-lowest --ignore-platform-req=ext-mongodb

## Install - Install Symfony 5.4 deps
install.54: setup
install.54: export SYMFONY_REQUIRE = 5.4.*@dev
install.54:
	symfony composer config minimum-stability dev
	symfony composer update --ignore-platform-req=ext-mongodb

## Install - Install Symfony 6.4 deps
install.64: setup
install.64: export SYMFONY_REQUIRE = 6.4.*@dev
install.64:
	symfony composer config minimum-stability dev
	symfony composer update --ignore-platform-req=ext-mongodb

## Install - Install Symfony 7.0 deps
install.70: setup
install.70: export SYMFONY_REQUIRE = 7.0.*@dev
install.70:
	symfony composer config minimum-stability dev
	symfony composer update --ignore-platform-req=ext-mongodb

## Install - Install Symfony 7.1 deps
install.71: setup
install.71: export SYMFONY_REQUIRE = 7.1.*@dev
install.71:
	symfony composer config minimum-stability dev
	symfony composer update --ignore-platform-req=ext-mongodb

## Install - Add Doctrine ODM deps
deps.odm.add: deps.odm.add+sf64

## Install - Add Doctrine ODM deps for Symfony 6.4+
deps.odm.add+sf64:
	symfony composer require --no-update --no-interaction --dev "doctrine/mongodb-odm:^2.6" "doctrine/mongodb-odm-bundle:^5.0"
	@$(call log_warning, Run again appropriate install target to update dependencies. Be careful not to commit compose.json changes.)

## Install - Add Doctrine ODM deps for Symfony 5.4+
deps.odm.add+sf54:
	symfony composer require --no-update --no-interaction --dev "doctrine/mongodb-odm:^2.4" "doctrine/mongodb-odm-bundle:^4.5.1"
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
lint: lint.php-cs-fixer lint.phpstan

## Lint - Fix Lint
lint.fix: lint.php-cs-fixer.fix lint.phpstan

## Lint - Update tools
lint.update:
	rm -f php-cs-fixer.phar
	make php-cs-fixer.phar

lint.php-cs-fixer.fix: php-cs-fixer.phar
lint.php-cs-fixer.fix: export PHP_CS_FIXER_IGNORE_ENV = 1
lint.php-cs-fixer.fix:
	symfony php ./php-cs-fixer.phar fix --no-interaction

lint.php-cs-fixer: php-cs-fixer.phar
lint.php-cs-fixer: export PHP_CS_FIXER_IGNORE_ENV = 1
lint.php-cs-fixer:
	symfony php ./php-cs-fixer.phar fix --no-interaction --dry-run --diff -vvv

php-cs-fixer.phar:
	wget --no-verbose https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/${PHP_CS_FIXER_VERSION}/php-cs-fixer.phar
	chmod +x php-cs-fixer.phar

lint.phpstan:
	@make deps.odm.add install >> /dev/null 2>&1
	$(call try_finally, ./vendor/bin/phpstan, make deps.odm.rm install >> /dev/null 2>&1)
