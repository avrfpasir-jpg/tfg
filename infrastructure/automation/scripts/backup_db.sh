#!/bin/bash
# ==============================================================================
# 🛡️ SENTINEL - Automatización de Backups a AWS S3 (Disaster Recovery)
# 
# Descripción: Volcado de la base de datos MariaDB garantizando integridad 
#              referencial mediante 'single-transaction' sin bloqueo de tablas.
# ==============================================================================

# Variables de configuración
FECHA=$(date +"%Y-%m-%d_%H-%M")
DIRECTORIO_TEMP="/tmp/sentinel_backups"
BBDD="tienda_segura" # Cambiar si la base de datos se llama diferente
BUCKET_S3="tfg-sentinel-backups-alex" # Substituir por el nombre exacto de tu Bucket
ARCHIVO_BACKUP="${BBDD}_${FECHA}.sql.gz"

echo "--------------------------------------------------------"
echo "🕒 [$(date +"%Y-%m-%d %H:%M:%S")] Iniciando Backup de SENTINEL"
echo "--------------------------------------------------------"

# 1. Crear directorio temporal si no existe
mkdir -p "$DIRECTORIO_TEMP"

# 2. Ejecutar mysqldump (NOTA: Usa ~/.my.cnf para evitar pedir contraseña)
echo "📦 1/3: Creando dump SQL de la base de datos '$BBDD'..."
mysqldump --single-transaction --quick --databases $BBDD | gzip > "$DIRECTORIO_TEMP/$ARCHIVO_BACKUP"

if [ $? -eq 0 ]; then
    echo "✅ Dump creado y comprimido: $ARCHIVO_BACKUP"
else
    echo "❌ ERROR: Fallo al crear el volcado de la base de datos."
    exit 1
fi

# 3. Subir el archivo de backup a AWS S3
echo "☁️ 2/3: Transfiriendo backup seguro a Amazon S3 (s3://$BUCKET_S3)..."
aws s3 cp "$DIRECTORIO_TEMP/$ARCHIVO_BACKUP" "s3://$BUCKET_S3/$ARCHIVO_BACKUP" --only-show-errors

if [ $? -eq 0 ]; then
    echo "✅ Backup transferido con éxito a la nube."
    
    # 4. Limpiar el archivo local para no saturar el disco duro
    echo "🧹 3/3: Limpiando archivos temporales locales."
    rm -f "$DIRECTORIO_TEMP/$ARCHIVO_BACKUP"
    
    echo "--------------------------------------------------------"
    echo "🚀 PROCESO COMPLETADO EXITOSAMENTE."
    echo "--------------------------------------------------------"
else
    echo "❌ ERROR CRÍITICO: Fallo de conexión con AWS S3. Revisa tus credenciales IAM/AWS Academy."
    exit 2
fi
