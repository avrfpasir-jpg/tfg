# Guion de Defensa — Proyecto SENTINEL
**Autor:** Álex Vidal Ródenas | **Tutor:** Álvaro Pérez Díez  
**Tiempo total estimado:** 15 min exposición + Q&A  
**Diapositivas:** 10-12 máximo

---

## BLOQUE 1 — APERTURA (1 min)
> *Tono: directo, seguro. No leer.*

"Buenas tardes. Mi proyecto se llama SENTINEL y su objetivo es responder a una pregunta concreta:
**¿Puede una PyME permitirse una infraestructura cloud de nivel empresarial, con seguridad activa y monitorización en tiempo real, sin pagar decenas de miles de euros?**

La respuesta es sí. Y este proyecto lo demuestra con infraestructura real, desplegada en AWS, que ha estado operativa durante meses."

**Diapositiva:** Portada con nombre, logo SENTINEL y frase gancho.

---

## BLOQUE 2 — EL PROBLEMA (1 min)
> *Tono: contextualizar, no teorizar.*

"Según ENISA 2024, el 46% de las brechas de datos afectan a PyMEs. La razón no es la falta de tecnología — es que nadie les monta una infraestructura que la integre.

Una tienda online típica tiene: un servidor, una base de datos, y si tiene suerte, un certificado SSL. Sin monitorización. Sin detección de intrusiones. Sin plan de recuperación.

SENTINEL ataca exactamente ese problema."

**Diapositiva:** Dos columnas — "Tienda típica" vs "SENTINEL". Simple, visual.

---

## BLOQUE 3 — ARQUITECTURA (2-3 min)
> *Tono: técnico pero explicado. Señalar el diagrama.*

"La arquitectura se divide en dos capas estrictas dentro de una VPC en AWS."

**Tier Público — lo que ve internet:**
- El único punto de entrada es el **ALB** (Application Load Balancer). Nada más tiene IP pública enrutable.
- El ALB gestiona la **terminación SSL** con un certificado Let's Encrypt importado en ACM, forzando HTTPS en todo el tráfico.
- El **Squid Proxy** actúa como pasarela de salida controlada para los nodos privados.

**Tier Privado — lo que nunca ve internet:**
- **Web-Node-1** (`10.0.1.250`): Apache + PHP 8.4. Solo recibe tráfico del ALB.
- **Sentinel-DB** (`10.0.0.242`): Amazon RDS MariaDB. El puerto 3306 solo acepta conexiones desde `10.0.1.250`.
- **Wazuh-Manager** (`10.0.1.170`): SIEM. Recibe eventos de todos los agentes.
- **Sentinel-Monitoring** (`10.0.1.233`): Stack WPG en Docker — Prometheus, Grafana, Loki, Alertmanager.

"La decisión de usar **RDS en lugar de MariaDB en EC2** fue deliberada: elimina el SPOF de la capa de datos, delega parches y backups a AWS, y deja la puerta abierta a activar Multi-AZ en producción real con un click."

**Diapositiva:** Diagrama de arquitectura TIER-2. Señalar flujos de tráfico.

---

## BLOQUE 4 — DEMO EN VIVO (2 min)
> *Si la infra está levantada. Si no, capturas preparadas.*

**Secuencia:**
1. Abrir `https://psicopompo.duckdns.org` → mostrar candado HTTPS, navegar productos
2. Abrir el visor de certificados → señalar Let's Encrypt, validez hasta junio 2026
3. Abrir `https://statuspsicopompo.duckdns.org` → Grafana con métricas en vivo

"Lo que ven aquí es CPU, RAM y tráfico Apache en tiempo real de los nodos de producción. No es un mockup."

**Diapositiva:** Captura del dashboard Grafana con métricas reales.

---

## BLOQUE 5 — SEGURIDAD ACTIVA (3 min)
> *Este es el punto fuerte. Tomarlo con calma.*

"La mayoría de proyectos monitoriza. SENTINEL responde."

**El flujo de Active Response:**

