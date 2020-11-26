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

fix-phpcsfixer: export PHP_CS_FIXER_FUTURE_MODE = 1
fix-phpcsfixer:
	vendor/bin/php-cs-fixer fix --config=.php_cs --no-interaction
