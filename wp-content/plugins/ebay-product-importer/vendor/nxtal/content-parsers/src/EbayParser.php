<?php
/**
 * @package: Nxtal\ContentParser
 */

namespace Nxtal\ContentParser;

/* Parser version 6.0 */

class EbayParser extends AbstractParser {

	private $dom;
	private $xpath;
	private $url;
	private $content;
	private $images = array();
	private $attrJsonArray = array();
	private $TITLE_SELECTOR = '//h1';
	private $TITLE_EXTRA_SELECTOR = '//h1/span[@class="g-hdn"]';
	private $CAEGORIES_SELECTOR = '//td[@id="vi-VR-brumb-lnkLst"]/table/tbody/tr/td/ul/li/a/span';
	private $CAEGORIES_SELECTOR2 = '//li[@id="vi-VR-brumb-lnkLst"]/ul/li[@role="listitem"]/a/span|//nav[contains(@class, "breadcrumbs")]/ul/li/a/span';
	private $DESCRIPTION_SELECTOR = '//div[@id="viTabs_0_pd"]';
	private $DESCRIPTION_SELECTOR2 = '//iframe[@id="desc_ifr"]/@src';
	private $PRICE_SELECTOR = '//span[@itemprop="price"]/@content|//span[@id="prcIsum"]|//div[@data-testid="x-price-primary"]/span';	
	private $REGULAR_PRICE_SELECTOR = '//span[contains(@class, "ux-textspans--STRIKETHROUGH")]';	
	private $CURRENCY_SELECTOR = '//span[@itemprop="priceCurrency"]/@content';
	private $COVER_IMAGE_SELECTOR = '//img[@id="icImg"]/@src|//div[@id="mainImgHldr"]//img/@data-zoom-src';
	private $IMAGE_SELECTOR = '//div[@id="vi_main_img_fs"]/ul/li/button/table/tbody/tr/td/div/img/@src|//div[@id="vi_main_img_fs"]/div/div/ul/li/a/div/img/@src|//div[@id="vi_main_img_fs"]/div/div/ul/li/a/div/img/@data-img-url|//div[contains(@class, "ux-image-carousel-item")]/img/@data-src|//div[contains(@class, "ux-image-carousel-item")]/img/@src';
	private $SKU_SELECTOR = '//div[@id="descItemNumber"]|//input[@id="iid"]/@value';
	private $BRAND_SELECTOR = '//*[@itemprop="brand"]/span';
	private $FEATURE_SELECTOR = '//section[@class="product-spectification"]/div';
	private $FEATURE_SELECTOR2 = '//div[contains(@class, "x-prp-product-details_content")]/div[@class="x-prp-product-details_section"]';
	private $FEATURE_SELECTOR3 = '//div[@id="viTabs_0_is"]/div/table/tbody/tr/*';
	private $FEATURE_SELECTOR4 = '//div[contains(@class, "ux-layout-section--features")]//div[@class="ux-labels-values__labels-content"]|//div[contains(@class, "ux-layout-section--features")]//div[@class="ux-labels-values__values-content"]';	
	private $REVIEW_SELECTOR_LINK = '//div[@class="reviews-right"]/div[@class="reviews-header"]/a/@href|//div[contains(@class, "x-review-details")]//div[@class="x-review-details__allreviews"]/a/@href';
	private $REVIEW_SELECTOR = '//div[@itemprop="review"]|//div[@itemprop="reviews"]|//div[contains(@class, "x-review-details")]/ul[@class="x-review-details__body"]/li|//ul[@class="reviews--body"]/li';
	private $META_TITLE_SELECTOR = '//title';
	private $META_DESCRIPTION_SELECTOR = '//meta[@name="description"]/@content';
	private $META_KEYWORDS_SELECTOR = '//meta[@name="keywords"]/@content';
	private $LD_JSON_SELECTOR = '//script[@type="application/ld+json"]';
	private $AVAILABILITY_SELECTOR = '//a[@id="binBtn_btn_1"]';
	
	
	public const HOST = array(
		'name' => '<b>ebay.com</b>, ebay.com.au, ebay.at, benl.ebay.be, befr.ebay.be, ebay.ca, ebay.cn, ebay.fr, ebay.de, ebay.ie, ebay.it, ebay.com.hk, ebay.com.my, ebay.nl, ebay.ph, ebay.pl, ebay.com.sg, ebay.es, ebay.ch, ebay.co.uk, ebay.vn',
		'parser' => 'EbayParser',
		'valid_url' => '/ebay\.(.+)/'
	);
	
