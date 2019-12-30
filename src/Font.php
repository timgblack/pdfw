<?php
namespace pdfw;
use pdfw\Types\Name;
use pdfw\Types\PDFObject;

class Font extends PDFObject {
  public static $allFonts = ['Ariel', 'Helvetica', 'Times', 'Symbol', 'ZapfDingbats'];
  private static $symbolFonts = ['Symbol', 'ZapfDingbats'];
  public $fontDefinition;

  private $name;

  public function __construct($name, $bold = false, $oblique = false) {
    if (!in_array($name, static::$allFonts)) {
      throw new Exception();
    }
    if (in_array($name, static::$symbolFonts)) {
      $bold = false;
      $oblique = false;
    }
    $fontDefinitionClass = __NAMESPACE__ . '\\Fonts\\';
    $fontDefinitionClass .= $name . ($bold ? 'Bold' : '') . ($oblique ? 'Oblique' : '');
    $this->fontDefinition = new $fontDefinitionClass();
  }

  public function GetName() {
    return $this->name;
  }

  public function SetName($name) {
    $this->name = new Name($name);
  }

  public function __toString() {
    return (string)$this->fontDefinition->object;
  }
}
