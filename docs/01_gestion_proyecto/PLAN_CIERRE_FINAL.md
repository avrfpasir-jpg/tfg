# 🏁 PLAN DE CIERRE FINAL — SENTINEL TFG
> **Este documento es el ÚNICO plan de acción vigente.**
> Sustituye y archiva: `ROADMAP_UNIFICADO_FASE4.md`, `ROADMAP_SENTINEL_PRO.md` (hist.), `TAREAS_PROXIMA_SEMANA.md` (hist.)
> Última actualización: 2026-04-01

---

## 🔴 BLOQUE 1 — Correcciones Críticas en MEMORIA_FINAL.md
> Estas inconsistencias serán detectadas por cualquier tribunal. Hacerlas primero.

- [x] **1.1 Contradicción Active Response:** Cambiado a ⚠️ con texto honesto en la tabla.
- [x] **1.2 "Zero Trust" mal aplicado:** Reescrito a "principios de mínimo privilegio y aislamiento, pilares del modelo Zero Trust".
- [x] **1.3 Usuario de BD inconsistente:** Unificado a `sentinel_web` en toda la memoria.
- [x] **1.4 Anexos con rutas inexistentes:** Actualizados a rutas reales de `docs/img/` y `docs/04_entregables_finales/`.

---

## 🟡 BLOQUE 2 — Correcciones Importantes en MEMORIA_FINAL.md
> Debilidades académicas que un tribunal con criterio detectará.

- [x] **2.1 Loki sin evidencia:** Eliminado del Resumen. En Objetivos queda sólo Prometheus/Grafana. La referencia a Loki en Recursos se mantiene como tecnología instalada.
- [x] **2.2 Afirmaciones de sector sin citar:** Añadidas referencias ENISA Threat Landscape 2024 y Verizon DBIR 2024 en el texto y en la bibliografía.
- [x] **2.3 Objetivos sin KPI:** Añadido un KPI medible y verificado junto a cada objetivo del Cap. 4.2.
- [x] **2.4 "Alta disponibilidad" prometida:** Matizado en el Resumen a "orientada a la alta disponibilidad".

---

## 🟢 BLOQUE 3 — Mejoras de Calidad en MEMORIA_FINAL.md
> No son críticas pero elevan la nota.

- [x] **3.1 Conclusiones conectadas a datos:** Reescrita con $2.29 USD, 59 RPS y CPU <1%.
- [x] **3.2 Riesgos Laborales reorientados:** Sustituidos por riesgos reales: credenciales en PHP, SPOF HAProxy, falta replicación, Alert Fatigue.
- [x] **3.3 "Metodología Ágil" → "Metodología Iterativa por Fases":** Cambiado en la memoria.
- [x] **3.4 Sección 8.2 Recomendaciones expandida:** 4 recomendaciones concretas añadidas: nftables, Secrets Manager, OPcache, Keepalived.

---

## 📑 BLOQUE 4 — Entregables Pendientes
> Documentos o acciones que faltan para cerrar el proyecto.

- [ ] **4.1 Agradecimientos:** Rellenar el placeholder del Cap. 2 en MEMORIA_FINAL.md.
- [ ] **4.2 Repaso GUIA_DEFENSA_TFG.md:** Leer y memoizar las 8 respuestas "Pro". Especialmente la de nftables (nueva) y Zero Trust (nueva).

---

## ✅ YA HECHO — No tocar
- [x] Test de estrés ab ejecutado (59.01 RPS) y documentado
- [x] Informe de Rendimiento actualizado con nuevas métricas
- [x] Evidencia Active Response documentada con imagen y log real
- [x] Diagrama de red SVG integrado en la memoria
- [x] Captura Grafana del test integrada en la memoria
- [x] GUIA_DEFENSA_TFG.md con 8 preguntas tipo tribunal respondidas
- [x] ANEXO_DISASTER_RECOVERY.md completado y documentado
- [x] Backups automáticos S3 y Systemd Timer operativos
- [x] Wazuh SIEM optimizado (JVM 1GB, rotación índices, alertas Telegram)

---

*Nota: `ROADMAP_SENTINEL_PRO.md` y `TAREAS_PROXIMA_SEMANA.md` han sido movidos a `docs/05_historico/` y quedan invalidados. `ROADMAP_UNIFICADO_FASE4.md` se mantiene como registro histórico de fases pero este documento es el plan operativo activo.*
