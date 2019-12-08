<?php
namespace pdfw\Types;

class Name extends PDFObject {
  private $value;

  public function __construct($value) {
   $this->SetValue($value);
  }

  public function GetValue() {
    return $this->value;
  }

  public function SetValue($value) {
    $this->value = ltrim((string)$value, '/');
  }

  public function __toString() {
    return '/' . $this->value;
  }
}