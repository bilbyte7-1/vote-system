install:
	docker compose exec app composer install

test:
	docker compose exec app composer test

lint:
	docker compose exec app vendor/bin/phpcs --standard=PSR12 src tests

lint-fix:
	docker compose exec app vendor/bin/phpcbf --standard=PSR12 src tests

audit:
	docker compose exec app composer audit

test-coverage:
	docker compose exec -e XDEBUG_MODE=coverage app php bin/phpunit --coverage-clover build/logs/clover.xml

test-coverage-text:
	docker compose exec -e XDEBUG_MODE=coverage app php bin/phpunit --coverage-text
