# DNI
Consulta de DNI.
> Fuente: **EsSalud**.

## Ejemplo

```php
use Davichano\DNI;

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

## Resultado

Resultado en formato json.

```json
{
  "dni": "46658592",
  "name": "LESLY LICET",
  "lastname": "PEREZ PEÑA",
  "code": "6"
}
```