.DEFAULT_GOAL := help
.PHONY: lint tests ci

lint: ## Runs cs and static analysis against the codebase
	./vendor/bin/phpcs && ./vendor/bin/phpstan analyse
tests: ## Runs PHPUnit
	./vendor/bin/phpunit
ci: ## Runs both linters and phpunit
	make lint && make tests
help:
	 @grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
