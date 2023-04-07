# makefile tutorial
# https://makefiletutorial.com/#include-makefiles

OTHER_MAKE_FILES = ./environment-setup/makefiles/*
include $(OTHER_MAKE_FILES)

MAIN_FILE=./environment-setup/docker-composes/docker-compose.yaml
ARTISAN_FILE=./environment-setup/docker-composes/docker-compose.artisan.yaml
COMPOSER_FILE=./environment-setup/docker-composes/docker-compose.composer.yaml

up:
	sudo docker compose -f $(MAIN_FILE) up -d

# use up-build for initiate the project
up-build:
	# build up docker images and containers
	sudo docker compose -f $(MAIN_FILE) up -d --build

	# composer install package for laravel project
	sudo docker compose -f $(MAIN_FILE) -f $(COMPOSER_FILE) run --rm composer install

	# migrate table
	sudo docker compose -f $(MAIN_FILE) -f $(ARTISAN_FILE) run --rm artisan migrate

	# generate user
	sudo docker compose -f $(MAIN_FILE) -f $(ARTISAN_FILE) run --rm artisan db:seed --class=Database\\Seeders\\Auth\\UserSeeder

	# install passport --> done
	sudo docker compose -f $(MAIN_FILE) -f $(ARTISAN_FILE) run --rm artisan passport:install

down:
	sudo docker compose -f $(MAIN_FILE) down

permission:
	sudo chown -R giangpzo ../aspire/