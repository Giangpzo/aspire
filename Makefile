# makefile tutorial
# https://makefiletutorial.com/#include-makefiles

OTHER_MAKE_FILES = ./environment-setup/makefiles/*
include $(OTHER_MAKE_FILES)

MAIN_FILE=./environment-setup/docker-composes/docker-compose.yaml

up:
	sudo docker compose -f $(MAIN_FILE) up -d

down:
	sudo docker compose -f $(MAIN_FILE) down

permission:
	sudo chown -R giangpzo ../aspire/