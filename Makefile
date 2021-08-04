###########
# Install #
###########

install:
	rm -f composer.lock
	composer remove --no-interaction --dev "symfony/symfony" "doctrine/mongodb-odm" "doctrine/mongodb-odm-bundle"
	composer update --prefer-dist

install-lowest:
	composer require "symfony/symfony:4.4.x" --no-update --no-interaction --dev
	composer update --prefer-lowest

install-highest:
	composer require "symfony/symfony:5.4.x" --no-update --no-interaction --dev
	composer update

install-odm:
	rm -f composer.lock
	composer require --no-interaction --dev "doctrine/mongodb-odm:^2.2" "doctrine/mongodb-odm-bundle:^4.3"
	composer update --prefer-dist

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
