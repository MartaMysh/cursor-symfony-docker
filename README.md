<span style="font-size: 24px;">Uruchomienie środowiska</span>

W głównym katalogu projektu dostępny jest Makefile, który automatyzuje pełną procedurę startową aplikacji.

Aby uruchomić środowisko oraz wykonać wszystkie niezbędne kroki inicjalizacyjne, użyj polecenia:

```bash
make up
```
Polecenie to:

- buduje i uruchamia kontenery Dockera,

- instaluje zależności aplikacji,

- wykonuje migracje bazy danych,

- uzupełnia tabelę użytkowników danymi z pliku users.xlsx,

- importuje dane produktów z pliku data.xlsx.


<span style="font-size: 24px;">Alternatywna procedura krok po kroku</span>

Jeżeli wykonanie make up zakończy się niepowodzeniem lub konieczne jest ręczne uruchomienie poszczególnych etapów, poniżej znajduje się pełna sekwencja poleceń, które należy wykonać w podanej kolejności:

```bash
docker-compose up -d --build

docker-compose exec php composer install

docker-compose exec php php bin/console doctrine:migrations:migrate

docker-compose exec php php bin/console app:fill-users src/DataFixtures/users.xlsx

docker-compose exec php php bin/console app:fill-data src/DataFixtures/data.xlsx
```
Każde polecenie powinno zakończyć się sukcesem przed przejściem do kolejnego kroku.
