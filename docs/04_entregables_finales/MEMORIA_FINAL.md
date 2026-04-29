# MEMORIA FINAL DEL PROYECTO INTERMODULAR (SÍNTESIS)

## 1. Portada
*   **Nombre del proyecto:** SENTINEL - Infraestructura Web Segura y Monitorizada (Tienda Segura)
*   **Ciclo formativo:** Grado Superior en Administración de Sistemas Informáticos en Red (ASIR)
*   **Módulos implicados:** Implantación de Sistemas Operativos, Planificación y Administración de Redes, Gestión de Bases de Datos, Seguridad y Alta Disponibilidad, Administración de Sistemas Gestores de Base de Datos, Servicios de Red e Internet.
*   **Integrantes del equipo:** Alex Vidal Ródenas

## 2. Resumen
Este proyecto, denominado **SENTINEL**, da respuesta a la necesidad recurrente en las PYMES de disponer de entornos de comercio electrónico (*e-commerce*) seguros. Para ello, se ha desarrollado una solución de infraestructura en la nube (IaaS) fundamentada en la arquitectura de *Zero Trust* y la *Defensa en Profundidad*. 
Las tecnologías clave utilizadas incluyen el aprovisionamiento de una red segmentada en Amazon Web Services (AWS), aislando los nodos web y datos (Amazon RDS) tras un Application Load Balancer (ALB). Asimismo, se integra de forma completa el stack WPG (Wazuh SIEM, Prometheus y Grafana) para el análisis de vulnerabilidades, respuesta activa ante intrusiones mediante `iptables` y centralización de alertas vía Telegram. El resultado final es un sistema completamente funcional, validado mediante pruebas de estrés (59 RPS) y dotado de mecanismos de resiliencia planificada (*Backup & Restore* automatizado externalizado en S3 asegurando cuotas RTO operativas).

## 3. Introducción
### 3.1 Contexto y justificación
En el ecosistema empresarial actual (agencias digitales, PYMES), se detecta un patrón crítico: los comercios electrónicos suelen desplegarse sin una capa de seguridad transversal que detecte y prevenga ataques. Ante esta fragilidad, SENTINEL surge de la necesidad de proveer una infraestructura empaquetada que combine la disponibilidad de los servicios en la nube con un blindaje continuo, mitigando pérdidas por secuestro de datos operativos (Ransomware) y cortes de servicio (DDoS o fallos de base de datos).

### 3.2 Objetivos del proyecto
1.  **Implementar una infraestructura de red funcional:** Proveer los servicios necesarios (Balanceo L7, Resolución DNS dinámica mediante DuckDNS, Servidores Web y Base de Datos) operando bajo la delegación de cifrado (SSL Termination) en la frontera de la VPC mediante el Application Load Balancer.
2.  **Garantizar seguridad, disponibilidad y escalabilidad:** Proteger la red mediante segmentación estricta de subredes, aislar la monitorización, y mitigar el Punto Único de Fallo (SPOF) crítico de la capa de persistencia migrando íntegramente a Amazon RDS Multi-AZ, sentando con ello una base *stateless* preparada para auto-escalado web.
3.  **Validar técnicamente la solución:** Emplear simulacros de estrés (Benchmarking) y auditoría técnica para constatar la eficiencia y reactividad autómata del sistema de detección de intrusos.

### 3.3 Alcance y limitaciones
El proyecto **incluye** el diseño de la topología de red cloud (VPC), la instalación y fortificación formal ("Hardening") del stack LAMP —aplicando directrices de Mínimo Privilegio restrictivo en SO, inyección de variables transitorias PDO en vez de contraseñas impresas y mitigaciones de enumeración en Apache—, el despliegue de herramientas de monitorización basada en TSDB y la activación del sistema de prevención activa (Wazuh IPS).
Queda **fuera del alcance** el desarrollo integral del código fuente de la tienda virtual desde cero (el código PHP proporcionado se emplea como mera base transaccional para auditar la infraestructura) y el *Auto-Scaling* automático entre zonas geográficas por limitaciones estrictas del presupuesto de los laboratorios educativos de AWS Academy, mitigado de forma práctica centralizando en RDS.

