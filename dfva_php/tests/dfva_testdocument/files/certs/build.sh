#!/bin/bash

openssl req -subj '/CN=CPF-539895508773/O=MICCIT/C=CR' -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /tmp/server.key -out 539895508773.crt
openssl req -subj '/CN=CPF-02-4132-3596/O=MICCIT/C=CR' -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /tmp/server.key -out 0241323596.crt
openssl req -subj '/CN=CPF-166306239151/O=MICCIT/C=CR' -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /tmp/server.key -out 166306239151.crt
openssl req -subj '/CN=CPF-03-4685-3514/O=MICCIT/C=CR' -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /tmp/server.key -out 0346853514.crt
openssl req -subj '/CN=CPF-03-4562-5753/O=MICCIT/C=CR' -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /tmp/server.key -out 0345625753.crt
openssl req -subj '/CN=CPF-08-2959-7760/O=MICCIT/C=CR' -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /tmp/server.key -out 0829597760.crt

