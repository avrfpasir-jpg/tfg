# MEMORIA FINAL DEL PROYECTO INTERMODULAR (SÍNTESIS)

## 1. Portada
*   **Nombre del proyecto:** SENTINEL - Infraestructura Web Segura y Monitorizada (Tienda Segura)
*   **Ciclo formativo:** Grado Superior en Administración de Sistemas Informáticos en Red (ASIR)
*   **Módulos implicados:** Implantación de Sistemas Operativos, Planificación y Administración de Redes, Gestión de Bases de Datos, Seguridad y Alta Disponibilidad, Administración de Sistemas Gestores de Base de Datos, Servicios de Red e Internet.
*   **Integrantes del equipo:** Alex Vidal Ródenas
*   **Tutor del TFG:** Álvaro Pérez Díez
*   **Centro:** IES San Vicente
*   **Convocatoria:** Junio / Curso 2024/2026

## 2. Resumen
Este proyecto, denominado **SENTINEL**, da respuesta a la necesidad recurrente en las PYMES de disponer de entornos de comercio electrónico (*e-commerce*) seguros. Para ello, se ha desarrollado una solución de infraestructura en la nube (IaaS) fundamentada en la arquitectura de *Zero Trust* y la *Defensa en Profundidad*. 
Las tecnologías clave utilizadas incluyen el aprovisionamiento de una red segmentada en Amazon Web Services (AWS), aislando los nodos web y datos (Amazon RDS) tras un Application Load Balancer (ALB). Asimismo, se integra de forma completa el stack WPG (Wazuh SIEM, Prometheus y Grafana) para el análisis de vulnerabilidades, respuesta activa ante intrusiones mediante `iptables` y centralización de alertas vía Telegram. El resultado final es un sistema completamente funcional, validado mediante pruebas de estrés (59 RPS) y dotado de mecanismos de resiliencia planificada (*Backup & Restore* automatizado externalizado en S3 asegurando cuotas RTO operativas).

## 3. Introducción
### 3.1 Contexto y justificación
En el ecosistema empresarial actual (agencias digitales, PYMES), se detecta un patrón crítico: los comercios electrónicos suelen desplegarse sin una capa de seguridad transversal que detecte y prevenga ataques. Ante esta fragilidad, SENTINEL surge de la necesidad de proveer una infraestructura empaquetada que combine la disponibilidad de los servicios en la nube con un blindaje continuo, mitigando pérdidas por secuestro de datos operativos (Ransomware) y cortes de servicio (DDoS o fallos de base de datos).

### 3.2 Objetivos del proyecto
1.  **Implementar una infraestructura de red funcional:** Proveer los servicios necesarios (Balanceo L7, Resolución DNS dinámica mediante DuckDNS, Servidores Web y Base de Datos) operando bajo la delegación de cifrado (SSL Termination) en la frontera de la VPC mediante el Application Load Balancer.
2.  **Garantizar seguridad, disponibilidad y escalabilidad:** Proteger la red mediante segmentación estricta de subredes, aislar la monitorización, y mitigar el Punto Único de Fallo (SPOF) crítico de la capa de persistencia mediante la migración a **Amazon RDS (Single-AZ, Multi-AZ Ready)**, eliminando la gestión manual del motor de base de datos y sentando una base *stateless* preparada para auto-escalado web en un entorno productivo real.
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
*   **Software / SO:** Distribuciones base Linux (Amazon Linux 2023 y Ubuntu 24.04). Stack de servicios formado por Apache httpd, PHP 8.4 y MariaDB 10.5.
*   **Red:** Segmentación IP (`10.0.0.0/16`) con provisión de direccionamiento público o privado en función de la función del nodo.

### 4.3 Organización del equipo
Asumiendo un enfoque unipersonal (*Solo Proyecto*), el autor ha transitado rotativamente los perfiles de Arquitecto Cloud, SysAdmin y Analista de Seguridad. Se empleó una metodología iterativa dividiendo el proyecto en *cinco fases* lógicas y crecientes, bloqueando la adición de nuevas topologías hasta validar modularmente las precedentes.

