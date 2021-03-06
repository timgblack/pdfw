<?php
namespace pdfw\Fonts;
use pdfw\Types\Dictionary;
use pdfw\Types\Name;

class CourierBoldOblique extends FontDefinition {
  private static $widths = [600 => range(0,255)];

  public function __construct() {
    $this->object = new Dictionary([
      'Type' => new Name('Font'),
      'BaseFont' => new Name('Courier-BoldOblique'),
      'Subtype' => new Name('Type1'),
      'Encoding' => new Name('StandardEncoding')
    ]);
    foreach(static::widths as $width => $characters) {
      if (gettype($characters) == 'array') {
        foreach ($characters as $character) {
          $this->fontWidths[chr($character)] = $width;
        }
      } else {
        $this->fontWidths[chr($characters)] = $width;
      }
    }
  }
}
