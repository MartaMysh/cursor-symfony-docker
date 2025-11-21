w głównym folderze uruchomić "make up"

jeśli coś nie zadziała - poniżej mamy pojedyncze polecenia w tej kolejności, w której muszą być uruchomione:

1. docker-compose up -d --build

2. docker-compose exec php composer install

3. docker-compose exec php php bin/console doctrine:migrations:migrate

4. docker-compose exec php php bin/console app:fill-users src/DataFixtures/users.xlsx

5. docker-compose exec php php bin/console app:fill-data src/DataFixtures/data.xlsx


Go http://localhost:8000
