MAIN_FILE=./environment-setup/docker-composes/docker-compose.yaml
ARTISAN_FILE=./environment-setup/docker-composes/docker-compose.artisan.yaml
COMPOSER_FILE=./environment-setup/docker-composes/docker-compose.composer.yaml

# use init-project for initiate the project
init-project: build-docker install-package migrate-table generate-user install-passport recover-modules

build-docker:
	# build up docker images and containers
	sudo docker compose -f $(MAIN_FILE) up -d --build

install-package:
	sudo docker compose -f $(MAIN_FILE) -f $(COMPOSER_FILE) run --rm composer install

migrate-table:
	sudo docker compose -f $(MAIN_FILE) -f $(ARTISAN_FILE) run --rm artisan migrate

generate-user:
	sudo docker compose -f $(MAIN_FILE) -f $(ARTISAN_FILE) run --rm artisan db:seed --class=Database\\Seeders\\Auth\\UserSeeder

install-passport:
	sudo docker compose -f $(MAIN_FILE) -f $(ARTISAN_FILE) run --rm artisan passport:install

recover-modules:
	sudo rm -f bootstrap/cache/packages.php
	sudo rm -f bootstrap/cache/services.php
	sudo docker compose -f $(MAIN_FILE) -f $(COMPOSER_FILE) run --rm --entrypoint='' composer sh -c "php artisan package:discover-modules && php artisan package:discover-modules"


# Disabling Parallel Execution
# https://www.gnu.org/software/make/manual/make.html#Parallel-Disable
.NOTPARALLEL: init-project