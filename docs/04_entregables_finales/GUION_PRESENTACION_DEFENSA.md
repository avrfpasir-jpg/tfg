# 🎙️ Guion de Presentación y Defensa - Proyecto SENTINEL
**Grado Superior ASIR**  
**Autor:** Alex Vidal Ródenas  

---

## 1. Introducción y Propósito (2 Minutos)
*   **Visión:** "SENTINEL no es solo una tienda online, es un **Blueprint de infraestructura blindada en la nube**."
*   **Problema:** "Las PYMES suelen tener e-commerce vulnerables y sin monitorización."
*   **Solución:** "Una arquitectura Multi-Tier en AWS que aplica el modelo de **Defensa en Profundidad**."

## 2. Arquitectura de Red y Cómputo (4 Minutos)
*   **Concepto:** "El Búnker Cloud".
*   **Detalles Técnicos:**
    *   **VPC Segmentada:** Uso de subredes públicas (para el balanceador) y privadas (para el servidor web y la base de datos).
    *   **ALB (Application Load Balancer):** "Punto de entrada único. Elimina la exposición directa de los servidores a Internet y permite el escalado horizontal."
    *   **RDS Multi-AZ:** "Base de datos gestionada por AWS. Si un centro de datos falla, la tienda sigue operando gracias a la replicación automática."

## 3. Demostración en Vivo: Disponibilidad y Datos (3 Minutos)
*   **Acción:** Abrir [http://psicopompo.duckdns.org](http://psicopompo.duckdns.org).
*   **Puntos Clave:**
    *   Navegación fluida por los productos.
    *   Explicación del **Stickiness** (Persistencia de sesión) gestionado por las cookies del balanceador.
    *   Mencionar que el servidor web es un nodo privado inaccesible por SSH directo (Seguridad).

## 4. El Centro de Operaciones: Monitorización (4 Minutos)
*   **Acción:** Abrir [http://statuspsicopompo.duckdns.org](http://statuspsicopompo.duckdns.org) (Grafana).
*   **Puntos Clave:**
    *   **Métricas de Salud:** Visualización de CPU y RAM de las instancias EC2.
    *   **Métricas de Negocio:** Tráfico de Apache (RPS) y carga de consultas SQL en MariaDB.
    *   **Valor Técnico:** "Esto permite detectar cuellos de botella antes de que afecten al usuario final."

## 5. Seguridad Avanzada: SIEM Wazuh (3 Minutos)
*   **Acción:** Enseñar evidencias/capturas de logs de Wazuh (Carpeta `docs/img/`).
*   **Discurso:**
    *   "He integrado Wazuh para la detección de intrusiones en tiempo real."
    *   "Validación del **Active Response**: El sistema es capaz de detectar un ataque de fuerza bruta y bloquear la IP automáticamente en el firewall (Iptables)."
    *   **Nota FinOps:** "Para la demo, el nodo está apagado para optimizar recursos, pero su eficacia está 100% validada técnicamente."

## 6. Conclusiones y Futuro (2 Minutos)
*   **Conclusión:** "Se ha demostrado que es posible desplegar una infraestructura industrial segura con un coste mínimo ($2.29 USD)."
*   **Futuro (Viabilidad):**
    *   **IaC (Infrastructure as Code):** Automatización total con Terraform para despliegues masivos.
    *   **Hacia Producción:** Migración de DNS a Route 53 y uso de certificados ACM para SSL Full-Stack.

---

## 🚩 Notas para Preguntas del Tribunal
1.  **¿Por qué no va el HTTPS?** -> *"Es una limitación de los certificados dinámicos y DuckDNS en el entorno controlado de AWS Academy. En producción se usaría AWS Certificate Manager (ACM) integrado directamente en el ALB."*
2.  **¿Es escalable?** -> *"Sí, gracias al ALB. Podríamos añadir 10 servidores web más en segundos y el sistema balancearía la carga automáticamente."*
3.  **¿Es seguro el acceso SSH?** -> *"Por ahora usamos un grupo de seguridad restringido, pero la recomendación profesional es usar AWS Systems Manager (SSM) para no abrir el puerto 22 nunca."*
