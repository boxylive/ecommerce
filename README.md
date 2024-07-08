# Ecommerce

Un projet ecommerce avec Symfony 7. On démarre ça et on verra jusqu'où on va...

## Installation

Vous avez besoin d'installer :

- Docker
- PHP 8.2

Vous devez lancer les containers Docker :

```bash
docker compose up -d
```

Vous devez lancer les migrations (On assume de ne pas faire de migrations pour le moment) :

```bash
symfony console d:s:u --force
# symfony console d:m:m
# symfony console d:f:l
```

## Frontend

Pour le front, on est parti sur AssetMapper de Symfony... Du coup, pour travailler avec Tailwind :

```bash
php bin/console tailwind:build --watch
```

## Testing

On utilise une base de données SQLite pour les tests.

```bash
php bin/console --env=test d:d:c
php bin/console --env=test d:s:c
php bin/phpunit
vendor/bin/php-cs-fixer fix src --dry-run --diff
```
