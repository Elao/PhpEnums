###########
# Install #
###########

install:
	# Require "phpspec/prophecy" for PHPUnit 9 used when PHP 8+ is used
	php -r "exit (PHP_MAJOR_VERSION == 8 ? 0 : 1);" \
		&& composer config platform.php 7.4.99 \
		&& composer require --dev --no-update "phpspec/prophecy-phpunit" || true
	composer update

install-lowest:
	composer require "symfony/symfony:^4.4" --no-update --no-interaction --dev
	composer update --prefer-lowest

install-highest:
	composer require "symfony/symfony:^5.3" --no-update --no-interaction --dev
	composer update

########
# Test #
########

test:
	vendor/bin/simple-phpunit

########
# Lint #
########

lint: lint-php-cs-fixer

fix-php-cs-fixer: export PHP_CS_FIXER_FUTURE_MODE = 1
fix-php-cs-fixer:
	vendor/bin/php-cs-fixer fix --config=.php_cs --no-interaction

lint-php-cs-fixer: export PHP_CS_FIXER_FUTURE_MODE = 1
lint-php-cs-fixer:
	vendor/bin/php-cs-fixer fix --config=.php_cs --no-interaction --dry-run --diff
