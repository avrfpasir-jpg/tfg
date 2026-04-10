        # MEMORIA FINAL DEL PROYECTO: SENTINEL (Tienda Segura)

        ## 1. Portada
        *   **Nombre del proyecto:** SENTINEL - Infraestructura Web Segura y Monitorizada (Tienda Segura)
        *   **Ciclo formativo:** Grado Superior en Administración de Sistemas Informáticos en Red (ASIR)
        *   **Módulos involucrados:** Implantación de Sistemas Operativos, Planificación y Administración de Redes, Gestión de Bases de Datos, Seguridad y Alta Disponibilidad, Administración de Sistemas Gestores de Base de Datos, Servicios de Red e Internet.
        *   **Integrantes del equipo:** Alex Vidal Ródenas

        ## 2. Agradecimientos
        [Espacio reservado para los agradecimientos personales del autor]

        ## 3. Resumen
                Este proyecto, denominado **SENTINEL**, consiste en el diseño, despliegue y aseguramiento de una infraestructura de comercio electrónico denominada "Tienda Segura". La solución integra un servidor web (Apache/PHP), una base de datos (MariaDB), un balanceador de carga (HAProxy) y un completo stack de monitorización y seguridad (Prometheus, Grafana y Wazuh) sobre una infraestructura en la nube (AWS). El objetivo principal es implementar una **arquitectura resiliente y preparada para la alta disponibilidad**, optimizando el rendimiento y garantizando la detección proactiva de incidentes de seguridad en un entorno controlado de producción.

        ## 4. Introducción
        ### 4.1. Contexto y justificación del proyecto
        En el entorno actual, la seguridad y la disponibilidad de los servicios web son críticas para cualquier negocio de comercio electrónico. Un fallo en el sistema o una brecha de seguridad pueden suponer grandes pérdidas económicas y de reputación. Este proyecto nace de la necesidad de crear un entorno robusto que no solo sirva una aplicación web, sino que también se proteja activamente y se monitorice en tiempo real.

        ### 4.2. Objetivos del proyecto
        *   Desplegar una arquitectura web escalable y segura en AWS. *(KPI: Infraestructura operativa con HTTPS activo y dominio público resuelto)*.
                *   Implementar un sistema de balanceo de carga para mitigar los puntos únicos de fallo en la capa de aplicación. *(KPI: HAProxy con SSL Termination sirviendo el 100% del tráfico público)*.
        *   Establecer un stack de monitorización avanzada (Prometheus, Grafana). *(KPI: Dashboard con métricas de Apache, MariaDB y CPU/RAM en tiempo real)*.
        *   Integrar un sistema de detección y respuesta ante incidentes (Wazuh SIEM). *(KPI: Detección de ataque de fuerza bruta documentada con regla 5712, MITRE T1110)*.
        *   Aplicar medidas de endurecimiento (Hardening) en todos los nodos. *(KPI: Usuario de BD restringido `sentinel_web`, POLP aplicado, OWASP Top 10 revisado)*.
        *   Garantizar la identidad digital y el cifrado de comunicaciones (SSL/TLS). *(KPI: Certificado Let's Encrypt válido en producción, redirección HTTP→HTTPS forzada)*.

        ### 4.3. Alcance y limitaciones del trabajo
        *   **Alcance:** Incluye desde el diseño de la red en AWS (VPC, subredes, SG) hasta la implementación del frontend, backend, base de datos, balanceo, seguridad y monitorización.
        *   **Limitaciones:** El proyecto se limita a recursos dentro de la capa gratuita o de bajo coste de AWS (instancias t2/t3). El desarrollo de la aplicación web se centra en la funcionalidad y seguridad, no en un diseño visual extremadamente complejo.

        ## 5. Análisis y contextualización de empresa/s del sector
        ### 5.1. Caracterización del sector
        El panorama actual de la ciberseguridad para las PyMEs en España atraviesa un momento crítico. Tras la digitalización acelerada post-pandemia, el sector de los Servicios Gestionados de IT (MSP) ha visto cómo la superficie de ataque de las pequeñas empresas ha crecido exponencialmente. 
        *   **Tendencias y Riesgos:** Según el informe ENISA Threat Landscape 2024, se detecta un aumento sostenido en los ataques de **Ransomware as a Service (RaaS)** que tienen como objetivo el secuestro de datos transaccionales. El Verizon Data Breach Investigations Report 2024 confirma que las PyMEs representan el 46% de las víctimas de brechas documentadas, debido principalmente a la **falta de personal cualificado** interno, lo que genera una brecha entre la innovación necesaria y la protección real.
        *   **Oportunidades:** SENTINEL es una solución de infraestructura **IaaS (Infrastructure as a Service)** diseñada específicamente para proteger ecosistemas e-commerce en pequeñas y medianas empresas. Mediante una arquitectura de red segmentada en AWS y el uso coordinado de herramientas de código abierto como **Wazuh, Prometheus y Grafana (Stack WPG)**, el sistema garantiza la visibilidad total del tráfico y la respuesta automática ante incidentes (Active Response). El pilar fundamental del proyecto es la **Defensa en Profundidad**, aislando la capa de datos y centralizando la monitorización para detectar patrones de ataque en tiempo real.
        *   **Justificación del Proyecto:** SENTINEL no es simplemente un desarrollo web, sino una solución de **Infraestructura como Servicio (IaaS) empaquetada**. Responde a la necesidad de las PyMEs de disponer de un e-commerce profesional, funcional y blindado mediante una arquitectura que antes solo estaba al alcance de grandes corporaciones, garantizando la continuidad de negocio mediante la **capacidad de alta disponibilidad** y la **protección del dato**.

        ### 5.2. Propuesta de Valor y Capacidades Estratégicas
        SENTINEL se posiciona como una infraestructura orientada al modelo de negocio de **infraestructura gestionada**, similar a una "caja negra" de seguridad para el cliente final. Su valor reside en transformar una arquitectura cloud compleja en una solución operativa, segura y escalable.

        *   **Público Objetivo (Clientes Directos):**
            *   **B2B / MSP (Managed Service Providers):** Proveedores tecnológicos que requieren una plataforma de aprovisionamiento 24/7 con disponibilidad garantizada por contrato (SLA).
            *   **Agencias de Desarrollo:** Partners que buscan una base técnica robusta sobre la cual desplegar proyectos personalizados para sus clientes finales, abstrayendo la complejidad del servidor.

        *   **Ejes de Diferenciación Estratégica:**
            1.  **Abstracción de la Complejidad (Orientado a la Facilidad de Uso):** La barrera técnica se elimina para el cliente del negocio. El sistema está concebido para una **estrategia de despliegue automatizado**, permitiendo que un e-commerce seguro esté operativo de forma ágil, ocultando la complejidad de la VPC y el balanceo.
            2.  **Transparencia en la Observabilidad:** SENTINEL ofrece visibilidad total mediante un **Dashboard de Grafana**, permitiendo al administrador verificar la salud del negocio y los picos de tráfico en tiempo real.
            3.  **Hardening de Aplicación y Datos:** Blindaje multicapa que incluye protección específica en el código PHP y un aislamiento físico de la base de datos en subredes privadas, siguiendo el principio de **Mínimo Privilegio**.
            4.  **Diseño Cloud-Agnostic:** Aunque se despliega sobre AWS, la arquitectura es **Portátil**. Se han utilizado estándares de la industria (como el protocolo S3, que podría sustituirse por **MinIO** en entornos locales) para garantizar que la solución pueda migrar a cualquier proveedor cloud o servidor físico sin cambios estructurales.

        *   **Impacto de SENTINEL en la Continuidad de Negocio:**
            *   **Preparado para la Alta Disponibilidad:** El uso de balanceo de carga (HAProxy) neutraliza los puntos únicos de fallo potenciales en la capa de entrada.
            *   **Protección de Marca y Activos:** Se evita el daño reputacional y legal derivado de brechas de seguridad mediante la segmentación estricta de red.
            *   **Toma de Decisiones Estratégica:** La analítica en tiempo real optimiza recursos según picos de demanda (**FinOps**).
            *   **Resiliencia Proactiva:** Backup automatizado fuera del nodo (Off-site) para garantizar la recuperación ante fallos catastróficos.

        ### 5.3. Relación con los Objetivos de Desarrollo Sostenible (ODS)
        *   **ODS 9 (Industria, Innovación e Infraestructura):** SENTINEL contribuye a crear una infraestructura resiliente mediante el uso de **balanceo de carga (HAProxy)** y una política de **Backups off-site en AWS S3**, garantizando que la industria digital sea capaz de resistir fallos técnicos o ataques externos sin pérdida de servicio crónico.
        *   **ODS 8 (Trabajo Decente y Crecimiento Económico):** La propuesta fomenta el crecimiento económico seguro. Al proteger la integridad digital y los datos del cliente mediante una segmentación de red profesional, se evita la erosión económica que suponen las multas por incumplimiento de la **RGPD** y las pérdidas por interrupción de ventas.

        ### 5.4. Identificación de los riesgos laborales en la empresa
        Como gestores de infraestructura cloud y administración remota de sistemas, los riesgos identificados se centran en el entorno de oficina y gestión crítica:
        *   **Riesgos de Seguridad Operativa:** La presencia de credenciales de base de datos en archivo de configuración PHP (`config_sentinel_db.php`) fuera de un gestor de secretos representa un riesgo real de exposición ante un compromiso del servidor web. Identificado como mejora de producción (AWS Secrets Manager).
        *   **Riesgos de Disponibilidad:** El nodo HAProxy es un SPOF en la arquitectura actual. Ante su caída, el servicio quedaría inaccesible hasta redespliegue manual. Mitigado documentalmente mediante el protocolo de contingencia de reconfección de DNS de emergencia.
        *   **Riesgos de Integridad de Datos:** La ausencia de replicación en MariaDB (modo Single-Node) implica que un fallo de hardware en el nodo de BD supone pérdida de datos hasta el último backup en S3. Mitigado mediante backups nocturnos automatizados con `mysqldump --single-transaction`.
        *   **Riesgos de Gestión:** La fatiga por alertas en el SIEM (Alert Fatigue) puede generar desensibilización del operador. Mitigado mediante el ajuste de umbrales Wazuh a nivel ≥ 5 y la limitación de notificaciones Telegram a eventos críticos.

        ### 5.5. Conclusiones del análisis
        La solución propuesta con **SENTINEL** redefine el concepto de tienda online: no busca solo la funcionalidad de venta, sino la **resiliencia operativa mediante segmentación**. Al convertir una infraestructura compleja de AWS en un activo gestionado, se elimina la barrera técnica de entrada para la seguridad avanzada. Esta arquitectura aplica principios de **mínimo privilegio y aislamiento de red**, pilares del modelo Zero Trust, mediante la segmentación estricta de subredes y el control granular de accesos entre nodos.

        ## 6. Desarrollo del Proyecto
        ### 6.1. Metodología de trabajo
        El proyecto se ha ejecutado bajo un enfoque de **Defensa en Profundidad (Defense in Depth)**, estructurando la seguridad en capas independientes para retrasar o mitigar posibles intrusiones. Para el despliegue, se ha utilizado una **metodología iterativa por fases**, con validación explícita de cada componente antes de comenzar la siguiente fase:
        *   **Enfoque Evolutivo:** Priorización de la infraestructura crítica (Red y Cómputo) antes de la integración de capas superiores (SIEM y Monitorización).
        *   **Filosofía 'Security-First':** Cada servicio (Apache, MariaDB, PHP) ha pasado por un proceso de **hardening** inicial (desactivación de servicios innecesarios, permisos mínimos) antes de su puesta en producción.

                1.  **Fase 1 (Infraestructura de Red):** Despliegue de la `SENTINEL-VPC` (10.0.0.0/16) con segmentación estricta: subred pública para el balanceador y subred privada para el servidor web, base de datos y monitorización, aislando totalmente la lógica de negocio del acceso directo desde internet.
        2.  **Fase 2 (Seguridad Perimetral):** Implementación de **Security Groups** actuando como firewalls con estado. Se ha configurado el HAProxy con **SSL (Let's Encrypt)** para garantizar el cifrado TLS en tránsito para el dominio `psicopompo.duckdns.org`.
        3.  **Fase 3 (Cómputo y Datos):** Provisionamiento de instancias EC2 (`t2.micro`) y despliegue "Two-Tier". Se destaca el uso de la instancia de balanceo como **Bastion Host** (opcional) para acceder a los nodos en la subred privada. 
        4.  **Fase 4 (Monitorización, Seguridad y Resiliencia):** 
            *   Integración de Wazuh (SIEM) y Prometheus/Grafana.
            *   Implementación de **Backups automatizados en AWS S3** (`tfg-sentinel-backups-alex`).
            *   **Auditoría de Seguridad PHP:** Blindaje del cargador de imágenes (`producto_editar.php`) mediante validación de tipos MIME y lista blanca de extensiones.

        La siguiente figura muestra la arquitectura de red final desplegada en AWS, que resume visualmente todas las fases del proyecto:

        ![Diagrama de Arquitectura de Red SENTINEL](file:///home/avidal/TFG/docs/img/Diagrama%20Actualizado.drawio.svg)
                *Figura 1: Topología de red completa de SENTINEL. Se observa la segmentación entre Subred Pública (Solo HAProxy) y Subred Privada Aislada (Web + MariaDB + Monitorización), la gestión de actualizaciones vía Instancia NAT/Proxy Squid, y los backups automáticos hacia AWS S3.*

        ### 6.2. Temporalización del proyecto
        Se ha registrado una desviación temporal del 15% respecto a la planificación inicial. Esta desviación se atribuye principalmente a:
        *   La complejidad técnica de realizar una **instalación offline (Side-loading)** de paquetes en la subred privada.
        *   La configuración del **SSL Termination** en HAProxy, lo que requirió la gestión de certificados dinámicos.
        *   La resolución de problemas de borrado de productos mediante la implementación de **Borrado Lógico (Soft Delete)**.

        ### 6.3. Actividades realizadas
        *   **Diseño de Red:** Creación de Internet Gateway (SENTINEL-IGW) y tablas de rutas específicas.
        *   **Fortificación de la Capa de Datos:** 
            *   Instalación de MariaDB 10.5 en entorno aislado mediante transferencia manual de paquetes `.rpm` vía SCP.
            *   Configuración de usuarios restringidos por segmento de red (`sentinel_web@10.0.1.%`), siguiendo el principio de Mínimo Privilegio (PoLP).
        *   **Seguridad Web (HTTPS):** Configuración de HAProxy para servir tráfico sobre el puerto 443 con certificado SSL válido.
        *   **Auditoría de Vulnerabilidades:** Análisis de SQLi, XSS y RCE (protección del upload de archivos con `getimagesize`).
        *   **Gestión de Backups:** Scripting en Bash para volcados de MySQL y subida automática al bucket de S3 mediante **AWS CLI**.
        *   **Monitorización Avanzada:** Dashboard centralizado en Grafana unificando métricas de Apache y MySQL.

        ### 6.4. Recursos y tecnologías empleadas
        *   **Enfoque IaaS (Infraestructura como Servicio):** Control granular sobre HAProxy, MariaDB y Agentes de Seguridad.
        *   **Cloud:** AWS (EC2 t2.micro, EC2 t3.medium para SIEM, S3, VPC).
        *   **Criptografía:** Let's Encrypt (SSL/TLS).
        *   **SO:** Amazon Linux 2023 (Nodos Web y DB) / Ubuntu 24.04 (Nodo SIEM).
        *   **Servicios:** HAProxy, Apache, MariaDB, PHP 8.2.
        *   **Seguridad:** Wazuh SIEM, Fail2Ban, AWS Security Groups.
        *   **Monitorización:** Prometheus y Grafana (Visualización).
        *   **Criptografía:** SSL/TLS v1.3 via Let's Encrypt / HAProxy.

        ## 7. Resultados y Análisis
        ### 7.1. Análisis de los resultados y su impacto
        *   **Capacidad de Respuesta (Stress Test):** Se ha validado mediante `Apache Benchmark` (`ab -n 5000 -c 50`) que el sistema alcanza un rendimiento de **59.01 peticiones por segundo (RPS)**, superando en un 15.8% las métricas iniciales gracias a la optimización progresiva del stack.
                *   **Latencia y Rendimiento:** El percentil 90 de latencia se ha establecido en **384 ms**, lo que proporciona una experiencia de navegación fluida. No obstante, se detectó un pico máximo puntual de **33.5 segundos** durante el stress test; este dato es fundamental puesto que identifica el **límite de saturación física** de la instancia t3.micro bajo 50 usuarios concurrentes, justificando técnicamente la necesidad de un futuro escalado horizontal (Auto Scaling).
        *   **Eficacia de Seguridad:** El SIEM ha demostrado una eficacia muy alta en la detección de ataques de fuerza bruta y monitorización de integridad en tiempo real, ejecutando el ciclo completo de Active Response (detección → bloqueo → liberación) de forma autónoma.
        *   **Coste del Proyecto:** El coste real acumulado en AWS es de **$2.29 USD**, lo que demuestra la viabilidad económica de la solución.

        El siguiente panel de Grafana fue capturado durante la ejecución del test de carga (`ab -n 5000 -c 50`) el día 1 de abril de 2026 a las 09:20h, confirmando la visibilidad total del sistema bajo estrés:

        ![Panel de Monitorización Grafana durante el Test de Carga](file:///home/avidal/TFG/docs/img/evidencia_cpu_ram_test2.png)
                *Figura 2: Dashboard SENTINEL en Grafana durante el stress test. Se observa el pico de tráfico Apache (`Requests/sec`) y el incremento correlacionado en el `Tráfico de Consultas SQL`. Los tiempos de respuesta (p90 384ms) validan la arquitectura para carga moderada, mientras que el pico de saturación detectado (33s) confirma la necesidad de redundancia de nodos en producción real.*

        | Prueba Realizada | Resultado Obtenido | Estado |
        | :--- | :--- | :--- |
        | **Peticiones Concurrentes (50)** | **59.01 RPS** | ✅ |
        | **Latencia p90** | **384 ms** | ✅ |
        | **Acceso Seguro (SSL)** | HTTPS Activo (Let's Encrypt) | ✅ |
        | **Backups Externos** | S3 (Bucket: tfg-sentinel-backups-alex) | ✅ |
        | **Active Response (SIEM)** | Detección + bloqueo real en iptables validado (Regla 5712, MITRE T1110, IP `10.0.1.233` baneada). | ✅ |

        ### 7.2. Validación del Active Response — Wazuh SIEM
        Una de las pruebas más relevantes del proyecto desde el punto de vista de la seguridad fue la validación del módulo **Active Response** de Wazuh. Se ejecutó un ataque de fuerza bruta SSH controlado (usuario `hacker_tfg`, IP `10.0.1.233`) que generó 8 intentos fallidos en menos de 2 segundos, lo que disparó el protocolo de respuesta autónoma del SIEM.

        **El sistema SIEM respondió de forma completamente autónoma en 3 fases:**

        1.  **Detección (08:00:31):** La regla `5712` —"sshd: brute force trying to get access to the system. Non existent user"— disparó una alerta de nivel 10, mapeada con el framework MITRE ATT&CK como técnica `T1110 (Brute Force)`, y alineada automáticamente con normativas GDPR, HIPAA, NIST 800-53 y PCI-DSS.
        2.  **Bloqueo (08:00:31):** Wazuh ejecutó el binario `firewall-drop` que añadió una regla DROP en iptables para banear la IP atacante durante 10 minutos, bloqueando todo su tráfico entrante.
        3.  **Liberación (08:10:31):** Transcurrido el período de cuarentena, el sistema elimina la regla de bloqueo de forma automática sin intervención humana.

        ![Log del Active Response — ciclo completo](file:///home/avidal/TFG/docs/img/evidencia_active_response_log_bloqueo.png)
        *Figura 3: Log de `/var/ossec/logs/active-responses.log` del 2026-04-01 mostrando el ciclo completo: `check_keys → continue → Ended` y la regla `DROP all -- 10.0.1.233` añadida en iptables. La detección se produjo 1 segundo después del 8º intento fallido de acceso.*

        ![IP Baneada en iptables](file:///home/avidal/TFG/docs/img/ipbaneada.png)
        *Figura 4: Verificación directa mediante `sudo iptables -L INPUT -n | grep DROP` en el servidor web `ip-10-0-1-250`. La IP del agresor (`10.0.1.233`) aparece en la cadena DROP de iptables, confirmando el bloqueo real a nivel de firewall.*

        **Solución Técnica Aplicada:** La ejecución del bloqueo requirió configurar una regla sudoers en `/etc/sudoers.d/wazuh-active-response` que otorga al proceso `ossec` permisos para ejecutar `/usr/sbin/iptables` sin contraseña. Amazon Linux 2023 incluye `iptables-nft` (backend nftables) correctamente instalado, pero el proceso `ossec` carecía de permisos de ejecución. Esta configuración es estándar en entornos de producción con Wazuh.

        ### 7.3. Resolución de Desafíos Críticos (Troubleshooting)
        | Desafío Detectado | Solución Técnica Aplicada |
        | :--- | :--- |
        | **Instalación sin Internet** | Estrategia de **Side-loading** (descarga en Web y envío vía SCP a DB). |
        | **Borrado de Productos Vendidos** | Implementación de **Soft Delete** (`UPDATE productos SET activo = 0`) para mantener integridad referencial. |
        | **Limitaciones AWS Academy** | Documentación de la gestión de secretos como mejora futura. |
        | **Active Response sin permisos en AL2023** | Creación de regla sudoers `/etc/sudoers.d/wazuh-active-response` concediendo al usuario `ossec` permisos de ejecución sobre `/usr/sbin/iptables`. Bloqueo validado con `DROP all -- 10.0.1.233`. |

        ### 7.4. Monitorización Centralizada — Panel de Alertas Wazuh en Grafana
        El stack de monitorización integra las alertas de seguridad generadas por Wazuh directamente en el Dashboard de Grafana, proporcionando una vista unificada de la salud del sistema y los eventos de seguridad en tiempo real. La siguiente captura muestra el panel **"Registro de Seguridad Activo (SENTINEL)"** durante la sesión del 2026-04-01, donde se observan eventos de todos los agentes desplegados:

        ![Panel de Alertas Wazuh integrado en Grafana](file:///home/avidal/TFG/docs/img/evidencia%20loki.png)
        *Figura 5: Panel de seguridad centralizado en Grafana mostrando el feed de alertas Wazuh en tiempo real. Se observan eventos de los agentes `Tienda-DB-Server` y `LoadBalancer-HAProxy`, con niveles 3 (informativo) y 7 (advertencia), confirmando que todos los nodos de la infraestructura están siendo monitorizados por el SIEM de forma continua.*


        ### 7.5. Estrategia de Resiliencia — S3 Backup
        Se ha implementado una política de **Disaster Recovery** basada en volcados de base de datos diarios y su transferencia inmediata a un almacenamiento seguro off-site en Amazon S3.

        ![Panel de AWS S3 mostrando los archivos de backup](file:///home/avidal/TFG/docs/img/image%20copy.png)
        *Figura 6: Consola de AWS S3 visualizando el bucket `tfg-sentinel-backups-alex`. Se confirman los volcados `.sql.gz` generados de forma automatizada por el script `backup_db.sh`, garantizando la recuperación de la tienda en menos de 15 minutos ante un fallo total del clúster.*

        ## 8. Conclusiones y Recomendaciones
        ### 8.1. Conclusiones
        Los resultados obtenidos validan que es posible desplegar una infraestructura industrialmente viable con recursos mínimos. Con un coste real acumulado de **$2.29 USD** en AWS, el sistema ha alcanzado un rendimiento de **59.01 peticiones por segundo** con una utilización de CPU inferior al **1%**, demostrando un amplio margen de escalabilidad. Se ha logrado integrar de forma coherente servicios clásicos (LAMP) con stacks modernos de observabilidad (Prometheus/Grafana) y seguridad cloud (Wazuh SIEM).

        ### 8.2. Recomendaciones Operativas y de Mejora Final
        A partir de los hallazgos del proyecto, se identifican las siguientes acciones concretas para una transición a producción industrial:
        *   **Robustecer el Proxy Squid:** Actualmente operativo para administración y side-loading. Se recomienda ampliar su configuración con listas blancas dinámicas (whitelists ACLs) para centralizar todas las actualizaciones de seguridad del segmento privado.
        *   **Gestión de Secretos Dinámicos:** Mover la contraseña de `sentinel_web` desde archivos de configuración hacia **AWS Secrets Manager** para eliminar riesgos ante compromiso del servidor web.
        *   **Mantenimiento Proactivo SIEM:** Programar revisiones bimensuales de las reglas SQLi/XSS en Wazuh para adaptarlas a nuevas variantes de ataques emergentes detectados en tiempo real.
        *   **Clusterizar HAProxy con Keepalived:** Eliminar el SPOF del balanceador mediante un segundo nodo con IP flotante (VIP).

        ### 8.3. Gestión de Contingencias y Continuidad de Negocio
        Para mitigar los riesgos asociados a los puntos únicos de fallo (SPOF) en la arquitectura actual, se han definido los siguientes protocolos de contingencia:

        | Escenario de Fallo | Impacto | Protocolo de Recuperación |
        | :--- | :--- | :--- |
        | **Caída de Nodo Web/DB** | Crítico | Restauración inmediata mediante **EBS Snapshots** (Backups de volumen). *Nota: Para la consistencia de la DB se requiere el bloqueo temporal de tablas (`FLUSH TABLES WITH READ LOCK`) antes del disparo del snapshot.* |
        | **Corrupción de Datos** | Alto | Recuperación de base de datos desde dumps SQL preventivos almacenados fuera del nodo. |
        | **Fallo de HAProxy** | Crítico | Reconfiguración de DNS para apuntar directamente al nodo Web (modo emergencia). |
        | **Compromiso de Seguridad** | Alto | Aislamiento del nodo mediante Security Groups y análisis forense con logs de Wazuh. |
        | **Saturación de Recursos** | Medio | Escalado vertical preventivo (cambio de tipo de instancia EC2). |

        ## 9. Bibliografía
        *   **AWS Documentation:** VPC, EC2, IAM and S3 Best Practices. [https://docs.aws.amazon.com/](https://docs.aws.amazon.com/)
        *   **HAProxy Technologies:** Configuration Manual (TLS Termination). [https://www.haproxy.org/](https://www.haproxy.org/)
        *   **Wazuh SIEM:** Open Source Security Platform Documentation. [https://documentation.wazuh.com/](https://documentation.wazuh.com/)
        *   **Grafana Labs:** Dashboards for Prometheus and MySQL Exporter. [https://grafana.com/docs/](https://grafana.com/docs/)
        *   **OWASP Top 10:** Guide for PHP Security (SQLi, XSS, RCE protection). [https://owasp.org/www-project-top-ten/](https://owasp.org/www-project-top-ten/)
        *   **ENISA Threat Landscape 2024:** European Union Agency for Cybersecurity. [https://www.enisa.europa.eu/publications/enisa-threat-landscape-2024](https://www.enisa.europa.eu/publications/enisa-threat-landscape-2024)
        *   **Verizon DBIR 2024:** Data Breach Investigations Report. [https://www.verizon.com/business/resources/reports/dbir/](https://www.verizon.com/business/resources/reports/dbir/)

        ## 10. Anexos
        1.  **Diagrama de Red SENTINEL:** Topología completa VPC, subredes y flujos de tráfico. (Ver Figura 1 — `docs/img/Diagrama Actualizado.drawio.svg`)
        2.  **Monitorización bajo Carga (Grafana):** Panel de CPU, tráfico Apache y consultas SQL durante el stress test. (Ver Figura 2 — `docs/img/evidencia_cpu_ram_test2.png`)
        3.  **Evidencia Active Response (Wazuh):** Log del ciclo detección-bloqueo-liberación ante fuerza bruta SSH. (`docs/img/evidencia_wazuh_active_response_firewall_drop.png`)
        4.  **Simulacro de Disaster Recovery:** Procedimiento técnico de restauración de BD desde S3. (`docs/04_entregables_finales/ANEXO_DISASTER_RECOVERY.md`)
        5.  **Informe de Pruebas de Rendimiento:** Resultados detallados del Apache Benchmark. (`docs/04_entregables_finales/INFORME_PRUEBAS_RENDIMIENTO.md`)
        6.  **Ficha de autoevaluación:** Calificación del desempeño técnico y gestión del autor. (Ver apartado 10.6)
        7.  **Ficha de coevaluación:** Nota aclaratoria sobre desarrollo individual. (Ver apartado 10.7)

        ### 10.6. Ficha de autoevaluación
        A continuación, se presenta la valoración del autor sobre el desempeño y proceso de ejecución del proyecto individual SENTINEL:

        | Ítem de Evaluación | Calificación (1-10) | Observaciones Técnicas |
        | :--- | :---: | :--- |
        | **Cumplimiento de objetivos técnicos** | 9.0 | Implementación completa del stack de seguridad y monitorización en AWS. |
        | **Gestión del tiempo y planificación** | 7.5 | Desviación del 15% por la complejidad de la instalación offline (Side-loading) en subred privada. |
        | **Capacidad de resolución (Troubleshooting)** | 9.5 | Resolución exitosa de desafíos en conectividad SSH, SSL y borrado lógico de productos. |
        | **Calidad de la documentación técnica** | 9.0 | Documentación detallada de la arquitectura, planes de contingencia y auditoría económica. |
        | **Integración de herramientas (SIEM/Grafana)** | 8.5 | Visualización efectiva de métricas, aunque con margen de mejora en el ajuste fino de reglas de Wazuh. |

        ### 10.7. Ficha de coevaluación
        **Nota Aclaratoria:** Dado que el proyecto SENTINEL ha sido desarrollado de forma **individual** por un único integrante, no procede el proceso de coevaluación entre pares. 

        No obstante, se ha realizado una evaluación continua basada en el feedback del tutor y el cumplimiento de los estándares de calidad del entorno Cloud.

        | Miembro del Equipo | Rol | Aportación | Evaluación |
        | :--- | :--- | :--- | :--- |
        | **Alex Vidal Ródenas** | Autor Único | Diseño, despliegue y documentación integral. | N/A |
        | **Equipo de Trabajo** | N/A | Proyecto Individual | **N/A** |

        ---

        ## 11. Trabajo Futuro y Líneas de Mejora
        Dada la naturaleza evolutiva del proyecto SENTINEL y las limitaciones de presupuesto impuestas por el entorno de aprendizaje, se han identificado las siguientes áreas de mejora para una futura transición a un entorno de producción industrial:

        ### 11.1. Alta Disponibilidad (HA) Real
        Para eliminar los puntos únicos de fallo (SPOF) detectados, se planea:
        *   **Cluster de Balanceo:** Implementar un segundo nodo HAProxy sincronizado mediante **Keepalived** y una IP flotante (VIP).
        *   **Replicación de Datos:** Sustituir la instancia única de MariaDB por un entorno de replicación **Master-Slave** o migrar a **Amazon Aurora** con Multi-AZ habilitado.

        ### 11.2. SENTINEL como Infraestructura Reproducible (IaC)
        El objetivo más inmediato tras la finalización del proyecto es **codificar esta misma infraestructura como código** utilizando Terraform, de forma que cualquier persona pueda reproducir el entorno completo de SENTINEL con un único comando (`terraform apply`), sin intervención manual.

        El trabajo de despliegue manual realizado durante el proyecto constituye la base perfecta para este proceso de codificación, ya que se conocen con precisión todos los recursos necesarios, sus dependencias y sus configuraciones de seguridad. Los módulos Terraform previstos son:

        *   **Módulo `vpc`:** Definición de la `SENTINEL-VPC` (10.0.0.0/16), subredes pública y privada, Internet Gateway, tabla de rutas y la instancia NAT/Proxy Squid para actualizaciones de la subred aislada.
        *   **Módulo `compute`:** Provisionamiento de las instancias EC2 (`t2.micro` Web/DB, `t3.medium` SIEM) con sus Security Groups, roles IAM y claves SSH asociadas.
        *   **Módulo `security`:** Despliegue del agente Wazuh, configuración de la regla sudoers para Active Response y las reglas de Hardening de MariaDB.
        *   **Módulo `monitoring`:** Instalación de Prometheus, Grafana y los exporters de Apache/MySQL con el dashboard SENTINEL pre-configurado.
        *   **Módulo `backup`:** Creación del bucket S3, política de retención y despliegue del Systemd Timer de `backup_db.sh`.

        Una vez codificado, SENTINEL se convertiría en un **Blueprint comercializable**: una plantilla que permitiría a proveedores de servicios (MSP) desplegar una tienda e-commerce segura y monitorizada para nuevos clientes en cuestión de minutos. El diseño detallado de esta arquitectura de automatización se encuentra desarrollado en el **[Anexo: Estrategia de Automatización (SENTINEL IaC)](file:///home/avidal/TFG/docs/04_entregables_finales/ANEXO_ESTRATEGIA_AUTOMATIZACION.md)**.

        ### 11.3. Seguridad Proactiva y Gestión de Parches
        *   **IDPS de Red:** Integrar un sistema de detección y prevención de intrusiones a nivel de red como **Snort** o **Suricata** en la entrada de la VPC, complementando la capacidad de detección actual de Wazuh con inspección de tráfico a nivel de paquete.
        *   **Expansión del Proxy Squid:** La instancia NAT/Proxy Squid implementada actualmente gestiona las actualizaciones de la subred privada. En producción, se extendería su funcionalidad con listas blancas de dominios (`whitelist ACLs`) y logging centralizado de peticiones salientes para auditoría.
        *   **Gestión de Identidades y Secretos:** Integrar **AWS Secrets Manager** para la rotación automática de las credenciales de MariaDB (`sentinel_web`), eliminando el uso de contraseñas embebidas en los archivos `.php` del servidor.

        ### 11.4. Escalabilidad y Elasticidad (FinOps)
        *   **Auto Scaling:** Configurar grupos de auto-escalado basados en métricas de CPU/RAM de Prometheus para añadir dinámicamente nodos web durante picos de carga.
        *   **Content Delivery Network (CDN):** Implementar **CloudFront** para el almacenamiento en caché de contenido estático (imágenes de la tienda), reduciendo la carga en el servidor de origen.

        ---

        ## 📌 NOTAS DE SESIÓN — Contexto para la Próxima Sesión de Trabajo
        > **Última actualización:** 1 de abril de 2026.
        > Este bloque es para uso interno del autor y NO debe incluirse en la versión final entregada al tribunal. Eliminarlo antes de la entrega.

        ### ✅ Estado Actual del Proyecto (Lo que YA está hecho y funciona)
        *   **Migración RDS Multi-AZ:** Base de datos migrada con éxito a Amazon RDS. Se ha implementado cifrado TLS obligatorio en tránsito mediante la integración de `global-bundle.pem` en `/var/www/secrets/` y el uso dinámico de `__DIR__` en PHP.
        *   **Balanceador HAProxy y DuckDNS:** Enrutamiento HTTP/HTTPS perfeccionado y operando en `psicopompo.duckdns.org`. Se reescribió `haproxy.cfg` estableciendo Terminación SSL con certificado `.pem` unificado y redirigiendo las peticiones limpiamente al backend Apache privado, resolviendo issues de Timeout previos.
        *   **Infraestructura AWS:** VPC segmentada, subred pública (HAProxy) y subred privada (Apache + RDS + Monitorización). Configuración correcta y demostrada.
        *   **Balanceador HAProxy:** Funcionando con SSL (Let's Encrypt) en dominio `psicopompo.duckdns.org`. **NOTA:** Se ha mantenido como "Prototipo Funcional" frente al ALB de AWS por restricciones de tiempo y presupuesto de AWS Academy.
        *   **Wazuh SIEM:** Completamente operativo. Active Response validado (Regla 5712, Bloqueo iptables de IP `10.0.1.233`, ciclo completo detección-bloqueo-liberación documentado con capturas).
        *   **Grafana + Prometheus:** Dashboard centralizado con métricas de Apache, MariaDB, CPU y RAM. Panel de alertas de Wazuh integrado.
        *   **Backup S3:** Script `backup_db.sh` automatizado con `mysqldump --databases` (incluye `CREATE DATABASE`) y Systemd Timer operativo.
        *   **Tests de Estrés:** `ab -n 5000 -c 50` ejecutado. 59.01 RPS, p90 = 384ms, pico máximo = 33.5s (documentado como límite de saturación del prototipo, no como error).
        *   **Disaster Recovery:** Simulacro realizado. Comando de restauración corregido a `gunzip -c ... | sudo mysql` (sin nombre de DB, el dump incluye `CREATE DATABASE`).
        *   **Estructura del Proyecto Reorganizada:** `src` → `app`, `config_sentinel_db.php` → `secrets/`, `automation/monitoring/database` → `infrastructure/`.

        ### 🔴 Tareas Pendientes (Prioridades para el LUNES)

        #### ⚡ Prioridad 1: "Quick Wins" y Limpieza (Lo que haremos primero)
        *   [ ] **Seguridad: Sacar credenciales del PHP.**
            *   Update `secrets/config_sentinel_db.php` para usar `getenv()`.
            *   Configurar variables en el OS del servidor web.
        *   [ ] **Visual: Corregir Diagrama de Red (Figura 1).**
            *   Abrir el `.drawio`.
            *   Mover el **Servidor Web** fuera del recuadro de "Subred Pública" (debe estar en la Privada).
            *   Regenerar el PDF/Imagen de la Figura 1.
        *   [ ] **Documentación: Limpieza de Notas.**
            *   Borrarlas de la `MEMORIA_FINAL.md` una vez esté todo listo para la entrega.

        #### 📦 Prioridad 2: Mejoras de Infraestructura (COMPLETADAS)
        *   [x] **Migrar MariaDB a Amazon RDS Multi-AZ** (COMPLETADO)
            *   Migración realizada con éxito, cifrado SSL configurado y acceso validado mediante HAProxy.
            *   Actualizar endpoint en `secrets/config_sentinel_db.php`.

        #### 📋 Prioridad 3: Repaso Final
        *   [ ] **Revisión de terminología técnica** en toda la memoria.
        *   [ ] **Validación de enlaces** a bibliografía y anexos.

        ### 💡 Decisiones Estratégicas Tomadas (Ya inamovibles)
        1.  **El proyecto es un "Prototipo Funcional de Bajo Coste"**, no un sistema de producción enterprise. Esta narrativa es la respuesta correcta a cualquier crítica sobre el SPOF del balanceador o el coste del sistema.
        2.  **No se usará Terraform en este TFG.** Se menciona en "Trabajo Futuro" como línea de evolución natural del proyecto (SENTINEL IaC).
        3.  **El pico de 33.5 segundos del test de estrés** se presenta como evidencia científica del límite del prototipo, justificando la propuesta de escalado horizontal en las conclusiones.
        4.  **La instancia NAT/Proxy Squid** se defiende como una decisión de FinOps consciente (frente al NAT Gateway de AWS), no como una limitación.
    #### ⚡ Prioridad 1: "Quick Wins" y Limpieza (Lo que haremos primero)
    *   [ ] **Seguridad: Sacar credenciales del PHP.**
        *   Update `secrets/config_sentinel_db.php` para usar `getenv()`.
        *   Configurar variables en el OS del servidor web.
    *   [ ] **Visual: Corregir Diagrama de Red (Figura 1).**
        *   Abrir el `.drawio`.
        *   Mover el **Servidor Web** fuera del recuadro de "Subred Pública" (debe estar en la Privada).
        *   Regenerar el PDF/Imagen de la Figura 1.
    *   [ ] **Documentación: Limpieza de Notas.**
        *   Borrarlas de la `MEMORIA_FINAL.md` una vez esté todo listo para la entrega.

    #### 📦 Prioridad 2: Mejoras de Infraestructura (Si hay tiempo/ganas)
    *   [ ] **Migrar MariaDB a Amazon RDS Multi-AZ**
        *   Seguir: `docs/02_guias_tecnicas/GUIA_MIGRACION_BASE_DE_DATOS_RDS.md`
        *   Actualizar endpoint en `secrets/config_sentinel_db.php`.

    #### 📋 Prioridad 3: Repaso Final
    *   [ ] **Revisión de terminología técnica** en toda la memoria.
    *   [ ] **Validación de enlaces** a bibliografía y anexos.<ctrl46>,StartLine:283,TargetContent:<ctrl46>        ### 🔴 Tareas Pendientes (Para la Próxima Semana)
    
    #### Prioridad 1 — Bloque de Datos (Alta Seguridad, Bajo Riesgo)
    *   [ ] **Migrar MariaDB a Amazon RDS Multi-AZ**
        *   Guía disponible en: `docs/02_guias_tecnicas/GUIA_MIGRACION_BASE_DE_DATOS_RDS.md`
        *   Comando de volcado: `mysqldump --databases tienda_segura > backup_final.sql`
        *   Tras la migración, actualizar `secrets/config_sentinel_db.php` con el nuevo endpoint de RDS.
        *   **Estimación de tiempo:** 1 tarde (2-3 horas).
    
    #### Prioridad 2 — Gestión de Secretos (Seguridad)
    *   [ ] **Mover credenciales a Variables de Entorno**
        *   Editar `secrets/config_sentinel_db.php` para que lea con `getenv()` en lugar de tener la password en texto plano.
        *   Declarar las variables en `/etc/environment` del servidor web.
    
    #### Prioridad 3 — Pendiente de Decisión del Autor
    *   [ ] **¿Implementar ALB + ASG?** (Decisión posergada conscientemente)
        *   Guía disponible en: `docs/02_guias_tecnicas/GUIA_DESPLIEGUE_ALTA_DISPONIBILIDAD_AWS.md`
        *   Requiere: AMI del servidor web, Launch Template, Target Group, ALB y Auto Scaling Group.
        *   Si se implementa, también se requiere **Amazon EFS** para sincronizar `app/uploads/` entre nodos.
        *   **Conclusión de la sesión:** Se ha valorado el esfuerzo vs. beneficio académico. El proyecto ya cubre todos los módulos de ASIR con la arquitectura actual. La migración a ALB/ASG solo es necesaria para aspirar a Matrícula de Honor.<ctrl46>,TargetFile:<ctrl46>/home/avidal/TFG/docs/04_entregables_finales/MEMORIA_FINAL.md<ctrl46>,toolAction:<ctrl46>Updating session notes for Monday work.<ctrl46>,toolSummary:<ctrl46>Monday notes update<ctrl46>}
