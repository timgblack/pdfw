<?php
namespace pdfw\Types;

class PDFString extends PDFObject {
  private $value;

  public function __construct(string $value) {
    $this->SetValue($value);
  }

  public function GetValue() {
    return $this->value;
  }

  public function SetValue($value) {
    $this->value = $value;
  }

  public function __toString() {
    return '(' . str_replace(chr(6), '\\', str_replace(['(', ')', '\\'], [chr(6).'(', chr(6).')', chr(6).'\\'], $this->value)) . ')';
  }
}