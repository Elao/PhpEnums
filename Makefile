###########
# Install #
###########

setup:
	composer global require --no-progress --no-scripts --no-plugins symfony/flex

install: setup
install:
	rm -f composer.lock
	composer config minimum-stability --unset
	composer update --prefer-dist

install-lowest: setup
install-lowest: export SYMFONY_REQUIRE = 4.4.*
install-lowest:
	composer config minimum-stability --unset
	composer update --prefer-lowest

install-5.4: setup
install-5.4: export SYMFONY_REQUIRE = 5.4.*@dev
install-5.4:
	composer config minimum-stability dev
	composer update

install-60: setup
install-60: export SYMFONY_REQUIRE = 6.0.*@dev
install-60: remove-60unready-deps
install-60:
	composer config minimum-stability dev
	composer update

install-61: setup
install-61: export SYMFONY_REQUIRE = 6.1.*@dev
install-61: remove-60unready-deps
install-61:
	composer config minimum-stability dev
	composer update

remove-60unready-deps:
	# Tmp remove packages not allowing Symfony 6 yet
	composer remove --no-update --no-interaction --dev "nelmio/alice" "api-platform/core" "friendsofphp/php-cs-fixer"

add-odm:
	composer require --no-update --no-interaction --dev "doctrine/mongodb-odm:^2.2" "doctrine/mongodb-odm-bundle:^4.3"
	@echo "Run again appropriate install target to update dependencies"

remove-odm:
	composer remove --no-update --no-interaction --dev "doctrine/mongodb-odm" "doctrine/mongodb-odm-bundle"
	@echo "Run again appropriate install target to update dependencies"

########
# Test #
########

test:
	vendor/bin/simple-phpunit

testdox:
	vendor/bin/simple-phpunit --testdox --verbose

########
# Lint #
########

lint: lint-php-cs-fixer

fix-php-cs-fixer:
	vendor/bin/php-cs-fixer fix --no-interaction

lint-php-cs-fixer:
	vendor/bin/php-cs-fixer fix --no-interaction --dry-run --diff
