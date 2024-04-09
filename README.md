# Prueba Técnica - Restaurante Solidario

URL de prueba: http://3.83.176.114

### Esta versión de la prueba técnica contiene correcciones de errores y mejoras del manejo asíncrono de los jobs del inventario. 
### (despues de la fecha de entrega, no se encuentra desplegado en la URL de prueba)

## Descripción

Este proyecto implementa un sistema para un restaurante que dona platos gratis. El sistema permite al gerente realizar pedidos masivos de platos, gestionar el inventario de ingredientes y observar el historial de pedidos y compras.

## Arquitectura

El sistema se compone de tres microservicios:

- Gateway: Redirige las peticiones del frontend al servicio adecuado.
- Cocina: Procesa las órdenes, prepara los platos y actualiza el inventario.
- Bodega: Gestiona el inventario de ingredientes y realiza compras en la plaza de mercado.

## Tecnologías

- Docker: Contenerización de los microservicios.
- Laravel-Lumen: Implementación de los microservicios.
- MySQL: Base de datos para almacenar información.
- RabbitMQ: Queue broker para comunicación asíncrona entre microservicios.
- Vue: Interfaz de usuario.

## Flujo de trabajo

1. El gerente realiza un pedido de platos.
2. El gateway envía el pedido al microservicio de cocina e inventario por medio del queue broker.
3. La cocina crea la orden en estado "Preparación".
4. La bodega verifica el inventario de ingredientes.
5. Si hay suficientes ingredientes, la bodega envía un evento a la cocina para preparar el plato y cambiar el estado de la orden a "Entregada".
6. Si no hay suficientes ingredientes:
  - La bodega ejecuta Jobs paralelos para solicitar a la plaza de mercado los ingredientes faltantes, registrando cada petición a la plaza como un item del historial de compras.
  - La bodega actualiza el inventario.
7. Después de verificar que la cantidad de ingredientes necesarios se cumple, se envía un evento a la cocina para la preparación de los platos.

## Esquema de la base de datos

### ingredients

- id uinteger
- name varchar(255)
- quantity integer

### recipes

- id uinteger
- name varchar(255)
- description text

### ingredients_recipes

- id uinteger
- ingredient_id uinteger
- recipe_id uinteger
- quantity integer

### orders

- id uinteger
- status enum('received', 'preparing', 'delivered')
- created_at timestamp
- updated_at timestamp

### order_items

- id uinteger
- order_id uinteger
- recipe_id uinteger
- quantity integer

### purchases

- id uinteger
- ingredient_id uinteger
- quantity integer
- created_at timestamp

## Consideraciones

- Se implementan transacciones en la base de datos para garantizar la integridad de los datos.
- Se utiliza long polling para evitar sobrecargar la plaza de mercado con peticiones constantes.
- Se implementan mecanismos de reintento para manejar errores en la comunicación con el queue broker.
