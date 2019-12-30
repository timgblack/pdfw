<?php
namespace pdfw\Fonts;

abstract class FontDefinition {
  public $object;
  public $fontWidths = [];

  public function CalculateWidth($string) {
    $width = 0;
    for ($i = 0; $i < strlen($string); $i++) {
      $width += $this->fontWidths[$string[$i]];
    }
    return $width;
  }
}