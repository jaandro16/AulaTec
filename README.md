# AulaTec

**Sistema Inteligente de Gestión de Aulas**

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)

## Descripción General

AulaTec es una plataforma web integral desarrollada con Laravel y MySQL que revoluciona la gestión de aulas en instituciones educativas. El sistema permite a los estudiantes reservar asientos a través de un mapa interactivo del aula, registrar su asistencia mediante códigos QR y gestionar sus reservas de forma autónoma, optimizando el uso de espacios educativos y simplificando la gestión de asistencia.

## Características Principales

- **🗺️ Reserva Interactiva de Asientos**: Mapeo visual del aula para selección intuitiva de asientos
- **🔄 Sistema de Intercambio Dinámico**: Transferencia de reservas entre estudiantes
- **📱 Integración con Códigos QR**: Seguimiento automatizado de asistencia mediante escaneo QR
- **📝 Gestión de Ausencias**: Flujo digital de envío y aprobación de faltas justificadas
- **👨‍🏫 Panel Administrativo**: Panel de gestión integral para educadores y administradores
- **📊 Analíticas e Informes**: Estadísticas de uso en tiempo real y reportes de asistencia

## Público Objetivo

Diseñado para instituciones educativas incluyendo:
- Universidades y centros de educación superior
- Institutos y centros de educación secundaria
- Academias de formación
- Centros de formación corporativa

## Arquitectura

El sistema sigue una arquitectura modular que promueve la utilización eficiente de recursos, reduce el absentismo y facilita el seguimiento académico integral.

## Estructura del Proyecto

```
AulaTec/
├── Análisis/              # Análisis del proyecto y requisitos
│   ├── Casos de uso
│   ├── Diagramas UML
│   └── Especificaciones funcionales
├── Base de Datos/         # Diseño y scripts de base de datos
│   ├── Scripts SQL de creación
│   └── Modelo relacional
├── Desarrollo/            # Código fuente (aplicación Laravel)
│   ├── app/
│   ├── routes/
│   ├── resources/
│   └── ...
├── Diseño/               # Recursos de diseño UI/UX
│   ├── Prototipos Figma
│   ├── Mockups
│   └── Diseños de pantallas
├── Implementación/       # Documentación de despliegue
│   ├── Material promocional
│   └── Documentación de pruebas
├── Mantenimiento/        # Documentación de mantenimiento
│   └── Planes de mantenimiento
└── Paper/               # Documentación académica
    └── Memoria final del proyecto (PDF)
```

## Inicio Rápido

### Requisitos Previos

- [Docker](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/)
- Puerto 3306 disponible (o modificar si ya está en uso)
- Git
- IMPRESCINDIBLE crear archivo .env con el contenido del archivo .env.example

### Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/AulaTec.git
   cd AulaTec
   ```

2. **Construir e iniciar contenedores**
   ```bash
   docker-compose up --build -d
   ```

3. **Configurar permisos**
   ```bash
   docker exec -it laravel-docker bash
   chmod -R 755 /var/www/html
   chown -R www-data:www-data /var/www/html
   exit
   ```

4. **Instalar dependencias y configurar base de datos**
   ```bash
   sudo docker exec laravel-docker bash -c "composer update"
   sudo docker exec laravel-docker bash -c "php artisan migrate"
   ```

5. **Acceder a la aplicación**
   Abre tu navegador y navega a `http://localhost:9000`. Puedes ver el panel de la base de datos en `http://localhost:9001` seleccionando sobre la base de datos "fichaje".

### Detener la Aplicación

```bash
docker-compose down -v --remove-orphans
```

## Stack Tecnológico

- **Backend**: Laravel (Framework PHP)
- **Base de Datos**: MySQL
- **Frontend**: Plantillas Blade, JavaScript
- **Contenedorización**: Docker y Docker Compose
- **Autenticación**: Laravel Auth
- **Códigos QR**: Librería PHP Bacon QR Code

## Contribuciones

¡Damos la bienvenida a las contribuciones a AulaTec! Por favor, lee nuestras pautas de contribución antes de enviar pull requests.

1. Haz fork del repositorio
2. Crea una rama de características (`git checkout -b feature/caracteristica-increible`)
3. Confirma tus cambios (`git commit -m 'Añadir característica increíble'`)
4. Sube la rama (`git push origin feature/caracteristica-increible`)
5. Abre un Pull Request

## Documentación

La documentación completa está disponible en las respectivas carpetas del proyecto:
- Especificaciones técnicas en `Análisis/`
- Documentación de base de datos en `Base de Datos/`
- Guías de despliegue en `Implementación/`
- Memoria académica en `Paper/`

## Licencia

Este proyecto está licenciado bajo la Licencia MIT.

## Soporte

Para soporte y consultas, por favor abre un issue en el repositorio de GitHub o contacta al equipo de desarrollo.

## Agradecimientos

- Profesores e Investigadores de la Unidad Docente de Informática Industrial de la Universidad Politécnica de Madrid
- Colaboradores y testers que ayudaron a mejorar el sistema

---

**Hecho con ❤️ por [Alejandro Pérez](https://www.linkedin.com/in/alejandro-pérez-rentero-396033265/) y [Andrés Patrascu](https://www.linkedin.com/in/andres-patrascu-85b572333/) para una mejor gestión educativa**