---

## 5. Diseño de la solución (Síntesis Fase 2)
### 5.1 Arquitectura de red
El diseño lógico en AWS plasma una jerarquía de confianza denominada **TIER-2**:
*   **Capa o Subred Pública (`TIER-1`, Ingress):** Acomoda los elementos expuestos libremente a internet. Acoge el Application Load Balancer y la pasarela NAT para el proxy de salida (Squid).
*   **Capa o Subred Privada (`TIER-2`, Aislada):** Rango de enrutamiento aislado (`10.0.1.X`, `10.0.0.X`) compuesto por el conjunto vital: Nodo Web (`10.0.1.250`), motor de Base de Datos (`10.0.0.242`), SIEM Wazuh (`10.0.1.170`) y el clúster de Observabilidad (`10.0.1.233`).

![Diagrama de Arquitectura de Red SENTINEL](docs/img/diagramas/TIER2.drawio.png)
*Figura 1: Topología de red completa de SENTINEL (arquitectura TIER-2). El único punto de entrada a internet es el ALB. Los nodos Web, RDS, Wazuh y Monitoring residen en subred privada sin exposición directa.*

### 5.2 Infraestructura
*   **Servidores Virtualizados:** Máquinas Linux segmentadas por contexto de ejecución (Frontend Apache, Backend Seguridad).
*   **Servicios Desplegados:** Con el avance de este diseño inicial, se migró conceptualmente hacia soluciones administradas como **Amazon RDS** para delegar parches automáticos y concentrarse sobre el core aplicativo.

### 5.3 Seguridad
*   **Políticas y Segmentación:** *Security Groups* configurados con denegación implícita. Las aperturas aplican a nodos específicos (ej. puerto 3306 admitido única y exclusivamente desde el origen `10.0.1.250`).
*   **Autenticación:** Limitación de conexiones EC2 mediante pareo de llaves criptográficas (.pem).

![Security Groups AWS](docs/img/Grupos de seguridad.png)
*Figura 2: Configuración de Security Groups en la consola AWS. Cada grupo implementa denegación implícita con apertura selectiva por nodo y puerto, materializando la segmentación TIER-2.*

### 5.4 Backup y recuperación
Estrategia definida de volcados periódicos programados y remotos. Integración directa de volcados de datos compactados con envío síncrono al ecosistema S3 off-site. Esto conforma un plan de contingencia pasivo de tipo "Backup and Restore" fuera de la posible zona de infección por secuestro de la instancia (Bucket `tfg-sentinel-backups-alex`), capaz de avalar tiempos de recuperación (RTO - Recovery Time Objective) calculados operativamente en menos de 30 minutos frente a caídas masivas de la capa de datos.

![Pipeline de Backup Automatizado S3](docs/img/diagramas/PipelineBackupS3.drawio.svg)
*Figura 3: Pipeline de backup automatizado. Un cron diario (00:00 UTC) ejecuta mysqldump, comprime el volcado en .sql.gz y lo transfiere al bucket S3 tfg-sentinel-backups-alex. RTO validado en menos de 15 minutos.*

### 5.5 Escalabilidad y alta disponibilidad
Concepción de despliegue horizontal: la disociación en capas aísla la carga de la BBDD de la del gestor HTML. La capa de datos opera en Amazon RDS Single-AZ, con arquitectura **Multi-AZ Ready**: la activación del modo Multi-AZ (standby en segunda AZ con failover automático < 60s) queda pospuesta por restricciones de crédito de AWS Academy, siendo el paso inmediato en un entorno productivo real.

### 5.6 Presupuesto inicial
Estructurado sobre los créditos de cortesía provistos (AWS Educate/Academy). Emulación que, en escenario de escalado productivo de PYME de entrada, mantiene sus cotas mensuales a precios muy contenidos ($20-$50/mes).