---

## 4. Análisis y requisitos (Síntesis Fase 1)
### 4.1 Estudio del cliente y necesidades
A través de un ejercicio prospectivo asumiendo el rol de proveedor Managed Service Provider (MSP), se definieron los requerimientos para hospedar el portal de ventas:
*   **Requisitos funcionales:** La tienda debe estar accesible al público 24/7 sin interrupciones, garantizando su capacidad transaccional para clientes.
*   **Servicios necesarios:** Alojamiento web, resolución de nombres de dominio permanente (DDNS), enrutamiento cifrado y un motor de base de datos relacional altamente disponible.
*   **Requisitos de seguridad y disponibilidad:** Imposibilidad de que peticiones ilícitas directas alcancen la subred privada de computación. Identificación instantánea de incidentes críticos.

### 4.2 Requisitos técnicos
*   **Hardware / Servidores:** Entorno operado mediante recursos EC2. Se especifican instancias `t2.micro` y `t3.medium` (necesaria para el despliegue del SIEM y visualización pesada).
*   **Software / SO:** Distribuciones base Linux (Amazon Linux 2023 y Ubuntu 24.04). Stack de servicios formado por Apache httpd, PHP 8.2 y MariaDB.
*   **Red:** Segmentación IP (`10.0.0.0/16`) con provisión de direccionamiento público o privado en función de la función del nodo.

### 4.3 Organización del equipo
Asumiendo un enfoque unipersonal (*Solo Proyecto*), el autor ha transitado rotativamente los perfiles de Arquitecto Cloud, SysAdmin y Analista de Seguridad. Se empleó una metodología iterativa dividiendo el proyecto en *cinco fases* lógicas y crecientes, bloqueando la adición de nuevas topologías hasta validar modularmente las precedentes.

---

## 5. Diseño de la solución (Síntesis Fase 2)
### 5.1 Arquitectura de red
El diseño lógico en AWS plasma una jerarquía de confianza denominada **TIER-2**:
*   **Capa o Subred Pública (`TIER-1`, Ingress):** Acomoda los elementos expuestos libremente a internet. Acoge el Application Load Balancer y la pasarela NAT para el proxy de salida (Squid).
*   **Capa o Subred Privada (`TIER-2`, Aislada):** Rango de enrutamiento aislado (`10.0.1.X`, `10.0.0.X`) compuesto por el conjunto vital: Nodo Web (`10.0.1.250`), motor de Base de Datos (`10.0.0.242`), SIEM Wazuh (`10.0.1.170`) y el clúster de Observabilidad (`10.0.1.233`).

![Diagrama de Arquitectura de Red SENTINEL](docs/img/diagramaFInal.drawio.png)
*Figura 1: Topología de red completa de SENTINEL, mostrando la segmentación estricta entre la Subred Pública (Load Balancer proxy HTTP/S) y la Subred Privada aislada (Web, RDS, Monitorización y SIEM).*

### 5.2 Infraestructura
*   **Servidores Virtualizados:** Máquinas Linux segmentadas por contexto de ejecución (Frontend Apache, Backend Seguridad).
*   **Servicios Desplegados:** Con el avance de este diseño inicial, se migró conceptualmente hacia soluciones administradas como **Amazon RDS** para delegar parches automáticos y concentrarse sobre el core aplicativo.

### 5.3 Seguridad
*   **Políticas y Segmentación:** *Security Groups* configurados con denegación implícita. Las aperturas aplican a nodos específicos (ej. puerto 3306 admitido única y exclusivamente desde el origen `10.0.1.250`).
*   **Autenticación:** Limitación de conexiones EC2 mediante pareo de llaves criptográficas (.pem).

