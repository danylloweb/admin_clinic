install:
	make up
up:
	docker-compose up -d
	docker ps
down:
	docker-compose down
build:
	docker-compose up --build --remove-orphans --force-recreate
ps:
	docker-compose ps
