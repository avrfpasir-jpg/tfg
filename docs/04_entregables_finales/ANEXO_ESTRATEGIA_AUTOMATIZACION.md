# ANEXO: Estrategia de Automatización y Escalabilidad (SENTINEL IaC)

## 1. Visión General
El objetivo es transformar la infraestructura desplegada de forma manual durante el TFG en un sistema **"One-Click Deploy"**. Esto permitiría a una empresa (MSP) aprovisionar una "Tienda Segura" completa para un nuevo cliente simplemente definiendo un par de variables (nombre del cliente, dominio, presupuesto).

## 2. Herramientas del Stack IaC
*   **Terraform:** Orquestación de la infraestructura cloud (VPC, Instancias, Red).
*   **Cloud-Init / User Data:** Scripts de Bash embebidos en el despliegue para el endurecimiento (Hardening) automático en el primer arranque.
*   **HCP Packer (Opcional):** Para crear "Golden Images" pre-configuradas de Wazuh y MariaDB.

## 3. Estructura de Archivos del Proyecto (Propuesta)
```text
sentinel-deploy/
├── main.tf                 # Orquestador principal
├── variables.tf            # Variables por cliente (dominio, región, etc)
├── outputs.tf              # IPs y credenciales generadas
├── modules/
│   ├── network/            # VPC, Subredes, IGW, Squid
│   ├── security/           # Security Groups y Roles IAM
│   ├── compute/            # EC2 (Web, DB, HAProxy)
│   └── monitoring/         # Provisionamiento de Wazuh/Grafana
└── scripts/
    ├── web_hardening.sh    # Script de instalación Apache/SSL
    └── db_hardening.sh     # Script de configuración MariaDB aislada
```

## 4. Flujo de Automatización por Cliente
1.  **Aislamiento:** Uso de *Terraform Workspaces* para separar el estado de cada cliente (ej: `terraform workspace new cliente-zapateria`).
2.  **Despliegue de Red:** Terraform crea una VPC dedicada con el direccionamiento 10.X.0.0/16 correspondiente.
3.  **Inyección de Seguridad:** Mediante la sección `user_data` de Terraform, se inyecta automáticamente la regla de *Sudoers* para Wazuh y se cierran los puertos innecesarios.
4.  **Certificación:** Automatización del reto de Let's Encrypt mediante el plugin `dns-route53` o similar para que el cliente reciba la tienda ya con HTTPS activo.

## 5. Ventaja Competitiva
Este enfoque reduce el **Time-to-Market** de una infraestructura de seguridad avanzada de 48 horas (manual) a menos de 10 minutos (automatizado), eliminando el error humano en la configuración de firewalls y permisos de base de datos.
