default:
	@echo 'Eight CLU - Comand Line Utillity';
	@echo 'Try `make help`';
	
help:
	@echo 'Eight CLI - Availiable commands';
	@echo "\t database \t Installs/Upgrades database"

database:
	@echo 'Running database scripts ...';
	php bin/database.php;