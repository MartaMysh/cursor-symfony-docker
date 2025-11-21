up:
	docker-compose up -d --build
	docker-compose exec php composer install
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose exec php php bin/console app:fill-users src/DataFixtures/users.xlsx
	docker-compose exec php php bin/console app:fill-data src/DataFixtures/data.xlsx

restart:
	docker-compose down
	make up