### 5.4 Backup y recuperación
Estrategia definida de volcados periódicos programados y remotos. Integración directa de volcados de datos compactados con envío síncrono al ecosistema S3 off-site. Esto conforma un plan de contingencia pasivo de tipo "Backup and Restore" fuera de la posible zona de infección por secuestro de la instancia (Bucket `tfg-sentinel-backups-alex`), capaz de avalar tiempos de recuperación (RTO - Recovery Time Objective) calculados operativamente en menos de 30 minutos frente a caídas masivas de la capa de datos.

### 5.5 Escalabilidad y alta disponibilidad
Concepción de despliegue horizontal: la disociación en capas aísla la carga de la BBDD de la del gestor HTML. Además, se asume Multi-AZ a los servicios críticos de datos.

### 5.6 Presupuesto inicial
Estructurado sobre los créditos de cortesía provistos (AWS Educate/Academy). Emulación que, en escenario de escalado productivo de PYME de entrada, mantiene sus cotas mensuales a precios muy contenidos ($20-$50/mes).

---

## 6. Implementación del sistema (Síntesis Fase 3)
### 6.1 Planificación
Según cronograma proyectivo, la implementación exigió configurar absolutamente la VPC (Routing y Entradas del IGW) previo al arranque en caliente del software interno de las subredes de confianza para asimilar el blindaje inicial y la comunicación con internet.

### 6.2 Despliegue técnico
1.  **Base de Red:** Mapeo de rutas dinámicas hacia el Gateway de Internet y control de las asociaciones de subredes.
2.  **Operatividad Vital:** Compilación y despliegue del componente Web e interconexión validada exitosamente mediante conector PDO hacia el backend MariaDB. 

### 6.3 Procedimientos
Automatizaciones primitivas (Guías). Elaboración y pruebas de scripts en Crontab (Bash) referenciados para solventar actualizaciones IP asociadas al Dynamic DNS:
```bash
ALB_DNS="Sentinel-ALB-89....us-east-1.elb.amazonaws.com"
IPS=$(nslookup $ALB_DNS | awk '/Address/ {print $2}')
```

### 6.4 Incidencias y cambios
*   **Ajuste frente al diseño offline:** La carencia de asignaciones EIP públicas sobre la red TIER-2 obligó al planteamiento de *side-loading* (traspaso de `.rpm` locales) y delegación final en proxies/NAT o servicios en red pública garantizada de la infraestructura global.

### 6.5 Coste real inicial
Seguimiento confirmando las conjeturas iniciales: Gasto de las emulaciones técnicas por debajo de la cota teórica impuesta (`2.29 USD` de saldo consumido tras varias iteraciones debido a prácticas diligentes y encendidos manuales programados).

---

## 7. Desarrollo avanzado (Síntesis Fase 4)
### 7.1 Alta disponibilidad y balanceo
Superación definitiva del formato de desarrollo empírico singular: Activación del **AWS ALB** con finalización de tráfico TLS 1.3 (SSL Termination vía Certificate Manager). Eliminación del modelo manual mediante la sustitución paralela hacia una base de datos distribuida relacional como servicio RDS Multi-AZ.

### 7.2 Monitorización
Despliegue transversal dockerizado del robusto **Stack WPG**:
*   **Prometheus** integrando los agentes exportadores `node_exporter` y conexiones abstractas YACE con las APIs oficiales de CloudWatch de Amazon.
*   **Grafana** gestionando y exponiendo la salud mediante dashboards parametrizados en `statuspsicopompo.duckdns.org`.
*   **Alertas Críticas:** Parametrización en *Alertmanager* con integraciones webhooks HTTP.

### 7.3 Seguridad (Hardening)
Transformación desde redes pasivas a un entorno reactivo avanzado implementando **Wazuh SIEM**. Más allá de los análisis tradicionales integradores, se definió un flujo programado denominado **Active Response**: En escenarios de escaneos dirigidos o ataques vectorizados transaccionales detectados (Fuerza bruta N=8), el componente administrador emite una llamada autónoma para insertar restricciones `DROP` a `iptables` en el servidor acosado.

