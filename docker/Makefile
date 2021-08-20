up:
	docker-compose up --build -d

test-7.1:
	docker-compose exec php7.1 vendor/bin/simple-phpunit
test-7.2:
	docker-compose exec php7.2 vendor/bin/simple-phpunit
test-7.3:
	docker-compose exec php7.3 vendor/bin/simple-phpunit
test-7.4:
	docker-compose exec php7.4 vendor/bin/simple-phpunit
test-8.0:
	docker-compose exec php8.0 vendor/bin/simple-phpunit

test: test-7.1 test-7.2 test-7.3 test-7.4 test-8.0