---

## 6. Implementación del sistema (Síntesis Fase 3)
### 6.1 Planificación
Según cronograma proyectivo, la implementación exigió configurar absolutamente la VPC (Routing y Entradas del IGW) previo al arranque en caliente del software interno de las subredes de confianza para asimilar el blindaje inicial y la comunicación con internet.

### 6.2 Despliegue técnico
1.  **Base de Red:** Mapeo de rutas dinámicas hacia el Gateway de Internet y control de las asociaciones de subredes.
2.  **Operatividad Vital:** Compilación y despliegue del componente Web e interconexión validada exitosamente mediante conector PDO hacia el backend MariaDB. 

![Instancias EC2 en consola AWS](docs/img/capturasEC2.png)
*Figura 4: Panel de instancias EC2 en la consola AWS. Se muestran los nodos desplegados (Web-Node-1, Wazuh-Manager, Sentinel-Monitoring, Squid Proxy) con sus IPs privadas y estado operativo.*

### 6.3 Procedimientos
Automatizaciones primitivas (Guías). Elaboración y pruebas de scripts en Crontab (Bash) referenciados para solventar actualizaciones IP asociadas al Dynamic DNS:
```bash
ALB_DNS="Sentinel-ALB-89....us-east-1.elb.amazonaws.com"
IPS=$(nslookup $ALB_DNS | awk '/Address/ {print $2}')
```

### 6.4 Incidencias y cambios
*   **Ajuste frente al diseño offline:** La carencia de asignaciones EIP públicas sobre la red TIER-2 obligó al planteamiento de *side-loading* (traspaso de `.rpm` locales) y delegación final en proxies/NAT o servicios en red pública garantizada de la infraestructura global.

### 6.5 Coste real inicial
El coste acumulado en AWS Academy durante las fases tempranas (instancias `t2.micro`, precios de crédito educativo) se situó en `$2.29 USD`. Este dato refleja únicamente el entorno de laboratorio. En un escenario de producción real con el stack correctamente dimensionado, el análisis FinOps detallado se expone en la sección 7.6.

---

## 7. Desarrollo avanzado (Síntesis Fase 4)
### 7.1 Alta disponibilidad y balanceo
Superación definitiva del formato de desarrollo empírico singular: Activación del **AWS ALB** con SSL Termination mediante certificado Let's Encrypt importado en **ACM (Amazon Certificate Manager)** y vinculado al Listener HTTPS (443), forzando redirección desde HTTP (80). Eliminación del modelo manual de base de datos mediante migración a **Amazon RDS Single-AZ (Multi-AZ Ready)**, delegando parches, backups automáticos y monitorización del motor a AWS.

![Application Load Balancer en consola AWS](docs/img/ALB.png)
*Figura 5: Configuración del Application Load Balancer en AWS. Listener HTTPS (443) activo con redirección desde HTTP (80). El ALB actúa como único punto de entrada público a la infraestructura.*

