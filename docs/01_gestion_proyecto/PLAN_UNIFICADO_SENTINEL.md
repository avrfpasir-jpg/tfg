He dividido lo que queda en 3 Bloques Cerrados. Si haces esto, el proyecto está terminado.

Bloque 1: El Corazón del Dato (RDS)
Qué es: Mover la DB del servidor a Amazon RDS.
Por qué: Te quita el 90% de los problemas de "pérdida de datos".
Estado: Tienes la guía lista. Es una tarea de 1 tarde.
Bloque 2: La Puerta de Entrada (ALB)
Qué es: Poner el Load Balancer de AWS y quitar el HAProxy.
Por qué: Arregla el fallo de seguridad y disponibilidad que detectamos.
Estado: Tienes la guía lista. Otra tarde de trabajo.
Bloque 3: Documentación y Defensa
Qué es: Hacer los últimos pantallazos de las gráficas y ensayar qué vas a decir.
Estado: Ya te he corregido la memoria para que sea "invulnerable" a críticas.
Aquí tienes el resumen de la estrategia de automatización para que nadie tenga que intervenir cuando se crea un servidor nuevo:

🤖 Automatización de "Sentinel" en el Auto-Scaling:
Para que cada instancia nueva se conecte sola a Wazuh y Grafana, he incluido en el plan técnico estos dos mecanismos:

Wazuh (Auto-Enrolamiento): En la Launch Template (la plantilla de AWS), meteremos un script de arranque (User Data). Este script instalará el agente de Wazuh al encenderse el servidor y le dirá: "Hola, soy un servidor nuevo de Sentinel, añádeme al panel".

Resultado: Aparecerá en el SIEM sin que tú escribas ni un comando.
Grafana/Prometheus (Auto-Discovery): Configuraremos el archivo de Prometheus de tu servidor de monitorización para que use una función llamada ec2_sd_config (Service Discovery).

Cómo funciona: Prometheus le preguntará a AWS cada 30 segundos: "¿Hay servidores nuevos con la etiqueta 'Sentinel'?". Si Amazon le dice que sí, Prometheus empezará a capturar sus métricas de CPU y RAM al instante.
Resultado: Tus gráficas de Grafana se actualizarán solas sumando (o restando) servidores según la carga.
