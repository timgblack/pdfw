<?php
namespace pdfw\Types;

class PDFArray extends PDFObject implements \ArrayAccess {
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
    return '[ ' . implode(' ', $this->value) . ' ]';
  }

  public function __get($key) {
    return $this->value[$key];
  }

  public function __set($key, $value) {
    $this->value[$key] = $value;
  }

  public function offsetSet($offset, $value) {
    $this->value[$offset] = $value;
  }

  public function offsetExists($offset) {
    return isset($this->value[$offset]);
  }

  public function offsetUnset($offset) {
    unset($this->value[$offset]);
  }

  public function offsetGet($offset) {
    return isset($this->value[$offset]) ? $this->value[$offset] : null;
  }
}