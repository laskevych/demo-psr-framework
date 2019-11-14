up: docker-up
down: docker-dowm
init: docker-dowm docker-pull docker-build docker-up
test: framework-test
test-cache: framework-test-cache

docker-up:
	docker-compose up -d

docker-dowm:
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

docker-pull:
	docker-compose pull

framework-init: framework-composer-install

framework-composer-install:
	docker-compose run --rm framework-php-cli composer install

framework-test:
	docker-compose run --rm framework-php-cli php vendor/bin/phpunit

framework-test-cache:
	docker-compose run --rm framework-php-cli rm -f .phpunit.result.cache