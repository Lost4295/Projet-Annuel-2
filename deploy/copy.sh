#!/bin/sh
cd ../
cp -r appWeb deploy/web/appWeb
cp -r pcs_api deploy/api/pcs_api
mkdir -p deploy/py/appTickets
cp -r appTickets/request.py deploy/py/appTickets/request.py 
cd deploy
