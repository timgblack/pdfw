<?php
namespace pdfw\Types;

class Boolean extends PDFObject {
  private $value;

  public function __construct(bool $value) {
    $this->SetValue($value);
  }

  public function GetValue() {
    return $this->value;
  }

  public function SetValue(bool $value) {
    $this->value = $value;
  }

  public function __toString() {
    return $this->value ? 'true' : 'false';
  }
}