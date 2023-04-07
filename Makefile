# makefile tutorial
# https://makefiletutorial.com/#include-makefiles

OTHER_MAKE_FILES = ./environment-setup/makefiles/*
include $(OTHER_MAKE_FILES)

up:
	sudo docker compose -f ./environment-setup/docker-composes/docker-compose.yaml up -d

up-build:
	sudo docker compose -f ./environment-setup/docker-composes/docker-compose.yaml up -d --build

down:
	sudo docker compose -f ./environment-setup/docker-composes/docker-compose.yaml down

permission:
	sudo chown -R giangpzo ../aspire/