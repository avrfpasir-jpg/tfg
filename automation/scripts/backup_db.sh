#!/bin/bash

# 🛡️ Script de Backup Automatizado para TFG SENTINEL
# Objetivo: Realizar dump de MariaDB y subirlo cifrado a AWS S3

# --- ⚙️ CONFIGURACIÓN ---
DB_NAME="tienda_segura"
DB_USER="admin_tienda" # El usuario que creamos segun GUIA_DESPLIEGUE_AWS.md
# DB_PASS="TU_CONTRASEÑA" # Es mejor usar un archivo .my.cnf o Secrets Manager

# AWS Config
S3_BUCKET="tfg-sentinel-backups-alex" # El nombre que has dado en la consola de AWS
BACKUP_PATH="/tmp/backups_db"
DATE=$(date +%Y-%m-%d_%H-%M)
FILENAME="backup_${DB_NAME}_${DATE}.sql.gz"

# --- 🚀 EJECUCIÓN ---

# 1. Crear directorio temporal si no existe
mkdir -p $BACKUP_PATH

echo "[$(date)] Iniciando backup de la base de datos: $DB_NAME..."

# 2. Realizar el dump y comprimirlo al vuelo
# Nota: Si no tienes configurado .my.cnf, pedira contraseña o deberas añadir -p$DB_PASS (sin espacio)
mysqldump -u $DB_USER $DB_NAME | gzip > $BACKUP_PATH/$FILENAME

if [ $? -eq 0 ]; then
    echo "✅ Backup local completado: $BACKUP_PATH/$FILENAME"
else
    echo "❌ ERROR: Fallo al realizar el mysqldump"
    exit 1
fi

# 3. Subir a AWS S3
echo "[$(date)] Subiendo a Amazon S3 ($S3_BUCKET)..."
aws s3 cp $BACKUP_PATH/$FILENAME s3://$S3_BUCKET/db_backups/$FILENAME

if [ $? -eq 0 ]; then
    echo "✅ Subida con exito a S3: $FILENAME"
    # 4. Limpieza local (opcional, dejamos solo los ultimos 7 dias si no se borrase)
    rm $BACKUP_PATH/$FILENAME
    echo "🧹 Archivo temporal eliminado para ahorrar espacio."
else
    echo "❌ ERROR: No se pudo subir el archivo a S3. Revisa los permisos de AWS CLI."
    exit 1
fi

echo "[$(date)] Backup finalizado correctamente."
