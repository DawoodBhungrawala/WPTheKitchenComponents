# Nxtal Web Content Parser

A universal content parser package for extracting structured data from multiple websites.

## Features

- **200+ Website Support**: Parse products from Amazon, eBay, AliExpress, Walmart, and many more
- **Standardized Interface**: All parsers implement the same interface for consistent usage
- **Namespace Protection**: Uses `Nxtal\ContentParser` namespace to avoid conflicts
- **Framework Agnostic**: Can be used in any PHP application, not just WordPress
- **Composer Ready**: Easy installation and autoloading

## Installation

```bash
composer require nxtal/content-parsers
```

## Usage

### Basic Usage

```php
use Nxtal\ContentParser\AmazonParser;

// Create parser instance
$parser = new AmazonParser('', 'https://www.amazon.com/dp/B08N5WRWNW');

// Extract product data
$title = $parser->getTitle();
$price = $parser->getPrice();
$description = $parser->getDescription();
$images = $parser->getImages();
$brand = $parser->getBrand();
```

### Available Methods

All parsers implement the `AbstractParser` interface with these methods:

- `getTitle()` - Product title
- `getPrice()` - Current price
- `getRegularPrice()` - Original price
- `getDescription()` - Product description
- `getShortDescription()` - Brief description
- `getImages()` - Product images array
- `getBrand()` - Brand name
- `getCategories()` - Product categories
- `getAttributes()` - Product attributes
- `getCombinations()` - Product variations
- `getFeatures()` - Product features
- `getSKU()` - Product SKU
- `getWeight()` - Product weight
- `getDimension()` - Product dimensions
- `isAvailableForSale()` - Stock availability
- `getCustomerReviews()` - Customer reviews
- `getVideos()` - Product videos

### Supported Websites

- **Amazon** (amazon.com, amazon.co.uk, amazon.de, etc.)
- **eBay** (ebay.com, ebay.co.uk, ebay.de, etc.)
- **AliExpress** (aliexpress.com)
- **Walmart** (walmart.com)
- **And 200+ more websites**

## Requirements

- PHP 7.4 or higher
- cURL extension
- DOM extension
- JSON extension

## License

This package is proprietary software. All rights reserved.

## Support

For support and documentation, visit: https://woocommerce.com/products/product-importer/