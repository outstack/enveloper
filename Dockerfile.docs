FROM node:10-alpine as build
COPY schemata/ /app/schemata
COPY docs/api/yarn.lock /app/docs/api/yarn.lock
COPY docs/api/package.json /app/docs/api/package.json
WORKDIR /app/docs/api
RUN yarn install
COPY docs /app/docs
RUN node build.js

FROM nginx:alpine
COPY docs /usr/share/nginx/html
COPY --from=build /app/docs/api/build /usr/share/nginx/html/api/build
COPY README.md /usr/share/nginx/html/README.md
RUN sed -i 's|](\./docs/|](\./|g' /usr/share/nginx/html/README.md
COPY docs/nginx-vhost.conf /etc/nginx/conf.d/default.conf

