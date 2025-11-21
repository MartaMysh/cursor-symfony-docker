w głównym folderze uruchomić "make up"
jeśli coś nie zadziała - poniżej mamy pojedyncze polecenie w tej kolejności, w której muszą być uruchomione.

docker-compose up -d --build
docker-compose exec php composer install
docker-compose exec php php bin/console doctrine:migrations:migrate
docker-compose exec php php bin/console app:fill-users src/DataFixtures/users.xlsx
docker-compose exec php php bin/console app:fill-data src/DataFixtures/data.xlsx


Go http://localhost:8000
