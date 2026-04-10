#!/bin/bash
DOMAIN="psicopompo"
TOKEN="2256f5ca-d5ce-432e-9bc4-c63827971e3c" # Recuerda volver a poner tu token aquí
ALB_DNS="Sentinel-ALB-891619477.us-east-1.elb.amazonaws.com"

# Obtenemos las IPs del balanceador
IPS=$(nslookup $ALB_DNS | grep -v "#" | grep "Address" | awk '{print $2}')
WORKING_IP=$(echo $IPS | awk '{print $1}') # Cogemos la primera IP disponible

if [ -z "$WORKING_IP" ]; then
    echo "Error: No se pudo resolver ninguna IP para $ALB_DNS"
    exit 1
fi

echo "Actualizando DuckDNS con la IP: $WORKING_IP"
RESULT=$(curl -s "https://www.duckdns.org/update?domains=$DOMAIN&token=$TOKEN&ip=$WORKING_IP")

echo "Respuesta de DuckDNS: $RESULT"

