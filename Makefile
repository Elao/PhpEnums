PHP_CS_FIXER_VERSION=v3.4.0

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
install-lowest: export SYMFONY_REQUIRE = 5.4.*@dev
install-lowest:
	composer config minimum-stability --unset
	composer update --prefer-lowest

install-54: setup
install-54: export SYMFONY_REQUIRE = 5.4.*@dev
install-54:
	composer config minimum-stability dev
	composer update

install-60: setup
install-60: export SYMFONY_REQUIRE = 6.0.*@dev
install-60:
	composer config minimum-stability dev
	composer update

install-61: setup
install-61: export SYMFONY_REQUIRE = 6.1.*@dev
install-61:
	composer config minimum-stability dev
	composer update

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

php-cs-fixer.phar:
	wget --no-verbose https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/${PHP_CS_FIXER_VERSION}/php-cs-fixer.phar
	chmod +x php-cs-fixer.phar

update-php-cs-fixer.phar:
	rm -f php-cs-fixer.phar
	make php-cs-fixer.phar

fix-php-cs-fixer: php-cs-fixer.phar
fix-php-cs-fixer:
	./php-cs-fixer.phar fix --no-interaction

lint-php-cs-fixer: php-cs-fixer.phar
lint-php-cs-fixer:
	./php-cs-fixer.phar fix --no-interaction --dry-run --diff -vvv