"Cuando un atacante intenta fuerza bruta SSH — detectado a los 8 intentos fallidos — ocurre esto sin intervención humana:

1. **Detección:** El agente Wazuh en el nodo dispara la Regla 5712, mapeada al framework MITRE ATT&CK técnica T1110 — Credential Access, Brute Force.
2. **Análisis:** El Manager correlaciona la alerta y activa el módulo `firewall-drop`.
3. **Ejecución:** Se inserta una regla `DROP` en `iptables` del nodo afectado en menos de 1 segundo. La IP queda bloqueada automáticamente."

"Esto es lo que en el sector se llama IPS — Sistema de Prevención de Intrusiones. Wazuh como servicio gestionado costaría unos 500€/mes. Aquí está desplegado en Open Source."

**Mostrar:** Log de `/var/ossec/logs/active-responses.log` o captura de la evidencia.

**Alerting:**
"Cualquier incidente crítico llega en menos de 30 segundos al móvil del administrador vía Telegram, a través del pipeline Alertmanager → Bot API."

**Mostrar:** Captura del bot SENTINEL-BOT con alertas formateadas.

**Diapositiva:** Diagrama del flujo Detección → Análisis → Bloqueo → Notificación.

---

## BLOQUE 6 — OBSERVABILIDAD Y RESILIENCIA (1 min)

"El stack de monitorización corre en Docker con `envsubst` para inyección dinámica de configuración — lo que permite reutilizarlo en cualquier entorno cambiando solo el `.env`."

**Backup:**
"Los volcados de base de datos se comprimen en `.sql.gz` y se envían automáticamente a S3 (`tfg-sentinel-backups-alex`). El RTO estimado en una restauración completa es inferior a 15 minutos."

**Diapositiva:** Esquema del pipeline de backup S3 + tabla de resultados de validación.

---

## BLOQUE 7 — RESULTADOS Y COSTES (1 min)

**Rendimiento validado:**

| Prueba | Resultado |
|---|---|
| Carga crítica (50 concurrentes) | 59.01 RPS |
| Latencia p90 | 384 ms |
| CPU Web Server bajo carga | < 1% |
| Cifrado SSL | Let's Encrypt activo |
| Backups | Automatizados en S3 |
| Active Response | Bloqueo en < 1s |

**Coste en producción real:**
"~$115/mes con el stack completo. Con Reserved Instances a 1 año, ~$70/mes. Comparado con contratar un MSSP con SIEM para PyME — entre $500 y $2.000/mes."

**Diapositiva:** Tabla de resultados + tabla comparativa de coste de mercado.

---

## BLOQUE 8 — APRENDIZAJE Y EVOLUCIÓN (1 min)

"Lo que más valor me ha dado de este proyecto no es haber montado la infraestructura. Es haber tenido que desmontarla, entender por qué fallaba, y volverla a montar mejor."

**Tres problemas reales resueltos:**
- Side-loading de paquetes en nodos sin salida a internet (SCP directo)
- Alertmanager no arrancaba por falta de interpolación de variables → sistema de plantillas con `envsubst`
- Security Group bloqueaba los puertos 1514/1515 de Wazuh → el SIEM no recibía eventos

"La evolución natural es **Terraform** — declarar toda esta infraestructura como código para pasar de 2 horas de ClickOps a un `terraform apply` de 4 minutos. El módulo ya está diseñado en la memoria."

**Diapositiva:** Fragmento de código HCL del módulo de red SENTINEL.

---

## BLOQUE 9 — CIERRE (30 seg)

"SENTINEL demuestra que la seguridad de nivel empresarial no es exclusiva de grandes corporaciones. Es una cuestión de arquitectura, no de presupuesto.

Gracias."

---

---

# PREGUNTAS TRAMPA DEL TRIBUNAL — RESPUESTAS PREPARADAS

## Sobre arquitectura