	public function __construct( $content = '', $url = '' ) {
	
		if ($content || $url) {
			$this->setContent($url, $content);			
		}
	}
	
	public function setContent( $url, $content = '') {
		
		if (empty($url) && empty($content)) {
			die('Content or url must be set.');
		}
		
		if (empty($content)) {
			$content = $this->fetch($url);
		}
		
		$this->url = $url;
		
		$content = str_replace("\n", '', $content);
		$content = $this->iconv($content);
		$this->content = preg_replace('/\s+/', ' ', $content);

		$this->dom = $this->getDomObj($content);

		/* Create a new XPath object */
		$this->xpath = new \DomXPath($this->dom);

		// Set json array
		$this->setJsonArray();
	}

	private function getDomObj( $content) {
		$dom = new \DomDocument('1.0', 'UTF-8');
		libxml_use_internal_errors(true);
		$dom->loadHTML(mb_decode_numericentity($content, array(0x80, 0xffff, 0, 0xffff), 'UTF-8'));
		libxml_use_internal_errors(false);

		return $dom;
	}

	private function setJsonArray() {
	
		$data = array();
		$patterns = array(
			'/init\((.*?)\);/',
			'/enImgCarousel\((.*?)\);/'
		);

		foreach ($patterns as $pattern) {
			preg_match_all($pattern, $this->content, $jsons);

			foreach ($jsons[1] as $json) {
				if ($json) {
					$d = $this->json_decode($json);

					if (!$d) {
						$json = $this->fixJSON($json);
						$d = $this->json_decode($json);
					}

					if ($d) {
						$data = array_merge($data, $d);
					}
				}
			}
		}
		
		$json = $this->getJson($this->content, ',"MSKU":', ',"QUANTITY":');

		if ($json) {
			
			$data['model'] = $this->json_decode($json);

			if (!is_array($data['model']) || !$data['model']) {
				
				$jsonData = $this->json_decode('{"a":' . $json . '}');

				if ($jsonData) {
					$data['model'] = $jsonData['a'];
				}
			}
		}

		if (!isset($data['model']) || !is_array($data['model']) || !$data['model']) {
			
			$s = '{"_type":"VariationViewModel"';
			$json = $this->getJson($this->content, $s, ',"options"');
			
			
			if ($json) {
				$data['model'] = $this->json_decode($s . $json);
			}
		}

		$json = $this->getJson($this->content, '"mediaList":', ',"imageContainerSize"');
		
		if ($json) {
			$data['mediaList'] = $this->json_decode($json);
		}
		
		$schemas = $this->getValue($this->LD_JSON_SELECTOR);
		
		if ($schemas) {
			$data['schemas'] = array_map(function( $json) {
					return $this->json_decode($json);
			},
				$schemas
			);
		}

		$this->attrJsonArray = $data;
	}

