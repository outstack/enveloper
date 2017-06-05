# Getting started

## What you'll need

The easiest way to run Enveloper is through docker. You'll download and run a small container serving as an API. 

You'll need SMTP details for sending the emails. If you don't have these you can get some for a domain you own through a service like [Mailgun](https://www.mailgun.com/).

You'll also need to share a folder where Enveloper will look for your templates. 

## Running the server

Let's assume you've created a folder called `enveloper-data`. Run this to start the server

    docker run \
        -v $(pwd)/enveloper-data:/app/data \
        -e ENVELOPER_SMTP_HOST=smtp.mailgun.org \
        -e ENVELOPER_SMTP_USER=postmaster@example.com \
        -e ENVELOPER_SMTP_PASSWORD=password \
        -e ENVELOPER_SMTP_PORT=1025 \
        -e ENVELOPER_DEFAULT_SENDER_EMAIL=noreply@example.com \
        -e ENVELOPER_DEFAULT_SENDER_NAME=Your\ App \
        -p 8080:80 \
        outstack/enveloper

## Sending your first email

If you're following this guide for the first time, `enveloper-data` will be empty and you'll have no templates to use. You can copy one from these docs at `docs/examples/hello-world` into `enveloper-data`. Make sure you take the whole folder, not just the files. 

Test you can access the API:

    curl http://localhost:8080/


Send your first email using curl, like this:

    curl -v http://localhost:8080/outbox \
        -X POST \
        -d '{"template":"hello-world","parameters":{"name":"Bob","email":"youremailaddresshere"}}'
