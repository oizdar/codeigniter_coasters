# Coasters Api

## To prepare images 
Project have a Makefile
Run commands:
- `make build`
- `make chmods` - sets writable permissions for writable folder
- `make up` - starts docker container & login to it

## Cli commands
- `./spark monitor:coasters` - starts monitoring coasters
- `./spark subscribe:coaster_errors` - subscribes to coaster_errors channel and log received messages

## To run prod environment:
- `make build-prod`





