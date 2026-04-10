# 🎬 Guion Completo: Defensa y Vídeo del Proyecto SENTINEL

Este documento estructura tu presentación ante el tribunal (12-15 minutos) dividida en dos bloques: la defensa verbal y la proyección del vídeo demostrativo.

---

## ⏱️ Distribución del Tiempo (14 Minutos Objetivo)
- **Minutos 0 a 3:** Introducción verbal y Arquitectura.
- **Minutos 3 a 9:** Proyección del vídeo demostrativo (6 minutos).
- **Minutos 9 a 13:** Defensa de Seguridad, Resiliencia y Conclusión.
- **Minutos 13 a 15:** Turno de Preguntas (apoyado en `GUIA_DEFENSA_TFG.md`).

---

## 🗣️ Parte 1: Defensa Verbal - Introducción (3 Minutos)

**1. Presentación (0:30):**
> "Buenos días a los miembros del tribunal. Soy Álex Vidal y vengo a presentarles 'SENTINEL', una infraestructura web orientada a entornos e-commerce que prioriza desde su diseño la Seguridad, la Resiliencia y la Monitorización Activa."

**2. El Contexto y el Problema (1:00):**
> "Hoy en día, desplegar una tienda online no consiste solo en instalar un Apache y un MariaDB. Las normativas exigen protección de datos, mitigación de ataques y alta disponibilidad. El problema es que las pymes a menudo no pueden pagar infraestructuras ultracomplejas. Mi objetivo con SENTINEL ha sido diseñar una arquitectura de nivel enterprise pero empleando un enfoque FinOps (costo-eficiente) usando tecnologías Open Source liderando sobre la nube de AWS."

**3. Arquitectura General (1:30):** *(Muestra tu diagrama de red en pantalla)*
> "Como pueden ver en el diagrama, he segmentado la red aislando los datos críticos. Tenemos un Load Balancer en la subred pública que filtra y cifra el tráfico (SSL End-to-End). Detrás, en un nivel protegido, nuestro Servidor Web, y enterrado en una subred privada sin acceso a Internet (un entorno 'Air-Gapped' simulado), reside el motor de base de datos MariaDB. Todo esto está siendo vigilado 24/7 por un SIEM potente: Wazuh.
> Para que vean cómo todos estos engranajes funcionan juntos ante un usuario real y ante un atacante, he preparado un vídeo que resume su puesta en marcha."

---

## 🎥 Parte 2: Proyección del Vídeo Demostrativo en Directo (6 Minutos)

*Nota importante: Como el vídeo no tiene audio, tú serás quien hable en vivo ante el tribunal mientras las imágenes se proyectan. Graba el vídeo a un ritmo pausado para que te dé tiempo a explicar cada escena sin atropellarte.*

**Escena 1: Experiencia de Usuario y Cifrado (1 min)**
*   **Vídeo:** Se abre el navegador, se accede al dominio (`https://psicopompo.duckdns.org` o similar). Se navega por la tienda, se mete un producto en el carrito y se registra un usuario.
*   **Tú explicas en vivo:** "Si miramos a la pantalla, comenzamos viendo la experiencia de un cliente legítimo. Todo el tráfico está cifrado por Let's Encrypt interactuando con el Load Balancer, y además usamos cifrado interno TLS hacia el backend. El usuario interactúa fluidamente con la tienda sin notar la complejidad que hay debajo."

**Escena 2: Demostración de Tolerancia a Fallos / Load Balancer (1 min)**
*   **Vídeo:** Se muestra el panel de estadísticas de HAProxy (`/stats`). Pantalla partida mostrando cómo se apaga Apache en el servidor web por SSH (`systemctl stop httpd`). Se recarga la web y muestra una página de contingencia limpia. Se vuelve a encender y la web carga de nuevo.
*   **Tú explicas en vivo:** "Aquí comprobamos la resiliencia. El Load Balancer monitoriza la salud de los servidores en tiempo real mediante Health Checks. Fíjense que al tirar a propósito el servidor web, el HAProxy interviene y levanta un aviso controlado. Esto protege al usuario de esperas infinitas y evita fugas de información de red (stack traces)."

**Escena 3: Ataque y Respuesta Activa de Wazuh (2.5 mins) 🔥 *[Momento Estrella]* **
*   **Vídeo:** Pantalla dividida. A un lado, terminal simulando el ataque (ej. escaneo masivo `ab` o ataque SSH). Al otro lado, el Dashboard de Wazuh. Transcurren unos segundos, salta una gráfica roja de alerta crítica en Wazuh y la terminal del ataque se congela ("Connection refused/Timeout").
*   **Tú explicas en vivo:** "Este es el núcleo de seguridad de SENTINEL. Estamos simulando un escaneo malicioso en tiempo real. En una jerarquía tradicional, el servidor caería por saturación. Sin embargo, observen el panel de Wazuh. El SIEM ha detectado la anomalía en el log. Automáticamente, mediante una 'Respuesta Activa', se conecta al servidor web e inyecta un Drop en el firewall, baneando al atacante de inmediato en capa 3."

**Escena 4: Continuidad de Negocio (Backups S3) (1.5 min)**
*   **Vídeo:** Se abre la consola de AWS S3. Se muestra en terminal la ejecución del script `backup_db.sh`. Se refresca AWS S3 y aparece el archivo `.sql.gz` nuevo.
*   **Tú explicas en vivo:** "Finalmente, la seguridad integral incluye la recuperación. En pantalla vemos cómo diariamente el sistema lanza un volcado MySQL de forma no bloqueante usando 'single-transaction'. Se comprime, se cifra y se almacena en un bucket inmutable en Amazon S3. Estaríamos listos para restaurar el servicio en otra región en caso de pérdida total."

---

## 🗣️ Parte 3: Defensa Verbal - Cierre Técnico (4 Minutos)

**1. El Hardening y Mínimo Privilegio (2:00):**
> *(Vuelves a hablar mirando al tribunal tras el vídeo).* 
> "Como han visto en el vídeo, SENTINEL no es solo funcional, es seguro. Además activa de la seguridad de Wazuh, he implementado seguridad pasiva mediante 'Hardening'. La base de datos no es accesible desde internet; de hecho, la aplicación PHP se conecta mediante un usuario que aplica el Principio de Mínimo Privilegio: no puede borrar tablas, solo actualizar datos. A su vez, los secretos y credenciales están aislados fuera del código fuente de la web."

**2. Retos Superados (1:00):**
> "Alcanzar este grado de seguridad con las restricciones de una cuenta educativa de AWS (como AWS Academy) ha sido el mayor reto. Al no disponer de servicios de pago como NAT Gateways o AWS Systems Manager (SSM) en redes privadas, tuve que diseñar una arquitectura de red a medida. Resolví el problema implementando soluciones híbridas como el uso de la propia máquina web actuando como un 'Bastion Host', y ajustando el firewall de Linux (`firewalld` y `SELinux`) manualmente para que permitiera todo el enrutamiento complejo sin abrir brechas."

**3. Conclusión (1:00):**
> "En conclusión, SENTINEL demuestra que un Administrador de Sistemas en red moderno ya no solo configura servidores; orquesta entornos en la nube balanceando la disponibilidad, la ciberseguridad profunda y la rentabilidad financiera. 
> 
> Muchas gracias por su atención, proyecto finalizado y quedo a su entera disposición para cualquier pregunta técnica."
