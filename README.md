
# XML Validator Library

A simple XML validator for validate large XML files.

## Installation

To install this library, use Composer:

```bash
composer require mihas2/xml_validator
```

## Usage

Here's an example of how to use the XML validator:

```php
use mihas2\XmlValidator\XmlValidator;

$xmlFile = 'path/to/your/xml/file.xml';
$xmlFileValidator = new XmlValidator($xmlFile);

if ($xmlFileValidator->validate()) {
    echo "XML file is valid.";
} else {
    foreach ($xmlFileValidator->getErrors() as $error) {
        printf("Line: %d, Column: %d, Message: %s\n", $error->lineNumber, $error->columnNumber, $error->message);
    }
}

```

```php
use mihas2\XmlValidator\XmlValidator;

$xmlUrl = 'https://www.yourdomain.com/validateme.xml';
$xmlUrlValidator = new XmlValidator($xmlUrl);

if ($xmlUrlValidator->validate()) {
    echo "XML file is valid.";
} else {
    foreach ($xmlUrlValidator->getErrors() as $error) {
        printf("Line: %d, Column: %d, Message: %s\n", $error->lineNumber, $error->columnNumber, $error->message);
    }
}

```

## Configuration

You can configure the XML validator with these options:

- `$xmlFile` (required): The path to your XML file.
- `$verifyPeer` (optional, default `false`): Set to true to verify the SSL/TLS certificate of the remote schema location.
- `$verifyPeerName` (optional, default `false`): Set to true to verify that the hostname matches the certificate's subject.
- `$maxDepth` (optional, default `10000`): The maximum depth for XML elements during validation.
- `$xmlFlags` (optional, default `LIBXML_BIGLINES | LIBXML_PARSEHUGE`): Additional flags to configure the libxml library's behavior.

## Testing

To run tests, execute this command:

```bash
composer test
```
 **Not implemented yet**

## Contributing

We welcome contributions! Please submit a pull request with your proposed changes and describe what they do in detail.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
```
