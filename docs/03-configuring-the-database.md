# Configuring the database

Emails are recorded by default into an SQLite database at `enveloper.sqlite` in the mounted data directory. 

## Overriding the DSN
This can be overridden by the `ENVELOPER_DB_DSN` environment variable using the [Doctrine DBAL format](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url).

## Supported DB platforms

The list of databases supported by doctrine is [here](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/platforms.html).
By default, not all PHP DB extensions are available, so you may need to install the relevant one by building a custom Dockerfile.

MySQL and SQLite are available by default.

