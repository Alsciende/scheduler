test: phpstan phpunit

phpstan:
	vendor/bin/phpstan analyse src tests --level=5

phpunit:
	vendor/bin/phpunit tests --colors=auto
