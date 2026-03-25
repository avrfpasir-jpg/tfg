# Guía de Defensa Crítica: Proyecto SENTINEL (ASIR)

Este documento contiene las preguntas más probables del tribunal y las respuestas técnicas recomendadas ("Versiones Pro") para defender el diseño del proyecto SENTINEL.

---

## 1. Alta Disponibilidad y el Nodo Único (HAProxy)
**Pregunta:** Has asegurado que el balanceador elimina puntos únicos de fallo (SPOF), pero solo tienes un nodo de HAProxy. Si este cae, todo el servicio desaparece. ¿Cómo justificas llamarlo "Alta Disponibilidad"?

**Respuesta Pro:**
> "Efectivamente, en la topología actual el HAProxy representa un SPOF (Single Point of Failure). Debido a las limitaciones del entorno de aprendizaje (AWS Academy), que restringe el uso de IPs flotantes o protocolos como VRRP necesarios para arquitecturas de alta disponibilidad real, se ha priorizado la implementación de la **lógica de balanceo y terminación SSL**. No obstante, en un entorno de producción industrial, la solución sería desplegar un clúster de nodos HAProxy sincronizados mediante **Keepalived**, garantizando una IP de servicio (VIP) redundante y transparente para el usuario."

---

## 2. Movimiento Lateral y el Servidor Web como Bastion
**Pregunta:** El servidor web actúa como puente (Bastion) hacia la red privada. Si el código PHP es comprometido, el atacante tiene acceso directo a la red privada. ¿Por qué mezclar el plano público con el de gestión?

**Respuesta Pro:**
> "La arquitectura actual utiliza el servidor web como salto por una cuestión de economía de recursos en el laboratorio de AWS. En un escenario profesional, esta práctica se evitaría implementando un **Bastion Host dedicado**, aislado y altamente endurecido, o preferiblemente mediante el uso de **AWS Systems Manager (SSM) Session Manager**. Esta última opción permitiría gestionar las instancias en subredes privadas sin necesidad de abrir el puerto 22 (SSH) al exterior y sin exponer el tráfico de gestión en la misma máquina que sirve el tráfico público."

---

## 3. Gestión de Paquetes y "Side-loading"
**Pregunta:** Has instalado la base de datos moviendo paquetes RPM manualmente por SCP. Esto no escala y es inseguro. ¿Por qué no usaste un Proxy o un NAT?

**Respuesta Pro:**
> "La técnica de side-loading se utilizó como solución puntual para superar el aislamiento de la subred privada sin incurrir en los costes de un NAT Gateway de AWS. Sin embargo, se reconoce que para la escalabilidad y el mantenimiento de parches, la solución óptima es el despliegue de un **servidor Proxy (ej. Squid)** o una **NAT Instance** (más económica y configurable). Esto permitiría a los nodos privados descargar actualizaciones de repositorios oficiales de forma unidireccional y controlada, garantizando la integridad de los orígenes de software."

---

## 4. Cifrado Interno (VPC Sniffing)
**Pregunta:** El SSL termina en el HAProxy (SSL Termination). El tráfico interno viaja en texto claro por la VPC. ¿Cómo evitas el sniffing interno?

**Respuesta Pro:**
> "En este diseño, el tráfico interno viaja por el puerto 80 para optimizar el rendimiento del servidor Apache, confiando en el aislamiento que proporcionan los **AWS Security Groups**, que restringen el tráfico entrante al puerto 80 únicamente desde la IP privada del HAProxy. No obstante, para entornos de máxima seguridad o cumplimiento de normativas (como PCI-DSS), la solución sería implementar **SSL de extremo a extremo (End-to-End Encryption)**, configurando el HAProxy para recolectar y recifrar el tráfico hacia los backends, eliminando cualquier ventana de exposición en tránsito."

---

## 5. Consistencia de Backups
**Pregunta:** Mencionas dumps SQL y snapshots de EBS. ¿Cómo garantizas la integridad de los datos si hay una venta en curso durante el backup?

**Respuesta Pro:**
> "El script de backup utiliza la utilidad `mysqldump` con el parámetro `--single-transaction`. Esto permite realizar una copia de seguridad consistente de las tablas InnoDB sin bloquear las lecturas ni escrituras de la aplicación, garantizando que el estado de la base de datos sea el exacto del momento en que se inició el proceso. Se han realizado pruebas de restauración en un entorno de pre-producción paralelo para validar que los dumps almacenados en S3 mantienen la integridad referencial y funcional de la tienda."

---

## 6. Sizing y Rendimiento del SIEM (Wazuh)
**Pregunta:** Wazuh consume muchos recursos. Con 43 RPS según tu test de estrés, ¿cuándo colapsará tu t3.medium?

**Respuesta Pro:**
> "El nodo SIEM actual está dimensionado como un **Mínimo Producto Viable (MVP)**. Para mitigar el agotamiento de recursos (CPU Credits y RAM), se han configurado **políticas de retención de logs** agresivas y un ajuste fino de las reglas para que el agente solo reporte eventos de nivel crítico (vulnerabilidades, cambios de integridad o ataques de fuerza bruta). En un entorno de producción, la arquitectura escalaría hacia un clúster distribuido (un nodo Manager para procesar y varios nodos Indexer para almacenar), permitiendo absorber picos de tráfico de logs sin impactar en la monitorización."

---

## 7. Privilegios de Base de Datos
**Pregunta:** ¿Qué usuario utiliza la web para conectar con la DB? ¿Es root o un usuario restringido?

**Respuesta Pro:**
> "La aplicación web utiliza un perfil de conexión restringido denominado `sentinel_user`, el cual solo posee permisos de `SELECT`, `INSERT` y `UPDATE` sobre el esquema de la tienda segura. Se ha deshabilitado el acceso remoto al usuario `root` de MariaDB y se han implementado reglas de firewall (Security Groups) para que el servidor de base de datos solo acepte conexiones entrantes por el puerto 3306 desde la IP privada específica del servidor web, siguiendo estrictamente el **Principio de Mínimo Privilegio**."
