# Sistema de Gestión de Inscripciones - Carrera Aceros Ocotlán

## Descripción
MVP para la gestión de inscripciones de la Carrera Aceros Ocotlán. 
Sistema que centraliza registros de Google Forms en una base de datos MySQL 
con panel administrativo para validación y análisis.

## Objetivo del MVP
- Registrar automáticamente participantes desde Google Forms
- Administrar y validar inscripciones
- Visualizar métricas clave del evento
- Exportar datos para la organización

## Stack Tecnológico
- **Backend:** CodeIgniter 4 (PHP 8.2+)
- **Base de datos:** MySQL 8.0+
- **Frontend:** Bootstrap 5 + Chart.js
- **Integración:** Google Sheets API

## Instalación
1. Clonar repositorio: `git clone https://github.com/CarlosNm0802/carrera-aceros-ocotlan.git`
2. Instalar dependencias: `composer install`
3. Configurar `.env` con credenciales de base de datos
4. Ejecutar migraciones: `php spark migrate`

## Roadmap de 7 Días
| Día | Objetivo | Estado |
|-----|----------|--------|
| 1 | Setup y configuración base | ✅ COMPLETADO |
| 2 | Autenticación y base de datos | ✅ COMPLETADO |
| 3 | CRUD completo de participantes | ✅ COMPLETADO |
| 4 | Integración Google Sheets | ✅ COMPLETADO |
| 5 | Dashboard con métricas | 📅 PENDIENTE |
| 6 | Exportación y filtros | 📅 PENDIENTE |
| 7 | Producción y documentación | 📅 PENDIENTE |

##  Enlaces importantes
- **Google Form:** [https://forms.gle/FYm138FdCUnKondJ9]
- **Google Sheet:** [https://docs.google.com/spreadsheets/d/18vjspw-uuMg9EwkHpT0xlIlSJlWgCle9nDyAuKaTKyw/edit?usp=sharing]
- **Panel Admin:** http://localhost:8080/admin (desarrollo)

## 📞 Contacto
- Desarrollador: [Carlos Nares]
- Email: [carlosnaresmon@gmail.com]
- Proyecto iniciado: [07/02/2026]