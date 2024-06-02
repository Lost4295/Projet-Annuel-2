#!/bin/bash
RUN certbot --apache --non-interactive --agree-tos --redirect --email admin@pcs.fr --domains pcs.freeboxos.fr
apachectl -D FOREGROUND
