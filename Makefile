up: docker-up
down: docker-dowm
init: docker-dowm docker-pull docker-build docker-up

docker-up:
	docker-compose up -d

docker-dowm:
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

docker-pull:
	docker-compose pull

blog-init: blog-composer-install

blog-composer-install:
	docker-compose run --rm blog-php-cli composer install