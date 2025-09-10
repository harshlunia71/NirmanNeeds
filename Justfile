up:
	export COMPOSE_FILE="docker-compose.yml"
	docker compose up -d mysql phpmyadmin wordpress

down:
	docker compose down

clean:
	docker compose down -v --remove-orphans

init: up
	just run-cli "/usr/local/bin/install-wp"

build file="": init 
	just restore {{file}}
	
backup:
	./bin/db_backup.sh

restore file="":
	./bin/db_restore.sh {{file}}

rebuild file="": backup clean 
	just build {{file}}

restart: down up

list:
    docker compose ps
    docker compose volumes

run-cli cmd="sh":
    docker compose run --rm --remove-orphans wp-cli {{cmd}}

make-pot:
    just run-cli "/usr/local/bin/make-pot"

