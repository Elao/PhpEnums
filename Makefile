###########
# Install #
###########

install:
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
