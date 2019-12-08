<?php
namespace pdfw\Types;

class Number extends PDFObject {
  private $value;

  public function __construct($value) {
    $this->SetValue($value);
  }

  public function GetValue() {
    return $this->value;
  }

  public function SetValue($value) {
    $this->value = $value;
  }

  public function __toString() {
    return (string)$this->value;
  }
}