# PSICOPOMPO / Proyecto SENTINEL (TFG)

Este repositorio contiene el código y la infraestructura desarrollada para el Trabajo de Fin de Grado (TFG).

En su núcleo, **Psicopompo** es una aplicación web de comercio electrónico desarrollada en PHP y MySQL. Sin embargo, el objetivo principal del proyecto abarca el diseño, despliegue y bastionado de la infraestructura que soporta esta aplicación en un entorno cloud (AWS).

## Características del Proyecto

- **Aplicación Web**: E-commerce funcional con registro, autenticación y gestión de productos.
- **Infraestructura Cloud (IaaS)**: Despliegue en AWS utilizando múltiples instancias EC2.
- **Alta Disponibilidad y Seguridad**: 
  - Balanceador de carga (HAProxy).
  - Bastionado de red (Network Hardening) restringiendo accesos directos a los servidores.
  - Cifrado SSL/TLS.
- **Monitorización y Logs**: Stack completo de observabilidad con Prometheus, Grafana y Loki desplegado vía Docker Compose.
- **Automatización y Respaldo**: Backups automatizados almacenados en AWS S3.
- **Pruebas de Rendimiento y Seguridad**: Pruebas de carga con Apache Benchmark y simulaciones de ataques de fuerza bruta SSH.
- **Estrategia FinOps**: Optimización y control de recursos en la nube.

## Tecnologías Utilizadas

- **Aplicación Base**: PHP, HTML, CSS, JavaScript.
- **Base de Datos**: MySQL.
- **Cloud & Infraestructura**: Amazon Web Services (AWS EC2, S3, VPC).
- **Servidores y Proxies**: Apache, HAProxy.
- **Monitorización**: Docker, Prometheus, Grafana, Loki.

## Estructura del Repositorio principal

- `/src`, `/includes` y `/admin`: Código fuente de la aplicación (Frontend, Backend PHP y panel de administración).
- `/database` / `/dbs`: Scripts de creación de la base de datos.
- `/monitoring`: Archivos de configuración para el entorno de Docker (Prometheus, Grafana, etc.).
- `/docs`: Documentación del TFG, memorias y esquemas.
- `/automation`: Scripts de tareas automatizadas (por ejemplo, backups).
- `/tests`: Pruebas de la aplicación e infraestructura.

## Despliegue

La aplicación está diseñada para desplegarse de manera distribuida:
1. **Load Balancer**: Recibe y balancea el tráfico HTTPS.
2. **Web Server**: Procesa la lógica de negocio en PHP protegido de accesos públicos directos.
3. **Database Server**: Aloja la base de datos MySQL de manera segura.
4. **Monitoring Stack**: Instancia independiente que agrupa la observabilidad y logs de todo el entorno.
