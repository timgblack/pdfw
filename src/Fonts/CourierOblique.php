<?php
namespace pdfw\Fonts;
use pdfw\Types\Dictionary;
use pdfw\Types\Name;

class CourierOblique extends FontDefinition {
  public $object = new Dictionary([
    'Type' => new Name('Font'),
    'BaseFont' => new Name('Courier-Oblique'),
    'Subtype' => new Name('Type1'),
    'Encoding' => new Name('StandardEncoding')
  ]);
  public $fontWidths = [];

  private static $widths = [600 => range(0,255)];

  public function __construct() {
    foreach(static::widths as $width => $characters) {
      if (typeof($characters) == 'array') {
        foreach ($characters as $character) {
          $this->$fontWidths[chr($character)] = $width;
        }
      } else {
        $this->$fontWidths[chr($characters)] = $width;
      }
    }
}