![Evidencia bloqueo iptables originado por Wazuh](docs/img/ipbaneada.png)
*Figura 2: Verificación directa mediante `iptables -L` en el nodo aplicativo web. Se constata la regla `DROP` contra la IP atacante añadida de forma absolutamente automatizada por el SIEM (Wazuh Active Response) sin requerir de ninguna intervención manual.*

### 7.4 Automatización
Aceleradores metodológicos IaC (Infrastructure and Conf. as Code) aplicados a contenedores. Implementación de fichas y shells `envsubst` inyectando subrutinas para flexibilizar IP transitorias (`start.sh`).

### 7.5 Pruebas de rendimiento
Ejecución pragmática de control mediante utilidades `ab` (`Apache Benchmark -n 5000 -c 50`). Resultados comprobando la absorción estelar que generó **~59.01 RPS** contínuos y picos de percentiles de latencia 90 asimilables debajo del rango funcional de 400ms para uso comercial recurrente.

![Panel Grafana durante estrés](docs/img/grafana monitoring.png)
*Figura 3: Dashboard Ejecutivo SENTINEL en Grafana. Despliega en vivo la telemetría métrica asimilada durante el estrés de carga, certificando el funcionamiento transversal fluido entre los exporters pasivos y el servidor TSDB.*

### 7.6 Coste final
La analítica FinOps de final de fase certificó la reducción colosal de esfuerzos o licenciamientos Enterprise extra: el aprovechamiento riguroso de soluciones Open Source y optimización por capas mantuvieron la infraestructura resiliente a costes operativos fijos bajos.

---

## 8. Validación y puesta en marcha (Síntesis Fase 5)
### 8.1 Ejecución final
Total integración: todos los componentes intergestionados sin falsos ecos locales. Frontend publicando contenido extraído del RDS administrado, balanceadores redirigiendo conexiones, panel monitorizando las CPU/Latencias de cada instancia interviniente y back-ups automáticos emitiendo al S3.

### 8.2 Validación funcional
Comprobación transversal al ecosistema: Conexiones seguras HTTPS validadas perimetralmente con Let's Encrypt. La resolución DNS es transparente para la lógica usuaria. El acceso a los recursos aplicativos web procede de acuerdo con los modelos de compra esperados de una tienda clásica.

### 8.3 Validación técnica
*   **Alta Disponibilidad:** El uso del clúster de Amazon y su ALB asegura la pervivencia en escenarios hostiles. Se mitiga todo punto orgánico aislado. 

### 8.4 Validación de seguridad
Auditoría completada confirmando la ausencia de vectores lógicos explícitos frente a OWASP debido al Hardening implementado a nivel SO (privilegios, chown `www-data`) y a las políticas estrictas cortafuegos de los grupos Amazon. La ejecución IPS de Wazuh quedó certificada localmente frente al tribunal con baneos generados en tiempo real.

![Flujo de notificaciones Telegram](docs/img/evidencia_telegram_alerta_1.jpg)
*Figura 4: Notificación Push vía integración de Alertmanager con el bot de API de Telegram. Reporta de forma instantánea un incidente de "ServerOffline" a los dispositivos móviles de los administradores.*

### 8.5 Validación de recuperación
Prueba de Failback atómica. Los paquetes comprimidos `.sql.gz` importados asimilando RTOs de re-ejecución muy por debajo de los topes recomendados de 15 minutos de parada. Simulación exitosa.

### 8.6 Cumplimiento de requisitos
Contraste en relación a FASE 1: Se ha superado sobradamente cualquier requerimiento técnico primitivo. Aspectos como el SIEM Activo o la arquitectura *Serverless/Managed* del DB introducida superaron el listón de la resiliencia pura dictaminada por defecto. 

