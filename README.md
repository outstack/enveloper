# OutStack Enveloper

## Installation

Install via docker

    docker run outstack/enveloper \
        -v ./enveloper/config/:/app/config \
        -v ./enveloper/data:/app/data \
        -e ENVELOPER_SMTP_HOST=smtp.mailgun.org \
        -e ENVELOPER_SMTP_USER=postmaster@example.com \
        -e ENVELOPER_SMTP_PASSWORD=password \
        -e ENVELOPER_SMTP_PORT=1025 \
        -e ENVELOPER_DEFAULT_SENDER_EMAIL=noreply@example.com \
        -e ENVELOPER_DEFAULT_SENDER_NAME=Your\ App

## Usage

Hello world

    curl -v http://localhost/outbox \
        -X POST \
        -d '{"template":"hello-world","parameters":{"name":"Bob","email":"adamquaile@gmail.com"}}'

## Issues

 - [ ] Config/detection for SSL/TLS SMTP connections

## Roadmap

Feel free to suggest other features we could add.

 - [x] Minimal alpha proof-of-concept
 - [ ] Good docker setup
 - [ ] Attachments
 - [ ] Setup instructions
 - [ ] Error pages, standardised content-types, etc..
 - [ ] API to view and reset sent messages, to enable integration tests within applications
 - [ ] Automatic generation of text/plain version from HTML
 - [ ] Automatic CSS inlining
 - [ ] MJML support
 - [ ] 