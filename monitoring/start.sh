#!/bin/bash

# ============================================================
#  SENTINEL - Script de Inicio del Stack de Monitorización
#  Genera configuraciones dinámicas y levanta contenedores
# ============================================================

# 1. Cargar variables del .env
if [ -f .env ]; then
    echo "Cargando variables desde .env..."
    export $(grep -v '^#' .env | xargs)
else
    echo "ERROR: No se encuentra el archivo .env"
    exit 1
fi

# 2. Generar prometheus.yml a partir de la plantilla
echo "Generando configuración de Prometheus..."
envsubst < prometheus/prometheus.yml.template > prometheus/prometheus.yml

# 3. Verificar si el archivo se generó correctamente
if [ $? -eq 0 ]; then
    echo "✅ prometheus.yml generado con éxito."
else
    echo "❌ ERROR al generar prometheus.yml"
    exit 1
fi

# 4. Levantar el stack con Docker Compose
echo "Iniciando Docker Compose..."
docker compose up -d

echo "============================================================"
echo " Stack SENTINEL levantado."
echo " Grafana: http://<IP-PUBLICA>:3000"
echo " Prometheus: http://<IP-PUBLICA>:9090"
echo "============================================================"
