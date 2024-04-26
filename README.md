# Perú Consulta de DNI
Consulta gratuita de DNI en Perú funcionando desde el 2024.

## Instalar
Vía composer desde packagist.org.
```bash
composer require davichano/peru-ruc-dni-php
```
### Requerimientos
- PHP 7.1 o superior.
- API Key de OCR Space, se puede obtener gratis [aquí](https://ocr.space/ocrapi/freekey).

### Servicios

- Consulta de DNI
    - Nombres
    - Apellidos
    - Código de verificación

## DNI
Consulta de DNI.
> Fuente: **EsSalud**.

### Ejemplo

```php
use Davichano\DNI\Person;

require 'vendor/autoload.php';

$dni = '46658592';
$apiKeyOCR="xxxxxxxxxxxxxxxxx";

$person = new Person($dni);
$data = $person->get_data($apiKeyOCR);
if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Persona no encontrada']);
}
```

### Resultado

Resultado en formato json.

```json
{
  "dni": "46658592",
  "name": "LESLY LICET",
  "lastname": "PEREZ PEÑA",
  "code": "6"
}
```
