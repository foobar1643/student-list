#!/bin/bash

# bootstrap
mkdir -p ./public/media/bootstrap/css
mkdir -p ./public/media/bootstrap/js
mkdir -p ./public/media/bootstrap/fonts
cp ./vendor/twbs/bootstrap/dist/css/bootstrap.min.css ./public/media/bootstrap/css/bootstrap.min.css
cp ./vendor/twbs/bootstrap/dist/js/bootstrap.min.js ./public/media/bootstrap/js/bootstrap.min.js
cp -r ./vendor/twbs/bootstrap/dist/fonts/* ./public/media/bootstrap/fonts

# jquery
mkdir -p ./public/media/js
cp ./vendor/components/jquery/jquery.min.js ./public/media/js/jquery.min.js