	public function fixJSON( $json) {
		// Do not indent
		$regex = <<<'REGEX'
~
	"[^"\\]*(?:\\.|[^"\\]*)*"
	(*SKIP)(*F)
  | '([^'\\]*(?:\\.|[^'\\]*)*)'
~x
REGEX;

		return preg_replace_callback($regex, function ( $matches) {
			return '"' . preg_replace('~\\\\.(*SKIP)(*F)|"~', '\\"', $matches[1]) . '"';
		}, $json);
	}

	private function getValue( $selector, $html = false) {
		if (empty($selector)) {
			return array();
		}
		$itmes = $this->xpath->query($selector);
		$response = array();
		foreach ($itmes as $itme) {
			if ($html) {
				$element = $this->dom->saveHTML($itme);
			} else {
				$element = $itme->nodeValue;
			}
			$response[] = trim($element);
		}
		return $response;
	}

	public function getTitle() {
		$titles = $this->getValue($this->TITLE_SELECTOR);
		
		$title = '';
		
		if ($titles) {
			$title = array_shift($titles);
		}

		$extras = $this->getValue($this->TITLE_EXTRA_SELECTOR);
		
		if ($extras && $title) {
			
			$extra = array_shift($extras);

			return str_replace($extra, '', $title);
		}
		
		return $title;
	}

	public function getCategories() {
		$categories = array_unique($this->getValue($this->CAEGORIES_SELECTOR));

		if (!$categories) {
			$categories = array_unique($this->getValue($this->CAEGORIES_SELECTOR2));
		}

		return $categories;
	}

	public function getDescription() {
		$descriptions = $this->getValue($this->DESCRIPTION_SELECTOR, true);
		return array_shift($descriptions) . $this->getDescription2();
	}

	public function getDescription2() {
		$descriptionLinks = $this->getValue($this->DESCRIPTION_SELECTOR2);
		$link = array_shift($descriptionLinks);

		if ($link) {
			return $this->fetch($link);
		}
	}

	public function getPrice() {
		
		if (isset($this->attrJsonArray['schemas']) && $this->attrJsonArray['schemas']) {
			foreach ($this->attrJsonArray['schemas'] as $schema) {
				if (isset($schema['offers']['price'])) {
					return $schema['offers']['price'];
				}
			}
		}
		
		$prices = $this->getValue($this->PRICE_SELECTOR);
		return $this->sanitizePrice(array_shift($prices));
	}	
	
	public function getRegularPrice() {
		
		$prices = $this->getValue($this->REGULAR_PRICE_SELECTOR);
		
		if ($prices) {
			return (float) $this->sanitizePrice(array_shift($prices));
		}
		
		return 0;
	}

	public function sanitizePrice( $priceText) {
		if (strpos($priceText, '.') !== false
			&& strpos($priceText, ',') !== false
			&& strpos($priceText, '.') < strpos($priceText, ',')
		) {
			$priceText = str_replace(array('.',','), array('','.'), $priceText);
		} elseif (strpos($priceText, '.') !== false) {
			$priceText = str_replace(',', '', $priceText);
		} else {
			$priceText = str_replace(',', '.', $priceText);
		}

		return preg_replace('/[^0-9.]/', '', $priceText);
	}

	public function getPriceCurrency() {
		$currencies = $this->getValue($this->CURRENCY_SELECTOR);
		return array_shift($currencies);
	}
	
	public function isAvailableForSale() {
		return (bool) $this->getValue($this->AVAILABILITY_SELECTOR);
	}

	public function getCoverImage() {
		$images = $this->getValue($this->COVER_IMAGE_SELECTOR);
		return array_shift($images);
	}

	public function getImages() {
		static $images = array();
		if ($images) {
			return $images;
		}

		$this->getAttributes();

		if (isset($this->attrJsonArray['model']['menuItemPictureIndexMap'])
			&& $this->attrJsonArray['model']['menuItemPictureIndexMap']
			&& isset($this->attrJsonArray['mediaList'])
			&& $this->attrJsonArray['mediaList']
		) {
			foreach ($this->attrJsonArray['model']['menuItemPictureIndexMap'] as $attrIndex => $imgMaps) {
				foreach ($imgMaps as $imageIndex) {
					
					$img = $this->getMediaListImages($imageIndex);
					
					if ($img) {
						$images[$attrIndex][] = $img;
					}
				}				
			}
			
			$imgs = $this->getMediaListImages();
			
			foreach ($imgs as $img) {
			
				$images[key($images)][] = current($img);
			}
			
			
		} elseif (isset($this->attrJsonArray['mediaList']) && $this->attrJsonArray['mediaList']) {
			
			$images = $this->getMediaListImages();
		}

		if (!$images) {
			if (!isset($this->attrJsonArray['imgArr']) || !$this->attrJsonArray['imgArr']) {
				if (isset($this->attrJsonArray['fsImgList'])) {
					$this->attrJsonArray['imgArr'] = $this->attrJsonArray['fsImgList'];
				}
			}

			if (isset($this->attrJsonArray['imgArr']) && $this->attrJsonArray['imgArr']) {
				foreach ($this->attrJsonArray['imgArr'] as $key => $imgs) {
					$url = null;

					if (isset($imgs['maxImageUrl'])) {
						$url = $imgs['maxImageUrl'];
					} elseif (isset($imgs['displayImgUrl'])) {
						$url = $imgs['displayImgUrl'];
					}

					if ($url) {
						$images[$key][] = $url;
					}
				}
			} elseif ($this->images) {
				$images = $this->images;
			} else {
				$urls = array_unique($this->getValue($this->IMAGE_SELECTOR));

				$urls = array_map(
					function ( $img) {
						return str_replace(array('l64', 'l500'), 'l1600', $img);
					},
					$urls
				);

				$images[] = $urls;
			}
		}

		foreach ($images as &$imgs) {
			$imgs = array_unique($imgs, SORT_STRING);
		}

		return $images;
	}
	
	public function getMediaListImages( $imageIndex = null) {
		$images = '';
		
		$mediaList = $this->attrJsonArray['mediaList'];
					
		if (isset($mediaList[$imageIndex])
			&& 'IMAGE' == $mediaList[$imageIndex]['mediaType']
		) {
			if (isset($mediaList[$imageIndex]['image']['zoomImg'])) {
				$images =  $mediaList[$imageIndex]['image']['zoomImg']['URL'];
			} elseif (isset($mediaList[$imageIndex]['image']['originalImg'])) {
				$images =  $mediaList[$imageIndex]['image']['originalImg']['URL'];
			}
		} elseif (null === $imageIndex) {
			
			$images = array();
			
			foreach ($mediaList as $i => $media) {
				if ('IMAGE' == $media['mediaType']) {
					if (isset($media['image']['zoomImg'])) {
						$images[$i][] =  $media['image']['zoomImg']['URL'];
					} elseif (isset($media['image']['originalImg'])) {
						$images[$i][] =  $media['image']['originalImg']['URL'];
					}
				}
			}
		}
		
		return $images;
	}

	public function getProductId() {
		
		return $this->getSKU();
	}
	
	public function getSKU() {
		
		if (isset($this->attrJsonArray['itemId']) && $this->attrJsonArray['itemId']) {
			return $this->attrJsonArray['itemId'];
		}
		
		$sku = $this->getValue($this->SKU_SELECTOR);
		if ($sku) {
			return array_shift($sku);
		}
		
		$parts = explode('?', $this->url);

		$parts = explode('/', $parts[0]);

		return array_pop($parts);
	}
	
	public function getDimension() {
		
		$dimensions = array(
			'length' => 0,
			'width' => 0,
			'height' => 0
		);

		$features = $this->getFeatures();
		
		$unit = 'in';

		if ($features) {
			foreach ($features as $feature) {
				foreach ($feature['attributes'] as $attr) {
					foreach ($dimensions as $dimension => $value) {
						if (stripos($attr['name'], $dimension) !== false) {
							$dimensions[$dimension] = (float) preg_replace('/[^0-9.]/', '', $attr['value']);
							
							$unit = preg_replace('/[0-9.]/', '', $attr['value']);
						}
					}
				}
			}
		}
		
		$dimensions['unit'] = $unit;

		return $dimensions;
	}
	
	public function getWeight() {
		
		$weight = array();

		$featureGroups = $this->getFeatures();
		
		if ($featureGroups) {
				
			$weightTexts = array(
				'weight',
				'poids',
				'berat',
				'الوزن',
				'gewicht',
				'peso',
				'重量',
				'무게',
				'bес',
				'น้ำหนัก',
				'Ağırlık',
				'Trọng',
				'משקל',
			);
			
			foreach ($featureGroups as $featureGroup) {
				
				foreach ($featureGroup['attributes'] as $feature) {
					
					if (in_array(trim(strtolower(str_ireplace('Item', '', $feature['name']))), $weightTexts)) {
						
						$weight = array(
							'value' => (float) preg_replace('/[^0-9.]/', '', $feature['value']),
							'unit' => preg_replace('/[0-9.]/', '', $feature['value'])
						);
						break 2;
					}
				}
			}
		}

		return $weight;
	}

	public function getBrand() {
		
		$brand = '';
		
		$featureGroups = $this->getFeatures();
		
		if ($featureGroups) {
			
			$brandTexts = array(
				'brand',
				'manufacturer',
				'marque',
				'merek',
				'العلامة',
				'marke',
				'marka',
				'marca',
				'銘柄',
				'브랜드',
				'merk',
				'бренда',
				'ชื่อยี่ห้อ',
				'hiệu',
				'מותג'
			);
			
			foreach ($featureGroups as $featureGroup) {
				
				foreach ($featureGroup['attributes'] as $feature) {
					
					if (in_array(strtolower($feature['name']), $brandTexts)) {
						$brand = $feature['value'];
						break 2;
					}
				}
			}
		}
		
		if (!$brand) {
			$brands = $this->getValue($this->BRAND_SELECTOR);
			
			if ($brands) {
				$brand = array_shift($brands);		
			}
		}
		
		return $brand;
	}

	public function getMetaTitle() {
		$metatitle = $this->getValue($this->META_TITLE_SELECTOR);
		return array_shift($metatitle);
	}

	public function getMetaDecription() {
		$metadescription = $this->getValue($this->META_DESCRIPTION_SELECTOR);
		return array_shift($metadescription);
	}

	public function getMetaKeywords() {
		$metakeywords = $this->getValue($this->META_KEYWORDS_SELECTOR);
		return array_shift($metakeywords);
	}

	public function getAttributes() {
		static $attrGroups = array();
		if ($attrGroups) {
			return $attrGroups;
		}

		if (isset($this->attrJsonArray['model']['selectMenus'])) {
			foreach ($this->attrJsonArray['model']['selectMenus'] as $group) {
				$attrValues = array();

				foreach ($group['menuItemValueIds'] as $i) {
					$attrValues[$i] = html_entity_decode($this->attrJsonArray['model']['menuItemMap'][$i]['displayName'], ENT_QUOTES);

					if (isset($this->attrJsonArray['itmVarModel']['menuItemMap'][$i]['thumbnailUrl'])) {
						$img = $this->attrJsonArray['itmVarModel']['menuItemMap'][$i]['thumbnailUrl'];
						if ($img) {
							$this->images[$i][] = str_replace('l64', 'l1600', $img);
						}
					}
				}
				$attrGroups[] = array(
					'name' => $group['displayLabel'],
					'is_color' => (int) ( stripos($group['displayLabel'], 'color') !== false || ( isset($group['hasPictures']) && $group['hasPictures'] ) ),
					'values' => $attrValues
				);
			}
		}

		return $attrGroups;
	}

	public function getCombinations() {
		static $combinations = array();
		if ($combinations) {
			return $combinations;
		}
		
		$attrs = $this->getAttributes();
		$weight = $this->getWeight();

		if (isset($this->attrJsonArray['model']['variationCombinations'])) {
			
			$price = $this->getPrice();
			
			foreach ($this->attrJsonArray['model']['variationCombinations'] as $attrKeys => $sku) {
				$attrKeys = explode('_', $attrKeys);
				
				$imageIndex = 0;
				
				$attributes = array();
				
				foreach ($attrKeys as $attrKey) {
					
					foreach ($attrs as $attrVal) {
	
						if (isset($attrVal['values'][$attrKey])) {
							
							$attributes[] = array(
								'name' => $attrVal['name'],
								'value' => $attrVal['values'][$attrKey]
							);
							
							if ($attrVal['is_color']) {
								$imageIndex = $attrKey;
							}
							
							break 1;
						}
					}
				}
				
				if (isset($this->attrJsonArray['model']['variationsMap'][$sku]['binModel']['price']['value']['convertedFromValue'])) {
					$combPrice = $this->attrJsonArray['model']['variationsMap'][$sku]['binModel']['price']['value']['convertedFromValue'];
				} elseif (isset($this->attrJsonArray['model']['variationsMap'][$sku]['binModel']['price']['value']['value'])) {
					$combPrice = $this->attrJsonArray['model']['variationsMap'][$sku]['binModel']['price']['value']['value'];
				} else {
					$combPrice = $price;
				}
				
				$regular_price = 0;
				
				if (isset($this->attrJsonArray['model']['variationsMap'][$sku]['binModel']['additionalInfo'])) {
					
					foreach ($this->attrJsonArray['model']['variationsMap'][$sku]['binModel']['additionalInfo'] as $additionalInfo) {
						
						if (isset($additionalInfo['additionalText']['textSpans'])) {
							foreach ($additionalInfo['additionalText']['textSpans'] as $textSpans) {
								
								if (isset($textSpans['styles']) && in_array('STRIKETHROUGH', $textSpans['styles'])) {
									
									$regular_price = $this->sanitizePrice($textSpans['text']);
									break 2;									
								}								
							}
						}						
					}					
				}
				
				$is_available_for_sale = true;
				
				if (isset($this->attrJsonArray['model']['variationsMap'][$sku]['quantity']['outOfStock']) && 
					$this->attrJsonArray['model']['variationsMap'][$sku]['quantity']['outOfStock']) {
					$is_available_for_sale = false;
				}
				
				if ($attributes) {
					$combinations[$sku] = array(
						'id' => $sku,
						'sku' => $sku,
						'upc' => null,
						'price' => $combPrice,
						'regular_price' => $regular_price,
						'is_available_for_sale' => $is_available_for_sale,
						'weight' => $weight,
						'image_index' => $imageIndex,
						'attributes' => $attributes
					);
				}
			}
		} elseif (isset($this->attrJsonArray['itmVarModel']['itemVariationsMap'])) {
			
			$combs = $this->attrJsonArray['itmVarModel']['itemVariationsMap'];
			
			if ($combs) {
				foreach ($combs as $sku => $comb) {
					$attributes = array();
					$imageIndex = 0;

					foreach ($attrs as $attrVal) {
						$attrIndex = '';

						if (isset($comb['traitValuesMap'][$attrVal['name']])) {
							$attrIndex = (int) $comb['traitValuesMap'][$attrVal['name']];
						}

						if ('' !== $attrIndex) {
							$attributes[] = array(
								'name' => $attrVal['name'],
								'value' => $attrVal['values'][$attrIndex]
							);

							if ($attrVal['is_color']) {
								if (isset($this->attrJsonArray['itmVarModel']['menuItemMap'][$comb['traitValuesMap'][$attrVal['name']]]['thumbnailIndex'])
								) {
									$imageIndex = (int) $this->attrJsonArray['itmVarModel']['menuItemMap'][$comb['traitValuesMap'][$attrVal['name']]]['thumbnailIndex'];
								} elseif (isset($this->attrJsonArray['model']['menuItemPictureIndexMap'][$attrIndex])
								) {
									$imageIndex = (int) current($this->attrJsonArray['model']['menuItemPictureIndexMap'][$attrIndex]);
								}
							}
						}
					}

					if ($attributes) {
						$combinations[$sku] = array(
							'id' => $sku,
							'sku' => $sku,
							'upc' => null,
							'price' => $comb['priceAmountValue']['value'],
							'regular_price' => 0,
							'is_available_for_sale' => 1,
							'weight' => $weight,
							'image_index' => $imageIndex,
							'attributes' => $attributes
						);
					}
				}
			}
		}

		return $combinations;
	}

	public function getFeatures() {
		static $featureGroups = array();

		if ($featureGroups) {
			return $featureGroups;
		}
		
		$features = $this->xpath->query($this->FEATURE_SELECTOR);

		if ($features->length) {			
			
			foreach ($features as $feature) {
				
				$attributes = array();
				
				$attrs = $this->xpath->query('.//ul/li', $feature);
				
				if ($attrs->length) {
					
					foreach ($attrs as $attr) {
						
						$attrNode = $this->xpath->query('.//div', $attr);
						
						if ($attrNode->length > 1) {
						
							$attributes[] = array(
								'name' => trim($attrNode->item(0)->nodeValue),
								'value' => trim($attrNode->item(1)->nodeValue)
							);
						}
					}
					
					if ($attributes) {
						
						$name = '';
						
						$groupObj = $this->xpath->query('.//h3', $feature);
						
						if ($groupObj->length) {
							$name = $groupObj->item(0)->nodeValue;
						}
						
						$featureGroups[] = array(
							'name' => trim($name),
							'attributes' => $attributes
						);
					}
				}
			}
		}

		if (!$featureGroups) {
			$featureGroups = $this->getFeatures2();
		}

		return $featureGroups;
	}
	
	public function getFeatures2() {
		
		$featureGroups = array();

		$features = $this->xpath->query($this->FEATURE_SELECTOR2);

		if ($features->length) {			
			
			foreach ($features as $feature) {
				
				$attributes = array();
				
				$attrs = $this->xpath->query('.//div[@class="x-prp-product-details_col"]', $feature);
				
				if ($attrs->length) {
					
					foreach ($attrs as $attr) {
						
						$nameObj = $this->xpath->query('.//span[@class="x-prp-product-details_name"]', $attr);
						$valueObj = $this->xpath->query('.//span[@class="x-prp-product-details_value"]', $attr);
						
						if ($nameObj->length && $valueObj->length) {
						
							$attributes[] = array(
								'name' => trim($nameObj->item(0)->nodeValue),
								'value' => trim($valueObj->item(0)->nodeValue)
							);
						}
					}
					
					if ($attributes) {
						
						$name = '';
						
						$groupObj = $this->xpath->query('.//h3', $feature);
						
						if ($groupObj->length) {
							$name = $groupObj->item(0)->nodeValue;
						}
						
						$featureGroups[] = array(
							'name' => trim($name),
							'attributes' => $attributes
						);
					}
				}
			}
		}
		
		if (!$featureGroups) {
			$featureGroups = $this->getFeatures3();
		}

		return $featureGroups;
	}
	
	public function getFeatures3() {
		
		$featureGroups = array();

		$attributes = array();

		$features = $this->getValue($this->FEATURE_SELECTOR3);

		if ($features) {
			$features = array_chunk($features, 2);

			foreach ($features as $attr) {
				$name = array_shift($attr);
				$value = array_shift($attr);
				$value = preg_replace('/\s+/S', ' ', $value);

				$attributes[] = array(
					'name' => trim(str_replace(':', '', $name)),
					'value' => trim($value)
				);
			}
		}

		if (!$attributes) {
			$attributes = $this->getFeatures4();
		}

		if ($attributes) {
			$featureGroups[] = array(
				'name' => 'General',
				'attributes' => $attributes,
			);
		}

		return $featureGroups;
	}

	public function getFeatures4() {
		$features = array();

		$arrtibutes = $this->getValue($this->FEATURE_SELECTOR4);

		for ($i = 0; $i < count($arrtibutes); $i+= 2) {
			if (isset($arrtibutes[$i+1])) {
				
				$name = trim(str_replace(':', '', $arrtibutes[$i]));
				$value = trim($arrtibutes[$i+1]);
				
				$features[] = array(
					'name' => $name,
					'value' => ( 'Condition' == $name ) ? current(explode(':', $value)) : $value
				);
			}
		}

		return $features;
	}

	public function getCustomerReviews( $maxReviews = 0, &$reviews = array(), $reviewlink = null) {
		if (!$reviews && !$reviewlink) {
			$reviewPageLinks = $this->getValue($this->REVIEW_SELECTOR_LINK);

			$reviewlink = array_shift($reviewPageLinks);
		}

		if ($reviewlink) {

			$content = $this->fetch($reviewlink);

			if ($content) {
				$dom = $this->getDomObj($content);
				$xpath = new \DomXPath($dom);
				
				$this->setReviews($xpath, $reviews);

				$isMaxReached = false;

				if (0 < $maxReviews && count($reviews) >= $maxReviews) {
					$isMaxReached = true;
				}

				$nextPages = $xpath->query('//a[@rel="next"]/@href');

				if ($nextPages->length && false == $isMaxReached) {
					$this->getCustomerReviews($maxReviews, $reviews, $nextPages->item(0)->nodeValue);
				}
			}
		}

		if (!$reviews) {
			$reviews = $this->getCustomerReviews2();
		}

		if (!$reviews && isset($this->attrJsonArray['schemas'])) {
			
			foreach ($this->attrJsonArray['schemas'] as $schemas) {
				
				if (isset($schemas['mainEntity']['offers']['itemOffered'][0]['review'])) {
					
					foreach ($schemas['mainEntity']['offers']['itemOffered'][0]['review'] as $review) {
						
						$reviews[] = array(
							'author' => $review['author'],
							'title' => $review['name'],
							'content' => $review['reviewBody'],
							'rating' => $review['reviewRating']['ratingValue'],
							'images' => array(),
							'videos' => array(),
							'timestamp' => gmdate(
								'Y-m-d H:i:s',
								strtotime(
									str_replace(
										',',
										'',
										$review['datePublished']
									)
								)
							)
						);
					}
					
					break;
				}
			}
		}

		return $reviews;
	}

	public function getCustomerReviews2() {
		
		return $this->setReviews($this->xpath);
	}
	
	public function setReviews( $xpath, &$reviews = array()) {
		
		$reviewArrayObject = $xpath->query($this->REVIEW_SELECTOR);

		if ($reviewArrayObject->length) {
			foreach ($reviewArrayObject as $reviewObject) {
				$author = trim(@$xpath->query('.//a[@itemprop="author"]|.//div[@class="x-review-section__author"]/a|.//a[@class="review--author"]', $reviewObject)->item(0)->nodeValue);

				if ($author) {
					$reviews[] = array(
						'author' => $author,
						'title' => @$xpath->query('.//*[@itemprop="name"]|.//h4[@class="x-review-section__title"]|.//h4[@class="review--title"]', $reviewObject)
							->item(0)->nodeValue,
						'content' => trim(@$xpath->query('.//p[@itemprop="reviewBody"]|.//div[@class="x-review-section__content"]/span[last()]|.//p[@class="review--content"]', $reviewObject)
							->item(0)->nodeValue),
						'rating' => (int) substr(
							@$xpath->query('.//div[@class="ebay-star-rating"]/@aria-label|.//*[@itemprop="ratingValue"]/@content|.//div[@class="star-rating"]/@data-stars|.//div[@class="review--star--rating"]/span[1]', $reviewObject)
							->item(0)->nodeValue,
							0,
							3
						),
						'images' => array(),
						'videos' => array(),
						'timestamp' => gmdate(
							'Y-m-d H:i:s',
							strtotime(
								str_replace(
									',',
									'',
									trim(
										@$xpath->query('.//span[@itemprop="datePublished"]/@content|.//span[@class="x-review-section__date"]|.//span[@class="review--date"]', $reviewObject)
										->item(0)->nodeValue
									)
								)
							)
						)
					);
				}
			}
		}
		
		return $reviews;
	}
	
	public function isSynchronizable() {
		return true;
	}
}

