# AulaTec

##  Resumen del Proyecto

**AulaTec** es una plataforma web desarrollada con **Laravel** y **MySQL** que permite a los alumnos **reservar puestos en aulas educativas** mediante un **mapa interactivo**, registrar su asistencia con **códigos QR** y gestionar sus reservas de forma autónoma. El objetivo es optimizar el uso de los espacios educativos y mejorar la gestión del control de asistencia de forma digital y accesible.

Entre sus funcionalidades principales destacan:

- **Reserva de puestos a través de un mapa visual del aula.**
- **Sistema de intercambio de reservas entre alumnos.**
- **Generación y escaneo de códigos QR** para registrar la asistencia.
- **Gestión de faltas justificadas** mediante formularios personalizados.
- **Panel de administración para docentes y gestores del aula.**

El sistema está diseñado para su uso en centros educativos (institutos, universidades, academias) y busca fomentar el **uso eficiente de los recursos**, **reducir las faltas** y facilitar el **seguimiento académico**.

---

##  Estructura del Proyecto

El repositorio está organizado en carpetas que siguen el ciclo de vida de desarrollo del proyecto. A continuación, dentro de la carpeta AulaTec, se describe el contenido de cada una:

### 1. `Análisis/`
Contiene los documentos iniciales del proyecto:
- Estudio del problema.
- Requisitos funcionales y no funcionales.
- Casos de uso.
- Diagramas UML (casos de uso, clases, actividades).

### 2. `Base de Datos/`
Incluye todo lo relacionado con la base de datos:
- Script SQL de creación.
- Modelo Relacional.

### 3. `Desarrollo/`
Contiene el código fuente del sistema:
- Estructura del proyecto Laravel (`app/`, `routes/`, `resources/`, etc.).
- Código fuente de la página web.

### 4. `Diseño/`
Incluye los recursos de interfaz y diseño visual:
- Prototipos en Figma o similares.
- Diseño de las pantallas.
- Mockups.

### 5. `Implementación/`
Describe el despliegue del proyecto:
- Cartel publicitario A3.
- Documentación de las pruebas del código.

### 6. `Mantenimiento/`
Incluye documentación y scripts para el mantenimiento del sistema:
- Plan de la fase de mantenimiento.

### 7. `Paper/`
Contiene el documento final del proyecto en formato PDF:
- Memoria del TFG.

---

##  Manual de Instalación (Docker)

###  Requisitos previos

- Tener instalado [Docker](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/).
- Puerto **3306** libre (o editarlo si ya está en uso).
- Clonar el repositorio de GitHub.

###  Pasos de instalación

1. **Clona el repositorio:**
2. comando instalación docker en wsl

```bash
git clone https://github.com/tu_usuario/AulaTec.git
````

```bash
cd AulaTec
```

```bash
docker-compose up --build -d
```

```bash
docker-compose down -v --remove-orphans
```

```bash
docker exec -it laravel-docker bash
```

```bash
chmod -R 755 /var/www/html
```

```bash
chown -R www-data:www-data /var/www/html
```

```bash
exit
```

```bash
sudo docker exec laravel-docker bash -c "composer update"

```
```bash
sudo docker exec laravel-docker bash -c "php artisan migrate"

```