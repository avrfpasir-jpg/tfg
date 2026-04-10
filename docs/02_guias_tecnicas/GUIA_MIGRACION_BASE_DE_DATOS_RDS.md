Aquí te explico la estrategia de migración resumida:

1. El "Destino" (Crear la instancia RDS)
En la consola de AWS, lanzamos una base de datos gestionada MariaDB 10.5 (la misma versión que tienes).

Truco para el TFG: Al elegir la opción "Multi-AZ", AWS crea automáticamente un servidor espejo en otro edificio. Si el principal falla, tus datos siguen vivos. ¡Esto es lo que garantiza la integridad!
2. El "Exilio" (Volcado de datos)
Usamos tu script de backup o el comando mysqldump para sacar una foto actual de tu tienda:

bash
# Lo hacemos desde tu servidor web
mysqldump --databases tienda_segura > backup_final.sql
3. La "Inyección" (Importar a RDS)
Subimos los datos al nuevo servidor en la nube:

bash
# Inyectamos el SQL en el endpoint de Amazon
mysql -h midb-cluster.xyz.aws.com -u admin -p < backup_final.sql
4. El "Cambio de Chips" (Actualizar secretos)
Lo mejor de haber organizado el proyecto es que ahora solo tienes que ir a un sitio: secrets/config_sentinel_db.php. Simplemente cambias el host 10.0.2.61 por el nuevo Endpoint que te dé AWS (algo como sentinel-db.xxxx.us-east-1.rds.amazonaws.com).

🛡️ Tu argumento estrella para el tribunal:
"Se ha migrado la base de datos local hacia Amazon RDS Multi-AZ para delegar en AWS la alta disponibilidad del almacenamiento. De esta forma, el sistema cuenta con replicación síncrona de datos y una gestión de snapshots automatizada, garantizando un RPO (Recovery Point Objective) de prácticamente 0 segundos ante fallos de hardware en los nodos de almacenamiento."

Esta frase es música para los oídos de un tribunal de sistemas. ¿Quieres que te ayude a configurar los Security Groups necesarios para que el servidor web pueda hablar con el nuevo RDS? Es un paso crítico para que la migración no falle.