---

## 9. Resultados y análisis
El análisis final constata que **la infraestructura de red desplegada soluciona íntegramente el problema expuesto en el ciclo de planteamiento empresarial**.
*   **Grado de cumplimiento de objetivos:** Excepcional. La tienda no solo es segura a los ojos del observador externo, también cuenta con la trazabilidad forense integral exigida bajo parámetros RGPD / SOC2 gracias al ecosistema Observability implementado (Logs Wazuh/Loki).
*   **Calidad técnica del sistema:** Un nivel propio de un profesional intermedio de Cloud Computing (IaaS). Se ha abstraído la administración del hardware operando integralmente en AWS.
*   **Valor añadido:** En comparación a proyectos donde el alumno interviene con soluciones *monolíticas locales*, SENTINEL brilla logrando el hito de aislar los componentes e incorporar canales automatizados contemporáneos donde eventos remotos se subsanan activando mensajes PUSH directos mediante ecosistemas como **Telegram**.

---

## 10. Conclusiones y recomendaciones
### 10.1 Conclusiones
SENTINEL es el compendio de los saberes aglutinados en los programas del currículo de ASIR. Tras resolver todos y cada uno de los problemas planteados, la gran lección asimilada es que *"configurar es relativamente fácil; administrar, dotar de aislamiento lateral (Defensa en profundidad) y generar respuesta autómata efectiva de sistemas es el verdadero desafío del administrador de sistemas digital actual."*

### 10.2 Propuestas de mejora (Trabajo a futuro)
*   **Infraestructura como Código (IaC):** Aunque los contenedores se declaran estáticamente, el despliegue fundacional Cloud (VPC) exigió en su etapa un elevado nivel de ClickOps. Esta métrica de mejora dicta pasar unánimemente el sistema hacia lenguajes asíncronos descriptivos como **HashiCorp Terraform** (`.tf`).
*   **Secret Manager:** Rotación asimétrica obligatoria de credenciales del PHP usando billeteras virtuales administradas (`AWS SDK` en backend web) eludiendo *passwords* nativas.
*   **Auto-Scaling Cloud (EC2):** Ampliaciones funcionales asimilando grupos dinámicos que se expanden re-ingresando *Application Instances* vacías al ALB ante sobresaturaciones (Superación empírica teórica superior a las 100 RPS detectadas en los benchmarks iniciales).

---

## 11. Bibliografía
*   **AWS Well-Architected Framework Handbook.**
*   **Documentación de Wazuh Security:** Implementador Oficial OpenSource (docs.wazuh.com).
*   **HAProxy y Apache Guía de Referencias Oficiales:** Ajustes de tunning limitativo frente a exploits y directrices TLS.
*   Documentación formativa general propia desarrollada y acumulada a lo largo del módulo de Grado Superior de ASIR.

---

## 12. Anexos
1.  **Evaluaciones / Fichas de Control:**
    *   *Ficha de autoevaluación:* Autoevaluación técnica calificada de Excelente sustentada en la superación de métricas de despliegue, Troubleshooting asíncrono (Dificultades Side-loading / NAT) y la inserción voluntaria de mitigaciones avanzadas SIEM no requeridas pero indispensables.
    *   *Ficha de coevaluación:* N/A (Desarrollo unipersonal bajo supervisión analítica de tutoría docente).
2.  **Tableros (Dashboards):** Documental visual de las métricas obtenidas durante el Apache Benchmark *[Ver Figura 3]*.
3.  **Capturas (Evidencias técnicas):** Operaciones Telegram Alerting resolutivas *[Ver Figura 4]* y barreras IPS perimetrales activas *[Ver Figura 2]*.
4.  **Scripts Transversales:** Referencia a rutinas localizadas sobre Github (`Update_duckdns.sh`, `start.sh`, `backup_db.sh`).
5.  **Diagramas de Red:** Arquitectura TIER-2 final *[Ver Figura 1]*.
