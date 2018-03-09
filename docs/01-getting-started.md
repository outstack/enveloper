# Getting started

## What you'll need

The easiest way to run Enveloper is through docker. You'll download and run a small container serving as an API. 

You'll need SMTP details for sending the emails. If you don't have these you can get some for a domain you own through a service like [Mailgun](https://www.mailgun.com/).

You'll also need to share a folder where Enveloper will look for your templates. 

## Running the server

Let's assume you've created a folder called `enveloper-data`. Run this to start the server

    docker run \
        -d \
        --name enveloper \
        -v $(pwd)/enveloper-data:/app/data \
        -e ENVELOPER_SMTP_HOST=smtp.mailgun.org \
        -e ENVELOPER_SMTP_USER=postmaster@example.com \
        -e ENVELOPER_SMTP_PASSWORD=password \
        -e ENVELOPER_SMTP_PORT=1025 \
        -e ENVELOPER_DEFAULT_SENDER_EMAIL=noreply@example.com \
        -e ENVELOPER_DEFAULT_SENDER_NAME=Your\ App \
        -e ENVELOPER_DB_DSN=sqlite:////app/data/enveloper.sqlite
        -p 8080:80 \
        outstack/enveloper

If you haven't already done so, you will need to create the database and schema: 

    docker exec -it enveloper /app/bin/console doctrine:database:create
    docker exec -it enveloper /app/bin/console doctrine:schema:create

## Sending your first email

If you're following this guide for the first time, `enveloper-data` will be empty and you'll have no templates to use. 
You can copy one from these docs at `docs/examples/hello-world` into `enveloper-data/templates`. 
You should now have `enveloper-data/templates/hello-world/hello-world.meta.yml` 

Test you can access the API:

    curl http://localhost:8080/


Send your first email using curl, like this:

    curl http://localhost:8080/outbox \
        -X POST \
        -d '{"template":"hello-world","parameters":{"name":"Bob","email":"youremailaddresshere"}}'

Now you can inspect your sent emails. This is useful in writing in your test-suite, for example:

    curl -X GET http://localhost:8080/outbox
        
You can also preview the content of a rendered message without sending it, for example:

    curl http://localhost:8080/outbox/preview \
        -X POST \
        -d '{"template":"hello-world","parameters":{"name":"Bob","email":"youremailaddresshere"}}'
        -H 'Accept: text/html'

or

    curl http://localhost:8080/outbox/preview \
        -X POST \
        -d '{"template":"hello-world","parameters":{"name":"Bob","email":"youremailaddresshere"}}'
        -H 'Accept: text/plain'
