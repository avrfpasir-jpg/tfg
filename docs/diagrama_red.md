```mermaid
graph TD
    %% Estilos de los Nodos
    classDef internet fill:#1d2633,stroke:#ffffff,color:#ffffff,stroke-width:2px;
    classDef aws fill:#ff9900,stroke:#232f3e,color:#232f3e,stroke-width:1px;
    classDef subredPub fill:#d4efdf,stroke:#229954,color:#145a32,stroke-width:2px,stroke-dasharray: 5 5;
    classDef subredPriv fill:#fadbd8,stroke:#cb4335,color:#7b241c,stroke-width:2px,stroke-dasharray: 5 5;
    classDef instancia fill:#ebedef,stroke:#5d6d7e,color:#212f3d,stroke-width:2px;
    classDef db fill:#f9e79f,stroke:#f1c40f,color:#7d3c98,stroke-width:2px;

    %% Internet
    Users["🌍 Usuarios / Clientes"]:::internet
    DNS["🌐 psicopompo.duckdns.org"]:::internet

    Users -->|Tráfico HTTPS (443)| DNS

    %% Nube AWS
    subgraph AWS ["☁️ Entorno Cloud: Amazon Web Services"]
        IGW["🚪 Internet Gateway (IGW)"]:::aws
        DNS -->|Resuelve IP Pública| IGW

        subgraph VPC ["🛡️ VPC (Virtual Private Cloud)"]
            
            subgraph Public ["🟢 Subred Pública (Expuesta a Internet)"]
                HAProxy["⚖️ HAProxy (Load Balancer)<br>Terminación SSL (Certbot)"]:::instancia
                IGW -->|Redirige tráfico| HAProxy
            end

            subgraph Private ["🔴 Subred Privada (Air-Gapped / Aislada)"]
                WebServer["🖥️ Web Server Bastionado<br>(Apache + PHP)"]:::instancia
                DBServer["🗄️ Servidor de Base de Datos<br>(MariaDB)"]:::db
                Monitoring["👁️ Monitoring Stack & SIEM<br>(Wazuh, Prometheus, Grafana)<br>*t3.medium con Swap*"]:::instancia
            end

            %% Flujo de tráfico web principal
            HAProxy == "HTTP (80)" ==> WebServer
            WebServer == "Consultas (3306)" ==> DBServer

            %% Flujo de monitorización y seguridad
            HAProxy -. "Métricas y Logs" .-> Monitoring
            WebServer -. "Métricas y Logs (Agente)" .-> Monitoring
            DBServer -. "Métricas y Logs" .-> Monitoring
        end

        %% Externo a la VPC pero dentro de AWS
        S3["📦 Amazon S3 Bucket<br>(Backups Seguros)"]:::aws
        DBServer -. "mysqldump automático (Cron)" .-> S3
    end
```
