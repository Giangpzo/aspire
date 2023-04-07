MAIN_COMPOSER_FILE = ./environment-setup/docker-composes/docker-compose.yaml
ARTISAN_COMPOSER_FILE = ./environment-setup/docker-composes/docker-compose.artisan.yaml
REQUEST_BASE_PATH = App/Modules/

migrate:
	# include docker-compose.yaml file first to merge some config (ex: network)
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan migrate

clear-cache:
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan cache:clear

maintenance-on:
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan down --render="maintenance"

maintenance-off:
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan up

artisan-sh:
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm --entrypoint='' artisan sh

artisan-sh-refresh:
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --build --rm --entrypoint='' artisan sh

form-request:
	# argument like this: file_path='Auth/Requests/TestRequest'
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan make:request $(REQUEST_BASE_PATH)$(file_path)

queue-work:
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm -d artisan queue:work

queue-restart:
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm -d artisan queue:restart

seeder:
	# argument like this: file_path='Auth/Requests/TestRequest'
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan make:seeder $(file_path)

test:
	# argument like this: filter=test_admin_can_request_index
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan test

test-filter:
	# argument like this: filter=test_admin_can_request_index
	sudo docker compose -f $(MAIN_COMPOSER_FILE) -f $(ARTISAN_COMPOSER_FILE) run --rm artisan test --filter=$(filter)