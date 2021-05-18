# Ilumno Modulo Programas Rest
Este modulo esta diseñado como proposito de prueba tecnica para Ilumno

Se puede clonar e instalar en un drupal limpio, si se desea manejar por independiente.

- El modulo expone un servicio REST con los metodos Get y Patch, estan disponibles en /ilumno-module/data/{id} 
- Para el caso del metodo GET, se dejo la opción de obtener todos los registros de progrmas bajo esta url ilumno-module/data/all
- El modulo depende de los siguientes modulos
  - restui
  - ilumno_programs 