![Certificado Let's Encrypt válido](docs/img/cert valido.png)
*Figura 6: Certificado TLS emitido por Let's Encrypt (CA: E7), importado en ACM y vinculado al Listener HTTPS del ALB. Válido hasta junio 2026. Protocolo mínimo TLS 1.2.*

### 7.2 Monitorización
Despliegue transversal dockerizado del robusto **Stack WPG**:
*   **Prometheus** integrando los agentes exportadores `node_exporter` y conexiones abstractas YACE con las APIs oficiales de CloudWatch de Amazon.
*   **Grafana** gestionando y exponiendo la salud mediante dashboards parametrizados en `statuspsicopompo.duckdns.org`.
*   **Alertas Críticas:** Parametrización en *Alertmanager* con integraciones webhooks HTTP.

![Dashboard Grafana — statuspsicopompo.duckdns.org](docs/img/capturastatuspsicopompo.png)
*Figura 7: Dashboard de monitorización SENTINEL accesible en statuspsicopompo.duckdns.org. Muestra métricas de CPU, RAM y tráfico Apache en tiempo real de los nodos de producción.*

![Logs centralizados en Loki](docs/img/evidencia loki.png)
*Figura 8: Visor de logs centralizado en Loki (integrado en Grafana). Promtail recopila y envía los logs de todos los servicios al TSDB de Loki para correlación y auditoría.*

![Pipeline de alertas Alertmanager → Telegram](docs/img/evidencia_flujo_telegram_1.jpg)
*Figura 9: Pipeline de alertas activo. Alertmanager detecta un evento crítico, lo procesa mediante webhook y lo entrega al bot SENTINEL-BOT vía Telegram API en menos de 30 segundos.*

### 7.3 Seguridad (Hardening)
Transformación desde redes pasivas a un entorno reactivo avanzado implementando **Wazuh SIEM**. Más allá de los análisis tradicionales integradores, se definió un flujo programado denominado **Active Response**: En escenarios de escaneos dirigidos o ataques vectorizados transaccionales detectados (Fuerza bruta N=8), el componente administrador emite una llamada autónoma para insertar restricciones `DROP` a `iptables` en el servidor acosado.

![Flujo de Active Response IPS](docs/img/diagramas/FlujoIPS.drawio.svg)
*Figura 10: Flujo completo del sistema IPS automatizado. Ante un ataque de fuerza bruta SSH (8 intentos), Wazuh detecta la Regla 5712 (MITRE T1110), correlaciona el evento, activa el módulo firewall-drop e inserta la regla iptables DROP en menos de 1 segundo.*

![Evidencia bloqueo iptables originado por Wazuh](docs/img/ipbaneada.png)
*Figura 11: Verificación directa mediante `iptables -L` en el nodo aplicativo web. Se constata la regla `DROP` contra la IP atacante añadida de forma completamente automatizada por Wazuh Active Response sin intervención manual.*

![Evidencia Wazuh Active Response firewall-drop](docs/img/evidencia_wazuh_active_response_firewall_drop.png)
*Figura 12: Panel de Wazuh mostrando el evento de Active Response ejecutado. El módulo firewall-drop actúa sobre el nodo afectado tras la correlación de la alerta de fuerza bruta.*

![Log de Active Response — bloqueo registrado](docs/img/evidencia_active_response_log_bloqueo.png)
*Figura 13: Extracto de `/var/ossec/logs/active-responses.log` en el nodo web. Registro del bloqueo automático con timestamp, IP atacante y acción aplicada.*

### 7.4 Automatización
Aceleradores metodológicos IaC (Infrastructure and Conf. as Code) aplicados a contenedores. Implementación de fichas y shells `envsubst` inyectando subrutinas para flexibilizar IP transitorias (`start.sh`).

### 7.5 Pruebas de rendimiento
Ejecución pragmática de control mediante utilidades `ab` (`Apache Benchmark -n 5000 -c 50`). Resultados comprobando la absorción estelar que generó **~59.01 RPS** contínuos y picos de percentiles de latencia 90 asimilables debajo del rango funcional de 400ms para uso comercial recurrente.

![Panel Grafana durante estrés](docs/img/grafana monitoring.png)
*Figura 14: Dashboard Ejecutivo SENTINEL en Grafana durante el test de estrés Apache Benchmark (-n 5000 -c 50). Telemetría en tiempo real: 59.01 RPS sostenidos, latencia p90 de 384 ms, CPU del nodo web por debajo del 1%.*

![CPU y RAM durante test de carga](docs/img/evidencia_cpu_ram_test.png)
*Figura 15: Métricas de CPU y RAM del nodo Web-Node-1 exportadas por node_exporter durante el test de rendimiento. Confirman el margen de escalabilidad disponible en las instancias t2.micro actuales.*

### 7.6 Coste final y análisis de mercado
La analítica FinOps del proyecto arroja el siguiente desglose para un entorno de **producción real** en AWS (región `us-east-1`):

| Concepto | Single-AZ (laboratorio) | Multi-AZ (producción) |
| :--- | :--- | :--- |
| Cómputo EC2 (Web + Wazuh + Monitoring + Squid) | $75.00/mes | $75.00/mes |
| Base de Datos RDS MariaDB | $12.50/mes | $25.00/mes |
| Application Load Balancer (ALB) | $18.20/mes | $18.20/mes |
| Almacenamiento EBS + S3 Backups | $7.40/mes | $7.40/mes |
| Transferencia de datos | ~$2.00/mes | ~$2.00/mes |
| **TOTAL** | **~$115/mes** | **~$127/mes** |

Con **Reserved Instances a 1 año**, el coste EC2 y RDS baja un 40-60%, dejando la infraestructura completa en **~$70/mes**.

**Comparativa de mercado:** Una solución equivalente contratada como servicio gestionado (SIEM-as-a-Service + hosting seguro) costaría entre **$500 y $2.000/mes** para una PyME. SENTINEL entrega la misma capacidad (SIEM Wazuh, IPS activo, observabilidad Prometheus/Grafana, backups S3, HTTPS) a **$115-130/mes** usando exclusivamente herramientas Open Source, sin licenciamiento Enterprise.

---

## 8. Validación y puesta en marcha (Síntesis Fase 5)
### 8.1 Ejecución final
Total integración: todos los componentes intergestionados sin falsos ecos locales. Frontend publicando contenido extraído del RDS administrado, balanceadores redirigiendo conexiones, panel monitorizando las CPU/Latencias de cada instancia interviniente y back-ups automáticos emitiendo al S3.

### 8.2 Validación funcional
Comprobación transversal al ecosistema: Conexiones seguras HTTPS validadas perimetralmente con Let's Encrypt. La resolución DNS es transparente para la lógica usuaria. El acceso a los recursos aplicativos web procede de acuerdo con los modelos de compra esperados de una tienda clásica.

![Tienda Psicopompo con HTTPS activo](docs/img/cert valido.png)
*Figura 16: Navegación a psicopompo.duckdns.org con candado HTTPS activo. El visor de certificado confirma la CA Let's Encrypt (E7), protocolo TLS 1.3 y validez hasta junio 2026.*

### 8.3 Validación técnica
*   **Rendimiento bajo carga:** Test de estrés con Apache Benchmark (`ab -n 5000 -c 50`) sobre la infraestructura final (ALB + RDS). Resultado: **59.01 RPS** sostenidos con latencia p90 de **384 ms**, dentro del umbral funcional de 400 ms para e-commerce. La CPU del nodo web se mantuvo por debajo del **1%**, evidenciando un margen de escalabilidad amplio. Nota: las instancias `t2.micro` imponen un techo práctico; en un entorno productivo con instancias `t3.medium`/`t3.large` el rendimiento escalaría linealmente.
*   **Alta Disponibilidad:** El ALB distribuye el tráfico entre nodos y elimina el SPOF de la capa de entrada. La migración a RDS desacopla el estado de los nodos de cómputo, habilitando reemplazos de instancia sin pérdida de datos.

### 8.4 Validación de seguridad
Se han aplicado mitigaciones frente a las principales categorías **OWASP Top 10** relevantes al stack:
*   **SQLi:** Uso exclusivo de PDO con *prepared statements* y tipado estricto de parámetros en toda la capa PHP.
*   **XSS:** Cabeceras `X-XSS-Protection` y `Content-Security-Policy` configuradas en Apache; salida escapada con `htmlspecialchars()`.
*   **Hardening SO:** Política de mínimo privilegio (`chown www-data`, `chmod 644/755`), ocultación de firma del servidor (`ServerTokens Prod`, `ServerSignature Off`).
*   **IPS validado en tiempo real:** Se ejecutó un ataque de fuerza bruta SSH controlado (8 intentos fallidos) confirmando que Wazuh Active Response (Regla 5712, MITRE T1110) insertó automáticamente la regla `DROP` en `iptables` del nodo afectado en menos de 1 segundo, sin intervención manual.

![Notificación de alerta en Telegram](docs/img/evidencia_telegram_alerta_2.jpg)
*Figura 17: Notificación Push recibida en el bot SENTINEL-BOT vía Telegram API. El mensaje formateado reporta el incidente crítico con severidad, nodo afectado y timestamp en menos de 30 segundos desde el disparo de Alertmanager.*

![Secuencia del flujo Telegram completo](docs/img/evidencia_flujo_telegram_2.jpg)
*Figura 18: Secuencia completa del pipeline de alertas: Alertmanager procesa la regla, el webhook envía el payload al bot de Telegram y el mensaje aparece en el canal de administración.*

### 8.5 Validación de recuperación
Prueba de Failback atómica. Los paquetes comprimidos `.sql.gz` importados asimilando RTOs de re-ejecución muy por debajo de los topes recomendados de 15 minutos de parada. Simulación exitosa.

![Bucket S3 con backups almacenados](docs/img/evidencia_aws_s3_bucket.png)
*Figura 19: Bucket S3 tfg-sentinel-backups-alex con los volcados .sql.gz almacenados. Cada archivo corresponde a una ejecución del cron diario de mysqldump.*

![Simulación de restauración DR en terminal](docs/img/evidencia_dr_auditoria_terminal.png)
*Figura 20: Terminal durante la prueba de recuperación ante desastre. Descarga del backup desde S3, descompresión y restauración en RDS validando el RTO operativo en menos de 15 minutos.*

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
*   **Secret Manager:** Rotación asimétrica obligatoria de credenciales del PHP usando billeteras virtuales administradas (`AWS Secrets Manager`) eludiendo passwords en texto plano.
*   **Auto-Scaling Cloud (EC2):** Grupos de Auto Scaling con políticas de escalado basadas en métricas de ALB (Target Tracking sobre RPS), permitiendo superar el techo práctico de las instancias `t2.micro` actuales.
*   **Multi-AZ RDS:** Activación del modo standby en una segunda Availability Zone con failover automático < 60s, eliminando el único SPOF restante en la capa de datos.

### 10.3 Evolución a Infraestructura como Código (IaC) — Terraform

El despliegue actual de SENTINEL se realizó mediante **ClickOps** (consola de AWS) y scripts Bash, metodología válida en un entorno de laboratorio educativo (AWS Academy) donde las restricciones de permisos y la naturaleza iterativa del aprendizaje lo justifican. La evolución natural hacia un entorno productivo real exige adoptar **HashiCorp Terraform** para declarar toda la infraestructura como código versionable, reproducible y auditable.

**Beneficios concretos para SENTINEL:**

| Situación actual (ClickOps) | Con Terraform (IaC) |
| :--- | :--- |
| Recrear la VPC: ~2 horas manual | `terraform apply`: ~4 minutos |
| Sin historial de cambios de infraestructura | Cada cambio en git con autor y fecha |
| Entorno irrepetible si se destruye | `terraform destroy` + `terraform apply` = entorno idéntico |
| Documentación separada del código | La infraestructura ES la documentación |

**Estructura del repositorio Terraform propuesta:**

```
sentinel-iac/
├── main.tf          # Recursos principales
├── variables.tf     # Parametrización
├── outputs.tf       # IPs, DNS, ARNs exportados
├── modules/
│   ├── networking/  # VPC, subnets, IGW, SG
│   ├── compute/     # EC2, ALB, Target Groups
│   └── data/        # RDS, S3
```

**Ejemplo de implementación — Red y Cómputo:**

```hcl
# modules/networking/main.tf
resource "aws_vpc" "sentinel_vpc" {
  cidr_block           = "10.0.0.0/16"
  enable_dns_hostnames = true
  tags = { Name = "SENTINEL-VPC" }
}

resource "aws_security_group" "alb_sg" {
  name   = "sentinel-alb-sg"
  vpc_id = aws_vpc.sentinel_vpc.id

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

# modules/compute/main.tf
resource "aws_instance" "web_node" {
  ami           = "ami-0c55b159cbfafe1f0" # Amazon Linux 2023
  instance_type = var.web_instance_type   # t3.micro (prod) / t2.micro (lab)
  subnet_id     = var.private_subnet_id
  key_name      = var.key_pair_name

  user_data = templatefile("${path.module}/scripts/provision_web.sh", {
    db_endpoint = var.rds_endpoint
    db_password = var.db_password
  })

  tags = { Name = "Web-Node-1", Role = "Application" }
}

# modules/data/main.tf
resource "aws_db_instance" "sentinel_rds" {
  identifier        = "sentinel-db"
  engine            = "mariadb"
  engine_version    = "10.5"
  instance_class    = "db.t3.micro"
  allocated_storage = 20
  username          = "sentinel_admin"
  password          = var.db_password

  # Activar en producción real:
  multi_az               = var.enable_multi_az  # false en lab, true en prod
  deletion_protection    = true
  backup_retention_period = 7

  tags = { Name = "Sentinel-DB" }
}
```

La parametrización mediante `variables.tf` permite usar el mismo código para levantar entornos de desarrollo (`t2.micro`, `multi_az = false`) y producción (`t3.medium`, `multi_az = true`) cambiando únicamente un fichero de variables, eliminando la posibilidad de deriva entre entornos.

---

## 11. Bibliografía
*   **AWS Documentation:** Guías de buenas prácticas para VPC, EC2, ALB, RDS, IAM y S3. https://docs.aws.amazon.com/
*   **Wazuh SIEM:** Documentación oficial de la plataforma de seguridad Open Source (XDR/SIEM). https://documentation.wazuh.com/
*   **Apache HTTP Server:** Guía de configuración y directrices de seguridad (ServerTokens, Headers). https://httpd.apache.org/docs/
*   **Grafana Labs:** Documentación para la integración de Prometheus y exporters. https://grafana.com/docs/
*   **OWASP Top 10:** Guía de referencia para la mitigación de vulnerabilidades en PHP (SQLi, XSS, RCE). https://owasp.org/www-project-top-ten/
*   **ENISA Threat Landscape 2024:** Informe de la Agencia de la Unión Europea para la Ciberseguridad sobre el panorama de amenazas. https://www.enisa.europa.eu/publications/enisa-threat-landscape-2024
*   **Let's Encrypt:** Documentación de la CA y proceso de emisión/renovación de certificados TLS. https://letsencrypt.org/docs/

---

## 12. Anexos
1.  **Evaluaciones / Fichas de Control:**
    *   *Ficha de autoevaluación:* Autoevaluación técnica calificada de Excelente sustentada en la superación de métricas de despliegue, Troubleshooting asíncrono (Dificultades Side-loading / NAT) y la inserción voluntaria de mitigaciones avanzadas SIEM no requeridas pero indispensables.
    *   *Ficha de coevaluación:* N/A (Desarrollo unipersonal bajo supervisión analítica de tutoría docente).
2.  **Tableros (Dashboards):** Documental visual de las métricas obtenidas durante el Apache Benchmark *[Ver Figura 3]*.
3.  **Capturas (Evidencias técnicas):** Operaciones Telegram Alerting resolutivas *[Ver Figura 4]* y barreras IPS perimetrales activas *[Ver Figura 2]*.
4.  **Scripts Transversales:** Referencia a rutinas localizadas sobre Github (`Update_duckdns.sh`, `start.sh`, `backup_db.sh`).
5.  **Diagramas de Red:** Arquitectura TIER-2 final *[Ver Figura 1]*.