**¿Por qué usas DuckDNS y no Route53?**
> "DuckDNS es la solución viable en AWS Academy donde los créditos son limitados. Route53 añade $0.50/zona/mes y resolución más robusta — es el paso natural en producción. El script `update_duckdns.sh` en cron es el workaround que elimina la dependencia de una IP fija."

**¿Por qué RDS Single-AZ si dices que eliminas SPOF?**
> "El SPOF que elimino con RDS es el de gestión: ya no hay un EC2 con MariaDB que si cae pierde datos o requiere intervención manual. RDS, incluso en Single-AZ, gestiona backups automáticos, parches y snapshots. El Multi-AZ con failover activo < 60s es el siguiente paso, pospuesto por restricciones de crédito de Academy — pero la arquitectura ya está preparada para activarlo con un parámetro."

**¿Qué pasa si cae el ALB?**
> "El ALB de AWS tiene SLA del 99.99% — está distribuido en múltiples zonas de disponibilidad por diseño. No es un punto único de fallo, es infraestructura gestionada por AWS con redundancia integrada."

**¿Por qué no usas Kubernetes?**
> "Kubernetes tiene sentido cuando gestionas múltiples microservicios con equipos distintos. SENTINEL es un stack monolítico de e-commerce donde Docker Compose con `envsubst` da el mismo resultado de portabilidad con mucha menos complejidad operativa. La regla es usar la herramienta adecuada, no la más sofisticada."

---

## Sobre seguridad

**¿Has probado SQLi o XSS contra la tienda?**
> "Se aplicaron mitigaciones activas: PDO con prepared statements en toda la capa de datos, cabeceras CSP y X-XSS-Protection en Apache, y ServerTokens Prod para ocultar la firma del servidor. La validación de intrusiones se centró en el vector que Wazuh puede detectar y bloquear en tiempo real — fuerza bruta SSH — que es el ataque más frecuente en instancias EC2 expuestas."

**¿Wazuh puede generar falsos positivos?**
> "Sí, y lo hizo — bloqueó el servidor de monitorización al interpretar el scraping de Prometheus como actividad anómala. La solución fue añadir las IPs internas a la `white_list` de Active Response en `ossec.conf`. Ese tipo de ajuste de tunning es exactamente el trabajo real de un analista de seguridad."

**¿Los datos de los usuarios están seguros?**
> "El tráfico va cifrado end-to-end con TLS 1.2/1.3. Las credenciales de BD están en `secrets/` fuera del webroot y no en el código. El siguiente paso es AWS Secrets Manager para rotación automática — está documentado en la memoria como mejora pendiente."

---

## Sobre costes y viabilidad

**¿Es viable para una empresa real?**
> "A ~$115/mes con el stack completo, SENTINEL ofrece lo que un MSSP cobra entre $500 y $2.000/mes. Con Reserved Instances baja a ~$70/mes. Para una PyME de e-commerce pequeño, es perfectamente viable y técnicamente superior a cualquier hosting gestionado del mismo precio."

**¿Por qué no usaste Terraform?**
> "AWS Academy tiene restricciones de permisos IAM que impiden ejecutar Terraform correctamente — por ejemplo, no se puede crear roles IAM arbitrarios que Terraform necesita. En un entorno productivo real con permisos completos, el módulo ya está diseñado en la memoria con el código HCL correspondiente."

---

## Sobre el proceso

**¿Qué cambiarías si lo hicieras de nuevo?**
> "Empezaría con el diagrama de arquitectura definitivo antes de tocar la consola de AWS. La evolución de HAProxy a ALB a mitad del proyecto fue correcta técnicamente, pero generó deuda de documentación. Con Terraform desde el día uno, cada cambio de infraestructura quedaría versionado automáticamente."

**¿Qué es lo que más te ha costado?**
> "Hacer que el stack de monitorización arrancara correctamente. Alertmanager fallaba porque Docker no interpolaba las variables de entorno en el YAML de configuración. La solución fue implementar un sistema de plantillas con `envsubst` en `start.sh` — un problema que en producción habría pasado desapercibido hasta el primer incidente real."
