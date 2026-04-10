quí tienes el resumen de los 4 pilares técnicos que contiene la guía:

1. La "Clonación" (Crear una AMI)
El primer paso es "congelar" el estado perfecto de tu servidor Apache actual en una Amazon Machine Image (AMI). Esto permitirá al sistema "clonar" tu servidor en milisegundos cuando detecte mucha carga o una caída.

2. El Grupo de Destino (Target Group)
Crear un Target Group es como definir la "lista de invitados" para el balanceador. Es donde el sistema monitoriza si tus servidores web están vivos (Healthy) o muertos (Unhealthy) mediante peticiones HTTP automáticas.

3. El Application Load Balancer (ALB)
Aquí es donde eliminas el SPOF del HAProxy. El ALB de AWS se despliega en múltiples zonas de disponibilidad de forma automática.

Certificados: Podrás usar certificados gratuitos de AWS Certificate Manager (ACM) que se renuevan solos para siempre.
4. El Grupo de Auto-Escalado (ASG)
Este es el cerebro. Usará tu AMI para:

Tener siempre 2 servidores vivos (en edificios diferentes de AWS).
Si uno falla, el ASG lo detecta y lo reemplaza solo. ¡Esta es la verdadera Alta Disponibilidad!
🛡️ Un consejo para tu defensa del TFG:
Cuando el tribunal te pregunte por qué has hecho este cambio, usa este argumento:

"Al migrar de un HAProxy manual hacia un AWS Application Load Balancer (ALB), no solo eliminamos el punto único de fallo, sino que conseguimos acelación TLS (SSL Offloading) y delegamos la gestión de los ataques de Capa 7 (DDoS) a la infraestructura global de Amazon, permitiendo que mi sistema 'SENTINEL' se centre exclusivamente en la lógica de negocio y la seguridad forense de los logs."

Ahora mismo tu Grafana monitoriza 1 IP fija. Cuando tengas un Auto Scaling Group, las IPs cambiarán. Necesitas asegurarte de que tu Dashboard use Service Discovery o que Prometheus busque los servidores dinámicamente.

Cuando el sistema levante un servidor nuevo automáticamente, el agente de Wazuh tiene que registrarse solo en el Manager. Si no, tendrás servidores "ciegos" que monitorizar.