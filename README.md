# OutStack Enveloper

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/93720c538eac41c78502805bfa6c04d2)](https://www.codacy.com/app/outstack/enveloper?utm_source=github.com&utm_medium=referral&utm_content=outstack/enveloper&utm_campaign=badger)

**Warning: This is pre-release alpha code. It may be buggy, and the docs may be wrong. Use with caution and if you're not sure about something, just ask!**

## What is it? 

Enveloper is a small service intended to be run in your infrastucture to speed up developing and testing transactional emails in your application.

Define your templates using simple template files and YAML, then send messages using simple API requests. 

See [Getting Started](./docs/01-getting-started.md) and [all docs](./docs)

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
